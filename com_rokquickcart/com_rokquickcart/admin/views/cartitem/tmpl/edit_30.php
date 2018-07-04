<?php
/**
 * @version   $Id: edit_30.php 19276 2014-02-28 03:47:35Z djamil $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('dropdown.init');


// Set toolbar items for the page

?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'cartitem.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<style>
.image-preview {
	margin-left: 0;
}
.CodeMirror {
	height: 250px;
}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_rokquickcart&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div class="row-fluid">
		<!-- Begin RokQuickCart -->
		<div class="span8 form-horizontal">
			<fieldset>
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#details" data-toggle="tab"><?php echo JText::_('ROKQUICKCART_ITEM_DETAILS'); ?></a>
					</li>
					<?php $fieldSets = $this->form->getFieldsets('params');
					foreach ($fieldSets as $name => $fieldSet) :
						?>
						<li>
							<a href="#params-<?php echo $name;?>" data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="details">
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
						</div>

						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('price'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price'); ?></div>
						</div>

						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('shipping'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('shipping'); ?></div>
						</div>

						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('image'); ?></div>
						</div>

						<div class="control-group">
							<div class="controls">
								<img
								src="<?php echo (RokQuickCartHelper::getShelfImage($this->item->image)) ? RokQuickCartHelper::getShelfImage($this->item->image) : RokQuickCartHelper::getShelfImage($this->default_image);?>"
								class="image-preview" border="2" alt="<?php echo JText::_('Preview', true);?>"/>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
						</div>

					</div>

					<?php echo $this->loadTemplate('params'); ?>

				</div>

			</fieldset>
		</div>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
		<!-- End RokQuickCart -->
		<!-- Begin Sidebar -->
		<div class="span4">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr/>
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getValue('name'); ?>
					</div>
				</div>
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
				</div>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>

			</fieldset>
		</div>
		<!-- End Sidebar -->
	</div>
</form>
