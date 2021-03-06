<?php

namespace Octo;

use b8\Config;

abstract class Controller extends \b8\Controller
{
    /**
     * @var Octo\AssetManager
     */
    public $assets;

    /**
     * b8 framework requires that controllers have an init() method
     */
    public function init()
    {
        $this->assets = Config::getInstance()->get('Octo.AssetManager');
    }

    public function handleAction($action, $params)
    {
        $output = parent::handleAction($action, $params);
        $this->response->setContent($output);

        return $this->response;
    }

    public static function getClass($controller)
    {
        $config = Config::getInstance();
        $siteModules = $config->get('ModuleManager')->getEnabled();

        foreach ($siteModules as $namespace => $modules) {
            foreach ($modules as $module) {
                $class = "\\{$namespace}\\{$module}\\Controller\\{$controller}Controller";

                if (class_exists($class)) {
                    return $class;
                }
            }
        }

        return null;
    }
}
