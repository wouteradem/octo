<?php

namespace Octo;

use b8\Database;
use b8\Config;
use b8\Store\Factory;

abstract class Store extends \b8\Store
{
    /**
     * @param string $store Name of the store you want to load.
     * @return \b8\Store
     */
    public static function get($store)
    {

        $namespace = self::getModelNamespace($store);

        if (!is_null($namespace)) {
            return Factory::getStore($store, $namespace);
        }

        return null;
    }

    public static function getModelNamespace($model)
    {
        $config = Config::getInstance();
        return $config->get('app.namespaces.'.$model, null);
    }

    protected function getNamespace($model)
    {
        return self::getModelNamespace($model);
    }

    /**
     * REPLACE INTO
     *
     * @param Model $obj
     * @param bool $saveAllColumns
     * @return null
     */
    public function saveByReplace(Model $obj, $saveAllColumns = false)
    {
        $rtn = null;
        $data = $obj->getDataArray();
        $modified = ($saveAllColumns) ? array_keys($data) : $obj->getModified();

        $cols = array();
        $values = array();
        $qParams = array();
        foreach ($modified as $key) {
            $cols[] = $key;
            $values[] = ':' . $key;
            $qParams[':' . $key] = $data[$key];
        }

        if (count($cols)) {
            $colString = implode(', ', $cols);
            $valString = implode(', ', $values);

            $qs = 'REPLACE INTO ' . $this->tableName . ' (' . $colString . ') VALUES (' . $valString . ')';
            $q = Database::getConnection('write')->prepare($qs);

            if ($q->execute($qParams)) {
                $id = !empty($data[$this->primaryKey]) ? $data[$this->primaryKey] : Database::getConnection(
                    'write'
                )->lastInsertId();
                $rtn = $this->getByPrimaryKey($id, 'write');
            }
        }

        return $rtn;
    }
}
