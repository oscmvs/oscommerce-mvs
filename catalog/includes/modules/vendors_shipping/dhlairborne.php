<?php

/*
  $Id: dhlairborne.php,v 1.4 2006/05/25 06:44:21 ken Exp $
  $Loc: catalog/includes/modules/vendors_shipping/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  Ported to the MVS system by Ken Savich (morphadaz) - ksavich@gmail.com

  Copyright (c) 2003-2004 Joshua Dechant

  Portions Copyright (c) 2002 osCommerce
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Portions Copyright (c) 2003 Hans Anderson
  Portions Copyright (c) 2003 Erich Spencer
  Portions Copyright (c) 2002 Fritz Clapp
  
  Released under the GNU General Public License
*/

  class dhlairborne {
    var $code, $title, $description, $icon, $enabled, $debug, $types, $allowed_methods;

    // DHL/Airborne Vars
    var $service;
    var $container;
    var $shipping_day;
    var $weight;
    var $dimensions;
    var $length;
    var $width;
    var $height;
    var $destination_city;
    var $destination_state;
    var $destination_postal;
    var $destination_country;
    var $additionalProtection;

    function dhlairborne() {
      global $order;

      $this->code = 'dhlairborne';
      $this->title = MODULE_SHIPPING_AIRBORNE_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_AIRBORNE_TEXT_DESCRIPTION;
      $this->icon = DIR_WS_ICONS . MODULE_SHIPPING_AIRBORNE_ICON;

      // The shipping rate estimate for ShipIt API is currently US based only
      if ($order->delivery['country']['iso_code_2'] != 'US') {
        $this->enabled = false;
      }

      $this->types = array (
        'G' => MODULE_SHIPPING_AIRBORNE_TEXT_GROUND,
        'S' => MODULE_SHIPPING_AIRBORNE_TEXT_SECOND_DAY,
        'N' => MODULE_SHIPPING_AIRBORNE_TEXT_NEXT_AFTERNOON,
        'E' => MODULE_SHIPPING_AIRBORNE_TEXT_EXPRESS,
        'E 10:30AM' => MODULE_SHIPPING_AIRBORNE_TEXT_EXPRESS_1030,
        'E SAT' => MODULE_SHIPPING_AIRBORNE_TEXT_EXPRESS_SAT
      );
    }

    function ship_key($vendors_id = '1') {
      $this->ship_key = constant('MODULE_SHIPPING_AIRBORNE_SHIP_KEY_' . $vendors_id);
      return $this->ship_key;
    }

    function system_id($vendors_id = '1') {
      $this->system_id = constant('MODULE_SHIPPING_AIRBORNE_SYSTEMID_' . $vendors_id);
      return $this->system_id;
    }

    function airborne_pass($vendors_id = '1') {
      $this->airborne_pass = constant('MODULE_SHIPPING_AIRBORNE_PASS_' . $vendors_id);
      return $this->airborne_pass;
    }
    function acct_nbr($vendors_id = '1') {
      $this->acct_nbr = constant('MODULE_SHIPPING_AIRBORNE_ACCT_NBR_' . $vendors_id);
      return $this->acct_nbr;
    }
    function protection_type($vendors_id = '1') {
      $this->protection_type = constant('MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_TYPE_' . $vendors_id);
      return $this->protection_type;
    }

    // mvs begin
    function sort_order($vendors_id = '1') {
      if (defined(@ constant('MODULE_SHIPPING_AIRBORNE_SORT_ORDER_' . $vendors_id))) {
        $this->sort_order = @ constant('MODULE_SHIPPING_AIRBORNE_SORT_ORDER_' . $vendors_id);
      } else {
        $this->sort_order = '0';
      }
      return $this->sort_order;
    }

    function tax_class($vendors_id = '1') {
      $this->tax_class = constant('MODULE_SHIPPING_AIRBORNE_TAX_CLASS_' . $vendors_id);
      return $this->tax_class;
    }

    function enabled($vendors_id = '1') {
      $this->enabled = false;
      $status = @ constant('MODULE_SHIPPING_AIRBORNE_STATUS_' . $vendors_id);
      if (isset ($status) && $status != '') {
        $this->enabled = (($status == 'True') ? true : false);
      }
      return $this->enabled;
    }

    function zones($vendors_id = '1') {
      if (($this->enabled($vendors_id) == true) && ((int) constant('MODULE_SHIPPING_AIRBORNE_ZONE_' . $vendors_id) > 0)) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) constant('MODULE_SHIPPING_AIRBORNE_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          }
          elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          } //if
        } //while

        if ($check_flag == false) {
          $this->enabled = false;
        } //if
      } //if
      return $this->enabled($vendors_id);
    } //function

    function debug($vendors_id = '1') {
      $this->enabled = false;
      $status = @ constant('MODULE_SHIPPING_AIRBORNE_DEBUG_' . $vendors_id);
      if (isset ($status) && $status != '') {
        $this->enabled = (($status == 'True') ? true : false);
      }
      return $this->enabled;
    }
    // mvs end

    // class methods
    function quote($method = '', $module = '', $vendors_id = '1') {
      global $order, $shipping_weight, $shipping_num_boxes;

      if ((tep_not_null($method)) && (isset ($this->types[$method]))) {
        $this->_setService($method);
      }

      $this->_setMethods(constant('MODULE_SHIPPING_AIRBORNE_TYPES_' . $vendors_id));
      $this->_setDestination($order->delivery['city'], $order->delivery['state'], $order->delivery['postcode'], $order->delivery['country']['iso_code_2']);
      $this->_setContainer(constant('MODULE_SHIPPING_AIRBORNE_PACKAGE_' . $vendors_id));
      $this->_setWeight($shipping_weight);
      $this->_setShippingDay(constant('MODULE_SHIPPING_AIRBORNE_DAYS_TO_SHIP_' . $vendors_id), constant('MODULE_SHIPPING_AIRBORNE_SHIPMENT_DAY_' . $vendors_id), $vendors_id);
      if (constant('MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_WEIGHT_' . $vendors_id) == 'true')
        $this->_setDimensions(constant('MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_EXCLUSIVE_' . $vendors_id));
      if (constant('MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_' . $vendors_id) == 'true')
        $this->_setAdditionalProtection(constant('MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_VALUE_' . $vendors_id));

      $vendors_data_query = tep_db_query("select handling_charge, 
                                                 handling_per_box,
                                                 vendor_country 
                                          from " . TABLE_VENDORS . " 
                                          where vendors_id = '" . (int) $vendors_id . "'");
      $vendors_data = tep_db_fetch_array($vendors_data_query);
      $country_name = tep_get_countries($vendors_data['vendor_country'], true);

      $handling_charge = $vendors_data['handling_charge'];
      $handling_per_box = $vendors_data['handling_per_box'];

      if ($handling_charge > $handling_per_box * $shipping_num_boxes) {
        $handling = $handling_charge;
      } else {
        $handling = $handling_per_box * $shipping_num_boxes;
      }

      $dhlAirborneQuotes = $this->_getQuote();

      if (is_array($dhlAirborneQuotes)) {
        if (isset ($dhlAirborneQuotes['error'])) {
          $this->quotes = array (
            'module' => $this->title . ' (' . $shipping_num_boxes . ' x ' . $this->weight . 'lbs)',
            'error' => $dhlAirborneQuotes['error']
          );
        } else {
          $this->quotes = array (
            'id' => $this->code,
            'module' => $this->title . ' (' . $shipping_num_boxes . ' x ' . $this->weight . 'lbs)'
          );

          $methods = array ();
          foreach ($dhlAirborneQuotes as $dhlAirborneQuote) {
            list ($type, $cost) = each($dhlAirborneQuote);
            if ($cost != '0.00') {
              $methods[] = array (
                'id' => $type,
                'title' => ((isset ($this->types[$type]
              )) ? $this->types[$type] : $type) . $dhlAirborneQuote['description'], 'cost' => ($cost * $shipping_num_boxes) + $handling);
            }
          }

          $this->quotes['methods'] = $methods;

          if ($this->tax_class($vendors_id) > 0) {
            $this->quotes['tax'] = tep_get_tax_rate($this->tax_class($vendors_id), $order->delivery['country']['id'], $order->delivery['zone_id']);
          }
        }
      } else {
        $this->quotes = array (
          'module' => $this->title,
          'error' => MODULE_SHIPPING_AIRBORNE_TEXT_ERROR
        );
      }

      if (tep_not_null($this->icon))
        $this->quotes['icon'] = tep_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check($vendors_id = '1') {
      if (!isset ($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key = 'MODULE_SHIPPING_AIRBORNE_STATUS_" . $vendors_id . "'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    // function install() {
    function install($vendors_id = '1') {
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable DHL/Airborne Domestic Shipping', 'MODULE_SHIPPING_AIRBORNE_STATUS_" . $vendors_id . "', 'True', 'Do you want to offer domestic DHL/Airborne shipping?<p><a href=\"javascript:window.open(\'dhlairborne_docs.html\',\'dhlairborne_docs\',\'height=375,width=550,toolbar=no,statusbar=no,scrollbars=yes,screenX=150,screenY=150,top=150,left=150\');\"><u>Help</u> [?]</a>', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Enter the DHL/Airborne system ID', 'MODULE_SHIPPING_AIRBORNE_SYSTEMID_" . $vendors_id . "', '', 'Enter your DHL/Airborne system ID.', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Enter the DHL/Airborne password', 'MODULE_SHIPPING_AIRBORNE_PASS_" . $vendors_id . "', '', 'Enter your DHL/Airborne password.', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Enter the DHL/Airborne shipping key', 'MODULE_SHIPPING_AIRBORNE_SHIP_KEY_" . $vendors_id . "', '', 'Enter the DHL/Airborne shipping key assigned to you.', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Enter the DHL/Airborne account number', 'MODULE_SHIPPING_AIRBORNE_ACCT_NBR_" . $vendors_id . "', '', 'Enter your DHL/Airborne customer/account number.', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Which server to use', 'MODULE_SHIPPING_AIRBORNE_SERVER_" . $vendors_id . "', 'test', 'An account with DHL/Airborne is needed to use the Production server', '6', '0', 'tep_cfg_select_option(array(\'test\', \'production\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ( 'Shipping Methods', 'MODULE_SHIPPING_AIRBORNE_TYPES_" . $vendors_id . "', 'G, S, N, E', 'Select the DHL/Airborne shipping services to be offered.<p>Available Methods:<br>G - Ground<br>S - Second Day Service<br>N - Next Afternoon<br>E - Express<br>E 10:30AM - Express 10:30 AM<br>E SAT - Express Saturday', '6', '0', '_selectOptions3254(array(\'G\',\'S\', \'N\', \'E\', \'E 10:30AM\', \'E SAT\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Estimated Delivery Time', 'MODULE_SHIPPING_AIRBORNE_EST_DELIVERY_" . $vendors_id . "', 'true', 'Display the estimated delivery time beside the shipment method in checkout?', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Shipment Type', 'MODULE_SHIPPING_AIRBORNE_PACKAGE_" . $vendors_id . "', 'P', 'Select the type of shipment:<br>P - Package<br>L - Letter', '6', '0', 'tep_cfg_select_option(array(\'P\', \'L\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Shipment Type', 'MODULE_SHIPPING_AIRBORNE_SHIPMENT_DAY_TYPE_" . $vendors_id . "', 'Ship in x number of days', 'Select whether you usually ship on a certain day (ex: every Monday) or usually in x number of days (ship out in 2 days).  Then set either the x number of days or set day below.', '6', '0', 'tep_cfg_select_option(array(\'Ship in x number of days\', \'Ship on certain day\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Days to Shipment', 'MODULE_SHIPPING_AIRBORNE_DAYS_TO_SHIP_" . $vendors_id . "', '2', 'If using \"Ship in x number of days,\" how many days do you estimate it will be from when a customers orders until you ship the packages? (0 = ship same day, 1 = ship the following day, etc)', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Shipment Day', 'MODULE_SHIPPING_AIRBORNE_SHIPMENT_DAY_" . $vendors_id . "', 'Monday', 'Select the day you ship on if using \"Ship on certain day\"', '6', '0', 'tep_cfg_select_option(array(\'Monday\', \'Tuesday\', \'Wednesday\', \'Thursday\', \'Friday\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Override Express Saturday Shipping', 'MODULE_SHIPPING_AIRBORNE_OVERRIDE_EXP_SAT_" . $vendors_id . "', 'false', 'If you want to enable shipping an Express Saturday shipment on a day that is not Friday, use this override to generate the shipping quote.', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Dimensional Weight', 'MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_WEIGHT_" . $vendors_id . "', 'false', 'Do you want to use dimensions in the rate request? (dimensions set below)', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Average Dimensions', 'MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_TABLE_" . $vendors_id . "', '1:12x8x2,3:24x10x3', 'Setup your average dimensions as follows: <em>number of products ordered<b>:</b>length<b>x</b>width<b>x</b>height</em> with a comma seperating each entry.', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Dimensional Exclusive Option', 'MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_EXCLUSIVE_" . $vendors_id . "', 'false', 'Do you want the dimensions to be exclusive the number of products in the cart?<p>Example: If this is true, and you have a dimensional table setup for 1 product and 3 products, only dimensions will be used if 1 product or 3 products are ordered.', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Additional Protection', 'MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_" . $vendors_id . "', 'false', 'Do you want to quote the rate with additional protection against potential loss or damage?', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Additional Protection Type', 'MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_TYPE_" . $vendors_id . "', 'NR', 'If you have additional protection enabled, select the type of additional protection you want to use.<p>AP - Shipment Value Protection<br>NR - No Additional Protection', '6', '0', 'tep_cfg_select_option(array(\'AP\', \'NR\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Additional Protection Value', 'MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_VALUE_" . $vendors_id . "', '', 'If you have additional protection enabled, by default the cart subtotal is used as the protection value.  Use this to add additional protection value on top of cart subtotal.<p>Example:<br>10 - adds $10 to cart subtotal<br>10% - adds 10% to cart subtotal', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Handling Fee', 'MODULE_SHIPPING_AIRBORNE_HANDLING_" . $vendors_id . "', '0', 'Handling fee for this shipping method.', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Tax Class', 'MODULE_SHIPPING_AIRBORNE_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_AIRBORNE_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort Order', 'MODULE_SHIPPING_AIRBORNE_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display.', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Module Debugging', 'MODULE_SHIPPING_AIRBORNE_DEBUG_" . $vendors_id . "', 'False', 'Do you want to debug the domestic DHL/Airborne shipping module (will save the XML request and response to a file in the directory specified below)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Debug Method', 'MODULE_SHIPPING_AIRBORNE_DEBUG_METHOD_" . $vendors_id . "', 'Print to screen', 'Method of debugging', '6', '0', 'tep_cfg_select_option(array(\'Print to screen\', \'Save to file\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Debug Directory', 'MODULE_SHIPPING_AIRBORNE_DEBUG_DIRECTORY_" . $vendors_id . "', '', 'Absolute path to the directory where to save XML requests and responses when in debug mode of \"Save to file\"<br /><em>Note:  Directory must be CHMOD to 777</em>', '6', '0', now(), '" . $vendors_id . "')");
    }

    function remove($vendors_id) {
      tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
    }

    function keys($vendors_id) {
      return array (
        'MODULE_SHIPPING_AIRBORNE_STATUS_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_SYSTEMID_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_PASS_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_SHIP_KEY_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_ACCT_NBR_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_SERVER_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_TYPES_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_EST_DELIVERY_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_PACKAGE_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_SHIPMENT_DAY_TYPE_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_DAYS_TO_SHIP_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_SHIPMENT_DAY_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_OVERRIDE_EXP_SAT_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_WEIGHT_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_TABLE_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_EXCLUSIVE_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_TYPE_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_ADDITIONAL_PROTECTION_VALUE_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_HANDLING_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_TAX_CLASS_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_ZONE_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_SORT_ORDER_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_DEBUG_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_DEBUG_METHOD_' . $vendors_id,
        'MODULE_SHIPPING_AIRBORNE_DEBUG_DIRECTORY_' . $vendors_id
      );
    }

    function _setService($service) {
      $this->service = $service;
    }

    function _setMethods($methods) {
      $this->allowed_methods = explode(", ", $methods);
    }

    function _setContainer($container) {
      $this->container = $container;
    }

    function _setShippingDay($days_to_ship, $day, $vendors_id) {
      if (constant('MODULE_SHIPPING_AIRBORNE_SHIPMENT_DAY_TYPE_' . $vendors_id) == 'Ship in x number of days') {
        $this->shipping_day = ((_makedate($days_to_ship, 'day', 'dddd') == 'Saturday') ? $days_to_ship +2 : ((_makedate($days_to_ship, 'day', 'dddd') == 'Sunday') ? $days_to_ship +1 : $days_to_ship));
      }
      elseif (constant('MODULE_SHIPPING_AIRBORNE_SHIPMENT_DAY_TYPE_' . $vendors_id) == 'Ship on certain day') {
        $i = 1;
        while (_makedate($i, 'day', 'dddd') != $day) {
          $i++;
        }

        $this->shipping_day = $i;
      }
    }

    function _setWeight($shipping_weight) {
      $shipping_weight = ($shipping_weight < .5 ? .5 : $shipping_weight);
      $shipping_pounds = round($shipping_weight, 0);
      $this->weight = $shipping_pounds;
    }

    function _setDimensions($exclusive, $vendors_id) {
      $dimensions = split("[:xX,]", constant('MODULE_SHIPPING_AIRBORNE_DIMENSIONAL_TABLE_' . $vendors_id));
      $size = sizeof($dimensions);
      for ($i = 0, $n = $size; $i < $n; $i += 4) {
        if ($exclusive == 'true') {
          if (($_SESSION['cart']->count_contents()) == $dimensions[$i]) {
            $this->dimensions = true;
            $this->length = round($dimensions[$i +1], 0);
            $this->width = round($dimensions[$i +2], 0);
            $this->height = round($dimensions[$i +3], 0);
          }
        } else {
          if (($_SESSION['cart']->count_contents()) >= $dimensions[$i]) {
            $this->dimensions = true;
            $this->length = round($dimensions[$i +1], 0);
            $this->width = round($dimensions[$i +2], 0);
            $this->height = round($dimensions[$i +3], 0);
          }
        }
      }
    }

    function _setDestination($city, $state, $postal, $country) {
      global $order;

      $postal = str_replace(' ', '', $postal);
      $state_query = tep_db_query("select zone_code from " . TABLE_ZONES . " where zone_name = '" . $state . "' and zone_country_id = '" . (int) $order->delivery['country']['id'] . "'");
      $state_info = tep_db_fetch_array($state_query);

      $this->destination_city = $city;
      $this->destination_state = $state_info['zone_code'];
      $this->destination_postal = substr($postal, 0, 5);
      $this->destination_country = $country;
    }

    function _setAdditionalProtection($additional_value) {
      global $order;

      $additional_protection = $order->info['subtotal'];
      if (substr_count($additional_value, '%') > 0) {
        $additional_protection += ((($additional_protection * 10) / 10) * ((str_replace('%', '', $additional_value)) / 100));
      } else {
        $additional_protection += $additional_value;
      }

      $this->additionalProtection = round($additional_protection, 0);
    }

    function _getQuote($vendors_id = '1') {
      global $order;

      // start the XML request
      $request = "<?xml version='1.0'?>" .
      "<eCommerce action='Request' version='1.1'>" .
      "<Requestor>" .
      "<ID>" . $this->system_id($vendors_id) . "</ID>" .
      "<Password>" . $this->airborne_pass($vendors_id) . "</Password>" .
      "</Requestor>";

      if (isset ($this->service)) {
        $this->types = array (
          $this->service => $this->types[$this->service]
        );
      }

      $allowed_types = array ();
      foreach ($this->types as $key => $value) {
        if (!in_array($key, $this->allowed_methods))
          continue;

        // Letter Express not allowed with ground
        if (($key == 'G') && ($this->container == 'L'))
          continue;

        // basic shipment information
        $allowed_types[$key] = $value;
        $request .= "<Shipment action='RateEstimate' version='1.0'>" .
        "<ShippingCredentials>" .
        "<ShippingKey>" . $this->ship_key($vendors_id) . "</ShippingKey>" .
        "<AccountNbr>" . $this->acct_nbr($vendors_id) . "</AccountNbr>" .
        "</ShippingCredentials>" .
        "<ShipmentDetail>" .
        "<ShipDate>" . _makedate($this->shipping_day, 'day', 'yyyy-mm-dd') . "</ShipDate>" .
        "<Service>" .
        "<Code>" . substr($key, 0, 1) . "</Code>" .
        "</Service>" .
        "<ShipmentType>" .
        "<Code>" . $this->container . "</Code>" .
        "</ShipmentType>";

        // special Express services
        if ($key == 'E SAT') {
          $request .= "<SpecialServices>" .
          "<SpecialService>" .
          "<Code>SAT</Code>" .
          "</SpecialService>" .
          "</SpecialServices>";
        }
        elseif ($key == 'E 10:30AM') {
          $request .= "<SpecialServices>" .
          "<SpecialService>" .
          "<Code>1030</Code>" .
          "</SpecialService>" .
          "</SpecialServices>";
        }

        // package weight & dimensions
        if ($this->container != 'L') {
          $request .= "<Weight>" . $this->weight . "</Weight>";
        }

        if (isset ($this->dimensions)) {
          $request .= "<Dimensions>" .
          "<Length>" . $this->length . "</Length>" .
          "<Width>" . $this->width . "</Width>" .
          "<Height>" . $this->height . "</Height>" .
          "</Dimensions>";
        }

        // package additional protection
        if (isset ($this->additionalProtection)) {
          $request .= "<AdditionalProtection>" .
          "<Code>" . $this->protection_type($vendors_id) . "</Code>" .
          "<Value>" . $this->additionalProtection . "</Value>" .
          "</AdditionalProtection>";
        }

        // billing & shipping information        
        $request .= "</ShipmentDetail>" .
        "<Billing>" .
        "<Party>" .
        "<Code>S</Code>" .
        "</Party>" .
        "</Billing>" .
        "<Receiver>" .
        "<Address>" .
        "<City>" . $this->destination_city . "</City>" .
        "<State>" . $this->destination_state . "</State>" .
        "<Country>" . $this->destination_country . "</Country>" .
        "<PostalCode>" . $this->destination_postal . "</PostalCode>" .
        "</Address>" .
        "</Receiver>";

        // shipment overrides
        if ((constant('MODULE_SHIPPING_AIRBORNE_OVERRIDE_EXP_SAT_' . $vendors_id) == 'true') && ($key == 'E SAT')) {
          $request .= "<ShipmentProcessingInstructions>" .
          "<Overrides>" .
          "<Override>" .
          "<Code>ES</Code>" .
          "</Override>" .
          "</Overrides>" .
          "</ShipmentProcessingInstructions>";
        }

        $request .= "</Shipment>";
      }

      $request .= "</eCommerce>";

      // select proper server
      switch (constant('MODULE_SHIPPING_AIRBORNE_SERVER_' . $vendors_id)) {
        case 'production' :
          $api = "ApiLanding.asp";
          break;
        case 'test' :
        default :
          $api = "ApiLandingTest.asp";
          break;
      }

      // begin cURL engine & execute the request
      if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ecommerce.airborne.com/$api");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$request");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //Added in 1.401 fix
        //curl_setopt($ch, CURLOPT_CAINFO, 'C:/apache/bin/curl-ca-bundle.crt');

        $airborne_response = curl_exec($ch);
        curl_close($ch);
      } else {
        // cURL method using exec() // curl -d -k if you have SSL issues
        exec("/usr/bin/curl -d \"$request\" https://eCommerce.airborne.com/$api", $response);
        $airborne_response = '';
        foreach ($response as $key => $value) {
          $airborne_response .= "$value";
        }
      }

      // Debugging
      if ($this->debug($vendors_id)) {
        $this->captureXML($request, $airborne_response);
      }

      $airborne = _parsexml($airborne_response);

      if ($airborne[eCommerce]['->'][Faults][0]['->'][Fault][0]['->'][Code][0]['->']) {
        $error_message = 'The following errors have occured:';
        for ($i = 0; $i < 5; $i++) {
          if ($airborne[eCommerce]['->'][Faults][0]['->'][Fault][$i]['->'][Code][0]['->'])
            $error_message .= '<br>' . ($i +1) . '.&nbsp;' . $airborne[eCommerce]['->'][Faults][0]['->'][Fault][$i]['->'][Description][0]['->'];
          if ($airborne[eCommerce]['->'][Faults][0]['->'][Fault][$i]['->'][Context][0]['->'])
            $error_message .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(' . htmlspecialchars($airborne[eCommerce]['->'][Faults][0]['->'][Fault][$i]['->'][Context][0]['->']) . ')</em>';
          if (!$airborne[eCommerce]['->'][Faults][0]['->'][Fault][$i +1]['->'][Code][0]['->'])
            break;
        }
        return array (
          'error' => $error_message
        );
      }
      elseif ($airborne[eCommerce]['->'][Shipment][0]['->'][Faults][0]['->'][Fault][0]['->'][Code][0]['->']) {
        $error_message = 'The following errors have occured:';
        for ($i = 0; $i < 5; $i++) {
          if ($airborne[eCommerce]['->'][Shipment][0]['->'][Faults][0]['->'][Fault][$i]['->'][Code][0]['->'])
            $error_message .= '<br>' . ($i +1) . '.&nbsp;' . $airborne[eCommerce]['->'][Shipment][0]['->'][Faults][0]['->'][Fault][$i]['->'][Desc][0]['->'];
          if ($airborne[eCommerce]['->'][Shipment][0]['->'][Faults][0]['->'][Fault][$i]['->'][Context][0]['->'])
            $error_message .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(' . htmlspecialchars($airborne[eCommerce]['->'][Shipment][0]['->'][Faults][0]['->'][Fault][$i]['->'][Context][0]['->']) . ')</em>';
          if (!$airborne[eCommerce]['->'][Shipment][0]['->'][Faults][0]['->'][Fault][$i +1]['->'][Code][0]['->'])
            break;
        }
        return array (
          'error' => $error_message
        );
      } else {
        $rates = array ();
        $i = 0;
        foreach ($allowed_types as $key => $value) {
          if ($airborne[eCommerce]['->'][Shipment][$i]['->'][EstimateDetail][0]['->'][Service][0]['->'][Code][0]['->']) {
            $service = $key;
            $postage = $airborne[eCommerce]['->'][Shipment][$i]['->'][EstimateDetail][0]['->'][RateEstimate][0]['->'][TotalChargeEstimate][0]['->'];
            $description = (constant('MODULE_SHIPPING_AIRBORNE_EST_DELIVERY_' . $vendors_id) == 'true') ? '&nbsp;<span class="smallText"><em>(' . $airborne[eCommerce]['->'][Shipment][$i]['->'][EstimateDetail][0]['->'][ServiceLevelCommitment][0]['->'][Desc][0]['->'] . ')</em></span>' : '';
            $rates[] = array (
              $service => $postage,
              'description' => $description
            );
          }
          $i++;
        }
      }

      return ((sizeof($rates) > 0) ? $rates : false);
    }

    function captureXML($request, $response, $vendors_id) {
      if (constant('MODULE_SHIPPING_AIRBORNE_DEBUG_METHOD_' . $vendors_id) == 'Print to screen') {
        echo 'Request:<br /><pre>' . htmlspecialchars($request) . '</pre><br /><br />';
        echo 'Response:<br /><pre>' . htmlspecialchars($response) . '</pre>';
      } else {
        $folder = ((substr(constant('MODULE_SHIPPING_AIRBORNE_DEBUG_DIRECTORY_' . $vendors_id), -1) != '/') ? constant('MODULE_SHIPPING_AIRBORNE_DEBUG_DIRECTORY_' . $vendors_id) . '/' : constant('MODULE_SHIPPING_AIRBORNE_DEBUG_DIRECTORY_' . $vendors_id));

        $filename = $folder . 'request.txt';
        if (!$fp = fopen($filename, "w"))
          die("Failed opening file $filename");
        if (!fwrite($fp, $request))
          die("Failed writing to file $filename");
        fclose($fp);

        $filename = $folder . 'response.txt';
        if (!$fp = fopen($filename, "w"))
          die("Failed opening file $filename");
        if (!fwrite($fp, $response))
          die("Failed writing to file $filename");
        fclose($fp);
      }

      return true;
    }

  }

  /*
    Function to parse the returned XML data into an array.
    Borrowed from Hans Anderson's xmlize() function.
    http://www.hansanderson.com/php/xml/
  */
  function _parsexml($data, $WHITE = 1) {
    $data = trim($data);
    $vals = $index = $array = array ();
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $WHITE);
    xml_parse_into_struct($parser, $data, $vals, $index);
    xml_parser_free($parser);

    $i = 0;
    $tagname = $vals[$i]['tag'];
    $array[$tagname]['@'] = (isset ($vals[$i]['attributes'])) ? $vals[$i]['attributes'] : array ();
    $array[$tagname]["->"] = xml_depth($vals, $i);
    return $array;
  }

  function xml_depth($vals, & $i) {
    $children = array ();
    if (isset ($vals[$i]['value']))
      array_push($children, $vals[$i]['value']);

    while (++ $i < count($vals)) {
      switch ($vals[$i]['type']) {
        case 'open' :
          $tagname = (isset ($vals[$i]['tag'])) ? $vals[$i]['tag'] : '';
          $size = (isset ($children[$tagname])) ? sizeof($children[$tagname]) : 0;
          if (isset ($vals[$i]['attributes']))
            $children[$tagname][$size]['@'] = $vals[$i]["attributes"];
          $children[$tagname][$size]['->'] = xml_depth($vals, $i);
          break;
        case 'cdata' :
          array_push($children, $vals[$i]['value']);
          break;
        case 'complete' :
          $tagname = $vals[$i]['tag'];
          $size = (isset ($children[$tagname])) ? sizeof($children[$tagname]) : 0;
          $children[$tagname][$size]["->"] = (isset ($vals[$i]['value'])) ? $vals[$i]['value'] : '';
          if (isset ($vals[$i]['attributes']))
            $children[$tagname][$size]['@'] = $vals[$i]['attributes'];
          break;
        case 'close' :
          return $children;
          break;
      }
    }

    return $children;
  }

  /* 
    Function to generate arbitrary, formatted numeric or string date.
    Copyright (C) 2003  Erich Spencer
  */
  function _makedate($unit = '', $time = '', $mask = '') {
    $validunit = '/^[-+]?\b[0-9]+\b$/';
    $validtime = '/^\b(day|week|month|year)\b$/i';
    $validmask = '/^(short|long|([dmy[:space:][:punct:]]+))$/i';

    if (!preg_match($validunit, $unit))
      $unit = -1;
    if (!preg_match($validtime, $time))
      $time = 'day';
    if (!preg_match($validmask, $mask))
      $mask = 'yyyymmdd';

    switch ($mask) {
      case 'short' : // 7/4/2003 
        $mask = "n/j/Y";
        break;
      case 'long' : // Friday, July 4, 2003 
        $mask = "l, F j, Y";
        break;
      default :
        $chars = (preg_match('/([[:space:]]|[[:punct:]])/', $mask)) ? preg_split('/([[:space:]]|[[:punct:]])/', $mask, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) : preg_split('/(m*|d*|y*)/i', $mask, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        foreach ($chars as $key => $char) {
          switch (TRUE) {
            case eregi("m{3,}", $chars[$key]) : // 'mmmm' = month string 
              $chars[$key] = "F";
              break;
            case eregi("m{2}", $chars[$key]) : // 'mm'   = month as 01-12 
              $chars[$key] = "m";
              break;
            case eregi("m{1}", $chars[$key]) : // 'm'    = month as 1-12 
              $chars[$key] = "n";
              break;
            case eregi("d{3,}", $chars[$key]) : // 'dddd' = day string 
              $chars[$key] = "l";
              break;
            case eregi("d{2}", $chars[$key]) : // 'dd'   = day as 01-31 
              $chars[$key] = "d";
              break;
            case eregi("d{1}", $chars[$key]) : // 'd'    = day as 1-31 
              $chars[$key] = "j";
              break;
            case eregi("y{3,}", $chars[$key]) : // 'yyyy' = 4 digit year 
              $chars[$key] = "Y";
              break;
            case eregi("y{1,2}", $chars[$key]) : // 'yy'   = 2 digit year 
              $chars[$key] = "y";
              break;
          }
        }

        $mask = implode('', $chars);
        break;
    }

    $when = date($mask, strtotime("$unit $time"));
    return $when;
  }

  /*
    Function to have options for shipping methods.
    Borrowed from UPS Choice v1.7
    Credit goes to Fritz Clapp
  */
  function _selectOptions3254($select_array, $key_value, $key = '') {
    foreach ($select_array as $select_option) {
      $name = (($key) ? 'configuration[' . $key . '][]' : 'configuration_value');
      $string .= '<br><input type="checkbox" name="' . $name . '" value="' . $select_option . '"';
      $key_values = explode(", ", $key_value);
      if (in_array($select_option, $key_values))
        $string .= ' checked="checked"';
      $string .= '> ' . $select_option;
    }
    return $string;
  }

?>
