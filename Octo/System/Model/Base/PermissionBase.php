<?php

/**
 * Permission base model for table: permission
 */

namespace Octo\System\Model\Base;

use b8\Store\Factory;

/**
 * Permission Base Model
 */
trait PermissionBase
{
    protected function init()
    {
        $this->tableName = 'permission';
        $this->modelName = 'Permission';

        // Columns:
        $this->data['id'] = null;
        $this->getters['id'] = 'getId';
        $this->setters['id'] = 'setId';
        $this->data['user_id'] = null;
        $this->getters['user_id'] = 'getUserId';
        $this->setters['user_id'] = 'setUserId';
        $this->data['uri'] = null;
        $this->getters['uri'] = 'getUri';
        $this->setters['uri'] = 'setUri';
        $this->data['can_access'] = null;
        $this->getters['can_access'] = 'getCanAccess';
        $this->setters['can_access'] = 'setCanAccess';

        // Foreign keys:
        $this->getters['User'] = 'getUser';
        $this->setters['User'] = 'setUser';
    }
    /**
    * Get the value of Id / id.
    *
    * @return int
    */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
    * Get the value of UserId / user_id.
    *
    * @return int
    */
    public function getUserId()
    {
        $rtn = $this->data['user_id'];

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
    * Get the value of CanAccess / can_access.
    *
    * @return int
    */
    public function getCanAccess()
    {
        $rtn = $this->data['can_access'];

        return $rtn;
    }


    /**
    * Set the value of Id / id.
    *
    * Must not be null.
    * @param $value int
    */
    public function setId($value)
    {
        $this->validateInt('Id', $value);
        $this->validateNotNull('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;
        $this->setModified('id');
    }

    /**
    * Set the value of UserId / user_id.
    *
    * Must not be null.
    * @param $value int
    */
    public function setUserId($value)
    {
        $this->validateInt('UserId', $value);

        // As this is a foreign key, empty values should be treated as null:
        if (empty($value)) {
            $value = null;
        }

        $this->validateNotNull('UserId', $value);

        if ($this->data['user_id'] === $value) {
            return;
        }

        $this->data['user_id'] = $value;
        $this->setModified('user_id');
    }

    /**
    * Set the value of Uri / uri.
    *
    * Must not be null.
    * @param $value string
    */
    public function setUri($value)
    {
        $this->validateString('Uri', $value);
        $this->validateNotNull('Uri', $value);

        if ($this->data['uri'] === $value) {
            return;
        }

        $this->data['uri'] = $value;
        $this->setModified('uri');
    }

    /**
    * Set the value of CanAccess / can_access.
    *
    * Must not be null.
    * @param $value int
    */
    public function setCanAccess($value)
    {
        $this->validateInt('CanAccess', $value);
        $this->validateNotNull('CanAccess', $value);

        if ($this->data['can_access'] === $value) {
            return;
        }

        $this->data['can_access'] = $value;
        $this->setModified('can_access');
    }
    /**
    * Get the User model for this Permission by Id.
    *
    * @uses \Octo\System\Store\UserStore::getById()
    * @uses \Octo\System\Model\User
    * @return \Octo\System\Model\User
    */
    public function getUser()
    {
        $key = $this->getUserId();

        if (empty($key)) {
            return null;
        }

        return Factory::getStore('User', 'Octo\System')->getById($key);
    }

    /**
    * Set User - Accepts an ID, an array representing a User or a User model.
    *
    * @param $value mixed
    */
    public function setUser($value)
    {
        // Is this an instance of User?
        if ($value instanceof \Octo\System\Model\User) {
            return $this->setUserObject($value);
        }

        // Is this an array representing a User item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setUserId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setUserId($value);
    }

    /**
    * Set User - Accepts a User model.
    *
    * @param $value \Octo\System\Model\User
    */
    public function setUserObject(\Octo\System\Model\User $value)
    {
        return $this->setUserId($value->getId());
    }
}
