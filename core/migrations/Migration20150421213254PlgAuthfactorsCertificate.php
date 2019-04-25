<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding certificate auth factor plugin
 **/
class Migration20150421213254PlgAuthfactorsCertificate extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('authfactors', 'certificate', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('authfactors', 'certificate');
	}
}
