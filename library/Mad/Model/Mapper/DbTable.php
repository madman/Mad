<?php

/**
 * Mad library for Zend Framework
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://devcomm.org.ua/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@devcomm.org.ua so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 Devcomm (http://www.devcomm.org.ua)
 * @license    http://devcomm.org.ua/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * Mapper that use table of database as storage
 *
 * @category   Mad
 * @package    Model
 * @author     madman <madman@devcomm.org.ua>
 */
class Mad_Model_Mapper_DbTable extends Mad_Model_Mapper_Abstract {

    /**
     * @var     Mad_Db_Table
     */
    protected $_dbTable;

    /**
     * Sets the database table gateway to this mapper
     * 
     * @param   string|Mad_Db_Table $dbTable
     * @throws  Mad_Exception
     */
    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            if (!class_exists($dbTable)) {
                require_once 'Mad/Exception.php';
                throw new Mad_Exception('Non-existing table class provided');
            }
            $dbTable = new $dbTable;
        }
        if (!$dbTable instanceof Mad_Db_Table) {
            require_once 'Mad/Exception.php';
            throw new Mad_Exception('Invalid table class provided');
        }
        $this->_dbTable = $dbTable;
    }

    /**
     * Retrieves the database table gateway from this mapper
     * 
     * @return Mad_Db_Table
     */
    abstract public function getDbTable();

    /**
     * Save model to database
     * 
     * @param   Mad_Model_Abstract $model
     * @return  Mad_Model_Abstract
     * @throws  Mad_Exception 
     */
    public function save(Mad_Model_Abstract $model) {
        if (!$model instanceof App_Model_Abstract) {
            require_once 'Mad/Exception.php';
            throw new Mad_Exception('Model should be instance of Mad_Model_Abstract');
        }

        $data = $model->toArray();
        $primaryKey = $this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY);
        $isNew = false;

        // prepare primary key data
        foreach ($primaryKey as $columnName) {
            $primaryKeyValue[$columnName] = $data[$columnName];
            if (!$data[$columnName]) {
                $isNew = true;
            }
        }

        // is autoincrimented?
        if ($this->getDbTable()->info(Zend_Db_Table_Abstract::SEQUENCE)) {
            // unset autoincriment key
            reset($primaryKey);
            $keyColumn = current($primaryKey);
            unset($data[$keyColumn]);
        }

        if ($isNew) { // insert
            $result = $this->getDbTable()->insert($data);
            // set value of primary key (for autoincrimented keys)
            if (is_array($result)) {
                foreach ($primaryKey as $columnName) {
                    $model->$columnName = $result[$columnName];
                }
            } else {
                $model->setId($result);
            }
        } else { // update
            foreach ($primaryKeyValue as $columnName => $value) {
                $where = array($columnName . ' = ?' => $value);
            }

            $result = $this->getDbTable()->update($data, $where);
        }

        return $model;
    }

    /**
     * Delete model from database
     * 
     * @param  Mad_Model_Abstract $model
     * @return boolean|int
     * @throws Mad_Exception 
     */
    public function delete($model) {
        if (!$model instanceof Mad_Model_Abstract) {
            require_once 'Mad/Exception.php';
            throw new Mad_Exception('Model should be instance of Mad_Model_Abstract');
        }

        $data = $model->toArray();
        $primaryKey = $this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY);
        $isNew = false;


        foreach ($primaryKey as $columnName) {
            $where = array($columnName . ' = ?' => $data[$columnName]);
            if (!$data[$columnName]) {
                $isNew = true;
            }
        }

        if ($isNew) {
            require_once 'Mad/Exception.php';
            throw new Mad_Exception('Model is not stored yet');
        }

        return $this->getDbTable()->delete($where);
    }

    /**
     * Fetch all entries that match given conditions
     * 
     * @param   string|array $where
     * @param   string|array $order
     * @param   int|null $count
     * @param   int|null $offset
     * @return  array
     * @throws  Mad_Exception
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $entries = array();
        $model = null;
        $className = $this->getModelClassname();

        if (!class_exists($className)) {
            require_once 'Mad/Exception.php';
            throw new Mad_Exception('Non-existing model class name provided');
        }

        if (null !== ($resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset))) {
            foreach ($resultSet as $row) {
                $model = new $className;
                if (!$model instanceof Mad_Model_Abstract) {
                    require_once 'Mad/Exception.php';
                    throw new Mad_Exception('Invalid model class provided');
                }
                $model->populate($row);
                $entries[] = $model;
                unset($model);
            }
        }
        return $entries;
    }

}