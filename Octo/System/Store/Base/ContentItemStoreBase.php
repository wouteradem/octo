<?php

/**
 * ContentItem base store for table: content_item
 */

namespace Octo\System\Store\Base;

use PDOException;
use b8\Cache;
use b8\Database;
use b8\Database\Query;
use b8\Database\Query\Criteria;
use b8\Exception\StoreException;
use Octo\Store;
use Octo\System\Model\ContentItem;

/**
 * ContentItem Base Store
 */
trait ContentItemStoreBase
{
    protected function init()
    {
        $this->tableName = 'content_item';
        $this->modelName = '\Octo\System\Model\ContentItem';
        $this->primaryKey = 'id';
    }
    /**
    * @param $value
    * @param string $useConnection Connection type to use.
    * @throws StoreException
    * @return ContentItem
    */
    public function getByPrimaryKey($value, $useConnection = 'read')
    {
        return $this->getById($value, $useConnection);
    }


    /**
    * @param $value
    * @param string $useConnection Connection type to use.
    * @throws StoreException
    * @return ContentItem
    */
    public function getById($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new StoreException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = new Query($this->getNamespace('ContentItem').'\Model\ContentItem', $useConnection);
        $query->select('*')->from('content_item')->limit(1);
        $query->where('`id` = :id');
        $query->bind(':id', $value);

        try {
            $query->execute();
            return $query->fetch();
        } catch (PDOException $ex) {
            throw new StoreException('Could not get ContentItem by Id', 0, $ex);
        }
    }
}
