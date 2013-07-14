<?php

class fedexwebservices {
	
	var $code, $title, $description, $icon, $sort_order, $enabled, $tax_class, $fedex_key, $fedex_pwd, $fedex_act_num, $fedex_meter_num, $country;

	//Class Constructor

  function fedexwebservices() {
    global $order, $customer_id;

	@define('MODULE_SHIPPING_FEDEX_WEB_SERVICES_INSURE', 0); 
	
	$this->code             = "fedexwebservices";	
	$this->title            = MODULE_SHIPPING_FEDEX_WEB_SERVICES_TEXT_TITLE;
	$this->description      = MODULE_SHIPPING_FEDEX_WEB_SERVICES_TEXT_DESCRIPTION;
	$this->icon 			= DIR_WS_ICONS . 'shipping_fedex.gif'; 
	$this->delivery_country_id = $order->delivery['country']['id'];
	$this->delivery_zone_id = $order->delivery['zone_id'];
		
    if (defined("SHIPPING_ORIGIN_COUNTRY")) {
      if ((int)SHIPPING_ORIGIN_COUNTRY > 0) {
        $countries_array = $this->get_countries(SHIPPING_ORIGIN_COUNTRY, true);
        $this->country = $countries_array['countries_iso_code_2'];
      } else {
        $this->country = SHIPPING_ORIGIN_COUNTRY;
      }
    } else {
      $this->country = STORE_ORIGIN_COUNTRY;
    }
  }

	  //Class Methods
		
	function enabled($vendors_id='1') {
      $this->enabled = false;
      $status = @constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS_' . $vendors_id);
      if (isset ($status) && $status != '') {
        $this->enabled = (($status == 'true') ? true : false);
      }
      if ( ($this->enabled == true) && ((int)constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE_' . $vendors_id) > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int)constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $this->delivery_country_id . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $this->delivery_zone_id) {
            $check_flag = true;
            break;
    }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }//if
      }//if

