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
 * Abstract mapper class
 *
 * @category   Mad
 * @package    Db
 * @author     madman <madman@devcomm.org.ua>
 */

abstract class Mad_Db_Table extends Zend_Db_Table_Abstract {
	
	/**
	 * Get table name
	 * 
	 * @return string Table name
	 */
	public function getName() {
		return $this->_name;
	}
	
	public function __toString() {
		return $this->getName();
	}
}
