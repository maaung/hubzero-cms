<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('connections')
     ->js()
     ->js('connections');
?>

<div class="">
	<form action="<?php echo Route::url($this->model->link('files') . '&action=saveconnection'); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend><?php echo Lang::txt('Connection'); ?></legend>

			<div class="input-wrap">
				<label for="param-name"><?php echo Lang::txt('Name'); ?>:</label>
				<input type="text" name="connect[name]" id="param-name" value="<?php echo $this->escape($this->connection->name); ?>" />
			</div>

			<?php
			Lang::load('plg_filesystem_' . $this->connection->provider->get('alias'), PATH_APP . DS . 'plugins' . DS . 'filesystem' . DS . $this->connection->provider->get('alias'));
			$xml = PATH_APP . DS . 'plugins' . DS . 'filesystem' . DS . $this->connection->provider->get('alias') . DS . $this->connection->provider->get('alias') . '.xml';
			if (!file_exists($xml))
			{
				Lang::load('plg_filesystem_' . $this->connection->provider->get('alias'), PATH_CORE . DS . 'plugins' . DS . 'filesystem' . DS . $this->connection->provider->get('alias'));
				$xml = PATH_CORE . DS . 'plugins' . DS . 'filesystem' . DS . $this->connection->provider->get('alias') . DS . $this->connection->provider->get('alias') . '.xml';
			}

			$form = new Hubzero\Form\Form('connection', array('control' => 'connect'));
			$form->loadFile($xml, false, '//config');

			$data = array();
			if ($data = $this->connection->get('params'))
			{
				$data = json_decode($data, true);
			}
			$data = new Hubzero\Config\Registry($data);

			$fieldSet = $form->getFieldset('user_credentials');

			if (count($fieldSet)) :
				?>
				<fieldset class="panelform">
					<legend><?php echo Lang::txt('Credentials'); ?></legend>
					<?php $hidden_fields = ''; ?>

					<?php foreach ($fieldSet as $field) : ?>
						<?php if (!$field->hidden) : ?>
							<div class="input-wrap <?php if ($field->type == 'Spacer') { echo ' input-spacer'; } ?>">
								<?php $field->setValue($data->get($field->fieldname)); ?>
								<?php echo $field->label; ?>
								<?php echo $field->input; ?>
							</div>
						<?php else : $hidden_fields.= $field->input; ?>
						<?php endif; ?>
					<?php endforeach; ?>

					<?php echo $hidden_fields; ?>
				</fieldset>
				<?php
			endif;
			?>

			<div class="input-wrap">
				<label for="param-share" class="option">
					<input type="checkbox" class="option" name="shareconnection" id="param-share" value="1" <?php echo $this->connection->isShared() ? ' checked="checked"' : ''; ?> />
					<?php echo Lang::txt('Share connection with everyone in the project?'); ?>
				</label>
			</div>

			<input type="hidden" name="connect[project_id]" value="<?php echo $this->escape($this->connection->get('project_id')); ?>" />
			<input type="hidden" name="connect[owner_id]" value="<?php echo $this->escape($this->connection->get('owner_id')); ?>" />
			<input type="hidden" name="connect[provider_id]" value="<?php echo $this->escape($this->connection->get('provider_id')); ?>" />
			<input type="hidden" name="connect[id]" value="<?php echo $this->escape($this->connection->get('id')); ?>" />

			<input type="hidden" name="option" value="com_projects" />
			<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="active" value="files" />
			<input type="hidden" name="action" value="saveconnection" />

			<p class="submit">
				<input type="submit" name="submit" class="btn btn-success" value="<?php echo Lang::txt('Save'); ?>" />
				<a class="btn btn-secondary" href="<?php echo Route::url($this->model->link('files')); ?>"><?php echo Lang::txt('Cancel'); ?></a>
			</p>
		</fieldset>
	</form>
</div>
