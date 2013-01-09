<?php

/*
  $Id: colissimo.php,v 1.0 Feb 1, 2008 8:48:41 PM kymation Exp $
  $Loc: catalog/includes/modules/vendors_shipping/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2008 osCommerce

  Released under the GNU General Public License
*/


  class colissimo {
    var $code, $title, $description, $enabled, $icon, $vendors_id; //multi vendor

    // class constructor
    function colissimo() {
      //MVS
      //      $this->vendors_id = ($products['vendors_id'] <= 0) ? 1 : $products['vendors_id'];
      $this->code = 'colissimo';
      $this->description = MODULE_SHIPPING_COLISSIMO_TEXT_DESCRIPTION;
      $this->title = MODULE_SHIPPING_COLISSIMO_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_COLISSIMO_TEXT_DESCRIPTION;
      $this->icon = DIR_WS_ICONS . 'colissimo.gif'; // ou shipping_laposte.gif au choix
    }
    //MVS Start
    function zones($vendors_id = '1') {
      if (($this->enabled == true) && ((int) constant('MODULE_SHIPPING_COLISSIMO_ZONE_' . $vendors_id) > 0)) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) constant('MODULE_SHIPPING_COLISSIMO_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
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
      return $this->enabled;
    } //function

    function sort_order($vendors_id = '1') {
      $sort_order = @ constant('MODULE_SHIPPING_COLISSIMO_SORT_ORDER_' . $vendors_id);
      if (isset ($sort_order)) {
        $this->sort_order = $sort_order;
      } else {
        $this->sort_order = '-';
      }
      return $this->sort_order;
    }

    function tax_class($vendors_id = '1') {
      $this->tax_class = constant('MODULE_SHIPPING_COLISSIMO_TAX_CLASS_' . $vendors_id);
      return $this->tax_class;
    }

    function enabled($vendors_id = '1') {
      $this->enabled = false;
      $status = @ constant('MODULE_SHIPPING_COLISSIMO_STATUS_' . $vendors_id);
      if (isset ($status) && $status != '') {
        $this->enabled = (($status == 'True') ? true : false);
      }
      return $this->enabled;
    }

    //Set the number of zones used for this vendor
    function num_zones($vendors_id = '1') {

      $vendors_data_query = tep_db_query("select zones
                                                from " . TABLE_VENDORS . "
                                                where vendors_id = '" . (int) $vendors_id . "'");
      $vendors_data = tep_db_fetch_array($vendors_data_query);
      $this->num_zones = $vendors_data['colissimo'];
      return $this->num_zones;
    }
    //MVS End

    //Get a quote
    function quote($method = '', $module = '', $vendors_id = '1') {

      global $order, $cart, $shipping_weight, $shipping_num_boxes;
      //MVS Start
      //return an error if the module is not enabled for this vendor
      if ($this->enabled($vendors_id) < 1) {
        $this->quotes['error'] = MODULE_SHIPPING_ZONES_INVALID_ZONE;
        return $this->quotes;
      }
      //MVS End

      $dest_country = $order->delivery['country']['iso_code_2'];
      $postcode = $order->delivery['postcode'];
      $store_postcode = constant('MODULE_SHIPPING_COLISSIMO_STORE_POSTCODE');
      $error = false;

      if (($dest_country != 'FR') && ($dest_country != 'FX')) {
        $this->quotes['error'] = MODULE_SHIPPING_COLISSIMO_INVALID_ZONE;
      } else {
        // Colissimo MVS Start
        $vendors_data_query = tep_db_query("select vendors_zipcode
                                                      from " . TABLE_VENDORS . "
                                                      where vendors_id = '" . (int) $vendors_id . "'");
        $vendors_data = tep_db_fetch_array($vendors_data_query);

        //Choisir entre Colissimo Intra ou Extra
        $colissimo_cost = $vendors_data['vendors_zipcode'];
        if (substr($postcode, 0, 2) == substr($store_postcode, 0, 2)) {
          $colissimo_cost = constant('MODULE_SHIPPING_COLISSIMO_INTRA_' . $vendors_id);
        } else {
          $colissimo_cost = constant('MODULE_SHIPPING_COLISSIMO_EXTRA_' . $vendors_id);
        }

        // Colissimo MVS End
        $colissimo_table = split("[:,]", $colissimo_cost);
        $size = sizeof($colissimo_table);
        for ($i = 0; $i < $size; $i += 2) {
          if ($shipping_weight <= $colissimo_table[$i]) {
            $shipping = $colissimo_table[$i +1];
            $shipping_method = MODULE_SHIPPING_COLISSIMO_TEXT_WAY . ' : ' . $shipping_weight . ' ' . MODULE_SHIPPING_COLISSIMO_TEXT_UNITS;

            //MVS Start
            $vendors_data_query = tep_db_query("select handling_charge,
                                                              handling_per_box
                                                       from " . TABLE_VENDORS . "
                                                       where vendors_id = '" . (int) $vendors_id . "'");
            $vendors_data = tep_db_fetch_array($vendors_data_query);

            $handling_charge = $vendors_data['handling_charge'];
            $handling_per_box = $vendors_data['handling_per_box'];
            if ($handling_charge > $handling_per_box * $shipping_num_boxes) {
              $handling = $handling_charge;
            } else {
              $handling = $handling_per_box * $shipping_num_boxes;
            }

            //Set handling to the module's handling charge if it is larger
            $handling += constant('MODULE_SHIPPING_COLISSIMO_HANDLING_' . $vendors_id);

            $shipping_cost = $shipping + $handling;
          }
          //MVS End

        }
      }
      $this->quotes = array (
        'id' => $this->code,
        'module' => MODULE_SHIPPING_COLISSIMO_TEXT_TITLE,
        'methods' => array (
          array (
            'id' => $this->code,
            'title' => $shipping_method,
            'cost' => $shipping_cost
          )
        )
      );

      if ($shipping = 0) {
        $shipping_cost = 350000;
        return $this->quotes;
      }

      // Pas possible de livrer
      if ($shipping_weight > 30) {
        $this->quotes['error'] = MODULE_SHIPPING_COLISSIMO_TOO_HEAVY;
        return $this->quotes;
      }

      $this->tax_class = constant('MODULE_SHIPPING_COLISSIMO_TAX_CLASS_' . $vendors_id);
      if ($this->tax_class($vendors_id) > 0) {
        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class($vendors_id), $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if ($error == true)
        $this->quotes['error'] = MODULE_SHIPPING_COLISSIMO_INVALID_ZONE;

      return $this->quotes;
    }

    function check($vendors_id = '1') {
      if (!isset ($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key = 'MODULE_SHIPPING_COLISSIMO_STATUS_" . $vendors_id . "'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install($vendors_id = '1') {
      $vID = $vendors_id;
      //multi vendor add 'vendors_id' to field names and '" . $vID . "', to values
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Valider Colissimo', 'MODULE_SHIPPING_COLISSIMO_STATUS_" . $vendors_id . "', 'True', 'Activer / D&eacute;sactiver Colissimo sans perdre les valeurs chang&eacute;es', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Colissimo intrad&eacute;partement', 'MODULE_SHIPPING_COLISSIMO_INTRA_" . $vendors_id . "', '0.250:5.15, 0.5:5.90, 0.75:6.55, 1:7.05, 1.5:7.70, 2:7.70, 3:8.30, 4:8.90, 5:9.50, 6:10.10, 7:10.70, 8:11.30, 9:11.90, 10:12.50, 15:14.30, 30:19.10', 'Port bas&eacute; sur le poids total des produits, tarif intrad&eacute;partemental. Exemple: 1:3.50,2:5.50,etc.. Jusqu\'&agrave; 1 Kg factur&eacute; 3.50, jusqu\'à 2 Kg, 5.50, etc', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Colissimo extrad&eacute;partement', 'MODULE_SHIPPING_COLISSIMO_EXTRA_" . $vendors_id . "', '0.250:5.35, 0.5:6.10, 0.75:6.75, 1:7.25, 1.5:7.90, 2:7.90, 3:8.50, 4:9.10, 5:9.70, 6:10.30, 7:10.90, 8:11.50, 9:12.10, 10:12.70, 15:14.50, 30:19.30', 'Port bas&eacute; sur le poids total des produits, tarif extrad&eacute;partemental. Exemple: 1:4.50, 2:6.50, etc. Jusqu\'à 1 Kg facturé 4.50, jusqu\'à 2 Kg , 6.50, etc', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_COLISSIMO_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable Store Pickup for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Frais d\'emballage', 'MODULE_SHIPPING_COLISSIMO_HANDLING_" . $vendors_id . "', '0', 'D&eacute;finir le cout fixe d\'emballage', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Code TVA', 'MODULE_SHIPPING_COLISSIMO_TAX_CLASS_" . $vendors_id . "', '0', 'Choisir le code TVA en fonction du taux', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Code postal', 'MODULE_SHIPPING_COLISSIMO_STORE_POSTCODE_" . $vendors_id . "', '78100', 'Code postal de la boutique', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Ordre de tri', 'MODULE_SHIPPING_COLISSIMO_SORT_ORDER_" . $vendors_id . "', '0', 'D&eacute;finir l\'ordre d\'affichage', '6', '0', now(), '" . $vendors_id . "')");
      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Descriptif au choix', 'MODULE_SHIPPING_COLISSIMO_SHIP_TEXT_" . $vendors_id . "', 'Vous recevrez par mail votre num&eacute;ro de suivi de colis', 'Le client pourra voir ce descriptif', '6', '0', now(), '" . $vendors_id . "')");
    }

    function remove($vendors_id) {
      tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
    }

    function keys($vendors_id) {
      return array (
        'MODULE_SHIPPING_COLISSIMO_STATUS_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_INTRA_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_EXTRA_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_ZONE_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_HANDLING_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_TAX_CLASS_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_STORE_POSTCODE_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_SORT_ORDER_' . $vendors_id,
        'MODULE_SHIPPING_COLISSIMO_SHIP_TEXT_' . $vendors_id
      );

      //MVS End

    }
  }

?>
