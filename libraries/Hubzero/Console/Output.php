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

namespace Hubzero\Console;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Output class for rendering content to command line
 **/
class Output
{
	/**
	 * Array containing lines to be rendered out
	 *
	 * @var (array) response lines
	 **/
	private $response = array();

	/**
	 * Track default indentation for lines
	 *
	 * If prefering predominant indentation other than 0,
	 * set with setDefaultIndentation() to avoid having 
	 * to set on all calls to addLine().
	 *
	 * @var string
	 **/
	private $defaultIndentation = '';

	/**
	 * Track whether we're in interactive mode
	 *
	 * While in interactive mode, output each line as it's given
	 * rather than pooling and waiting until render is called.
	 *
	 * @var string
	 **/
	private $isInteractive = false;

	/**
	 * Render out stored output to command line
	 *
	 * @param  (bool) $newLine - whether or not to include new line with each response (really only applies to interactive output)
	 * @return void
	 **/
	public function render($newLine=true)
	{
		// Make sure there is something there
		if (count($this->response) > 0)
		{
			foreach ($this->response as $line)
			{
				// Echo out the message
				echo $line['message'];

				if ($newLine)
				{
					echo "\n";
				}
			}

			// Reset response
			$this->response = array();
		}
	}

	/**
	 * Add a new line to the output buffer (not actually a real php output buffer)
	 *
	 * @param  (string) $message - text of line
	 * @param  (mixed)  $styles  - array of custom styles or string containing predefined term (see formatLine() for posibilities)
	 * @param  (bool)   $newLine - whether or not line should end with a new line
	 * @return (object) $this    - for method chaining
	 **/
	public function addLine($message, $styles=null, $newLine=true)
	{
		$this->formatLine($message, $styles);

		$this->response[] = array(
			'message' => $message
		);

		if ($this->isInteractive())
		{
			$this->render($newLine);
		}

		return $this;
	}

	/**
	 * Add a new string to the output buffer
	 *
	 * Main difference between this and addLine() is that this is a shortcut for not
	 * including a new line at the end of the output
	 *
	 * @param  (string) $message - text of string
	 * @return (object) $this    - for method chaining
	 **/
	public function addString($message)
	{
		$this->addLine($message, null, false);

		return $this;
	}

	/**
	 * Add a paragraph to the output buffer.
	 * This will chop the text up to maintain lines of approximately 80 characters.
	 *
	 * @param  (string) $paragraph - text to be chopped into lines and stored
	 * @param  (mixed)  $styles    - array of custom styles or string containing predefined term (see formatLine() for posibilities)
	 * @return (object) $this      - for method chaining
	 **/
	public function addParagraph($paragraph, $styles=array())
	{
		// Sanitize the given text of new lines, double spaces, and tabs
		$paragraph = str_replace("\n", " ", $paragraph);
		$paragraph = str_replace("  ", " ", $paragraph);
		$paragraph = str_replace("\t", "",  $paragraph);

		// Now check if the paragraph is longer than 70 characters and subdivide as appropriate
		do
		{
			if (strlen($paragraph) > 70 && $break = strpos($paragraph, " ", 70))
			{
				$message   = substr($paragraph, 0, $break);
				$paragraph = trim(substr($paragraph, $break));
			}
			else
			{
				$message = trim($paragraph);
				$break   = false;
			}

			// Add the individual line
			$this->addLine($message, $styles);
		}
		while ($break !== false);

		return $this;
	}

	/**
	 * Helper method to add an array of lines to the output buffer.
	 *
	 * Here we're expecting an array, with each entry also containing an
	 * array with at least one key of 'message'. Another key
	 * can also be provided with a message type, which translates to 
	 * one of the predefined styles used in formatLine().
	 *
	 * @param  (array) $lines - array of lines
	 * @return void
	 **/
	public function addLinesFromArray($lines)
	{
		foreach ($lines as $line)
		{
			$this->addLine($line['message'], $line['type']);
		}
	}

	/**
	 * Add a blank line to the output
	 *
	 * @return (object) $this - for method chaining
	 **/
	public function addSpacer()
	{
		$this->response[] = array('message' => '');

		return $this;
	}

