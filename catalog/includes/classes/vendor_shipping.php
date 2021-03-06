<?php
/*
  $Id: vendor_shipping.php,v 1.5 2009/02/28 kymation Exp $
  $Modified_from: shipping.php,v 1.23 2003/06/29 11:22:05 hpdl Exp $
  $Loc: /catalog/includes/classes/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class shipping {
    var $modules;

////
// Find all of the modules and instantiate the module classes
    function shipping($module = '') {
      global $language, $PHP_SELF;

      $installed_modules_array = array();
			//Get the vendors_id for each vendor in the database
      $vendors_data_query = tep_db_query("select vendors_id from " . TABLE_VENDORS);
      while ($vendors_data = tep_db_fetch_array($vendors_data_query)) {;
        $vendors_id = $vendors_data['vendors_id'];
        $installed_modules = @constant ('MODULE_VENDOR_SHIPPING_INSTALLED_' . $vendors_id);

        if (isset ($installed_modules) && tep_not_null ($installed_modules)) {
          $modules_array = explode(';', $installed_modules);
          $this->modules[$vendors_id] = $modules_array;

          foreach ($modules_array as $module_name) {
            //if the module is not already in the array, add it in
            if (!in_array ($module_name, $installed_modules_array)) {  
              $installed_modules_array[] = $module_name;
            }//if !in_array
          }//foreach
        }//if isset
      }//while

					
      $include_modules = array();
      if ( (tep_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $modules_array)) ) {
        $include_modules[] = array('class' => substr($module['id'], 0, strpos($module['id'], '_')), 
                                   'file' => substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)));
      } else {
        reset($modules_array);
        foreach ($installed_modules_array as $value) {
          $class = substr($value, 0, strrpos($value, '.'));
          $include_modules[] = array('class' => $class, 
                                     'file' => $value);
        }//foreach
      }//if tep_not_null

      for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {
        include(DIR_WS_LANGUAGES . $language . '/modules/vendors_shipping/' . $include_modules[$i]['file']);
        include(DIR_WS_MODULES . 'vendors_shipping/' . $include_modules[$i]['file']);

        $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
      }//for
    }//function

////
// Get a quote for one or many shipping methods, for a specific vendor
    function quote($method = '', $module = '', $vendors_id='1') {
      global $shipping_quoted, $order, $cart, $shipping_num_boxes, $shipping_weight, $shiptotal;

      $quotes_array = array();
      if (is_array($this->modules[$vendors_id])) {
        $shipping_quoted = '';
        $shipping_num_boxes = 1;
        
        $shipping_weight = $cart->vendor_shipping[$vendors_id]['weight'];
//mod indvship start
		$shiptotal = $cart->vendor_shipping[$vendors_id]['ship_cost'];
		$shipping_cost = $cart->vendor_shipping[$vendors_id]['cost'];
//mod indvship end

        $total_count = $cart->vendor_shipping[$vendors_id]['qty'];

        $vendors_data_query = tep_db_query("select percent_tare_weight, 
                                                   tare_weight, 
                                                   max_box_weight 
                                            from " . TABLE_VENDORS . " 
                                            where vendors_id = '" . (int)$vendors_id . "'"
                                          );
        $vendors_data = tep_db_fetch_array($vendors_data_query);  //Only the row of the table that is for this vendor
        if ($vendors_data['max_box_weight'] == 0) $vendors_data['max_box_weight'] = 1000000;
        if ($vendors_data['tare_weight'] >= $shipping_weight*$vendors_data['percent_tare_weight']/100) {
          $shipping_weight = $shipping_weight + $vendors_data['tare_weight'];
        } else {
          $shipping_weight = $shipping_weight + ($shipping_weight*$vendors_data['percent_tare_weight']/100);
        }

        if ($shipping_weight > $vendors_data['max_box_weight']) { // Split into many boxes
          $shipping_num_boxes = ceil($shipping_weight/$vendors_data['max_box_weight']);
          $shipping_weight = $shipping_weight/$shipping_num_boxes;
        }

        $include_quotes = array();
        reset($this->modules[$vendors_id]);
        foreach ($this->modules[$vendors_id] as $value) {
          $class = substr($value, 0, strrpos($value, '.'));  // $class is the filename without the .php
          if (tep_not_null($module)) {
            if ( ($module == $class) && ($GLOBALS[$class]->enabled($vendors_id)) ) {
              $include_quotes[] = $class;
            }
          } elseif ($GLOBALS[$class]->enabled($vendors_id)) {  //Module is enabled for this vendor
            $include_quotes[] = $class;
          }
        }

        reset($include_quotes);
        $size = sizeof($include_quotes);
        for ($i=0; $i<$size; $i++) {
          $quotes = $GLOBALS[$include_quotes[$i]]->quote($method, '', $vendors_id);
          if (is_array($quotes)) $quotes_array[] = $quotes;
        }
      }

      return $quotes_array;
    }

////
//Find the cheapest shipping method for a specific vendor
    function cheapest($vendors_id='1') {
      if (is_array($this->modules[$vendors_id])) {
        $rates = array();

        reset($this->modules[$vendors_id]);
        foreach ($this->modules[$vendors_id] as $value) {
          $class = substr($value, 0, strrpos($value, '.'));

          if ($GLOBALS[$class]->enabled($vendors_id)) {
            $quotes = $GLOBALS[$class]->quote('', '', $vendors_id);

            for ($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++) {
              if (isset($quotes['methods'][$i]['cost']) && tep_not_null($quotes['methods'][$i]['cost'])) {
                $rates[] = array('id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
                                 'title' => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',
                                 'cost' => $quotes['methods'][$i]['cost']);
              }
            }
          }
        }

        $cheapest = false;
        for ($i=0, $n=sizeof($rates); $i<$n; $i++) {
          if (is_array($cheapest)) {
            if ($rates[$i]['cost'] < $cheapest['cost']) {
              $cheapest = $rates[$i];
            }
          } else {
            $cheapest = $rates[$i];
          }
        }

        return $cheapest;
      }
    }
  }
?>