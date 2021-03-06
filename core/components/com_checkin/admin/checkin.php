<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Checkin\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_checkin'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'inspector.php';
require_once __DIR__ . DS . 'controllers' . DS . 'checkin.php';

// Instantiate controller
$controller = new Controllers\Checkin();
$controller->execute();
