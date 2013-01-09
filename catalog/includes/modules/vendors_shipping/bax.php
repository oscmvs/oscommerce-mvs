<?php
/*
  $Id: bax.php,v 2.0 2007/10/07 kymation Exp $
  $Loc: catalog/includes/modules/vendors_shipping/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
 
// Incorporate the XML conversion library 
  if (PHP_VERSION >= '5.0.0') { // PHP 5 does not need to use call-time pass by reference
    require_once (DIR_WS_CLASSES . 'xml_5.php');
  } else {
    require_once (DIR_WS_CLASSES . 'xml.php');
  }

  class bax {
    var $code, $title, $descrption, $icon, $enabled, $types;

//////////
// Class constructor
    function bax() {
      global $order;
      $this->code = 'bax';
      $this->title = MODULE_SHIPPING_BAX_TEXT_TITLE;   //Set in language file
      $this->description = MODULE_SHIPPING_BAX_TEXT_DESCRIPTION; //Set in language file
      $this->icon = DIR_WS_ICONS . 'shipping_bax.gif';
      $this->protocol = 'https';
      $this->port = '';
      $this->timeout = '60';
      $this->items_qty = 0;
			
				 
      $this->logfile = false;
// To enable logging, create an empty "bax.log" file at the location you set below, 
//    give it write permissions (CHMOD 777) and uncomment the next line
//      $this->logfile = '/usr/www/mystore/includes/modules/vendors_shipping/bax.log';

// When cURL is not compiled into PHP (Windows users, some Linux users),
//   set $this->use_exec to "1" and then exec(curl -d $xmlRequest, $xmlResponse)
//   will be used. (If that exists, of course.)
      $this->use_exec = '0';


// Available domestic service levels (Choose which to provide in Admin)
      $this->domestic_service_levels = array(
        'Guaranteed First Arrival',
        'Guaranteed Overnight',
        'Standard Overnight',
        'Guaranteed Second Day',
        'Standard Second Day',
        'BAXSaver Guaranteed',
        'Economy'
      );

// Available international service levels (Not available in this version)
      $this->international_service_levels = array(
        'Time Definite Premier',
        'Standard Air Freight',
        'International Express',
        'EMR Door to Door',
        'EMR Airport to Door',
        'BAXSaver',
        'Other'
      );

// Available domestic special services (set in admin)
      $this->domestic_special_services_pickup = array(
        'CONVENTION PICKUP',
        'DANGEROUS GOODS',
        'DRY ICE SHIPMENT',
        'HELPER - 2 MAN DELIVERY',
        'HELPER - 2 MAN PICKUP',
        'INSIDE PICKUP',
        'LIFTGATE TRUCK PICKUP',
        'SIGNATURE SERVICE'
      );

// Available domestic special services (Choose which to provide in Admin)
      $this->domestic_special_services_delivery = array(
        'STANDARD',
        'RESIDENTIAL DELIVERY',
        'LIFTGATE TRUCK DELIVERY',
        'INSIDE DELIVERY'
      );
    }

//	determine the shipping date for the quote (BAX wants this in the request)
    function ship_date($vendors_id='1') {
			if (constant ('MODULE_SHIPPING_BAX_DAYS_DELAY_' . $vendors_id) == '') {
			  $days_delay = 0;
      } else {
		    $days_delay = (int)constant ('MODULE_SHIPPING_BAX_DAYS_DELAY_' . $vendors_id);
			}
      return date("Y-m-d", mktime (0,0,0, date("m") ,date("d")+$days_delay, date("Y")));
    }

    function sort_order($vendors_id='1') {
      if (defined (@constant ('MODULE_SHIPPING_BAX_SORT_ORDER_' . $vendors_id))) {
        $sort_order = @constant('MODULE_SHIPPING_BAX_SORT_ORDER_' . $vendors_id);
      } else {
        $sort_order = '0';
      }
      return $sort_order;
    }

    function tax_class($vendors_id='1') {
      $tax_class = constant('MODULE_SHIPPING_BAX_TAX_CLASS_' . $vendors_id);
      return $tax_class;
    }

    function enabled($vendors_id='1') {
      $enabled = false;
      $status = @constant('MODULE_SHIPPING_BAX_STATUS_' . $vendors_id);
			if (isset ($status) && $status != '') {
        $enabled = (($status == 'True') ? true : false);
      }
      return $enabled;
    }

    function access_username($vendors_id='1') {
      $access_username = constant('MODULE_SHIPPING_BAX_USERNAME_' . $vendors_id);
      return $access_username;
    }

    function access_password($vendors_id='1') {
      $access_password = constant('MODULE_SHIPPING_BAX_PASSWORD_' . $vendors_id);
      return $access_password;
    }

    function origin_postalcode($vendors_id='1') {
      $origin_postalcode = constant('MODULE_SHIPPING_BAX_POSTAL_CODE_' . $vendors_id);
      return $origin_postalcode;
    }

    function rate_quote_type($vendors_id='1') {
      $rate_quote_type = constant('MODULE_SHIPPING_BAX_RATE_QUOTE_TYPE_' . $vendors_id);
      return $rate_quote_type;
    }

    function payment_terms($vendors_id='1') {
      $payment_terms = constant('MODULE_SHIPPING_BAX_PAYMENT_TERMS_' . $vendors_id);
      return $payment_terms;
    }

    function domestic_services($vendors_id='1') {
      $domestic_services = constant('MODULE_SHIPPING_BAX_DOMESTIC_SERVICES_' . $vendors_id);
      $domestic_services = str_replace (", --none--", "", $domestic_services);
      return $domestic_services;
    }

    function delivery_services($vendors_id='1') {
      $delivery_services = constant('MODULE_SHIPPING_BAX_DELIVERY_SERVICES_' . $vendors_id);
      $delivery_services = str_replace (", --none--", "", $delivery_services);
      return $delivery_services;
    }

    function special_services($vendors_id='1') {
      $special_services = constant('MODULE_SHIPPING_BAX_SPECIAL_SERVICES_' . $vendors_id);
      $special_services = str_replace (", --none--", "", $special_services);
      return $special_services;
    }

    function unit_dimension($vendors_id='1') {
      $unit_dimension = constant('MODULE_SHIPPING_BAX_UNIT_DIMENSIONS_' . $vendors_id);
      return $unit_dimension;
    }

    function unit_weight($vendors_id='1') {
      $unit_weight = constant('MODULE_SHIPPING_BAX_UNIT_WEIGHT_' . $vendors_id);
      return $unit_weight;
    }

    function default_density($vendors_id='1') {
      $default_density = constant('MODULE_SHIPPING_BAX_DEFAULT_DENSITY_' . $vendors_id);
      return $default_density;
    }

    function customs_fee_usa($vendors_id='1') {
      $customs_fee_usa = constant('MODULE_SHIPPING_BAX_CUSTOMS_FEE_USA_' . $vendors_id);
      return $customs_fee_usa;
    }

    function customs_fee_canada($vendors_id='1') {
      $customs_fee_canada = constant('MODULE_SHIPPING_BAX_CUSTOMS_FEE_CANADA_' . $vendors_id);
      return $customs_fee_canada;
    }

    function handling_charge($vendors_id='1') {
      global $shipping_num_boxes;
      $vendors_data_query = tep_db_query ("select handling_charge,
                                                  handling_per_box
                                           from " . TABLE_VENDORS . "
                                           where vendors_id = '" . (int) $vendors_id . "'"
                                         );
      $vendors_data = tep_db_fetch_array ($vendors_data_query);

      $handling_charge = $vendors_data['handling_charge'];
      $handling_per_box = $vendors_data['handling_per_box'];
      if ($handling_charge > $handling_per_box * $shipping_num_boxes) {
        $handling = $handling_charge;
      } else {
        $handling = $handling_per_box * $shipping_num_boxes;
      }
			
			if ($order->delivery['country']['iso_code_2'] == 'CA') { //Add the Customs fee for Canada
        $handling += $this->customs_fee_canada($vendors_id);
			}
			
			if ($order->delivery['country']['iso_code_2'] == 'US') { //Add the Customs fee for USA
        $handling *= $this->customs_fee_usa($vendors_id);
			}
			
      return $handling;
    }

    function host($vendors_id='1') {
      $host = ((constant('MODULE_SHIPPING_BAX_TEST_MODE_' . $vendors_id) == 'Test') ? 'secure.baxglobal.com/xmlRateQuote/webservicetest/XmlRateQuoteTest.asmx/XmlStringRateQuote' : 'secure.baxglobal.com/xmlRateQuote/webservice/XmlRateQuote.asmx/XmlStringRateQuote');
      return $host;
    }

    function zones($vendors_id='1') {
      if ( ($this->enabled($vendors_id) == true) && ((int)constant('MODULE_SHIPPING_BAX_ZONE_' . $vendors_id) > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id 
                                     from " . TABLE_ZONES_TO_GEO_ZONES . " 
                                     where geo_zone_id = '" . (int)constant('MODULE_SHIPPING_BAX_ZONE_' . $vendors_id) . "' 
                                       and zone_country_id = '" . $order->delivery['country']['id'] . "' 
                                     order by zone_id"
                                   );
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          } //if
        }//while
      }//if
      return $check_flag;
    }//function
		
		
//////////
// class method for getting either a specific quote or all quotes
    function quote($method = '', $module = '', $vendors_id = '1') {
      global $HTTP_POST_VARS, $order, $total_weight, $cart;

      //If method is properly set, extract the delivery special service and the shipping method from the encoded method
			if (strpos ($method, '-')) {  
        $shipping_method = explode ('-', $method);
				$delivery_service = ereg_replace('\+', ' ', $shipping_method[0]);
				$shipping_method = ereg_replace('\+', ' ', $shipping_method[1]);
			} else {  //Otherwise no method is set
				$delivery_service = '';
        $shipping_method = '';
      }

      $productsArray = $cart->get_products();
      $this->destination_postcode = $order->delivery['postcode'];

// Get the data needed for each product in the quote, and calculate the total number of pieces and total weight
//   (Set number of boxes equal to the number of products being shipped.
//   Unless we add another field to the Products table to give the number of boxes per product.)
      $this->items_qty = 0;
      $this->total_weight = 0;
			$this->declared_value = 0;
      $this->pieces = array();
      $this->item_length = array();
      $this->item_width = array();
      $this->item_height = array();

      if (is_array($order->products)) {  //Check that we actually have products to quote on

        foreach ($order->products as $products_data) {  // For each product
          $products_dimensions_query = tep_db_query("select vendors_id,
                                                            products_length, 
                                                            products_width, 
                                                            products_height 
                                                     from " . TABLE_PRODUCTS . " 
                                                     where products_id = '" .  $products_data['id'] . "'"
                                                    );

          //Check that the query actually returned a product
          if (tep_db_num_rows($products_dimensions_query)) {  
            $products_dimensions = tep_db_fetch_array($products_dimensions_query);

            if ($products_dimensions['vendors_id'] == $vendors_id) {  //Only for products shipping from this vendor
              $this->items_qty += (int)$products_data['qty'];
              $this->total_weight += $products_data['weight'];
              $this->declared_value += $products_data['final_price'];

              //Use the default calculation if one or more dimensions is missing (zero)
              if ($products_dimensions['products_length'] == 0 || 
                  $products_dimensions['products_width'] == 0 || 
                  $products_dimensions['products_height'] == 0) {
                $volume = $products_data['weight'] / $this->default_density($vendors_id);  //Volume of the container
                $dimensions = ceil (pow ($volume, (1/3)));  //Assume the container is a cube
                $this->products[] = array ('qty' => $products_data['qty'],
                                           'products_length' => $dimensions,
                                           'products_width' => $dimensions,
                                           'products_height' => $dimensions
                                          );
              } else { //Dimensions are available, so use them
                $this->products[] = array ('qty' => $products_data['qty'],
                                           'products_length' => $products_dimensions['products_length'],
                                           'products_width' => $products_dimensions['products_width'],
                                           'products_height' => $products_dimensions['products_height']
                                          );
              }
            }
          }
        }
      }
      $this->total_weight = ceil ($this->total_weight); //Weight must be an integer
      $this->declared_value = ceil ($this->declared_value); //Declared Value must be an integer dollar amount
				
//Start building the output array
      $this->quotes = array('id' => $this->code,  //code for this module
                            'module' => $this->title);  //Title for this module
			
// Get the quotes (multiple quotes if more than one Delivery Service is selected)
      if (strlen($shipping_method) < 1) {   //Get all of the quotes
        $domestic_services = explode (",", $this->domestic_services($vendors_id)); 
        $domestic_services = array_map ("trim", $domestic_services);
			
        $delivery_services = $this->delivery_services($vendors_id);
        if ($delivery_services == '') {  //Just in case no services were selected
          $delivery_services = 'STANDARD';
        }
        $delivery_services = explode (',', $delivery_services);
        $delivery_services = array_map ("trim", $delivery_services);
		
        foreach ($delivery_services as $delivery_service) {  //Each of the services selected in Admin
          $delivery_service_text = ucfirst (strtolower ($delivery_service)) . ' - ';  //The input is all uppercase
			    if ($delivery_service_text == 'Standard - ') {  //We don't want to show this
            $delivery_service_text = '';
          }

          $bax_quotes = $this->_baxGetQuote($delivery_service, $vendors_id);  //Get the quote
          
          if (is_array ($bax_quotes) && (sizeof ($bax_quotes) > 0) ) {  //Build the array to output
            // Set the error message if an error has been returned
            if (isset ($bax_quotes['Messages']['Comments']) && strlen (trim ($bax_quotes['Messages']['Comments'])) > 1) {  //There was an error
              $this->quotes['error'] = MODULE_SHIPPING_BAX_TEXT_COMM_UNKNOWN_ERROR . 
                                       '<br>' . $bax_quotes['Messages']['Comments'] . '<br>' . 
                                       MODULE_SHIPPING_BAX_TEXT_IF_YOU_PREFER;
            }

            $quotes_array = $bax_quotes['RateQuoteResultDetails']['RateQuoteResultByService'];
            if (is_array ($quotes_array) ) {  
              foreach ($quotes_array as $bax_quote) {
                $service_id = ereg_replace(' ', '+', ($delivery_service . '-' . $bax_quote['Service']));
    
                if (count ($domestic_services) > 0 && in_array ($bax_quote['Service'], $domestic_services) ) {
                  $cost = ($bax_quote['TotalCharges']) + $this->handling_charge;
                  $methods[] = array ('id' => $service_id,  
                                      'title' => ($delivery_service_text . $bax_quote['Service']),
                                      'cost' => $cost 
                                     );
                }  // if (count
              } // foreach ($quotes_array
            } // if (is_array ($quotes_array)
          } // if (is_array ($bax_quotes)
        } // foreach ($delivery_services
        
        if ($this->tax_class($vendors_id) > 0) {
          $this->quotes['tax'] = tep_get_tax_rate($this->tax_class($vendors_id), $order->delivery['country']['id'], $order->delivery['zone_id']);
        }
        $this->quotes['methods'] = $methods;  //Add these methods to the array

      } else {  //We only need to get the selected quote
        $delivery_service = trim ($delivery_service);
        $delivery_service_text = ucfirst (strtolower ($delivery_service)) . ' - ';  //The input is all uppercase
			  if ($delivery_service_text == 'Standard - ') {  //We don't want to show this
          $delivery_service_text = '';
        }

        $bax_quotes = $this->_baxGetQuote ($delivery_service, $vendors_id);  //Get the quote
  
        if ( (is_array ($bax_quotes)) && (sizeof ($bax_quotes) > 0) ) {  //Build the array to output
          // Set the error message if an error has been returned
          if (isset ($bax_quotes['Messages']['Comments']) && strlen (trim ($bax_quotes['Messages']['Comments'])) > 1) {  //There was an error
            $this->quotes['error'] = MODULE_SHIPPING_BAX_TEXT_COMM_UNKNOWN_ERROR . 
                                     '<br>' . $bax_quotes['Messages']['Comments'] . '<br>' . 
                                     MODULE_SHIPPING_BAX_TEXT_IF_YOU_PREFER;
          }

          $quotes_array = $bax_quotes['RateQuoteResultDetails']['RateQuoteResultByService'];
          if (is_array ($quotes_array) ) {  
            foreach ($quotes_array as $bax_quote) {
              $service_id = ereg_replace(' ', '+', ($delivery_service . '-' . $bax_quote['Service']));
            
              //If this service is the one we want
              if ($bax_quote['Service'] == $shipping_method) {
                $cost = ($bax_quote['TotalCharges']) + $this->handling_charge;
                $methods[] = array('id' => $service_id,
                                   'title' => ($delivery_service_text . $bax_quote['Service']),
                                   'cost' => $cost );
              } // if ($bax_quote['service']
            }  // foreach ($quotes_array
          } // if (is_array ($quotes_array)
        } // if (isset

        if ($this->tax_class($vendors_id) > 0) {
          $this->quotes['tax'] = tep_get_tax_rate($this->tax_class($vendors_id), $order->delivery['country']['id'], $order->delivery['zone_id']);
        }
        $this->quotes['methods'] = $methods;  //Add these methods to the array
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);

      return $this->quotes;
    }

//////////
//The method to check if the module is enabled
    function check($vendors_id='1') {
        if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '". $vendors_id ."' and configuration_key = 'MODULE_SHIPPING_BAX_STATUS_" . $vendors_id . "'");
            $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

//////////
//The install method
    function install($vendors_id='1') {
		  //Convert arrays to properly formatted strings to use in the Admin settings
		  $special_services_string = 'array(\\\'' . implode("\',\'", $this->domestic_special_services_pickup) . "\'";
		  $delivery_services_string = 'array(\\\'' . implode("\',\'", $this->domestic_special_services_delivery) . "\'";
			$domestic_services_string = 'array(\\\'' . implode("\',\'", $this->domestic_service_levels) . "\'";
			
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable BAX Shipping', 'MODULE_SHIPPING_BAX_STATUS_" . $vendors_id . "', 'True', 'Do you want to offer BAXGlobal shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort order of display.', 'MODULE_SHIPPING_BAX_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display. Lowest is displayed first.', '6', '1', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Tax Class', 'MODULE_SHIPPING_BAX_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '2', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('MyBAX Username', 'MODULE_SHIPPING_BAX_USERNAME_" . $vendors_id . "', 'baxdemo', 'Enter your MyBAX account username.', '6', '3', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('MyBAX Password', 'MODULE_SHIPPING_BAX_PASSWORD_" . $vendors_id . "', 'gobax!', 'Enter your MyBAX account password.', '6', '4', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Origin Zip Code', 'MODULE_SHIPPING_BAX_POSTAL_CODE_" . $vendors_id . "', '', 'Enter your origin zip/postalcode.', '6', '5', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Unit Length', 'MODULE_SHIPPING_BAX_UNIT_DIMENSIONS_" . $vendors_id . "', 'INCHES', 'By what unit are your packages sized?', '6', '6', 'tep_cfg_select_option(array(\'INCHES\', \'CENTIMETERS\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Unit Weight', 'MODULE_SHIPPING_BAX_UNIT_WEIGHT_" . $vendors_id . "', 'POUNDS', 'By what unit are your packages weighed?', '6', '7', 'tep_cfg_select_option(array(\'POUNDS\', \'KILOS\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Default Density', 'MODULE_SHIPPING_BAX_DEFAULT_DENSITY_" . $vendors_id . "', '0.01', 'Density for packages where dimensions are not specified?', '6', '7', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Test or Production Mode', 'MODULE_SHIPPING_BAX_TEST_MODE_" . $vendors_id . "', 'Test', 'Use this module in Test or Production mode?', '6', '8', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_BAX_ZONE_" . $vendors_id . "', '', 'If a zone is selected, only enable this shipping method for that zone.', '6', '9', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Rate Quote Type', 'MODULE_SHIPPING_BAX_RATE_QUOTE_TYPE_" . $vendors_id . "', '', 'Limit to USA & Canada or International', '6', '10', 'tep_cfg_select_option(array(\'DOMESTIC\', \'INTERNATIONAL\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Payment Terms', 'MODULE_SHIPPING_BAX_PAYMENT_TERMS_" . $vendors_id . "', 'DOMESTIC', 'Select how you pay for BAX service', '6', '11', 'tep_cfg_select_option(array(\'PREPAID\', \'COLLECT\', \'THIRDPARTY\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Services', 'MODULE_SHIPPING_BAX_DOMESTIC_SERVICES_" . $vendors_id . "', 'PREPAID', 'Restrict to one or more domestic services', '6', '12', 'tep_cfg_select_multioption(" . $domestic_services_string . "), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Delivery Services', 'MODULE_SHIPPING_BAX_DELIVERY_SERVICES_" . $vendors_id . "', '', 'Special delivery services that you want to offer', '6', '13', 'tep_cfg_select_multioption(" . $delivery_services_string . "), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Special Services', 'MODULE_SHIPPING_BAX_SPECIAL_SERVICES_" . $vendors_id . "', '', 'Select any special services that you need', '6', '14', 'tep_cfg_select_multioption(" . $special_services_string . "), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Handling Fee', 'MODULE_SHIPPING_BAX_HANDLING_" . $vendors_id . "', '0', 'Handling fee for BAX.', '6', '15', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Customs Fee USA', 'MODULE_SHIPPING_BAX_CUSTOMS_FEE_USA_" . $vendors_id . "', '0', 'Customs fee for shipment to USA.', '6', '16', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Customs Fee Canada', 'MODULE_SHIPPING_BAX_CUSTOMS_FEE_CANADA_" . $vendors_id . "', '0', 'Customs fee for shipment to Canada.', '6', '17', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function, vendors_id) values ('Shipping Delay', 'MODULE_SHIPPING_BAX_DAYS_DELAY_" . $vendors_id . "', '1', 'How many days from when an order is placed to when you ship it. ', '6', '18', NULL, now(), NULL, NULL, '" . $vendors_id . "')");
    }

//////////
//The remove method
    function remove($vendors_id) {
      tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '". $vendors_id ."' and configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
    }

//////////
//The keys to use in the remove method
    function keys($vendors_id) {
      return array('MODULE_SHIPPING_BAX_STATUS_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_SORT_ORDER_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_TAX_CLASS_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_USERNAME_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_PASSWORD_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_POSTAL_CODE_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_UNIT_DIMENSIONS_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_UNIT_WEIGHT_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_DEFAULT_DENSITY_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_TEST_MODE_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_ZONE_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_RATE_QUOTE_TYPE_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_PAYMENT_TERMS_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_DOMESTIC_SERVICES_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_DELIVERY_SERVICES_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_SPECIAL_SERVICES_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_HANDLING_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_CUSTOMS_FEE_USA_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_CUSTOMS_FEE_CANADA_' . $vendors_id,
                   'MODULE_SHIPPING_BAX_DAYS_DELAY_' . $vendors_id
                   );
    }

//////////
// Magic quote method
  function _baxGetQuote($delivery_service='', $vendors_id='1') {

// Create the XML request (text string)
    $accessRequestHeader =   //Header and Login section
      "	<BAXRateQuoteRequest>\n" .
      "	<RateQuoteRequest>\n" .
      "	<Login>\n" . 
      "   <UserId>" . $this->access_username($vendors_id) . "</UserId>\n" .
      "   <Password>" . $this->access_password($vendors_id) . "</Password>\n" .
      "</Login>\n";

    $ratingServiceSelectionRequestHeader =         //Shipper informnation
      "<RateQuoteType>" . $this->rate_quote_type($vendors_id) . "</RateQuoteType>\n".
      "<PaymentTerms>" . $this->payment_terms($vendors_id) . "</PaymentTerms>\n".
      "<ShipDate>" . $this->ship_date($vendors_id) . "</ShipDate>\n".
      "<OriginZipCode>" . $this->origin_postalcode($vendors_id) . "</OriginZipCode>\n";

    $ratingServiceSelectionDeliveryInformation =   //Delivery information
      "	<DeliveryInformation>\n" .
      "<DestinationZipCode>". $this->destination_postcode . "</DestinationZipCode>\n";
		
    if ($delivery_service != '' && $delivery_service != 'STANDARD' && $delivery_service != '--none--') {  //Standard is the default
      switch ($delivery_service) {
        case 'LIFTGATE TRUCK DELIVERY':
          $ratingServiceSelectionDeliveryInformation .=   
            "<SpecialService>LIFTGATE TRUCK DELIVERY</SpecialService>\n";
          break;
        case 'INSIDE DELIVERY':
          $ratingServiceSelectionDeliveryInformation .=   
            "<SpecialService>LIFTGATE TRUCK DELIVERY</SpecialService>\n" .
            "<SpecialService>INSIDE DELIVERY</SpecialService>\n";
          break;
        case 'RESIDENTIAL DELIVERY':
          $ratingServiceSelectionDeliveryInformation .=   
            "<SpecialService>LIFTGATE TRUCK DELIVERY</SpecialService>\n" .
//            "<SpecialService>INSIDE DELIVERY</SpecialService>\n" .
            "<SpecialService>RESIDENTIAL DELIVERY</SpecialService>\n";
          break;
        default:
          break;
      }
    }
		
    if ($this->special_services($vendors_id) != '') {
      $special_services = explode (',', $this->special_services($vendors_id));
			if (count($special_services) > 0) {
        foreach ($special_services as $special_service) {  //Add the special services (selected in Admin)
				  if ($special_service != '--none--') {
            $ratingServiceSelectionDeliveryInformation .=   
              "<SpecialService>". $special_service . "</SpecialService>\n";
          }
			  }
			}
    }
		
    $ratingServiceSelectionDeliveryInformation .=   //End delivery information
		  "	</DeliveryInformation>\n";

    $ratingServiceSelectionRequestPackageInformation = //Package information
      "	<PackageInformation>\n" .
      "<TotalPieces>" . $this->items_qty . "</TotalPieces>\n" .
      "<ShipmentWeight>" . $this->total_weight . "</ShipmentWeight>\n" .
      "<WeightUnitOfMeasure>" . $this->unit_weight($vendors_id) . "</WeightUnitOfMeasure>\n" .
      "<DeclaredValue>" . $this->declared_value . "</DeclaredValue>\n";
// Add COD cost to the quote (not supported in this version)
//      "<CODValue>". $this->cod_value ."</CODValue>\n";
				
    $ratingServiceSelectionRequestDimensions =   //Dimensions of packages
      "	<Dimensions>\n" .
      "<DimensionUnitOfMeasure>" . $this->unit_dimension($vendors_id) . "</DimensionUnitOfMeasure>\n".
      "	<Packages>\n";
				
    foreach ($this->products as $product) {    //Data on each package (by product)
      $ratingServiceSelectionRequestDimensions .= 
        "	<PackageDimensions>\n" .
        "<Pieces>" . (int)$product['qty'] . "</Pieces>\n".
        "<Length>" . (int)$product['products_length'] . "</Length>\n".
        "<Width>" .  (int)$product['products_width'] . "</Width>\n".
        "<Height>" . (int)$product['products_height'] . "</Height>\n".
        "</PackageDimensions>\n";
    }
		
    $ratingServiceSelectionRequestDimensions .=   //Close the package information section
      "</Packages>\n" .
      "</Dimensions>\n" .
      "</PackageInformation>\n";

    $ratingServiceSelectionSaveRateQuote =    //Save rate quote (Must be YES, so why do they bother?)
      "	<SaveRateQuote>\n" .
      "<ServiceLevel1>Yes</ServiceLevel1>\n" .
      "<ServiceLevel2>Yes</ServiceLevel2>\n" .
      "<ServiceLevel3>Yes</ServiceLevel3>\n" .
      "<ServiceLevel4>Yes</ServiceLevel4>\n" .
      "<ServiceLevel5>Yes</ServiceLevel5>\n" .
      "<ServiceLevel6>Yes</ServiceLevel6>\n" .
      "<ServiceLevel7>Yes</ServiceLevel7>\n" .
      "<ServiceLevel8>Yes</ServiceLevel8>\n" .
      "</SaveRateQuote>\n";
						
    $ratingServiceSelectionRequestFooter =   //Close the request
      "</RateQuoteRequest>\n".
      "</BAXRateQuoteRequest>\n";

    $xmlRequest = $accessRequestHeader .  
    $ratingServiceSelectionRequestHeader .
    $ratingServiceSelectionDeliveryInformation .
    $ratingServiceSelectionRequestPackageInformation .
    $ratingServiceSelectionRequestDimensions .
    $ratingServiceSelectionSaveRateQuote .
    $ratingServiceSelectionRequestFooter;

//post the request to the BAX server and return the result
    $xmlResult = $this->_post($this->protocol, $this->host($vendors_id), $this->port, $this->path, $this->version, $this->timeout, $xmlRequest);

//Uncomment the following lines if you want to see what is being returned from BAX		
//  (This normally gets logged if logging is turned on)		
//     print 'Result from BAX: <br>';
//     print '<pre>';
//		 print_r ($xmlResult);  
//     print '</pre>';

    return $this->_parseResult($xmlResult);
  }

//////////
//Magic post method to send the request to BAX
    function _post($protocol, $host, $port, $path, $version, $timeout, $xmlRequest) {
      $url = $protocol . "://" . $host;
      $request_string = "XmlString=" . $xmlRequest;
			
      if ($this->logfile) {  //Log header for the request
        error_log("\n------------------------------------------\n", 3, $this->logfile);
        error_log("DATE AND TIME: ".date('Y-m-d H:i:s')."\n", 3, $this->logfile);
        error_log("BAX URL: " . $url . "\n", 3, $this->logfile);
      }
			
      if (function_exists('exec') && $this->use_exec == '1' ) {  //Only used if libcurl is not installed
        exec('which curl', $curl_output);
        if ($curl_output) {
          $curl_path = $curl_output[0];
        } else {
          $curl_path = 'curl'; // add the full path to cURL if necessary
        }
				
        if ($this->logfile) {  //Write the request to the logfile
          error_log("BAX REQUEST using exec(): " . $xmlRequest . "\n", 3, $this->logfile);
        }
				
        $command = "" . $curl_path . " -d \"" . $request_string . "\" " . $url . "";
// If you get curl error 60: "error setting certificate verify locations" then
//   comment out the previous line and uncomment the following line
//        $command = "" . $curl_path . " -k -d \"" . $request_string . "\" ". $url . "";

        exec($command, $xmlResponse);
        if ( empty($xmlResponse) && $this->logfile) { // using exec no curl errors can be retrieved
          error_log("Error from cURL using exec() since there is no \$xmlResponse\n", 3, $this->logfile);
        }
        if ($this->logfile) {  //Write the response to the logfile
          error_log("BAX RESPONSE using exec(): " . $xmlResponse[0] . "\n", 3, $this->logfile);
        }
				
      } elseif ($this->use_exec == '1') { //No curl at all; we're out of luck
        if ($this->logfile) {
	        error_log("Sorry, exec() cannot be called\n", 3, $this->logfile);
        }
				
	    } else { // default behavior: cURL is assumed to be compiled in PHP
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_INFILESIZE, strlen($request_string));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_string);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);

// Uncomment the next line if you get curl error 60: error setting certificate verify locations
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// Uncommenting the next line is most likely not necessary in case of error 60
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        if ($this->logfile) {  //Log the full request
          error_log("BAX REQUEST: \n" . $xmlRequest . "\n", 3, $this->logfile);
        }
        $xmlResponse = curl_exec ($ch);
        $xmlResponse = html_entity_decode ($xmlResponse);  //Needed to decode the htmlencoded response
				
        if (curl_errno($ch) && $this->logfile) {  //Log errors
        $error_from_curl = sprintf('Error [%d]: %s', curl_errno($ch), curl_error($ch));
          error_log("Error from cURL: " . $error_from_curl . "\n", 3, $this->logfile);
        }
				
        if ($this->logfile) {  //Write the response to the logfile
          error_log("BAX RESPONSE: " . $xmlResponse . "\n", 3, $this->logfile);
        }
        curl_close ($ch);
      }

      if (strlen ($xmlResponse) < 10)  {  //If there is no usable response, create an error message
        $xmlResponse = "<BaxRateQuoteResponse>\n" . 
                       "<Error>" . MODULE_SHIPPING_BAX_TEXT_COMM_UNKNOWN_ERROR . "</Error>\n" . 
				               "</BaxRateQuoteResponse>\n";
      }
			
      if ($this->use_exec == '1') {
        return $xmlResponse[0]; // $xmlResponse is an array in this case; we only need element 0
      } else {
        return $xmlResponse;
      }
    }

//////////
// Parse the XML message returned by the BAX post server
    function _parseResult($xmlResult) {
      $data_array = XML_unserialize ($xmlResult);
      $quotes_array = $data_array['string']['BAXRateQuoteResponse']['RateQuoteResult'];

        
      if ($this->logfile) {  //Write the response to the logfile
        error_log("Returned data array: " . var_export ($quotes_array, TRUE) . "\n", 3, $this->logfile);
      }
    
// Sample output array
// -> First section is the data that was sent to BAX
// $quotes_array[AccountNumber] => 123456789
// $quotes_array[Messages][Comments] => (Error messages if any)
// $quotes_array[RateQuoteRequest][Login][UserId] => baxdemo
// $quotes_array[RateQuoteRequest][Login][Password] => gobax!
// $quotes_array[RateQuoteRequest][RateQuoteType] => DOMESTIC
// $quotes_array[RateQuoteRequest][PaymentTerms] => THIRDPARTY
// $quotes_array[RateQuoteRequest][ShipDate] => 2007-10-06
// $quotes_array[RateQuoteRequest][OriginZipCode] => 90248
// $quotes_array[RateQuoteRequest][DeliveryInformation][DestinationZipCode] => 98520
// $quotes_array[RateQuoteRequest][DeliveryInformation][SpecialService] => RESIDENTIAL DELIVERY
// $quotes_array[RateQuoteRequest][PackageInformation][TotalPieces] => 3
// $quotes_array[RateQuoteRequest][PackageInformation][ShipmentWeight] => 33
// $quotes_array[RateQuoteRequest][PackageInformation][WeightUnitOfMeasure] => POUNDS
// $quotes_array[RateQuoteRequest][PackageInformation][DeclaredValue] => 400
// $quotes_array[RateQuoteRequest][PackageInformation][Dimensions][DimensionUnitOfMeasure] => INCHES
// $quotes_array[RateQuoteRequest][PackageInformation][Dimensions][Packages][PackageDimensions][0][Pieces] => 1
// $quotes_array[RateQuoteRequest][PackageInformation][Dimensions][Packages][PackageDimensions][0][Length] => 12
// $quotes_array[RateQuoteRequest][PackageInformation][Dimensions][Packages][PackageDimensions][0][Width] => 18
// $quotes_array[RateQuoteRequest][PackageInformation][Dimensions][Packages][PackageDimensions][0][Height] => 8
// $quotes_array[RateQuoteRequest][PackageInformation][Dimensions][Packages][PackageDimensions][1][Pieces] => 2
// ... etc.

// -> Second section is the rate quote for the requested special (or standard) service
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][Service] => Guaranteed First Arrival
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][FreightCharges] => 163
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][AccessorialCharges][0] => 54.48
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][AccessorialCharges][1][AccessorialFee][0 attr][Description] => Excess Value Fee                 (EVF)
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][AccessorialCharges][1][AccessorialFee][0] => 2.6
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][AccessorialCharges][1][AccessorialFee][1 attr][Description] => Delivery Charge                  (DLC)
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][AccessorialCharges][1][AccessorialFee][1] => 40
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][AccessorialCharges][1][AccessorialFee][2 attr][Description] => Fuel Surcharge                   (FSC)
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][AccessorialCharges][1][AccessorialFee][2] => 11.88
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][TotalCharges] => 217.48
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][LatestDeliveryDateTime] => Wednesday, October 10, 2007 5:00 PM
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][RateQuoteNumber] => 1859247
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][0][DimensionWeight] => 33
// $quotes_array[RateQuoteResultDetails][RateQuoteResultByService][1][Service] => Guaranteed Overnight
// ... etc.

      return $quotes_array;
    }
  }
?>
