<?php

/*
  Released under the GNU General Public License
*/

  class tntzipzonesroad {
    var $code, $title, $description, $icon, $sort_order, $enabled, $num_zones, $surcharge_factor, $vendors_id;
    // class constructor:w
    function tntzipzonesroad() {
      global $order, $vendors_id;
      $this->code = 'tntzipzonesroad';
      $this->title = MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_DESCRIPTION;
      $this->icon = DIR_WS_ICONS . ''; // If no graphic leave empty. Can also be called from language file if image wanted shown in clients order history and on invoices etc. See language file for comments.
      //   $this->enabled = MODULE_SHIPPING_TNTZIPZONESROAD_STATUS;
      //More options can be added later for Express, Overnight etc when someone codes it.
      $this->types = array (
        'TNT Off-Peak' => 'std'
      );
      // Change this surcharge factor to increase your profit margin on freight Set as 1 for standard.
      $this->surcharge_factor = 1.0;
      $this->delivery_country_id = $order->delivery['country']['id'];
      $this->delivery_zone_id = $order->delivery['zone_id'];
    }
    
    // Customize this setting for the number of zones needed (no change required by default)
    //  $this->num_zones = 1;
    function num_zones($vendors_id = '1') {
      $this->num_zones = constant('1' . $vendors_id);
      return $this->num_zones;
    }
    
    //MVS Start
    function sort_order($vendors_id = '1') {
      $sort_order = 'MODULE_SHIPPING_TNTZIPZONESROAD_SORT_ORDER_' . $vendors_id;
      if (defined($sort_order)) {
        $this->sort_order = constant($sort_order);
      } else {
        $this->sort_order = '-';
      }
      return $this->sort_order;
    }
    
    function tax_class($vendors_id = '1') {
      $this->tax_class = constant('MODULE_SHIPPING_TNTZIPZONESROAD_TAX_CLASS_' . $vendors_id);
      return $this->tax_class;
    }
    
    function enabled($vendors_id = '1') {
      $this->enabled = false;
      $status = @ constant('MODULE_SHIPPING_TNTZIPZONESROAD_STATUS_' . $vendors_id);
      if (isset ($status) && $status != '') {
        $this->enabled = (($status == 'True') ? true : false);
      }
      if (($this->enabled == true) && ((int) constant('MODULE_SHIPPING_TNTZIPZONESROAD_ZONE_' . $vendors_id) > 0)) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) constant('MODULE_SHIPPING_TNTZIPZONESROAD_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $this->delivery_country_id . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          }
          elseif ($check['zone_id'] == $this->delivery_zone_id) {
            $check_flag = true;
            break;
          }
        }
        if ($check_flag == false) {
          $this->enabled = false;
        } //if
      } //if
      return $this->enabled;
    }
    //MVS End
    
    function zones($vendors_id = '1') {
      if (($this->enabled == true) && ((int) constant('MODULE_SHIPPING_TNTZIPZONESROAD_ZONE_' . $vendors_id) > 0)) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) constant('MODULE_SHIPPING_TNTZIPZONESROAD_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $this->delivery_zone_id . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          }
          elseif ($check['zone_id'] == $this->delivery_zone_id) {
            $check_flag = true;
            break;
          } //if
        } //while
        if ($check_flag == false) {
          $this->enabled = false;
        } //if
      } //if
      return $this->enabled;
    } //function
    
    // class methods
    function quote($method = '', $module = '', $vendors_id = '1') {
      global $HTTP_POST_VARS, $order, $shipping_weight, $cart, $shipping_num_boxes;

      //MVS Start
      $vendors_data_query = tep_db_query("select handling_charge,
                                                 handling_per_box,
                                                 vendor_country,
                                                 vendors_zipcode
                                          from " . TABLE_VENDORS . "
                                          where vendors_id = '" . (int) $vendors_id . "'");
      $vendors_data = tep_db_fetch_array($vendors_data_query);
      $country_name = tep_get_countries($vendors_data['vendor_country'], true);
      // begin mod for extra handling fee
      $vendors_handling_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key = 'MODULE_SHIPPING_TNTZIPZONESROAD_HANDLING_" . $vendors_id . "'");
      $vendors_handling_data = tep_db_fetch_array($vendors_handling_query);
      $handling_charge = $vendors_data['handling_charge'] + $vendors_handling_data['configuration_value'];
      // end mod for extra handling fee
      $handling_per_box = $vendors_data['handling_per_box'];
      if ($handling_charge > $handling_per_box * $shipping_num_boxes) {
        $handling = $handling_charge;
      } else {
        $handling = $handling_per_box * $shipping_num_boxes;
      }
      //MVS End

      // First get the dest zip and check the db for our dest zone
      $zip = $order->delivery['postcode'];
      if ($zip == '') {
        // Something is wrong, we didn't find any zone
        //     if ($this->enabled($vendors_id) < 1) {
        $this->quotes['error'] = MODULE_SHIPPING_TNTZIPZONESROAD_NO_ZIPCODE_FOUND;
        return $this->quotes;
      }
      $sql = "SELECT *
                      FROM tnt_zones
                      WHERE
                              $zip >= t_postcode and
                              $zip <= t_postcode";
      $qResult = tep_db_query($sql); // run the query
      $rec = tep_db_fetch_array($qResult); // get the first row of the result
      $zone_id = $rec['t_zone'];
      if ($zone_id == '') {
        // Something is wrong, we didn't find any zone
        //       if ($this->enabled($vendors_id) < 1) {
        $this->quotes['error'] = MODULE_SHIPPING_TNTZIPZONESROAD_NO_ZONE_FOUND;
        return $this->quotes;
      }
      $sql = "SELECT t_rate
                FROM tnt_rates_road WHERE t_zone_id = '$zone_id'";
      $qResult = tep_db_query($sql);
      while ($rec = tep_db_fetch_array($qResult)) {
        $retArr[] = $rec;
      }
      foreach ($retArr as $aquote) {
        $cost = $aquote['t_rate'];
      }
      // Consignment charge insertion start
      $sql = "SELECT t_ccharge
                FROM tnt_rates_road WHERE t_zone_id = '$zone_id'";
      $qResult = tep_db_query($sql);
      while ($rec = tep_db_fetch_array($qResult)) {
        $retArr[] = $rec;
      }
      foreach ($retArr as $aquote) {
        $ccharge = $aquote['t_ccharge'];
        // Consignment charge insertion end
      }
      if ($shipping_weight <= '10') {
        $this->quotes = array (
          'id' => $this->code,
          'module' => MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_TITLE,
          'methods' => array (
            array (
              'id' => $this_code,
              'title' => MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_DESCRIPTION,
                //1.10 added to calculate 10% GST on freight in Australia // changed to 1.00 to work with MVS tax class.
  'cost' => ((($ccharge
            ) * 1.00
          ) * $this->surcharge_factor
        ) + $handling)));
      } else {
        $this->quotes = array (
          'id' => $this->code,
          'module' => MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_TITLE,
          'methods' => array (
            array (
              'id' => $this_code,
              'title' => MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_DESCRIPTION,
                //1.10 added to calculate 10% GST on freight in Australia // changed to 1.00 to work with MVS tax class.
  'cost' => ((($cost * ($shipping_weight -10
            ) + $ccharge
          ) * 1.00
        ) * $this->surcharge_factor) + $handling)));
      }
      if ($this->tax_class($vendors_id) > 0) {
        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class($vendors_id), $order->delivery['country']['id'], $order->delivery['zone_id']);
      }
      if (tep_not_null($this->icon))
        $this->quotes['icon'] = tep_image($this->icon, $this->title);
      //     if ($this->enabled($vendors_id) < 1) {
      if ($error == true)
        $this->quotes['error'] = MODULE_SHIPPING_TNTZIPZONESROAD_INVALID_ZONE;
      return $this->quotes;
    }

    //function check() {
    function check($vendors_id = '1') {
      if (!isset ($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key = 'MODULE_SHIPPING_TNTZIPZONESROAD_STATUS_" . $vendors_id . "'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }
    
    function install($vendors_id = '1') {
      //    $vID = $vendors_id;
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) VALUES ('Enable TNT Postcode Zones Method', 'MODULE_SHIPPING_TNTZIPZONESROAD_STATUS_" . $vendors_id . "', 'True', 'Do you want to offer TNT postcode/zone rate shipping?', '6', '0','tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Handling Fee', 'MODULE_SHIPPING_TNTZIPZONESROAD_HANDLING_" . $vendors_id . "', '0', 'Handling Fee for this shipping method', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Tax Class', 'MODULE_SHIPPING_TNTZIPZONESROAD_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_TNTZIPZONESROAD_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort Order', 'MODULE_SHIPPING_TNTZIPZONESROAD_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display.', '6', '0', now(), '" . $vendors_id . "')");
    }
    
    function remove($vendors_id) {
      //     $keys = '';
      //     $keys_array = $this->keys();
      //     for ($i=0; $i<sizeof($keys_array); $i++) {
      //     $keys .= "'" . $keys_array[$i] . "',";
      //   }
      //    $keys = substr($keys, 0, -1);
      tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
    }
    function keys($vendors_id) {
      $keys = array (
        'MODULE_SHIPPING_TNTZIPZONESROAD_STATUS_' . $vendors_id,
        'MODULE_SHIPPING_TNTZIPZONESROAD_HANDLING_' . $vendors_id,
        'MODULE_SHIPPING_TNTZIPZONESROAD_TAX_CLASS_' . $vendors_id,
        'MODULE_SHIPPING_TNTZIPZONESROAD_ZONE_' . $vendors_id,
        'MODULE_SHIPPING_TNTZIPZONESROAD_SORT_ORDER_' . $vendors_id,
        
      );
      return $keys;
    }
  }

?>