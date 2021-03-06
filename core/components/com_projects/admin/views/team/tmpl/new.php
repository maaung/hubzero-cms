<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getString('tmpl', '');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_NEW'));

//$canDo = Components\Projects\Helpers\Permissions::getActions('project');

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_PROJECTS') . ': ' . $text);
	if (User::authorise('core.edit', $this->option))
	{
		Toolbar::save();
	}
	Toolbar::cancel();
}

Html::behavior('framework');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if (form.newmember.value == '' && form.newgroup.value == '') {
		alert('<?php echo Lang::txt('COM_PROJECTS_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
	window.top.setTimeout("window.parent.location='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&project=<?php echo $this->model->get('id'); ?>'", 700);
}

jQuery(document).ready(function($){
	$(window).on('keypress', function(){
		if (window.event.keyCode == 13) {
			submitbutton('addusers');
		}
	})
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form">
	<?php if ($tmpl == 'component') { ?>
		<fieldset>
			<div class="configuration" >
				<div class="fltrt configuration-options">
					<button type="button" onclick="submitbutton('addusers');"><?php echo Lang::txt('COM_PROJECTS_SAVE'); ?></button>
					<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt('JCANCEL'); ?></button>
				</div>
				<?php echo Lang::txt('COM_PROJECTS') ?>
			</div>
		</fieldset>
	<?php } ?>
	<div class="col span12">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_PROJECTS_TEAM_ADD_NEW_MEMBERS'); ?></span></legend>

			<input type="hidden" name="project" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
			<input type="hidden" name="task" value="addusers" />

			<div class="input-wrap">
				<label for="field-usernames"><?php echo Lang::txt('COM_PROJECTS_TEAM_ADD_IND_USER'); ?>:</label>
				<input type="text" name="newmember" class="input-username" id="field-newmember" value="" size="50" />
			</div>

			<div class="input-wrap">
				<strong><?php echo Lang::txt('COM_PROJECTS_TEAM_OR'); ?></strong>
			</div>

			<div class="input-wrap">
				<label for="field-newgroup"><?php echo Lang::txt('COM_PROJECTS_TEAM_ADD_GROUP_OF_USERS'); ?>:</label>
				<input type="text" name="newgroup" class="input-username" id="field-newgroup" value="" size="50" />
			</div>

			<fieldset class="adminform">
				<div class="input-wrap">
					<strong><?php echo Lang::txt('COM_PROJECTS_TEAM_ROLE'); ?>:</strong>
				</div>

				<div class="input-wrap">
					<input class="option" name="role" id="role_owner" type="radio" value="1"  />
					<label for="role_owner"><?php echo Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER'); ?></label>
				</div>

				<div class="input-wrap">
					<input class="option" name="role" id="role_collaborator" type="radio" value="0" checked="checked" />
					<label for="role_collaborator"><?php echo Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR'); ?></label>
				</div>

				<div class="input-wrap">
					<input class="option" name="role" id="role_reviewer" type="radio" value="5" />
					<label for="role_reviewer"><?php echo Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER'); ?></label>
				</div>
			</fieldset>
		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>
