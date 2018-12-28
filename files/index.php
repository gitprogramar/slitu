<?php
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */


define( '_JEXEC', 1 );
define('JPATH_ROOT', realpath(dirname(__FILE__).'/../') );
require_once ( JPATH_ROOT .'/api/utils.php');
$utils = new Utils();
		
error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');

JFactory::getApplication("site");
$folder = JRequest::getVar('folder', '', 'post');

$query_string_array = JRequest::get( 'get' );
$path = "";
foreach ($query_string_array as $name => $value)
{
   if($name == "path") {
	   $path = $value;
   }
}

echo $path;
$upload_handler = new UploadHandler(null, true, null, $folder, $path);
