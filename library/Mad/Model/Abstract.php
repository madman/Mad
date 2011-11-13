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
 * Abstract model class
 *
 * @category   Mad
 * @package    Model
 * @author     madman <madman@devcomm.org.ua>
 */
class Mad_Model_Abstract {

    /**
     * return Mad_Model_Mapper_Abstract
     */
    abstract function getMapper();

    /**
     * Populate this data model with given data elements
     * 
     * @param   array
     * @return  Mad_Model_Abstract
     */
    abstract public function populate($row);

    /**
     * Converts this model into an array for storage or rendering purposes
     * 
     * @return  array
     */
    abstract public function toArray();

    /**
     * Magic method.
     * 
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args = array()) {
        $params = array_merge(array($this), $args);

        return call_user_func_array(array($this->getMapper(), $name), $params);
    }

}