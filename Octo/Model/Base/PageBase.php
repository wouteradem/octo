<?php

/**
 * Page base model for table: page
 */

namespace Octo\Model\Base;

use Octo\Model;
use b8\Store\Factory;

/**
 * Page Base Model
 */
class PageBase extends Model
{
    /**
    * @var array
    */
    public static $sleepable = [];

    /**
    * @var string
    */
    protected $tableName = 'page';

    /**
    * @var string
    */
    protected $modelName = 'Page';

    /**
    * @var array
    */
    protected $data = [
        'id' => null,
        'parent_id' => null,
        'current_version_id' => null,
        'uri' => null,
    ];

    /**
    * @var array
    */
    protected $getters = [
        // Direct property getters:
        'id' => 'getId',
        'parent_id' => 'getParentId',
        'current_version_id' => 'getCurrentVersionId',
        'uri' => 'getUri',

        // Foreign key getters:
        'CurrentVersion' => 'getCurrentVersion',
        'Parent' => 'getParent',
    ];

    /**
    * @var array
    */
    protected $setters = [
        // Direct property setters:
        'id' => 'setId',
        'parent_id' => 'setParentId',
        'current_version_id' => 'setCurrentVersionId',
        'uri' => 'setUri',

        // Foreign key setters:
        'CurrentVersion' => 'setCurrentVersion',
        'Parent' => 'setParent',
    ];

    /**
    * @var array
    */
    public $columns = [
        'id' => [
            'type' => 'char',
            'length' => 5,
            'primary_key' => true,
        ],
        'parent_id' => [
            'type' => 'char',
            'length' => 5,
            'nullable' => true,
            'default' => null,
        ],
        'current_version_id' => [
            'type' => 'int',
            'length' => 11,
            'nullable' => true,
            'default' => null,
        ],
        'uri' => [
            'type' => 'varchar',
            'length' => 500,
        ],
    ];

    /**
    * @var array
    */
    public $indexes = [
        'PRIMARY' => ['unique' => true, 'columns' => 'id'],
        'uniq_page_uri' => ['unique' => true, 'columns' => 'uri'],
        'fk_page_parent' => ['columns' => 'parent_id'],
        'fk_page_current_version' => ['columns' => 'current_version_id'],
    ];

    /**
    * @var array
    */
    public $foreignKeys = [
        'fk_page_current_version' => [
            'local_col' => 'current_version_id',
            'update' => 'CASCADE',
            'delete' => 'SET NULL',
            'table' => 'page_version',
            'col' => 'id'
        ],
        'fk_page_parent' => [
            'local_col' => 'parent_id',
            'update' => 'CASCADE',
            'delete' => 'SET NULL',
            'table' => 'page',
            'col' => 'id'
        ],
    ];

    /**
    * Get the value of Id / id.
    *
    * @return string
    */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
    * Get the value of ParentId / parent_id.
    *
    * @return string
    */
    public function getParentId()
    {
        $rtn = $this->data['parent_id'];

        return $rtn;
    }

    /**
    * Get the value of CurrentVersionId / current_version_id.
    *
    * @return int
    */
    public function getCurrentVersionId()
    {
        $rtn = $this->data['current_version_id'];

        return $rtn;
    }

    /**
    * Get the value of Uri / uri.
    *
    * @return string
    */
    public function getUri()
    {
        $rtn = $this->data['uri'];

        return $rtn;
    }

    /**
    * Set the value of Id / id.
    *
    * Must not be null.
    * @param $value string
    */
    public function setId($value)
    {
        $this->validateNotNull('Id', $value);
        $this->validateString('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;
        $this->setModified('id');
    }

    /**
    * Set the value of ParentId / parent_id.
    *
    * @param $value string
    */
    public function setParentId($value)
    {
        $this->validateString('ParentId', $value);

        if ($this->data['parent_id'] === $value) {
            return;
        }

        $this->data['parent_id'] = $value;
        $this->setModified('parent_id');
    }

    /**
    * Set the value of CurrentVersionId / current_version_id.
    *
    * @param $value int
    */
    public function setCurrentVersionId($value)
    {
        $this->validateInt('CurrentVersionId', $value);

        if ($this->data['current_version_id'] === $value) {
            return;
        }

        $this->data['current_version_id'] = $value;
        $this->setModified('current_version_id');
    }

    /**
    * Set the value of Uri / uri.
    *
    * Must not be null.
    * @param $value string
    */
    public function setUri($value)
    {
        $this->validateNotNull('Uri', $value);
        $this->validateString('Uri', $value);

        if ($this->data['uri'] === $value) {
            return;
        }

        $this->data['uri'] = $value;
        $this->setModified('uri');
    }

    /**
    * Get the PageVersion model for this Page by Id.
    *
    * @uses \Octo\Store\PageVersionStore::getById()
    * @uses \Octo\Model\PageVersion
    * @return \Octo\Model\PageVersion
    */
    public function getCurrentVersion()
    {
        $key = $this->getCurrentVersionId();

        if (empty($key)) {
            return null;
        }

        return Factory::getStore('PageVersion', 'Octo')->getById($key);
    }

    /**
    * Set CurrentVersion - Accepts an ID, an array representing a PageVersion or a PageVersion model.
    *
    * @param $value mixed
    */
    public function setCurrentVersion($value)
    {
        // Is this an instance of PageVersion?
        if ($value instanceof \Octo\Model\PageVersion) {
            return $this->setCurrentVersionObject($value);
        }

        // Is this an array representing a PageVersion item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setCurrentVersionId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setCurrentVersionId($value);
    }

    /**
    * Set CurrentVersion - Accepts a PageVersion model.
    *
    * @param $value \Octo\Model\PageVersion
    */
    public function setCurrentVersionObject(\Octo\Model\PageVersion $value)
    {
        return $this->setCurrentVersionId($value->getId());
    }
    /**
    * Get the Page model for this Page by Id.
    *
    * @uses \Octo\Store\PageStore::getById()
    * @uses \Octo\Model\Page
    * @return \Octo\Model\Page
    */
    public function getParent()
    {
        $key = $this->getParentId();

        if (empty($key)) {
            return null;
        }

        return Factory::getStore('Page', 'Octo')->getById($key);
    }

    /**
    * Set Parent - Accepts an ID, an array representing a Page or a Page model.
    *
    * @param $value mixed
    */
    public function setParent($value)
    {
        // Is this an instance of Page?
        if ($value instanceof \Octo\Model\Page) {
            return $this->setParentObject($value);
        }

        // Is this an array representing a Page item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setParentId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setParentId($value);
    }

    /**
    * Set Parent - Accepts a Page model.
    *
    * @param $value \Octo\Model\Page
    */
    public function setParentObject(\Octo\Model\Page $value)
    {
        return $this->setParentId($value->getId());
    }
}
