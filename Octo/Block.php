<?php

namespace Octo;

use b8\Config;
use b8\Http\Response;
use b8\Http\Request;
use Octo\Pages\Model\Page;
use Octo\Pages\Model\PageVersion;
use Octo\Template;

abstract class Block
{
    /**
     * @var bool
     */
    protected $hasUriExtensions = false;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $templateParams = [];

    /**
     * @var \b8\Http\Request
     */
    protected $request;

    /**
     * @var \b8\Http\Response
     */
    protected $response;

    /**
     * @var Template
     */
    protected $view;

    /**
     * @var \Octo\Pages\Model\Page
     */
    protected $page;

    /**
     * @var \Octo\Pages\Model\PageVersion
     */
    protected $pageVersion;

    protected $dataStore;


    public static function getBlocks()
    {
        $blocks = Config::getInstance()->get('Octo.namespaces.blocks');
        $rtn = [];

        foreach ($blocks as $block => $namespace) {
            $namespace .= '\\Block';
            $rtn[$block] = self::getBlockInformation($namespace, $block);
        }

        return $rtn;
    }

    protected static function getBlockInformation($namespace, $block)
    {
        $fullClass = $namespace . "\\" . $block;
        $rtn = array_merge(['namespace' => $namespace, 'class' => $block], $fullClass::getInfo());
        return $rtn;
    }

    /**
     * @param $type
     * @param $content
     * @return Block
     * @throws \Exception
     */
    public static function create($type, $content)
    {
        $config = \b8\Config::getInstance();
        $namespace = $config->get('Octo.namespaces.blocks.' . $type);
        $class = '\\' . $namespace . '\\Block\\' . $type;

        if (class_exists($class)) {
            return new $class($content);
        }

        throw new \Exception('Block type ' . $type . ' does not exist');
    }

    public function __construct($content)
    {
        $this->content = $content;
        $this->init();
    }

    protected function init()
    {

    }

    public function hasUriExtensions()
    {
        return $this->hasUriExtensions;
    }

    public function render()
    {
        $parts = explode('\\', get_class($this));
        $class = array_pop($parts);

        if (isset($this->templateParams['template'])) {
            try {
                $this->view = Template::getPublicTemplate('Block/' . $this->templateParams['template']);
            } catch (\Exception $e) {
                // TODO: Something with this
                throw $e;
            }
        } else {
            $this->view = Template::getPublicTemplate('Block/' . $class);
        }

        $rtn = $this->renderNow();

        if (method_exists($this, 'renderDeferred')) {
            $manager = Event::getEventManager();
            $manager->registerListener('PageLoaded', [$this, 'renderDeferred']);
        }

        if ($rtn === false) {
            return '';
        }

        if (!is_string($rtn)) {
            $rtn = $this->view->render();
        }

        if (!empty($rtn) && isset($this->templateParams['wrapper'])) {
            $wrapper = Template::getPublicTemplate('Block/' . $this->templateParams['wrapper']);
            $wrapper->content = $rtn;
            $rtn = $wrapper->render();
        }

        return $rtn;
    }

    abstract public function renderNow();

    public function setRequest(Request &$request)
    {
        $this->request =& $request;
    }

    public function setResponse(Response &$response)
    {
        $this->response =& $response;
    }

    public function setUriExtension($uri)
    {
        $this->uri = $uri;
    }

    public function setTemplateParams(array $args)
    {
        $this->templateParams = $args;
    }

    public function setPage(Page &$page)
    {
        $this->page =& $page;
    }

    public function setPageVersion(PageVersion &$pageVersion)
    {
        $this->pageVersion =& $pageVersion;
    }

    public function setDataStore(array &$dataStore)
    {
        $this->dataStore =& $dataStore;
    }

    public function getContent($tagId, $default = null)
    {
        $rtn = $default;

        if (array_key_exists($tagId, $this->content)) {
            $rtn = $this->content[$tagId];
        }

        $hasInherit = array_key_exists('inherit', $this->templateParams);
        if ($rtn === $default && $hasInherit && $this->templateParams['inherit']) {
            $page = $this->page;

            while ($rtn === $default) {
                if (!$page || !$page->getParentId()) {
                    break;
                }

                $page = $page->getParent();
                $content = $page->getCurrentVersion()->getContentItem()->getContent();
                $content = json_decode($content, true);

                if (isset($content[$this->templateParams['id']][$tagId])) {
                    $rtn = $content[$this->templateParams['id']][$tagId];
                }
            }
        }

        return $rtn;
    }
}
