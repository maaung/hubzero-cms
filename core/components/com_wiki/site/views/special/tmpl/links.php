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

// No direct access.
defined('_HZEXEC_') or die();

// Breadcrumbs
Pathway::append(
	Lang::txt('COM_WIKI_SPECIAL_LINKS'),
	$this->page->link()
);

// Sorting
$sort = strtolower(Request::getString('sort', 'title'));
if (!in_array($sort, array('timestamp', 'title'))):
	$sort = 'timestamp';
endif;

$dir = strtoupper(Request::getString('dir', 'DESC'));
if (!in_array($dir, array('ASC', 'DESC'))):
	$dir = 'DESC';
endif;

$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';

// Pagination
$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);

// The current page
$page = $this->book->pages()
	->whereEquals('pagename', Request::getString('page', ''))
	->whereEquals('path', Request::getString('scope', ''))
	->row();

if ($v = Request::getInt('version', 0)):
	$revision = $page->versions()
		->whereEquals('id', $v)
		->row();
else:
	$revision = $page->version();
endif;

$permalink = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($page->link() . '&version=' . $revision->get('version')), '/');

// Find what links to the current page
$l = Components\Wiki\Models\Link::blank()->getTableName();
$p = Components\Wiki\Models\Page::blank()->getTableName();

$rows = Components\Wiki\Models\Page::all()
	->select($p . '.*')
	->select($l . '.timestamp')
	->join($l, $l . '.scope_id', $p . '.id', 'inner')
	->whereEquals($p . '.scope', $page->get('scope'))
	->whereEquals($p . '.scope_id', $page->get('scope_id'))
	->whereEquals($p . '.state', Components\Wiki\Models\Page::STATE_PUBLISHED)
	->whereEquals($l . '.scope', 'internal')
	->whereEquals($l . '.scope_id', $page->get('id'))
	->order($sort, $dir)
	->paginated()
	->rows();
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
	<p><?php echo Lang::txt('The following pages link to %s', '<a href="' . Route::url($page->link()) . '">' . $this->escape($page->get('title')) . '</a>'); ?></p>
	<div class="container">
		<table class="file entries">
			<thead>
				<tr>
					<th scope="col">
						<a<?php if ($sort == 'timestamp') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=timestamp&dir=' . $altdir); ?>">
							<?php if ($sort == 'timestamp') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_DATE'); ?>
						</a>
					</th>
					<th scope="col">
						<a<?php if ($sort == 'title') { echo ' class="active"'; } ?> href="<?php echo Route::url($this->page->link() . '&sort=title&dir=' . $altdir); ?>">
							<?php if ($sort == 'title') { echo ($dir == 'ASC') ? '&uarr;' : '&darr;'; } ?> <?php echo Lang::txt('COM_WIKI_COL_TITLE'); ?>
						</a>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($rows->count()):
				foreach ($rows as $row):
					?>
					<tr>
						<td>
							<time datetime="<?php echo $this->escape($row->get('timestamp')); ?>"><?php echo $this->escape(Date::of($row->get('timestamp'))->toLocal()); ?></time>
						</td>
						<td>
							<a href="<?php echo Route::url($row->link()); ?>">
								<?php echo $this->escape(stripslashes($row->get('title'))); ?>
							</a>
						</td>
					</tr>
					<?php
				endforeach;
			else:
				?>
				<tr>
					<td colspan="4">
						<?php echo Lang::txt('COM_WIKI_NONE'); ?>
					</td>
				</tr>
				<?php
			endif;
			?>
			</tbody>
		</table>

		<?php
		$pageNav = $rows->pagination;
		$pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
		$pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));

		echo $pageNav;
		?>
		<div class="clearfix"></div>
	</div>
</form>