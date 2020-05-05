<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

//citation params
$label    = $this->config->get('citation_label', 'type');
$rollover = $this->config->get('citation_rollover', 'no');

$citationsFormat = new \Components\Citations\Helpers\Format($this->database);
$template = $citationsFormat->getDefaultFormat();

//batch downloads
$batch_download = $this->config->get("citation_batch_download", 1);

//do we want to number li items
if ($label == 'none')
{
	$citations_label_class = ' no-label';
}
elseif ($label == 'type')
{
	$citations_label_class = ' type-label';
}
elseif ($label == 'both')
{
	$citations_label_class = ' both-label';
}
else
{
	$citations_label_class = ' both-label';
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="icon-browse browse btn" href="<?php echo Route::url('index.php?option=com_citations'); ?>">
					<?php echo Lang::txt('COM_CITATIONS_BACK'); ?>
				</a>
			</li>
			<li>
				<a class="btn icon-upload" href="<?php echo Route::url('index.php?option='.$this->option.'&task=import'); ?>">
					<?php echo Lang::txt('COM_CITATIONS_IMPORT_IMPORT_MORE'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section id="import" class="section">
	<div class="section-inner">
		<?php
		foreach ($this->messages as $message)
		{
			echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
		}
		?>

		<ul id="steps">
			<li>
				<a href="<?php echo Request::base(true); ?>/citations/import" class="passed">
					<?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP1'); ?><span><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME'); ?></span>
				</a>
			</li>
			<li>
				<a href="<?php echo Request::base(true); ?>/citations/import_review" class="passed">
					<?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP2'); ?><span><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME'); ?></span>
				</a>
			</li>
			<li>
				<a href="<?php echo Request::base(true); ?>/citations/import_saved" class="active">
					<?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP3'); ?><span><?php echo Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME'); ?></span>
				</a>
			</li>
		</ul><!-- / #steps -->

		<?php if (count($this->citations) > 0) : ?>
			<?php
			$formatter = new \Components\Citations\Helpers\Format();
			$formatter->setTemplate($template);

			$counter = 1;
			?>

			<h3><?php echo Lang::txt('COM_CITATIONS_IMPORT_SUCCESS'); ?></h3>

			<table class="citations">
				<tbody>
					<?php foreach ($this->citations as $cite) : ?>
						<tr>
							<?php if ($label != "none") : ?>
								<td class="citation-label <?php echo $this->escape($citations_label_class); ?>">
									<?php
										$type = $cite->relatedType()->row()->get('type_title', 'Generic');

										switch ($label)
										{
											case 'number':
												echo "<span class=\"number\">{$counter}.</span>";
												break;
											case 'type':
												echo "<span class=\"type\">{$type}</span>";
												break;
											case 'both':
												echo "<span class=\"number\">{$counter}.</span>";
												echo "<span class=\"type\">{$type}</span>";
												break;
										}
									?>
								</td>
							<?php endif; ?>
							<td class="citation-container">
								<?php
								$formatted = $cite->formatted(array('format'=>$this->defaultFormat));
								if ($cite->doi)
								{
									$formatted = str_replace(
										'doi:' . $cite->doi,
										'<a href="' . $cite->url . '" rel="external">' . 'doi:' . $cite->doi . '</a>',
										$formatted
									);
								}

								echo $formatted;
								?>

								<?php if ($rollover == 'yes' && $cite->abstract != '') : ?>
									<div class="citation-notes">
										<p><?php echo nl2br($cite->abstract); ?></p>
									</div>
								<?php endif; ?>

								<div class="citation-details">
									<?php
									$singleCitationView = $this->config->get('citation_single_view', 0);
									if (!$singleCitationView)
									{
										echo $cite->citationDetails($this->openurl);
									}
									?>

									<?php if ($this->config->get('citation_show_badges', 'no') == 'yes') : ?>
										<?php echo \Components\Citations\Helpers\Format::citationBadges($cite); ?>
									<?php endif; ?>

									<?php if ($this->config->get('citation_show_tags', 'no') == 'yes') : ?>
										<?php echo \Components\Citations\Helpers\Format::citationTags($cite); ?>
									<?php endif; ?>
								</div>
							</td>
						</tr>
						<?php $counter++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</section><!-- / .section -->