	/**
	 * Shortcut function to print an error, render the error, and halt execution
	 *
	 * @param  (string) $message - line of text used in error
	 * @return void
	 **/
	public function error($message)
	{
		$this->addLine($message, 'error');
		$this->render();
		exit(1);
	}

	/**
	 * Set the default indentation. This will be used unless an indentation is
	 * explicitly given while adding a line.
	 *
	 * @param  (int) $indentation - intiger of number of spaces to indent lines
	 * @return void
	 **/
	public function setDefaultIndentation($indentation)
	{
		$ind = '';
		for ($i=0; $i < (int) $indentation; $i++)
		{
			$ind .= ' ';
		}
		$this->defaultIndentation = $ind;
	}

	/**
	 * Get our output subclass specialized for rendering help doc
	 *
	 * @return (object) $obj - new Help output class
	 **/
	public function getHelpOutput()
	{
		$class = __NAMESPACE__ . '\\Output\\Help';

		return new $class();
	}

	/**
	 * Get our output subclass specialized for rendering progress tracking
	 *
	 * @return (object) $obj - new Progress output class
	 **/
	public function getProgressOutput()
	{
		$class = __NAMESPACE__ . '\\Output\\Progress';

		return new $class();
	}

	/**
	 * Take line of text and styles and give back a formatted line.
	 *
	 * This will also translate textual colors and formatting words
	 * to bash escape sequences. 
	 *
	 * @param  (string) $message - raw line of text
	 * @param  (mixed)  $styles  - string or array of styles
	 * @return void
	 **/
	private function formatLine(&$message, $styles)
	{
		$style = array(
			'format'      => '0',
			'color'       => ' ',
			'indentation' => $this->defaultIndentation
		);

		// If array, parse for individual style declarations
		if (is_array($styles) && count($styles) > 0)
		{
			foreach ($styles as $k => $v)
			{
				switch ($k)
				{
					case 'color':
						$style['color'] = $this->translateColor($v);
						break;

					case 'format':
						$style['format'] = $this->translateFormat($v);
						break;

					case 'indentation':
						$style['indentation'] = '';
						for ($i=0; $i < $v; $i++)
						{
							$style['indentation'] .= ' ';
						}
						break;
				}
			}
		}
		// If string, parse for predefined formatting key words
		elseif (is_string($styles))
		{
			switch ($styles)
			{
				case 'warning':
					$style['color'] = '43';
					break;

				case 'error':
					$style['format'] = '1';
					$style['color']  = '41';
					break;

				case 'info':
					$style['color'] = $this->translateColor('blue');
					break;

				case 'success':
					$style['color'] = $this->translateColor('green');
					break;
			}
		}

		$messageStyles = $style['format'] . ';' . $style['color'];
		$message       = chr(27) . "[" . $messageStyles . "m" . $style['indentation'] . $message . chr(27) . "[0m";
	}

	/**
	 * Make output stream rather than pooled and dumped out at the end when render is called
	 *
	 * @return void
	 **/
	public function makeInteractive()
	{
		$this->isInteractive = true;
	}

	/**
	 * Check if output is streamed
	 *
	 * @return void
	 **/
	public function isInteractive()
	{
		return $this->isInteractive;
	}

	/**
	 * Simple translation table to map color words to bash equivalents
	 *
	 * @param  (string) $color - human readable color name
	 * @return (string) $color - bash color number
	 **/
	private function translateColor($color)
	{
		$colors = array(
			'black'  => '30',
			'red'    => '31',
			'green'  => '32',
			'yellow' => '33',
			'blue'   => '34',
			'purple' => '35',
			'cyan'   => '36',
			'white'  => '37'
		);

		return $colors[$color];
	}

	/**
	 * Simple translation table to map formatting key words to bash equivalents
	 *
	 * @param  (string) $format - human readable format name
	 * @return (string) $format - bash format number
	 **/
	private function translateFormat($format)
	{
		$formats = array(
			'normal'    => '0',
			'bold'      => '1',
			'underline' => '4'
		);

		return $formats[$format];
	}
}