<?php
/*
  $Id: fxfreight.php,v 1.3 2006/05/25 06:44:21 ken Exp $

  Ported to the MVS system by Ken Savich (morphadaz) - ksavich@gmail.com

  This module is for use with FedEx's freight shipping service, not with their regular shipping service.
  
  Copyright (c) 2005 Brian Burton - brian@dynamoeffects.com

  Released under the GNU General Public License
*/

  class fxfreight {
    var $code, $title, $description, $icon, $enabled;

// class constructor
    function fxfreight() {
      global $order;

      $this->code = 'fxfreight';
      $this->title = MODULE_SHIPPING_FXFREIGHT_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_FXFREIGHT_TEXT_DESCRIPTION;
      $this->icon = '';
     
      //FedEx Freight is only available in the US and Canada, so don't enable it if the customer ain't from these here parts
      $dest_country = $order->delivery['country']['iso_code_2'];
      if (($dest_country == 'US' || $dest_country == 'CA') && MODULE_SHIPPING_FXFREIGHT_STATUS == 'True') {
        $this->enabled = true;
      } else {
        $this->enabled = false;
      }
    }

    function sort_order($vendors_id='1') {
      if (defined (@constant ('MODULE_SHIPPING_FXFREIGHT_SORT_ORDER_' . $vendors_id))) {
        $this->sort_order = @constant('MODULE_SHIPPING_FXFREIGHT_SORT_ORDER_' . $vendors_id);
      } else {
        $this->sort_order = '0';
      }
      return $this->sort_order;
    }

    function tax_class($vendors_id='1') {
      $this->tax_class = constant('MODULE_SHIPPING_FXFREIGHT_TAX_CLASS_' . $vendors_id);
      return $this->tax_class;
    }

    function enabled($vendors_id='1') {
      $this->enabled = false;
      $status = @constant('MODULE_SHIPPING_FXFREIGHT_STATUS_' . $vendors_id);
			if (isset ($status) && $status != '') {
        $this->enabled = (($status == 'True') ? true : false);
      }
      return $this->enabled;
    }
		
    function zones($vendors_id='1') {
      if ( ($this->enabled == true) && ((int)constant('MODULE_SHIPPING_FXFREIGHT_ZONE_' . $vendors_id) > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int)constant('MODULE_SHIPPING_FXFREIGHT_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          } //if
        }//while

        if ($check_flag == false) {
          $this->enabled = false;
        }//if
      }//if
      return $this->enabled;
    }//function

// class methods
    function quote($method = '', $module = '', $vendors_id = '1') {
      global $order, $shipping_num_boxes, $cart;
      
      $error_msg = '';
      
      //First, we get the customer's zipcode and country in the right format.
      $dest_zip = $order->delivery['postcode'];
      $dest_country = $order->delivery['country']['iso_code_2'];
      
      if ($dest_country == 'US') {
          $dest_zip = substr($dest_zip, 0, 5);
        } elseif ($dest_country == 'CA') {
          $dest_zip = substr($dest_zip, 0, 6);
      } else {
        $error_msg = '<br>' . MODULE_SHIPPING_FXFREIGHT_TEXT_ERROR_BAD_COUNTRY;
      }
	  
	  
      if ($error_msg == '') {
        //Now, build an array of URLs to call.  Their server only allows 6 items at a time, so big carts will take a few calls
        
        //The base URL
        $base_URL = 'http://www.fedexfreight.fedex.com/XMLRating.jsp?';
        $base_URL .= 'as_shipterms=' . constant('MODULE_SHIPPING_FXFREIGHT_SHIP_TERMS_' . $vendors_id);
        $base_URL .= '&as_shzip=' . constant('MODULE_SHIPPING_FXFREIGHT_SHIP_ZIP_' . $vendors_id);
        $base_URL .= '&as_shcntry=' . constant('MODULE_SHIPPING_FXFREIGHT_SHIP_COUNTRY_' . $vendors_id);
        $base_URL .= '&as_cnzip=' . $dest_zip;
        $base_URL .= '&as_cncntry=' . $dest_country;
        $base_URL .= '&as_iamthe=shipper';
        if (trim(constant('MODULE_SHIPPING_FXFREIGHT_ACCT_NUM_' . $vendors_id)) != '') {
          $base_URL .= '&as_acctnbr=' . constant('MODULE_SHIPPING_FXFREIGHT_ACCT_NUM_' . $vendors_id);
        }
        
        //Get the shopping cart contents
        $products = $cart->get_products();
        $url_attr = '';
        $x = 1;
        $fxf_urls = array();
        $n = sizeof($products);
        
        for ($i=0; $i<$n; $i++) {
          $prod_query = tep_db_query("SELECT products_fxf_class, products_fxf_desc, products_fxf_nmfc, products_fxf_haz, products_fxf_freezable FROM " . TABLE_PRODUCTS . " WHERE products_id = '".$products[$i]['id']."'");
          $prod_info = tep_db_fetch_array($prod_query);
          //class, weight, pcs, descr, nmfc, haz, freezable
          $url_attr .= '&as_class' . $x . '=' . $prod_info['products_fxf_class'];
          $url_attr .= '&as_weight' . $x . '=' . $products[$i]['quantity'] * (int)$products[$i]['weight'];
          $url_attr .= '&as_pcs' . $x . '=' . $products[$i]['quantity'];

          if (trim($prod_info['products_fxf_desc']) != '') {
            $url_attr .= '&as_descr' . $x . '=' . urlencode($prod_info['products_fxf_desc']);
          }
          if (trim($prod_info['products_fxf_nmfc']) != '') {
            $url_attr .= '&as_nmfc' . $x . '=' . urlencode($prod_info['products_fxf_nmfc']);
          }
          if (trim($prod_info['products_fxf_haz']) != '') {
            $url_attr .= '&as_haz' . $x . '=' . $prod_info['products_fxf_haz'];
          }
          if (trim($prod_info['products_fxf_freezable']) != '') {
            $url_attr .= '&as_freezable' . $x . '=' . $prod_info['products_fxf_freezable'];
          }          

          //Six is the maximum number of products that FedEx will take at a time.
          if ($x >= 6) {
            $fxf_urls[] = array('pcs' => '6', 'url' => $base_URL . $url_attr);
            $x = 1;
            $url_attr = '';
          } else {
            $x++;
          }
        }
        
        if ($url_attr != '') $fxf_urls[] = array('pcs' => $x - 1, 'url' => $base_URL . $url_attr);
        
        $total_shipping_price = 0;
        
        //URL array is finished, now start calling FedEx.
        $n = sizeof($fxf_urls);
        for ($i=0; $i<$n; $i++) {
          $ship_price = $this->getFXFQuote($fxf_urls[$i]['url']);
          
          if (!$ship_price) { 
            $error_msg .= '<br>' . MODULE_SHIPPING_FXFREIGHT_TEXT_ERROR_BAD_RESPONSE . '<br>' . $fxf_urls[$i]['url'];
            break;
          }
          $total_shipping_price += $ship_price + ((float)constant('MODULE_SHIPPING_FXFREIGHT_HANDLING_' . $vendors_id) * $fxf_urls[$i]['pcs']);
        }
      }
      $vendors_data_query = tep_db_query("select handling_charge, 
                                                 handling_per_box,
                                                 vendor_country 
                                          from " . TABLE_VENDORS . " 
                                          where vendors_id = '" . (int)$vendors_id . "'"
                                        );
      $vendors_data = tep_db_fetch_array($vendors_data_query);
      $country_name = tep_get_countries($vendors_data['vendor_country'], true); 	

      $handling_charge = $vendors_data['handling_charge'];
      $handling_per_box = $vendors_data['handling_per_box'];
      if ($handling_charge > $handling_per_box*$shipping_num_boxes) {
        $handling = $handling_charge;
      } else {
        $handling = $handling_per_box*$shipping_num_boxes;
      }
      $total_shipping_price += $handling;
      if (!$error_msg) {
        $this->quotes = array('id' => $this->code,
                              'module' => MODULE_SHIPPING_FXFREIGHT_TEXT_TITLE,
                              'methods' => array(array('id' => $this->code,
                                                       'title' => MODULE_SHIPPING_FXFREIGHT_TEXT_WAY,
                                                       'cost' => $total_shipping_price)));            
  
        if ($this->tax_class > 0) {
          $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
        }
  
        if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);
      } else {
      
        switch (constant('MODULE_SHIPPING_FXFREIGHT_ERROR_ACTION_' . $vendors_id)) {
          case 'Email':
            if (tep_session_is_registered('customer_first_name') && tep_session_is_registered('customer_id')) {
              $error_msg_heading = 'This error log was generated when customer ' . $_SESSION['customer_first_name'] . ' (Customer ID: ' . $_SESSION['customer_id'] . ') checked out on ' . date('Y-m-d H:i') . ": \r\n\r\n";
            }
            tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, 'FedEx Freight Error Log ' . date('Y-m-d H:i'), $error_msg_heading . $error_msg , STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

          case 'None':
            $error_msg = '';
            break;
        }
        
        $this->quotes = array('module' => $this->title,
                              'error' => MODULE_SHIPPING_FXFREIGHT_TEXT_ERROR_DESCRIPTION . $error_msg);
      }
      return $this->quotes;
    }
    
    function getFXFQuote($url) {
      $isError = false;
      
      if (!($fp = @fopen($url, "r"))) {
        $isError = true;
      }

      if (!$isError) {
        $data = fread($fp, 4096);
        
        if (strpos($fp, 'RATINGERROR') === true || strpos($data, '<NetFreightCharges>') === false) {
          return false;
        } else {
          
          $start_pos = strpos($data, '<NetFreightCharges>') + 20;
          $string_len = strpos($data, '</NetFreightCharges>') - $start_pos;
          $shipping_price = str_replace(',', '', substr($data, $start_pos, $string_len));
        
          if (is_numeric($shipping_price)) {
            return $shipping_price;
          } else {
            return false;
          }
        }
      } else {
        return false;
      }
    }
    
	function check($vendors_id='1') {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '". $vendors_id ."' and configuration_key = 'MODULE_SHIPPING_FXFREIGHT_STATUS_" . $vendors_id . "'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install($vendors_id='1') {
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable FedEx Freight Shipping', 'MODULE_SHIPPING_FXFREIGHT_STATUS_" . $vendors_id . "', 'True', 'Do you want to offer FedEx Freight shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Shipping Terms', 'MODULE_SHIPPING_FXFREIGHT_SHIP_TERMS_" . $vendors_id . "', 'prepaid', 'Will these shipments be prepaid or COD? (This is here for future dev.  No COD support right now)', '6', '0', 'tep_cfg_select_option(array(\'prepaid\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Shipper\'s Zip Code', 'MODULE_SHIPPING_FXFREIGHT_SHIP_ZIP_" . $vendors_id . "', '', 'Enter the zip code of where these shipments will be sent from. (Required)', '6', '1', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Shipper\'s Country', 'MODULE_SHIPPING_FXFREIGHT_SHIP_COUNTRY_" . $vendors_id . "', 'US', 'Select the country where these shipments will be sent from.', '6', '0', 'tep_cfg_select_option(array(\'US\', \'CA\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Account Number', 'MODULE_SHIPPING_FXFREIGHT_ACCT_NUM_" . $vendors_id . "', '', 'If you have a FedEx Freight account number, enter it here. (Optional)', '6', '1', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Declare Shipment Value?', 'MODULE_SHIPPING_FXFREIGHT_DECLARE_VALUE_" . $vendors_id . "', 'False', 'Do you want to declare the value of the shipments? (the order total will be used)', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Residential Pickup', 'MODULE_SHIPPING_FXFREIGHT_RES_PICKUP_" . $vendors_id . "', 'False', 'Will FedEx be picking up the shipments at a residence?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Lift Gate', 'MODULE_SHIPPING_FXFREIGHT_LIFT_GATE_" . $vendors_id . "', 'False', 'Will FedEx need a lift gate to pick up the shipments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Error Logs', 'MODULE_SHIPPING_FXFREIGHT_ERROR_ACTION_" . $vendors_id . "', 'Email', 'If FedEx kicks back an error, how do you want to display it? (Email to store owner, display to customer, or none)', '6', '0', 'tep_cfg_select_option(array(\'Email\', \'Display\', \'None\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Handling Fee', 'MODULE_SHIPPING_FXFREIGHT_HANDLING_" . $vendors_id . "', '0', 'Handling fee for this shipping method (per item).', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Tax Class', 'MODULE_SHIPPING_FXFREIGHT_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_FXFREIGHT_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort order of display.', 'MODULE_SHIPPING_FXFREIGHT_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now(), '" . $vendors_id . "')");
    }

    function remove($vendors_id) {
      tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '". $vendors_id ."' and configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
    }

    function keys($vendors_id) {
      return array('MODULE_SHIPPING_FXFREIGHT_STATUS_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_SHIP_TERMS_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_SHIP_ZIP_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_SHIP_COUNTRY_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_ACCT_NUM_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_DECLARE_VALUE_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_RES_PICKUP_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_LIFT_GATE_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_ERROR_ACTION_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_HANDLING_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_TAX_CLASS_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_ZONE_' . $vendors_id, 'MODULE_SHIPPING_FXFREIGHT_SORT_ORDER_' . $vendors_id);
    }
  }
?>