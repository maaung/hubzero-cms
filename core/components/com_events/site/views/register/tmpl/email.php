<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$message  = Lang::txt('COM_EVENTS_NAME') . ': ' . $this->register['firstname'].' '.$this->register['lastname'] ."\n";
$message .= Lang::txt('COM_EVENTS_TITLE') . ': ' . $this->register['title'] ."\n";
$message .= Lang::txt('COM_EVENTS_AFFILIATION') . ': ' . $this->register['affiliation'] ."\n";
$message .= Lang::txt('COM_EVENTS_EMAIL') . ': ' . $this->register['email'] ."\n";
$message .= Lang::txt('COM_EVENTS_WEBSITE') . ': ' . $this->register['website'] ."\n";
$message .= Lang::txt('COM_EVENTS_PHONE') . ': ' . $this->register['telephone'] ."\n";
$message .= Lang::txt('COM_EVENTS_FAX') . ': ' . $this->register['fax'] ."\n\n";

$message .= Lang::txt('COM_EVENTS_CITY') . ': ' . $this->register['city'] ."\n";
$message .= Lang::txt('COM_EVENTS_STATE') . ': ' . $this->register['state'] ."\n";
$message .= Lang::txt('COM_EVENTS_ZIP') . ': ' . $this->register['postalcode'] ."\n";
$message .= Lang::txt('COM_EVENTS_COUNTRY') . ': ' . $this->register['country'] ."\n\n";

if (isset($this->register['position']) || isset($this->register['position_other']))
{
	$message .= Lang::txt('COM_EVENTS_POSITION') . ': ';
	$message .= ($this->register['position']) ? $this->register['position'] : $this->register['position_other'];
	$message .= "\n\n";
}

if (isset($this->register['degree']))
{
	$message .= Lang::txt('COM_EVENTS_DEGREE') . ': ' . $this->register['degree'] ."\n\n";
}

if (isset($this->register['sex']))
{
	$message .= Lang::txt('COM_EVENTS_GENDER') . ': ' . $this->register['sex'] ."\n\n";
}

if ($this->race)
{
	//$message .= 'Race: '.implode(', ',$race) ."\n\n";
	$message .= Lang::txt('COM_EVENTS_RACE') . ': ';
	foreach ($this->race as $r => $t)
	{
		$message .= ($r != 'nativetribe') ? $r.', ' : '';
	}

	if ($this->race['nativetribe'] != '')
	{
		$message .= $this->race['nativetribe'];
	}
	$message .= "\n\n";
}

if ($this->disability)
{
	$message .= Lang::txt('COM_EVENTS_HAS_DISABILITY')."\n\n";
}
else
{
	$message .= Lang::txt('COM_EVENTS_NO_DISABILITY')."\n\n";
}

if (isset($this->dietary['needs']) || (isset($this->dietary['specific']) && $this->dietary['specific'] != ''))
{
	$message .= Lang::txt('COM_EVENTS_HAS_DIETARY', $this->dietary['specific']);
}
else
{
	$message .= Lang::txt('COM_EVENTS_NO_DIETARY')."\n\n";
}

if ($this->arrival)
{
	$message .= Lang::txt('COM_EVENTS_ARRIVAL')."\n";
	$message .= Lang::txt('COM_EVENTS_ARRIVAL_DAY', $this->arrival['day']) ."\n";
	$message .= Lang::txt('COM_EVENTS_ARRIVAL_TIME', $this->arrival['time']) ."\n\n";
}

if ($this->departure)
{
	$message .= Lang::txt('COM_EVENTS_DEPARTURE')."\n";
	$message .= Lang::txt('COM_EVENTS_DEPARTURE_DAY', $this->departure['day']) ."\n";
	$message .= Lang::txt('COM_EVENTS_DEPARTURE_TIME', $this->departure['time']) ."\n\n";
}

if ($this->dinner)
{
	$message .= Lang::txt('COM_EVENTS_ATTENDING_DINNER')."\n\n";
}
else
{
	$message .= Lang::txt('COM_EVENTS_NOT_ATTENDING_DINNER')."\n\n";
}

if (isset($this->register['additional']))
{
	$message .= Lang::txt('COM_EVENTS_ADDITIONAL', $this->register['additional'])."\n\n";
}

if (isset($this->register['comments']))
{
	$message .=  Lang::txt('COM_EVENTS_COMMENTS', $this->register['comments'])."\n\n";
}
echo $message;
