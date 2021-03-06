<?php

namespace Octo;

use Exception;
use b8\Exception\HttpException;
use b8\Http\Response;
use b8\Http\Response\RedirectResponse;
use Octo;
use Octo\Admin;
use Octo\Admin\Controller;
use Octo\Admin\Menu;
use Octo\BlockManager;
use Octo\System\Model\Log;
use Octo\Store;
use Octo\Template;

/**
 * Class Application
 * @package Octo
 */
class Application extends \b8\Application
{
    /**
     * Setup the application and register basic routes
     */
    public function init()
    {
        $path = $this->request->getPath();

        if (substr($path, -1) == '/' && $path != '/') {
            header('HTTP/1.1 301 Moved Permanently', true, 301);
            header('Location: ' . substr($path, 0, -1));
            die;
        }

        $this->router->clearRoutes();
        Event::trigger('RegisterRoutes', $this->router);
        $this->router->register('/:controller/:action', ['namespace' => 'Controller', 'action' => 'index']);

        $route = '/'.$this->config->get('site.admin_uri').'/:controller/:action';
        $defaults = ['namespace' => 'Admin\\Controller', 'controller' => 'Dashboard', 'action' => 'index'];
        $request =& $this->request;

        $denied = [$this, 'permissionDenied'];

        return $this->registerRouter($route, $defaults, $request, $denied);
    }

    /**
     * Register advanced routers
     *
     * @param $route
     * @param $defaults
     * @param $request
     * @param $denied
     */
    public function registerRouter($route, $defaults, $request, $denied)
    {
        $this->router->register($route, $defaults, function (&$route, Response &$response) use (&$request, &$denied) {
            if (!empty($_GET['session_auth'])) {
                session_id($_GET['session_auth']);
            }

            session_start();

            if ($route['controller'] != 'session') {
                if (!empty($_SESSION['user_id'])) {
                    return $this->setupUserProperties($route, $response, $denied);
                }

                if ($request->isAjax()) {
                    $response->setResponseCode(401);
                    $response->setContent('');
                } else {
                    $_SESSION['previous_url'] = $_SERVER['REQUEST_URI'];
                    $response = new RedirectResponse($this->response);
                    $response->setHeader('Location', '/'.$this->config->get('site.admin_uri').'/session/login');
                }

                return false;
            }

            return true;
        });
    }

    /**
     * Setup the user's permissions etc. for the route
     *
     * @param $route
     * @param $response
     * @param $denied
     * @return bool
     */
    protected function setupUserProperties($route, $response, $denied)
    {
        $user = Store::get('User')->getByPrimaryKey($_SESSION['user_id']);

        if ($user && $user->getActive()) {
            $_SESSION['user'] = $user;

            $uri = '/';

            if ($route['controller'] != 'Dashboard') {
                $uri .= $route['controller'];
            }

            if ($route['action'] != 'index') {
                $uri .= '/' . $route['action'];
            }

            if (in_array($route['controller'], ['categories', 'media']) && isset($route['args'][0])) {
                $uri .= '/' . $route['args'][0];
            }

            if (!$user->canAccess($uri) && is_callable($denied)) {
                $denied($user, $uri, $response);
                return false;
            }

            return true;
        }
    }

    /**
     * Handle the request
     *
     * @return mixed
     * @throws \b8\Exception\HttpException
     * @throws \Exception
     * @throws \Exception
     */
    public function handleRequest()
    {
        try {
            $rtn = parent::handleRequest();
        } catch (HttpException $ex) {
            if (defined('CMS_ENV') && CMS_ENV == 'development' && !array_key_exists('ex', $_GET)) {
                throw $ex;
            }

            $rtn = $this->handleHttpError($ex->getErrorCode());
        } catch (Exception $ex) {
            if (defined('CMS_ENV') && CMS_ENV == 'development' && !array_key_exists('ex', $_GET)) {
                throw $ex;
            }

            $rtn = $this->handleHttpError(500);
        }

        return $rtn;
    }

    /**
     * Handle HTTP error
     *
     * @param $code
     * @return mixed
     */
    protected function handleHttpError($code)
    {
        if (Template::exists('Error/' . $code)) {

            $this->response->setResponseCode($code);

            $template = Template::getPublicTemplate('Error/' . $code);
            $blockManager = new BlockManager();
            $blockManager->setRequest($this->request);
            $blockManager->setResponse($this->response);
            $blockManager->attachToTemplate($template);

            $content = $template->render();
            $content = str_replace('{!@octo.meta}', '<title>Error</title>', $content);
            $this->response->setContent($content);
        }

        return $this->response;
    }

    /**
     * @return \b8\Controller
     */
    public function getController()
    {
        if (empty($this->controller)) {
            $class = null;
            $controller = $this->toPhpName($this->route['controller']);
            $controllerClass = '\\Octo\\' . $this->route['namespace'];

            if (class_exists($controllerClass)) {
                $class = $controllerClass::getClass($controller);
            }

            if (!is_null($class)) {
                $this->controller = $this->loadController($class);
            }
        }

        return $this->controller;
    }

    /**
     * @param $route
     * @return bool True if controller exists
     */
    protected function controllerExists($route)
    {
        $controller = $this->toPhpName($route['controller']);
        $controllerClass = '\\Octo\\' . $route['namespace'];
        $class = $controllerClass::getClass($controller);

        return !is_null($class);
    }

    /**
     * Callback if permission denied to access
     *
     * @param $user
     * @param $uri
     * @param $response
     */
    protected function permissionDenied($user, $uri, &$response)
    {
        $_SESSION['GlobalMessage']['error'] = 'You do not have permission to access: ' . $uri;

        $log = Log::create(Log::TYPE_PERMISSION, 'user', 'Unauthorised access attempt.');
        $log->setUser($user);
        $log->setLink($uri);
        $log->save();

        $response = new RedirectResponse();
        $response->setHeader('Location', '/'.$this->config->get('site.admin_uri'));
    }
}
