<?php
/**
 * @version   $Id: view.html.php 28483 2015-05-27 13:24:58Z james $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');
include_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/legacy_class.php');

if (!class_exists("RokQuickCartHelper")) {
    require_once(JPath::clean(JPATH_COMPONENT . '/helpers/rokquickcart.php'));
}

class RokQuickCartViewRokQuickCart extends RokQuickCartLegacyJView
{
    var $cart_images_dir = 'images/rokquickcart/';
    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null)
    {

        $app              = JFactory::getApplication();
        $option           = JFactory::getApplication()->input->get('option');
        $user             = JFactory::getUser();
        $doc              = JFactory::getDocument();
        $items            = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }


        $com_params = JComponentHelper::getParams($option);

        $configured = true;

        //output the checkout mech
        if (!$com_params->get('checkout_method', false)) {
            $configured = false;
        }
        $config_options = array();

        $checkout_mode     = $com_params->get('checkout_mode');
        $checkout_method   = $com_params->get('checkout_method');
        $shipping          = $com_params->get('shipping', false);
        $shipping_type     = $com_params->get('shipping_type', 'items');
        $tax               = $com_params->get('tax', false);
        $tax_shipping      = $com_params->get('tax_shipping', false);
        $shipping_per_item = false;

        $currency          = ($checkout_method != 'GoogleCheckout') ? $com_params->get('paypal_currency', 'USD') : $com_params->get('googlecheckout_currency', 'USD');
        $currency_symbol   = $this->_getCurrecySymbol($currency);

        $uri = JURI::getInstance();
        $returnURI = $uri->__toString(array('scheme','user','pass','host','port','path','query','fragment'));

        /*$config_options[] = 'simpleCart.continue_url = "' . $uri->__toString(array(
                                                                                  'scheme',
                                                                                  'user',
                                                                                  'pass',
                                                                                  'host',
                                                                                  'port',
                                                                                  'path',
                                                                                  'query',
                                                                                  'fragment'
                                                                             )) . '";';*/


        // Cart Settings
        $config_options[] = 'simpleCart({';
        $config_options[] = '   cartStyle: "table"';
        if ($checkout_method != 'GoogleCheckout') $config_options[] = ',   currency: "' . $com_params->get('paypal_currency', 'USD') . '"'; // to keep backward compatibility, we keep the field 'paypal_currency' name
        else $config_options[] = ',   currency: "' . $com_params->get('googlecheckout_currency', 'USD') . '"';
        /*$config_options[] = ',   cartColumns: [
            { attr: "image", label: false, view: function(item, column){ return "<a href=\'" + item.get(column.attr) + "\' data-rokbox><img src=\'" + item.get(column.attr) + "\'/></a>"; }},
            { attr: "name" , label: "Name", view: function(item, column){
                    var options = item.options(), option, badges = [], cleanKey, cleanValue;
                    for (option in options){
                        if (option == "image") continue;
                        cleanKey = option.replace(/-/g, " ").capitalize();
                        cleanValue = options[option].replace(/_/g, " ");

                        badges.push(\'<span class="cart_badge">\'+cleanValue+\'</span>\');
                    }
                    if (!badges.length) return item.get(column.attr) || "";
                    else return (item.get(column.attr) || "") + \'<div class="cart_badges">\'+ badges.join(" ") +\'</div>\';
                }
            },
            { attr: "quantity" , label: "Qty", view: "input" },
            { view: "remove" , text: "Remove" , label: false },
            { attr: "price" , label: "Price", view: "currency" },
            { attr: "total" , label: "SubTotal", view: "currency" }
        ]';*/
        if ($shipping){
            switch ($shipping_type) {
                case "flat":
                    $config_options[] = ',   shippingFlatRate:' . $com_params->get('shipping_flat', 0);
                    break;
                case "quantity":
                    $config_options[] = ',   shippingQuantityRate:' . $com_params->get('shipping_quantity', 0);
                    break;
                case  "percent":
                    $config_options[] = ',   shippingTotalRate:' . $com_params->get('shipping_percent', 0);
                    break;
                default:
                    $shipping_per_item = true;
                    break;
            }
        }
        if ($tax) $config_options[] = ',   taxRate:' . $com_params->get('tax_rate', 0);
        if ($tax_shipping) $config_options[] = ',   taxShipping:' . $com_params->get('tax_shipping', false);
        $config_options[] = '});';

        // Checkout Settings
        $config_options[] = 'simpleCart({checkout: {';
        switch ($checkout_method) {
            case 'PayPal':
                $email = $com_params->get('paypal_email', false);

                $config_options[] = '   type: "' . $checkout_method . '"';
                $config_options[] = ',  success: "'.$returnURI.'"';
                $config_options[] = ',  cancel: "'.$returnURI.'"';
                if ($email) $config_options[] = ',   email: "' . $email . '"';
                if ($checkout_mode == 'sandbox')  $config_options[] = ',    sandbox: true';
                break;

            case 'GoogleCheckout':
                // 1. only accepts USD and GBP
                // 2. does not have sandbox
                $merch_id = $com_params->get('googlecheckout_marchant_id', false);
                $config_options[] = '   type: "' . $checkout_method . '"';
                if ($merch_id) $config_options[] = '   marchantID: "' . $merch_id . '"';
                break;

            case 'AmazonPayments':
                $merch_sign = $com_params->get('amazonpayments_merchant_signature', false);
                $merch_id   = $com_params->get('amazonpayments_merchant_id', false);
                $aws_key_id = $com_params->get('amazonpayments_aws_access_key_id', false);
                $config_options[] = '   type: "' . $checkout_method . '"';
                if ($merch_sign) $config_options[] = ',   merchant_signature: "' . $merch_sign . '"';
                if ($merch_id) $config_options[] = ',   merchant_id: "' . $merch_id . '"';
                if ($aws_key_id) $config_options[] = ',   aws_access_key_id: "' . $aws_key_id . '"';
                if ($checkout_mode == 'sandbox')  $config_options[] = ',    sandbox: true';
                break;

            case 'SendForm':
                // 1. does not have sandbox
                $url = $com_params->get('sendform_url', false);
                $config_options[] = '   type: "' . $checkout_method . '"';
                $config_options[] = ',  success: "'.$returnURI.'"';
                $config_options[] = ',  cancel: "'.$returnURI.'"';
                if ($url) $config_options[] = ',   url: "' . $url . '"';
                break;

            default:
                # code...
                break;
        }
        $config_options[] = '}});';



        // Add CSS
        RokQuickCartHelper::load_css($com_params->get('include_css', 1), $com_params);

        // Add JS
        JHtml::_('behavior.framework');
        $doc->addScript('components/com_rokquickcart/assets/js/simplecart/simpleCart.js');
        $doc->addScript('components/com_rokquickcart/assets/js/rokquickcart.js');
        $config_vars_js = implode("\n", $config_options);
        $doc->addScriptDeclaration($config_vars_js);

        $current_url = $uri->__toString(array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'));
        $page_title     = $com_params->get('page_title');
        $use_rokbox     = $com_params->get('use_rokbox', 1);
        $cols           = $com_params->get('page_columns', 2);
        $image_width    = $com_params->get('shelf_image_width', 100);
        $same_height    = $com_params->get('sameheight_items', true);
        $display_symbol = $com_params->get('display_item_symbol', 1);
        $items          = self::_prepItems($items);
        $this->items    = $items;

        // Assign page refs
        $this->assignRef('current_url', $current_url);
        $this->assignRef('same_height', $same_height);
        $this->assignRef('display_item_symbol', $display_symbol);
        $this->assignRef('image_width', $image_width);
        $this->assignRef('cols', $cols);
        $this->assignRef('tax', $tax);
        $this->assignRef('shipping', $shipping);
        $this->assignRef('page_title', $page_title);
        $this->assignRef('shipping_per_item', $shipping_per_item);
        $this->assignRef('use_rokbox', $use_rokbox);
        $this->assignRef('currency_symbol', $currency_symbol);
        $this->assignRef('checkout_mode', $checkout_mode);

        parent::display($tpl);

    }


    function _prepItems(&$items)
    {
        $app    = JFactory::getApplication();
        $option = JFactory::getApplication()->input->get('option');
        jimport('joomla.html.parameter');
        $com_params    = JComponentHelper::getParams($option);
        $default_image = $com_params->get('default_image', 'images/rokquickcart/samples/noimage.png');
        reset($items);
        foreach ($items as $item) {
            $item->_params           = new JRegistry($item->params);
            $item->_component_params = $com_params;

            if ($item->_params->get('has_sizes', null) !== null) $item->_params = $this->_normalizeParams($item->_params);

            $custom_fields      = $item->_params->get('custom_fields', false);
            $custom_fields_vars = $custom_fields == false ? false : get_object_vars($custom_fields);

            if (!empty($custom_fields_vars)){
                $item->custom_fields = new stdClass;
                foreach($custom_fields as $type => $selection){
                    $options = array();

                    foreach ($selection as $index => $option) {
                        $selected = !$index ? ' selected="selected"' : '';
                        $options[]   = '<option value="'.str_replace(' ', '_', $option).'"'.$selected.'>'.$option.'</option>';
                    }

                    $item->custom_fields->{$type} = '<select class="item_' . strtolower(str_replace(" ", '-', $type)) . '">'.implode("\n", $options).'</select>';
                }
            } else {
                $item->custom_fields = false;
            }

            $item->image            = (file_exists($item->image)) ? $item->image : $default_image;
            $item->fullImage        = RokQuickCartHelper::getFullImage($item->image);
            $item->shelfImage       = RokQuickCartHelper::getShelfImage($item->image);
            //$item->shelfImageHeight = RokQuickCartHelper::getShelfImageHeight($item->image);
            //$item->shelfImageWidth  = RokQuickCartHelper::getShelfImageWidth($item->image);
            //$item->cartImage        = RokQuickCartHelper::getCartImage($item->image);

            $decimal_point = (preg_match("/\,/", $item->price)) ? ',' : '.';
            $item->price = '<span class="cart_item-price_digits">'.implode('</span><span class="cart_item-price_decimals">'.$decimal_point, explode($decimal_point, $item->price)) . '</span>';
        }
        return $items;
    }

    function _getCurrecySymbol($currency)
    {
        switch ($currency) {
            case 'JPY':
                return "&yen;";
            case 'EUR':
                return "&euro;";
            case 'GBP':
                return "&pound;";
            case 'USD':
            case 'CAD':
            case 'AUD':
            case 'NZD':
            case 'HKD':
            case 'SGD':
            case 'MXN':
                return "&#36;";
            case 'BRL':
                return "R&#36;";
            case 'DKK':
                return "DKK&nbsp;";
            case 'HUF':
                return "&#70;&#116;&nbsp;";
            case 'ILS':
                return "&#8362;";
            case 'MYR':
                return "RM&nbsp;";
            case 'NOK':
                return "NOK&nbsp;";
            case 'PHP':
                return "&#8369;";
            case 'PLN':
                return "PLN&nbsp;";
            case 'RUB':
                return "&#8381;";
            case 'SEK':
                return "SEK&nbsp;";
            case 'CHF':
                return "CHF&nbsp;";
            case 'TWD':
                return "&#78;&#84;&#36;";
            case 'THB':
                return "&#3647;";
            case 'TRY':
                return "&#8378;";
            case 'BTC':
                return "BTC&nbsp;";
            default:
                return "";
        }
    }

    function _normalizeParams($params){
        $old_params = array('has_sizes', 'sizes', 'has_colors', 'colors');
        $new_params = new stdClass;
        foreach ($old_params as $param) {
            if (preg_match("/^has_/", $param)) continue;
            if ($params->get('has_' . $param, false)){
                $key   = ucfirst($param);
                $value = $params->get($param, null);
                if ($value) $new_params->$key = is_object($value) ? array_values(get_object_vars($value)) : $value;
            }
        }

        $params->set('custom_fields', $new_params);

        return $params;
    }
}
