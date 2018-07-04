<?php
/**
 * @version   $Id: edit_25.php 19276 2014-02-28 03:47:35Z djamil $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

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

<form action="<?php echo JRoute::_('index.php?option=com_rokquickcart&layout=edit&id=' . (int)$this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('ROKQUICKCART_ITEM_DETAILS'); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
					<?php echo $this->form->getInput('name'); ?></li>

				<li><?php echo $this->form->getLabel('price'); ?>
					<?php echo $this->form->getInput('price'); ?></li>

				<li><?php echo $this->form->getLabel('shipping'); ?>
					<?php echo $this->form->getInput('shipping'); ?></li>

				<li><?php echo $this->form->getLabel('image'); ?>
					<?php echo $this->form->getInput('image'); ?></li>

				<li>
					<img src="<?php echo (RokQuickCartHelper::getShelfImage($this->item->image)) ? RokQuickCartHelper::getShelfImage($this->item->image) : RokQuickCartHelper::getShelfImage($this->default_image);?>"
					     class="image-preview" border="2" alt="<?php echo JText::_('Preview', true);?>"/>
				</li>

				<li><?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?></li>

				<li><?php echo $this->form->getLabel('ordering'); ?>
					<?php echo $this->form->getInput('ordering'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?></li>
			</ul>
			<div class="clr"></div>
			<?php echo $this->form->getLabel('description'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description'); ?>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo  JHtml::_('sliders.start', 'cartitem-slider'); ?>
		<?php
		$fieldSets = $this->form->getFieldsets('params');
		foreach ($fieldSets as $name => $fieldSet) :
			echo JHtml::_('sliders.panel', JText::_($fieldSet->label), $name . '-params');
			if (isset($fieldSet->description) && trim($fieldSet->description)) :
				echo '<p class="tip">' . $this->escape(JText::_($fieldSet->description)) . '</p>';
			endif;
			?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li><?php echo $field->label; ?>
							<?php echo $field->input; ?></li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php endforeach; ?>
		<?php echo JHtml::_('sliders.end'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div class="clr"></div>
