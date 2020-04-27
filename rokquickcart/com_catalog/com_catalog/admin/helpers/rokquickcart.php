<?php
/**
 * @version   $Id: rokquickcart.php 30072 2016-03-09 08:19:47Z matias $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.path');
if (!class_exists("Thumbnail")) {
	require_once(JPath::clean(JPATH_ADMINISTRATOR . '/components/com_rokquickcart/libs/thumbnail.inc.php'));
}
/**
 * Contact component helper.
 *
 * @package        Joomla.Administrator
 * @subpackage     com_rokquickcart
 * @since          1.6
 */
class RokQuickCartHelper
{
	public function __construct()
	{
	}

	public function getInstance()
	{
		static $instance;

		if (empty($instance)) {
			$instance = new RokQuickCartHelper();
		}

		return $instance;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param    int        The category ID.
	 * @param    int        The contact ID.
	 *
	 * @return    JObject
	 * @since    1.6
	 */
	public static function getActions($cartitemId = 0)
	{
		$user   = JFactory::getUser();
		$result = new JObject;
		$assetName = $cartitemId;

		if (empty($cartitemId)) {
			$assetName = 'com_rokquickcart';
		}

		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.edit.own',
			'core.edit.state',
			'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return    array    An array of JHtmlOption elements.
	 */
	static function getStateOptions()
	{
		// Build the filter options.
		$options   = array();
		$options[] = JHtml::_('select.option', '1', JText::_('JPUBLISHED'));
		$options[] = JHtml::_('select.option', '0', JText::_('JUNPUBLISHED'));
		$options[] = JHtml::_('select.option', '-2', JText::_('JTRASHED'));
		return $options;
	}

	public static function getShelfImage($image)
	{
		if (empty($image)) {
			return '';
		}
		$version         = new JVersion();
		$config          = JFactory::getConfig();
		$app             = JFactory::getApplication();
		$com_params      = JComponentHelper::getParams('com_rokquickcart');
		$path            = $app->isAdmin() && version_compare($version->getShortVersion(), '3.0', '>=') ? 'administrator/' : '';
		$full_image_file = JPath::clean(JPATH_ROOT . '/' . $image);
		$image_file      = JPath::clean(JPATH_CACHE . '/rokquickcart/shelf/' . $image);
		$image_url       = JURI::root(false) . $path . 'cache/rokquickcart/shelf/' . $image;
		$image_width     = (int)$com_params->get('shelf_image_width', 100);
		if (!self::createThumbImage($full_image_file, $image_file, $image_width)) {
			return '';
		}
		return $image_url;
	}

	public static function getShelfImageHeight($image)
	{
		$com_params       = JComponentHelper::getParams('com_rokquickcart');
		$shelf_image_size = (int)$com_params->get('shelf_image_size');
		if (empty($image)) {
			return 0;
		}
		if (empty($shelf_image_size)) {
			self::getShelfImage($image);
			$image_file       = JPath::clean(JPATH_CACHE . '/rokquickcart/shelf/' . $image);
			$shelf_image_size = getimagesize($image_file);
		}
		return $shelf_image_size[1];
	}

	public static function getShelfImageWidth($image)
	{
		$com_params       = JComponentHelper::getParams('com_rokquickcart');
		$shelf_image_size = (int)$com_params->get('shelf_image_size');
		if (empty($image)) {
			return 0;
		}
		if (empty($shelf_image_size)) {
			self::getShelfImage($image);
			$image_file       = JPath::clean(JPATH_CACHE . '/rokquickcart/shelf/' . $image);
			$shelf_image_size = getimagesize($image_file);
		}
		return $shelf_image_size[0];
	}

	public static function getCartImage($image)
	{
		if (empty($image)) {
			return '';
		}
		$config          = JFactory::getConfig();
		$app             = JFactory::getApplication();
		$com_params      = JComponentHelper::getParams('com_rokquickcart');
		$full_image_file = JPath::clean(JPATH_ROOT . '/' . $image);
		$image_file      = JPath::clean(JPATH_CACHE . '/rokquickcart/cart/' . $image);
		$image_url       = JURI::root(false) . 'cache/rokquickcart/cart/' . $image;
		$image_width     = (int)$com_params->get('cart_image_width', 100);
		if (!self::createThumbImage($full_image_file, $image_file, $image_width)) {
			return '';
		}
		return $image_url;
	}

	public static function getCartImageHeight($image)
	{
		$com_params      = JComponentHelper::getParams('com_rokquickcart');
		$cart_image_size = (int)$com_params->get('cart_image_size');
		if (empty($image)) {
			return 0;
		}
		if (empty($cart_image_size)) {
			self::getCartImage($image);
			$image_file      = JPath::clean(JPATH_CACHE . '/rokquickcart/cart/' . $image);
			$cart_image_size = getimagesize($image_file);
		}
		return $cart_image_size[1];
	}

	public static function getCartImageWidth($image)
	{
		$com_params      = JComponentHelper::getParams('com_rokquickcart');
		$cart_image_size = (int)$com_params->get('cart_image_size');
		if (empty($image)) {
			return 0;
		}
		if (empty($cart_image_size)) {
			self::getCartImage($image);
			$image_file      = JPath::clean(JPATH_CACHE . '/rokquickcart/cart/' . $image);
			$cart_image_size = getimagesize($image_file);
		}
		return $cart_image_size[0];
	}

	public static function getFullImage($image)
	{
		return $image;
	}

	public static function createThumbImage($full_image_path, $thumb_image_path, $image_width)
	{

		if (!JFile::exists($full_image_path)) {
			return false;
		}
		if (!JFolder::exists(dirname($thumb_image_path))) {
			JFolder::create(dirname($thumb_image_path));
		}
		$current_size = 0;

		if (JFile::exists($thumb_image_path)) {
			$existing_thumb = new Thumbnail($thumb_image_path);
			$current_size   = $existing_thumb->getCurrentWidth();
			$existing_thumb->destruct();
		}

		// create a new shelf image if the it doesnt exists, is older the full image, of if the image size has changed
		if (!JFile::exists($thumb_image_path) || (filemtime($full_image_path) > filemtime($thumb_image_path)) || $current_size != $image_width) {
			$thumb = new Thumbnail($full_image_path);

			if ($thumb->error) {
				echo "ERROR: " . $thumb->errmsg . ": " . $full_image_path;
				return false;
			}
			$thumb->resize($image_width);
			if (!is_writable(dirname($thumb_image_path))) {
				$thumb->destruct();
				return false;
			}
			$thumb->save($thumb_image_path);
			$thumb->destruct();
		}
		return true;
	}
}
