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
 * Migration script for adding Content - External HREF plugin
 **/
class Migration20190319000000PlgContentExternalhref extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('content', 'externalhref');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('content', 'externalhref');
	}
}
