<?php

namespace Octo\Blog\Block;

use b8\Config;
use Octo\News\Block\News;

class Blog extends News
{
    /**
     * @var string Type of article to load
     */
    protected static $articleType = 'Blog';

    /**
     * @var string Scope of articles to filter
     */
    protected static $scope = 'blog';

    public static function getInfo()
    {
        $config = Config::getInstance();
        return ['title' => 'Blog Archive', 'editor' => true, 'js' => ['/assets/backoffice/js/block/blog.js']];
    }
}