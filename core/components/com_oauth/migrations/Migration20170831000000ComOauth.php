<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_oauth
 **/
class Migration20170831000000ComOauth extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('oauth');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('oauth');
	}
}
