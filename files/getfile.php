<?php

/* Usage: 

http://pulsion-ar.com.ar/download/getfile.php?file=/images/files/827/cv%20test.docx

*/

define( '_JEXEC', 1 );
define('JPATH_ROOT', realpath(dirname(__FILE__).'/../') );
require_once ( JPATH_ROOT .'/api/utils.php');
$utils = new Utils();

/* get params */
$file = JRequest::getVar('file',null,'get','string');
$app = JRequest::getVar('app',null,'get','string');

/* Create the Application */
$application = JFactory::getApplication($app);
jimport('joomla.plugin.helper');

//get current user
$user = JFactory::getUser();

/*
$frontAccess = true;
$frontEndId = between('downloads/', '/', $file);
$id = (string)$user->get('id');

// restric front end user visibility to owner folder
if($application->isSite()) {
	$frontAccess = ($frontEndId == $id) ? true : false;
}
*/

// check user is logged on and file name is present
if($user->get('id') != 0 && isset($file) /*&& $frontAccess*/) {		
	download(urldecode($file));	
}
else {	
	//var_dump($user);	
	echo "No tenes acceso a este archivo. Por favor inici&#225; sesi&#243;n.";
}


JFactory::getApplication()->close();

function download($filePath) {
	$fileName = basename($filePath);
	$filePath = JPATH_SITE.$filePath; 
	//echo "File: ".rawurlencode($fileName);
	//echo "Path: ".$filePath;
	
	$path_parts = pathinfo($filePath);
    $ext = strtolower($path_parts["extension"]);
    
	if($ext == "docx" || $ext == "xlsx"){
		/* .docx and .xlsx files */
		$fdl = @fopen($filePath,'rb');
		header("Status: 200");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header("Pragma: hack");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Description: File Transfer");
		header("Content-Type: application/force-download");
		header("Content-Type: application/download");
		header("Content-Type: $ctype");
		header("Content-Disposition: attachment; filename=\"".rawurlencode($fileName)."\""); 
		header("Content-Transfer-Encoding: binary");
		header("Content-Length:".filesize($filePath));  
		if($fdl)
		{
			while(!feof($fdl)) {
				print(fread($fdl, filesize($filePath)));
				flush();
				if (connection_status()!=0) 
				{
					@fclose($fdl);
					die();
				}
			}
		}	
	}
	elseif($ext == "pdf") {
		/* Adobe pdf */
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header("Content-Type: application/force-download");
		header('Content-Disposition: attachment; filename=' . rawurlencode($fileName));
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filePath));
		ob_clean();
		flush();
		readfile($filePath);
		exit;
	}	
	else {
		/* Other files */
		header("Content-Disposition: attachment; filename=".rawurlencode($fileName));
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");        
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");
		header("Content-Length: " . filesize($filePath));
		flush(); // this doesn't really matter.		
		
		$fp = fopen($filePath, "r");
		while (!feof($fp))
		{
		  echo fread($fp, 65536);
		  flush(); // this is essential for large downloads
		}
		fclose($fp);			
	}	
}

// Smart Sub-string privates
	
	//after ('@', 'biohazard@online.ge');
	//returns 'online.ge'
	//from the first occurrence of '@'

	//before ('@', 'biohazard@online.ge');
	//returns 'biohazard'
	//from the first occurrence of '@'

	//between ('@', '.', 'biohazard@online.ge');
	//returns 'online'
	//from the first occurrence of '@'

	//after_last ('[', 'sin[90]*cos[180]');
	//returns '180]'
	//from the last occurrence of '['

	//before_last ('[', 'sin[90]*cos[180]');
	//returns 'sin[90]*cos['
	//from the last occurrence of '['

	//between_last ('[', ']', 'sin[90]*cos[180]');
	//returns '180'
	//from the last occurrence of '['

	function after ($inThis, $inthat)
	{
		if (!is_bool(strpos($inthat, $inThis)))
		return substr($inthat, strpos($inthat,$inThis)+strlen($inThis));
	}

	function after_last ($inThis, $inthat)
	{
		if (!is_bool($inThis->strrevpos($inthat, $inThis)))
		return substr($inthat, $inThis->strrevpos($inthat, $inThis)+strlen($inThis));
	}

	function before ($inThis, $inthat)
	{
		return substr($inthat, 0, strpos($inthat, $inThis));
	}

	function before_last ($inThis, $inthat)
	{
		return substr($inthat, 0, $inThis->strrevpos($inthat, $inThis));
	}

	function between ($inThis, $that, $inthat)
	{
		return before ($that, after($inThis, $inthat));
	}

	function between_last ($inThis, $that, $inthat)
	{
	 return after_last($inThis, before_last($that, $inthat));
	}

	// use strrevpos private in case your php version does not include it
	function strrevpos($instr, $needle)
	{
		$rev_pos = strpos (strrev($instr), strrev($needle));
		if ($rev_pos===false) return false;
		else return strlen($instr) - $rev_pos - strlen($needle);
	}
?>