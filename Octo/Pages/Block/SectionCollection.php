<?php

namespace Octo\Pages\Block;

use b8\Database;
use Octo\Block;
use Octo\Pages\Model\Page;
use Octo\Store;
use Octo\Template;

class SectionCollection extends Block
{
    /**
     * @var \Octo\Pages\Store\PageStore
     */
    protected $pageStore;

    public static function getInfo()
    {
        return [
            'title' => 'Section Collection',
            'editor' => true,
            'js' => ['/assets/backoffice/js/block/sectioncollection.js'],
        ];
    }

    public function init()
    {
        $this->pageStore = Store::get('Page');
    }

    public function renderNow()
    {
        $this->limit = 25;

        if (array_key_exists('limit', $this->templateParams)) {
            $this->limit = $this->templateParams['limit'] ? $this->templateParams['limit'] : $this->limit;
        }

        $this->parent = $this->page;

        if (array_key_exists('parent', $this->content)) {
            $this->parent = $this->pageStore->getById($this->content['parent']);
        }

        $this->view->pages = $this->getChildren($this->parent);
    }

    protected function getChildren(Page $page)
    {
        $options = ['order' => [['position', 'ASC']], 'limit' => $this->limit];
        $children = $this->pageStore->getByParentId($page->getId(), $options);

        if (count($children)) {
            return $children;
        }

        return null;
    }
}