      return $this->enabled;
    }
		
    function tax_class($vendors_id='1') {
      $this->tax_class = constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_TAX_CLASS_' . $vendors_id);
      return $this->tax_class;
    }
		
    function zones($vendors_id='1') {
      if ( ($this->enabled == true) && ((int)constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE_' . $vendors_id) > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int)constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $this->delivery_country_id . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $this->delivery_zone_id) {
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

	function sort_order($vendors_id='1') {
		$sort_order = @constant ('MODULE_SHIPPING_FEDEX_WEB_SERVICES_SORT_ORDER_' . $vendors_id);
		if (tep_not_null($sort_order)) {
			$this->sort_order = $sort_order;
		} else {
			$this->sort_order = '-';
		}
		return $this->sort_order;
	}

  	function quote($method = '', $module = '', $vendors_id = '1') {
			
		$this->sort_order       = @constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_SORT_ORDER_' . $vendors_id);
		$this->handling_fee     = (float)@constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_HANDLING_FEE_' . $vendors_id);
		$this->tax_class        = @constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_TAX_CLASS_' . $vendors_id);
		$this->fedex_key        = @constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_KEY_' . $vendors_id);
		$this->fedex_pwd        = @constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_PWD_' . $vendors_id);
		$this->fedex_act_num    = @constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_ACT_NUM_' . $vendors_id);
		$this->fedex_meter_num  = @constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_METER_NUM_' . $vendors_id);
		
		/* FedEx integration starts */
		global $shipping_weight, $shipping_num_boxes, $order;
		require_once(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'functions/fedex-common.php5');
		 
		$path_to_wsdl = DIR_FS_CATALOG . DIR_WS_INCLUDES . "wsdl/RateService_v9.wsdl";
	
	
		ini_set("soap.wsdl_cache_enabled", "0");
	
		$client = new SoapClient($path_to_wsdl, array('trace' => 1));
	
		$this->types = array();

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_PRIORITY_' . $vendors_id) == 'true') {
      $this->types[] = 'INTERNATIONAL_PRIORITY';
      $this->types[] = 'EUROPE_FIRST_INTERNATIONAL_PRIORITY';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_ECONOMY_' . $vendors_id) == 'true') {
      $this->types[] = 'INTERNATIONAL_ECONOMY';
    }  

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_STANDARD_OVERNIGHT_' . $vendors_id) == 'true') {
      $this->types[] = 'STANDARD_OVERNIGHT';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_FIRST_OVERNIGHT_' . $vendors_id) == 'true') {
      $this->types[] = 'FIRST_OVERNIGHT';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_PRIORITY_OVERNIGHT_' . $vendors_id) == 'true') {
      $this->types[] = 'PRIORITY_OVERNIGHT';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_2DAY_' . $vendors_id) == 'true') {
      $this->types[] = 'FEDEX_2_DAY';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_GROUND_' . $vendors_id) == 'true') {
      $this->types[] = 'FEDEX_GROUND';
      $this->types[] = 'GROUND_HOME_DELIVERY';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_GROUND_' . $vendors_id) == 'true') {
      $this->types[] = 'INTERNATIONAL_GROUND';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_SAVER_' . $vendors_id) == 'true') {
      $this->types[] = 'FEDEX_EXPRESS_SAVER';
    }

    if (constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREIGHT_' . $vendors_id) == 'true') {
      $this->types[] = 'FEDEX_FREIGHT';
      $this->types[] = 'FEDEX_NATIONAL_FREIGHT';
      $this->types[] = 'FEDEX_1_DAY_FREIGHT';
      $this->types[] = 'FEDEX_2_DAY_FREIGHT';
      $this->types[] = 'FEDEX_3_DAY_FREIGHT';
      $this->types[] = 'INTERNATIONAL_ECONOMY_FREIGHT';
      $this->types[] = 'INTERNATIONAL_PRIORITY_FREIGHT';
    }                      


    $this->types[] = 'SMART_POST';

                        
    // Customer Information
    $street_address = $order->delivery['street_address'];
    $street_address2 = $order->delivery['suburb'];
    $city = $order->delivery['city'];
    $state = tep_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], '');
    if ($state == "QC") $state = "PQ";
    $postcode = str_replace(array(' ', '-'), '', $order->delivery['postcode']);
    $country_id = $order->delivery['country']['iso_code_2'];
     
    $this->_setInsuranceValue($totals);

   
    $request['WebAuthenticationDetail'] = array('UserCredential' =>
                                          array('Key' => $this->fedex_key, 'Password' => $this->fedex_pwd));
    $request['ClientDetail'] = array('AccountNumber' => $this->fedex_act_num, 'MeterNumber' => $this->fedex_meter_num);
    $request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v9 using PHP ***');
    $request['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
    $request['ReturnTransitAndCommit'] = true;
	
    $request['RequestedShipment']['DropoffType'] = $this->_setDropOff($vendors_id); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
    $request['RequestedShipment']['ShipTimestamp'] = date('c');
    $request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
    $request['RequestedShipment']['TotalInsuredValue']=array('Ammount'=> $this->insurance, 'Currency' => $_SESSION['currency']);
    
	$request['WebAuthenticationDetail'] = array('UserCredential' => array('Key' => $this->fedex_key, 'Password' => $this->fedex_pwd));                     
    $request['ClientDetail'] = array('AccountNumber' => $this->fedex_act_num, 'MeterNumber' => $this->fedex_meter_num);                    

    $request['RequestedShipment']['Shipper'] = array('Address' => array(
									 'StreetLines' => array(constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_1_' . $vendors_id),
									  constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_2_' . $vendors_id)), // Origin details
									 'City' => constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_CITY_' . $vendors_id),
									 'StateOrProvinceCode' => constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATE_' . $vendors_id),
									 'PostalCode' => constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_POSTAL_' . $vendors_id),
									 'CountryCode' => $this->country));          

    $request['RequestedShipment']['Recipient'] = array('Address' => array (  // customer info
			 'StreetLines' => array($street_address, $street_address2),
			 'City' => $city,
			 'StateOrProvinceCode' => $state,
			 'PostalCode' => $postcode,
			 'CountryCode' => $country_id,
			 'Residential' => ($order->delivery['company'] != '' ? false : true))); 

    $request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
									'Payor' => array('AccountNumber' => $this->fedex_act_num, // Replace 'XXX' with payor's account number
									'CountryCode' => $this->country));

    $request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
    $request['RequestedShipment']['PackageCount'] = $shipping_num_boxes;
    $request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
    $request['RequestedShipment']['RequestedPackageLineItems'] = array();

    if ($shipping_weight == 0) $shipping_weight = 0.1;

    for ($i=0; $i<$shipping_num_boxes; $i++) {
      $request['RequestedShipment']['RequestedPackageLineItems'][] = array('Weight' => array('Value' => $shipping_weight,
                                                            'Units' => constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT_' . $vendors_id)));
    }

    $response = $client->getRates($request);

    if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' && is_array($response->RateReplyDetails) || is_object($response->RateReplyDetails)) {

      if (is_object($response->RateReplyDetails)) {

        $response->RateReplyDetails = get_object_vars($response->RateReplyDetails);

      }

      switch (SHIPPING_BOX_WEIGHT_DISPLAY) {

        case (0):
        $show_box_weight = '';
        break;

        case (1):
        $show_box_weight = ' (' . $shipping_num_boxes . ' ' . TEXT_SHIPPING_BOXES . ')';
        break;

        case (2):
        $show_box_weight = ' (' . number_format($shipping_weight * $shipping_num_boxes,2) . TEXT_SHIPPING_WEIGHT . ')';
        break;

        default:
        $show_box_weight = ' (' . $shipping_num_boxes . ' x ' . number_format($shipping_weight,2) . TEXT_SHIPPING_WEIGHT . ')';
        break;

      }      

      $this->quotes = array('id' => $this->code,
                            'module' => $this->title . $show_box_weight);

      $methods = array();
      foreach ($response->RateReplyDetails as $rateReply) {
        if (in_array(strtoupper($rateReply->ServiceType), $this->types) && ($method == '' || str_replace('_', '', $rateReply->ServiceType) == $method)){
          $cost = $rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
          $cost = (float)round(preg_replace('/[^0-9.]/', '',  $cost), 2);
          $methods[] = array('id' => str_replace('_', '', $rateReply->ServiceType),
                             'title' => ucwords(strtolower(str_replace('_', ' ', $rateReply->ServiceType))),
                             'cost' => $cost + $this->handling_fee);
        }
      }

		if(!function_exists(cmp)){
			function cmp($a, $b) {
				if ($a['cost'] == $b['cost']) {
						return 0;
				}
				return ($a['cost'] < $b['cost']) ? -1 : 1;
			}
		}

		usort($methods, 'cmp');

      $this->quotes['methods'] = $methods;



      if ($this->tax_class > 0) {

        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);

      } 

    } else {

      $message = 'Error in Processing the Request.<br /><br />'; 

      foreach ($response -> Notifications as $notification) {           
        if(is_array($response -> Notifications)) {              
          $message .= $notification->Severity;
          $message .= ': ';           
          $message .= $notification->Message . '<br />';
        } else {
          $message .= $notification . '<br />';
        }
      }

      $this->quotes = array('module' => $this->title,
                            'error'  => $message);
    }

    if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);
    return $this->quotes;

  }



  function _setInsuranceValue($order_amount){
    if ($order_amount > (float)MODULE_SHIPPING_FEDEX_WEB_SERVICES_INSURE) {
      $this->insurance = sprintf("%01.2f", $order_amount);
    } else {
      $this->insurance = 0;
    }
  }

  

  function objectToArray($object) {
    if( !is_object( $object ) && !is_array( $object ) ) {
      return $object;
    }

    if( is_object( $object ) ) {
      $object = get_object_vars( $object );
    }

    return array_map( 'objectToArray', $object );
  }

  

  function _setDropOff($vendors_id = 1) {
    switch(constant('MODULE_SHIPPING_FEDEX_WEB_SERVICES_DROPOFF_' . $vendors_id)) {
      case '1':
        return 'REGULAR_PICKUP';
        break;

      case '2':
        return 'REQUEST_COURIER';
        break;
		
      case '3':
        return 'DROP_BOX';
        break;

      case '4':
        return 'BUSINESS_SERVICE_CENTER';
        break;

      case '5':
        return 'STATION';
        break;

    }
  }



  function check($vendors_id = 1){
    if(!isset($this->_check)){
      $check_query  = tep_db_query("SELECT configuration_value FROM ". TABLE_VENDOR_CONFIGURATION ." WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS_" . $vendors_id."'");
      $this->_check = tep_db_num_rows ($check_query);
    }
    return $this->_check;

  }



  function install($vendors_id = 1) {

    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) VALUES ('Enable FedEx Web Services','MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS_" . $vendors_id . "','true','Do you want to offer FedEx shipping?','6','0','tep_cfg_select_option(array(\'true\',\'false\'),',now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('FedEx Web Services Key', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_KEY_" . $vendors_id . "', '', 'Enter FedEx Web Services Key', '6', '3', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('FedEx Web Services Password', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PWD_" . $vendors_id . "', '', 'Enter FedEx Web Services Password', '6', '3', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('FedEx Account Number', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ACT_NUM_" . $vendors_id . "', '', 'Enter FedEx Account Number', '6', '3', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('FedEx Meter Number', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_METER_NUM_" . $vendors_id . "', '', 'Enter FedEx Meter Number', '6', '4', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Weight Units', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT_" . $vendors_id . "', 'LB', 'Weight Units:', '6', '10', 'tep_cfg_select_option(array(\'LB\', \'KG\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('First line of street address', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_1_" . $vendors_id . "', '', 'Enter the first line of your ship-from street address, required', '6', '20', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Second line of street address', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_2_" . $vendors_id . "', '', 'Enter the second line of your ship-from street address, leave blank if you do not need to specify a second line', '6', '21', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('City name', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_CITY_" . $vendors_id . "', '', 'Enter the city name for the ship-from street address, required', '6', '22', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('State or Province name', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATE_" . $vendors_id . "', '', 'Enter the 2 letter state or province name for the ship-from street address, required for Canada and US', '6', '23', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Postal code', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_POSTAL_" . $vendors_id . "', '', 'Enter the postal code for the ship-from street address, required', '6', '24', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Phone number', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PHONE_" . $vendors_id . "', '', 'Enter a contact phone number for your company, required', '6', '25', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Drop off type', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_DROPOFF_" . $vendors_id . "', '1', 'Dropoff type (1 = Regular pickup, 2 = request courier, 3 = drop box, 4 = drop at BSC, 5 = drop at station)?', '6', '30', 'tep_cfg_select_option(array(\'1\',\'2\',\'3\',\'4\',\'5\'),', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable Express Saver', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_SAVER_" . $vendors_id . "', 'true', 'Enable FedEx Express Saver', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable Standard Overnight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STANDARD_OVERNIGHT_" . $vendors_id . "', 'true', 'Enable FedEx Express Standard Overnight', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable First Overnight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FIRST_OVERNIGHT_" . $vendors_id . "', 'true', 'Enable FedEx Express First Overnight', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable Priority Overnight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PRIORITY_OVERNIGHT_" . $vendors_id . "', 'true', 'Enable FedEx Express Priority Overnight', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable 2 Day', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_2DAY_" . $vendors_id . "', 'true', 'Enable FedEx Express 2 Day', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable International Priority', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_PRIORITY_" . $vendors_id . "', 'true', 'Enable FedEx Express International Priority', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable International Economy', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_ECONOMY_" . $vendors_id . "', 'true', 'Enable FedEx Express International Economy', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable Ground', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_GROUND_" . $vendors_id . "', 'true', 'Enable FedEx Ground', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable International Ground', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_GROUND_" . $vendors_id . "', 'true', 'Enable FedEx International Ground', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable Freight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREIGHT_" . $vendors_id . "', 'true', 'Enable FedEx Freight', '6', '10', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Handling Fee', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_HANDLING_FEE_" . $vendors_id . "', '', 'Add a handling fee or leave blank', '6', '25', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '98', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Tax Class', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '25', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");
    tep_db_query ("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort Order', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display.', '6', '99', now(), '" . $vendors_id . "')");

  }



  function remove($vendors_id = 1) {
    tep_db_query ("DELETE FROM ". TABLE_VENDOR_CONFIGURATION ." WHERE vendors_id = '". $vendors_id ."' and configuration_key in ('". implode("','",$this->keys($vendors_id)). "')");
  }



  function keys($vendors_id = 1) {

    return array('MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_KEY_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PWD_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ACT_NUM_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_METER_NUM_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_1_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_2_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_CITY_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATE_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_POSTAL_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PHONE_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_DROPOFF_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_SAVER_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STANDARD_OVERNIGHT_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FIRST_OVERNIGHT_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PRIORITY_OVERNIGHT_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_2DAY_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_PRIORITY_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_ECONOMY_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_GROUND_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_GROUND_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREIGHT_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_TAX_CLASS_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_HANDLING_FEE_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE_' . $vendors_id,
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SORT_ORDER_' . $vendors_id
                 );
  }



	  function get_countries($countries_id = '', $with_iso_codes = false) {

			$countries_array = array();
			if (tep_not_null($countries_id)) {
				if ($with_iso_codes == true) {
					$countries = tep_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "' order by countries_name");
					$countries_values = tep_db_fetch_array($countries);
					$countries_array = array('countries_name' => $countries_values['countries_name'],
																	 'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
																	 'countries_iso_code_3' => $countries_values['countries_iso_code_3']);

				} else {

					$countries = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'");
					$countries_values = tep_db_fetch_array($countries);
					$countries_array = array('countries_name' => $countries_values['countries_name']);
				}

			} else {

				$countries = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " order by countries_name");
				while ($countries_values = tep_db_fetch_array($countries)) {
					$countries_array[] = array('countries_id' => $countries_values['countries_id'],
																		 'countries_name' => $countries_values['countries_name']);
				}
			}
			return $countries_array;
		}

}