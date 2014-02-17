<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration class
 **/
class Migration implements CommandInterface
{
	/**
	 * Output object, implements the Output interface
	 *
	 * @var object
	 **/
	private $output;

	/**
	 * Arguments object, implements the Argument interface
	 *
	 * @var object
	 **/
	private $arguments;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @return void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		$this->output    = $output;
		$this->arguments = $arguments;
	}

	/**
	 * Default (required) command - just executes run
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->run();
	}

	/**
	 * Run migration
	 *
	 * @return void
	 **/
	public function run()
	{
		// Direction, up or down
		$direction = 'up';
		if ($this->arguments->getOpt('d'))
		{
			if ($this->arguments->getOpt('d') == 'up' || $this->arguments->getOpt('d') == 'down')
			{
				$direction = $this->arguments->getOpt('d');
			}
			else
			{
				$this->output->error('Error: Direction must be one of "up" or "down"');
			}
		}

		// Overriding default document root?
		$directory = null;
		if ($this->arguments->getOpt('r'))
		{
			if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r')))
			{
				$directory = rtrim($this->arguments->getOpt('r'), DS);
			}
			else
			{
				$this->output->error('Error: Provided directory is not valid');
			}
		}

		// Forcing update
		$force = false;
		if ($this->arguments->getOpt('force'))
		{
			if (!$this->arguments->getOpt('e') && !$this->arguments->getOpt('file'))
			{
				$this->output->error('Error: You cannot specify the "force" option without specifying a specific extention or file');
			}
			else
			{
				$force = true;
			}
		}

		// Logging only - record migration
		$logOnly = false;
		if ($this->arguments->getOpt('m'))
		{
			if (!$this->arguments->getOpt('e') && !$this->arguments->getOpt('file'))
			{
				$this->output->error('Error: You cannot specify the "Log only (-m)" option without specifying a specific extention or file');
			}
			else
			{
				$logOnly = true;
			}
		}

		// Ignore dates
		$ignoreDates = false;
		if ($this->arguments->getOpt('i'))
		{
			$ignoreDates = true;
		}

		// Specific extension
		$extension = null;
		if ($this->arguments->getOpt('e'))
		{
			if (!preg_match('/^com_[[:alnum:]]+$|^mod_[[:alnum:]]+$|^plg_[[:alnum:]]+_[[:alnum:]]+$|^core$/i', $this->arguments->getOpt('e')))
			{
				$this->output->error('Error: extension should match the pattern of com_*, mod_*, plg_*_*, or core');
			}
			else
			{
				$extension = $this->arguments->getOpt('e');
			}
		}

		// Specific file
		$file = null;
		if ($this->arguments->getOpt('file'))
		{
			if (!preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $this->arguments->getOpt('file')))
			{
				$this->output->error('Error: Provided filename does not appear to be valid');
			}
			else
			{
				$file = $this->arguments->getOpt('file');

				// Also force "ignore dates mode", as that's somewhat implied by giving a specific filename
				$ignoreDates = true;
			}
		}

		// Dryrun
		$dryrun = true;
		if ($this->arguments->getOpt('f'))
		{
			$dryrun = false;
		}

		// Email results
		$email = false;
		if ($this->arguments->getOpt('email'))
		{
			if(!preg_match('/^[a-zA-Z0-9\.\_\-]+@[a-zA-Z0-9\.]+\.[a-zA-Z]{2,4}$/', $this->arguments->getOpt('email')))
			{
				$this->output->error('Error: ' . $this->arguments->getOpt('email') . ' does not appear to be a valid email address');
			}
			else
			{
				$email = $this->arguments->getOpt('email');
			}
		}

		// Create migration object
		$migration = new \Hubzero\Content\Migration($directory);

		// Make sure we got a migration object
		if ($migration === false)
		{
			$this->output->error('Error: failed to instantiate new migration object.');
		}

		// For now, we're just going to force into interactive mode (no reason not to)
		$this->output->makeInteractive();
		if ($this->output->isInteractive())
		{
			// Register callback function for adding lines interactively
			$output   = $this->output;
			$callback = function($message, $type=null) use ($output)
			{
				$output->addLine($message, $type);
			};
			$migration->registerCallback('message', $callback);

			// Add progress callback as well
			$progress = $this->output->getProgressOutput();
			$migration->registerCallback('progress', $progress);
		}

		// Find migration files
		if ($migration->find($extension, $file) === false)
		{
			// Find failed, do nothing
			$this->output->error('Migration find failed! See log messages for details.');
		}
		else // no errors during 'find', so continue
		{
			// Run migration itself
			if (!$result = $migration->migrate($direction, $force, $dryrun, $ignoreDates, $logOnly))
			{
				$this->output->error('Migration failed! See log messages for details.');
			}
			else
			{
				if (!$this->output->isInteractive())
				{
					$this->output->addLinesFromArray($migration->get('log'));
				}
				$this->output->addLine('Success: ' . ucfirst($direction) . ' migration complete!', 'success');
			}
		}

		// Email results if requested (only do so if there's something to report)
		if ($email && count($migration->get('affectedFiles')) > 0)
		{
			$this->output->addLine("Emailing results to: {$email}");

			$headers = "From: Migrations <automator@" . php_uname("n") . ">";
			$subject = "Migration output - " . php_uname("n") . " [" . date("d-M-Y H:i:s") . "]";

			$message = "";
			foreach ($migration->get('log') as $line)
			{
				$message .= $line['message'] . "\n";
			}

			// Send the message
			if(!mail($email, $subject, $message, $headers))
			{
				$this->output->addLine("Error: failed to send message!", 'warning');
			}
		}
		elseif ($email)
		{
			$this->output->addLine('Ignoring email as no files were affected in this run.', 'info');
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Run a migration. This includes searching for migration files,
				depending on the options provided.'
			)
			->addArgument(
				'-d: direction [up|down]',
				'If not specified, defaults to "up".',
				'Example: -d=up or -d=down'
			)
			->addArgument(
				'-r: document root',
				'Specify the document root through which the the application
				will search for migrations directories. The primary use case
				for this is specifying an alternate directory for testing.
				By default, it will use the JPATH_ROOT constant for
				the document root.',
				'Example: -r=/www/myhub/unittests/migrations'
			)
			->addArgument(
				'-e: extension',
				'Explicity give the extension on which the migration should be run.
				This could be one of "com_componentname", "mod_modulename",
				or "plg_plugingroup_pluginname". This option is required
				when using the force (--force) option and the log only option (-m).',
				'Example: -e=com_courses, -e=plg_members_dashboard'
			)
			->addArgument(
				'-i: ignore dates',
				'Using this option will scan for and run all migrations that haven\'t
				previously been run, irrespective of the date of the migration.
				This differs from the default behavior in that normally, only files
				dated after the last run date will be eligable to be included in the
				migration. This option also differs from force mode (--force) in that it
				will find all migrations, but only run those that haven\'t been run
				before (whereas --force will run them irrespective of whether or not it
				thinks they\'ve already been run). You do not have to use -e with this
				option. This option is necessary when needing to run migrations that
				have been skipped for one reason or another.'
			)
			->addArgument(
				'-f: full run',
				'By default, using the migration command without any options will run
				in dry-run mode (meaning no changes will actually be made), displaying
				the migrations that would be run, were the command to be fully executed.
				Use the "-f" (full run) option to do the full migration run.'
			)
			->addArgument(
				'-m: log only',
				'Using this option, a migration will run as normal, and log entries
				will be created, but the SQL itself will not be run. As a general
				precaution, this should not be run without the extension option (-e).
				The primary use case for this option would be marking a migration
				as run in the event that it had already been run (manually), yet
				not logged in the database.'
			)
			->addArgument(
				'--file: run a provided filed',
				'Provide the filename to be run. This and only this file will be run.
				This will automatically place the migration in (-i) mode, ignoring dates.
				It will not, however, force it to be run, if a log entry for this file
				and direction already exists. Use the (--force) option to override this
				behavior or run the opposite direction first.',
				'Example: --file=Migration20130101000000ComMigrations.php'
			)
			->addArgument(
				'--force: force mode',
				'This option should be used carefully. It will run a migration,
				even if it thinks it has already been run. When using this option,
				you must also give a specific extension using the (-e) option.'
			)
			->addArgument(
				'--email: send email',
				'Specify an email address to receive the output of this run. If no
				files are executed during the migration, an email will not be sent.',
				'Example: --email=sampleuser@hubzero.org'
			);
	}
}