<?php
/**
 * @version   $Id: controller.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// Check to ensure this file is included in Joomla!

// No direct access
defined('_JEXEC') or die;
include_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/legacy_class.php');
jimport('joomla.application.component.controller');
require_once (JPATH_ROOT.'/mercadopago/mercadopago.php');
require_once (JPATH_COMPONENT . '/models/rokquickcart.php');
require_once (JPATH_ROOT.'/linq/YaLinqo/Linq.php');

/**
 * RokQuickCart Component Controller
 *
 * @package        RokQuickCart
 * @subpackage     com_rokquickcart
 * @since          1.5
 */
class RokQuickCartController extends RokQuickCartLegacyJController
{
	/**
	 * Method to display a view.
	 *
	 * @param    boolean            If true, the view output will be cached
	 * @param    array              An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return    JController        This object to support chaining.
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;

		// Set the default view name and format from the Request.
		$vName = JFactory::getApplication()->input->get('view', 'rokquickcart');
		JFactory::getApplication()->input->set('view', $vName);


		$safeurlparams = array(
			'catid'            => 'INT',
			'id'               => 'INT',
			'cid'              => 'ARRAY',
			'year'             => 'INT',
			'month'            => 'INT',
			'limit'            => 'INT',
			'limitstart'       => 'INT',
			'showall'          => 'INT',
			'return'           => 'BASE64',
			'filter'           => 'STRING',
			'filter_order'     => 'CMD',
			'filter_order_Dir' => 'CMD',
			'filter-search'    => 'STRING',
			'print'            => 'BOOLEAN',
			'lang'             => 'CMD'
		);

		parent::display($cachable, $safeurlparams);

		return $this;
	}
	
	//	Returns Mercado Pago Link
	public function mercadoPago() {	
		$items = JRequest::getVar('cart', 'No data', 'post');
		$back_urls = JRequest::getVar('backurls', 'No data', 'post');
		$shipments = JRequest::getVar('shipments', 'No data', 'post');
			
		// show cart items
		//echo var_dump(json_decode($items)[0]->id);
		//return;
			
		// show DB items
		//$model = new RokQuickCartModelRokQuickCart();
		//$stock = $model->getItems(); 
		//echo var_dump($stock);
		//return;
		
		// server side validation			
		if(!$this->validateItems(json_decode($items))) {
			echo json_decode($back_urls)->failure . '?collection_status=rejected';
			return;
		}		
	
		// get mode: sandbox or production
		$option = jfactory::getapplication()->input->get('option');
		$com_params = jcomponenthelper::getparams($option);
		$checkout_mode = $com_params->get('checkout_mode');
			
		// mercado pago credentials
		$mp;
		if($checkout_mode == 'sandbox') {
			// sandbox credentials
			$mp = new MP ("6330820886431614", "RkjM1UmTPQWrtyt9i7fgFLPFeWb42SFB");
		}
		else {
			// add production credentials
			$mp = new MP ($com_params->get('client_id'), $com_params->get('client_secret'));
		}		
		//var_dump($shipments);
						
		$preference_data = array(
			"items" => $items,
			"back_urls" => json_decode($back_urls),//array("success" => "http://localhost/zuk/index.php/catalogo"),
			//"auto_return" => "approved",
			"shipments" => json_decode($shipments),
		);
		$preference = $mp->create_preference($preference_data);		
		
		$_SESSION["refreshPage"] = false;

		// build mercado pago link
		if($checkout_mode == 'sandbox???') {
			echo $preference['response']['sandbox_init_point'];
		}
		else {
			echo $preference['response']['init_point'];
		}
		
	}

	// Process Paypal
	public function paypal() {	
		$items = JRequest::getVar('cart', 'No data', 'post');		
		
		// server side validation			
		if(!$this->validateItems(json_decode($items))) {			
			echo '?collection_status=rejected';
			return;
		}

		

		// get current rate from http://themoneyconverter.com
		$page_text = file_get_contents("http://themoneyconverter.com/CurrencyConverter.aspx?tab=0&dccy1=USD&dccy2=ARS");
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);
		$dom->loadHTML($page_text);
		// parse rate value
		$rate = $dom->getElementById('ratebox');
		if(is_null($rate)) {
			$rate = $dom->getElementById('cc-ratebox');			
		}				
		if(is_null($rate)) {
			$rate = $this->between('ARS/USD', '</div>', $page_text);
			$rate = preg_split("/ = /", $rate);
		}
		else {
			$rate = preg_split("/ = /", $rate->nodeValue);
		}		
		libxml_clear_errors();
		// all is ok, so convert values and then checkout
		echo 'var converted; $.each(simpleCart.items(), function(index, item) { converted = parseFloat(simpleCart.items()[index].get("price")) / ' . $rate[1] . '; simpleCart.items()[index].set("price", converted.toString())}); simpleCart.checkout();';
	}
	
	public function buy() {	
		$items = JRequest::getVar('cart', 'No data', 'post');
		$text = JRequest::getVar('text', 'No data', 'post');
		$payment = JRequest::getVar('payment', 'No data', 'post');
		
		// site data
		$config = JFactory::getConfig();
		$siteName = $config->get('sitename');
		$siteMail = $config->get('mailfrom');

		// server side validation			
		if(!$this->validateItems(json_decode($items))) {
			echo "No valid data";
			return;
		}		
		
		// check login
		$user = JFactory::getUser();
		$inputCookie  = JFactory::getApplication()->input->cookie;
		$cookieLogin = $inputCookie->get($name = 'cookieLogin', $defaultValue = null);
		if ($user->guest || !empty($cookieLogin)) {
			echo "login";
			return;
		}

		// format email
		if($payment == "local") {
			$text = $text . "\r\n \r\n Usuario: " . $user->username . "\r\n" . "Forma de pago: En el local" .  "\r\n \r\n Nos pondremos en contacto para coordinar los detalles de tu compra.\r\n \r\n Muchas gracias por elegirnos. \r\n " . $siteName . "\r\n" . JURI::base();
		}
		elseif($payment == "transfer") {
			$text = $text . "\r\n \r\n Usuario: " . $user->username . "\r\n" . "Forma de pago: Transferencia Bancaria" .  "\r\n \r\n Nos pondremos en contacto para coordinar los detalles de tu compra.\r\n \r\n Muchas gracias por elegirnos. \r\n " . $siteName . "\r\n" . JURI::base();
		}
		else {
			echo "No valid data";
			return;
		}
		
		// email BUYER
		$to=$user->email;
		$subject="Compra de Items - " . $siteName;
		$txt=$text; 
		$headers="From: " . $siteMail; // . "\r\n" . "Bcc: reynicolas2001@yahoo.com.ar"; 
		//send 
		mail($to,$subject,$txt,$headers); 
		
		// email SELLER
		$to=$siteMail;
		//$to="reynicolas2001@yahoo.com.ar";
		$subject="Compraron productos en tu sitio: " . $siteName;
		$txt=$text;
		$headers="From: " . $user->email; // . "\r\n" . "Bcc: reynicolas2001@yahoo.com.ar"; 
		//send 
		mail($to,$subject,$txt,$headers);
		
		echo("ok");
	}
	
	// validates the items have correct id and price
	private function validateItems($items) {
		// current items from DB
		$model = new RokQuickCartModelRokQuickCart();
		$stock = $model->getItems(); 				
		
		// perform check
		$result = false;
		$index = 0;
		foreach ($items as $item):
			$index++;
			foreach ($stock as $stockItem):
				if($item->id == $stockItem->id && 
					$item->unit_price == $stockItem->price &&
					$item->currency_id == "ARS" && $item->quantity > 0
					)					
				{
					$result = true;
				}
			endforeach;
			if(!$result) { // breaks, the item does NOT match
				break;
			}
			else if($index < count($items)){
				$result = false; // item is OK, reset the result (if not end of array)
			}
		endforeach;
		
		// return result
		return $result;		
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

	private function after ($inThis, $inthat)
	{
		if (!is_bool(strpos($inthat, $inThis)))
		return substr($inthat, strpos($inthat,$inThis)+strlen($inThis));
	}

	private function after_last ($inThis, $inthat)
	{
		if (!is_bool($inThis->strrevpos($inthat, $inThis)))
		return substr($inthat, $inThis->strrevpos($inthat, $inThis)+strlen($inThis));
	}

	private function before ($inThis, $inthat)
	{
		return substr($inthat, 0, strpos($inthat, $inThis));
	}

	private function before_last ($inThis, $inthat)
	{
		return substr($inthat, 0, $inThis->strrevpos($inthat, $inThis));
	}

	private function between ($inThis, $that, $inthat)
	{
		return $this->before ($that, $this->after($inThis, $inthat));
	}

	private function between_last ($inThis, $that, $inthat)
	{
	 return $this->after_last($inThis, $this->before_last($that, $inthat));
	}

	// use strrevpos private in case your php version does not include it
	private function strrevpos($instr, $needle)
	{
		$rev_pos = strpos (strrev($instr), strrev($needle));
		if ($rev_pos===false) return false;
		else return strlen($instr) - $rev_pos - strlen($needle);
	}
}
