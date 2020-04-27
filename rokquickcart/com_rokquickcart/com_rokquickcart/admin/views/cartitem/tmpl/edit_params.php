<?php
/**
 * @version   $Id: edit_params.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('params');
foreach ($fieldSets as $name => $fieldSet) :
	?>
	<div class="tab-pane" id="params-<?php echo $name;?>">
		<?php
		if (isset($fieldSet->description) && trim($fieldSet->description)) :
			echo '<p class="alert alert-info">' . $this->escape(JText::_($fieldSet->description)) . '</p>';
		endif;
		?>
		<?php foreach ($this->form->getFieldset($name) as $field) : ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
