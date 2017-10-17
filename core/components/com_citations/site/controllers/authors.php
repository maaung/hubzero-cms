<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Site\Controllers;

use Components\Citations\Models\Citation;
use Components\Citations\Models\Author;
use Hubzero\Component\SiteController;
use Exception;
use Request;
use User;
use Lang;

/**
 * Manage a citation's author entries
 */
class Authors extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Incoming member ID
		$id = Request::getInt('citation', 0);

		if ($id == 0)
		{
			$this->setError(Lang::txt('COM_CITATIONS_ERROR_MISSING_CITATION'));
		}

		$this->citation = Citation::oneOrNew($id);

		if ($this->citation->isNew())
		{
			if ($id < 0)
			{
				$this->citation->set('id', $id);
			}
			else
			{
				$this->setError(Lang::txt('COM_CITATIONS_ERROR_INVALID_CITATION'));
			}
		}
		parent::execute();
	}

	/**
	 * Add a user as a manager of a course
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if ($this->getError())
		{
			return $this->displayTask();
		}

		// Incoming host
		$m = Request::getVar('author', '');

		$mbrs = explode(',', $m);
		$mbrs = array_map('trim', $mbrs);

		foreach ($mbrs as $mbr)
		{
			$user = null;
			if (!strstr($mbr, ' '))
			{
				$user = User::getInstance($mbr);
			}

			// Make sure the user exists
			if (!is_object($user) || !$user->get('username'))
			{
				$mbr = trim($mbr);
				$mbr = preg_replace('/\s+/', ' ', $mbr);

				$user = new \Hubzero\User\User;
				$user->set('name', $mbr);

				$parts = explode(' ', $mbr);

				if (count($parts) > 1)
				{
					$surname = array_pop($parts);
					$user->set('surname', $surname);

					$givenName = array_shift($parts);
					$user->set('givenName', $givenName);

					if (!empty($parts))
					{
						$user->get('middleName', implode(' ', $parts));
					}
				}
			}

			$authorValues = array(
				'cid'          => $this->citation->id,
				'author'       => $user->get('name'),
				'uidNumber'    => $user->get('id', 0),
				'organization' => $user->get('organization', ''),
				'givenName'    => $user->get('givenName', ''),
				'middleName'   => $user->get('middleName', ''),
				'surname'      => $user->get('surname'),
				'email'        => $user->get('email', '')
			);
			$author = Author::blank()->set($authorValues);

			if (!$author->save())
			{
				$this->setError($author->getError());
				continue;
			}
		}

		// Push through to the view
		$this->displayTask();
	}

	/**
	 * Update author ordering
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if ($this->getError())
		{
			return $this->displayTask();
		}

		$mbrs = Request::getVar('author', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		foreach ($mbrs as $i => $mbr)
		{
			$author = Author::one($mbr);
			$author->set('ordering', $i + 1);

			if ($author === false || !$author->save())
			{
				$this->setError(Lang::txt('COM_CITATIONS_ERROR_UNABLE_TO_UPDATE') . ' ' . $mbr);
			}
		}

		// Push through to the view
		$this->displayTask();
	}

	/**
	 * Remove one or more authors from a citation
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if ($this->getError())
		{
			return $this->displayTask();
		}


		$mbrs = Request::getVar('author', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);
		$authors = Author::all()->whereIn('id', $mbrs)->rows();

		foreach ($authors as $author)
		{
			if (!$author->destroy())
			{
				$this->setError(Lang::txt('COM_CITATIONS_ERROR_UNABLE_TO_REMOVE') . ' ' . $author->get('id'));
			}
		}

		// Push through to the view
		$this->displayTask();
	}

	/**
	 * Display a list of authors for a citation
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Output the HTML
		$this->view
			->set('row', $this->citation)
			->setErrors($this->getErrors())
			->setLayout('display');
		echo $this->view->loadTemplate('authors');
		exit();
	}
}
