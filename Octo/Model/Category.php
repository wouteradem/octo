<?php

/**
 * Category model for table: category
 */

namespace Octo\Model;

use Octo\Model\Base\CategoryBase;

/**
 * Category Model
 * @uses Octo\Model\Base\CategoryBase
 */
class Category extends CategoryBase
{

    public function __construct($initialData = array())
    {
        parent::__construct($initialData);

        $this->getters['has_children'] = 'getHasChildren';
    }

    /**
     * Get the absolute path to the image
     *
     * @return string
     * @author James Inman
     */
    public function getHasChildren()
    {
        if (isset($this->data['has_children'])) {
            return $this->data['has_children'];
        }
    }
}
