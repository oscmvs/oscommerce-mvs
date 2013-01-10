<?php
/*
$Id: usps.php 6.1 by Kevin L Shelton on September 7, 2011
+++++ Original contribution by Brad Waite and Fritz Clapp ++++
++++ Revisions and Modifications made by Greg Deeth, 2008 ++++
Copyright 2008 osCommerce
Released under the GNU General Public License
*/
/////////////////////////////////////////
////////// Sets up USPS Class ///////////
/////////////////////////////////////////

class usps
{

	/////////////////////////////////////////
	///////////// Sets Variables ////////////
	/////////////////////////////////////////
	
	var $code, $title, $description, $icon, $enabled, $countries;
	
	function usps(){
		if ( !function_exists('htmlspecialchars_decode') )
		{
		  function htmlspecialchars_decode($text)
		  {
		     return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
		   }
		}
		global $order;
		$this->code = 'usps';
		$this->title = MODULE_SHIPPING_USPS_TEXT_TITLE;
		$this->description = MODULE_SHIPPING_USPS_TEXT_DESCRIPTION;
		$this->icon = DIR_WS_ICONS . 'shipping_usps.gif';
		
		$this->delivery_country_id = $order->delivery['country']['id'];
    	$this->delivery_zone_id = $order->delivery['zone_id'];
	
		$this->countries = $this->country_list();

		$this->types = array(
			'First-Class Mail' => 'First-Class Mail',
			'Media Mail' => 'Media Mail',
			'Parcel Post' => 'Parcel Post',
			'Priority Mail' => 'Priority Mail',
			'Priority Mail Flat Rate Envelope' => 'Priority Mail Flat Rate Envelope',
			'Priority Mail Small Flat Rate Box' => 'Priority Mail Small Flat Rate Box',
			'Priority Mail Medium Flat Rate Box' => 'Priority Mail Medium Flat Rate Box',
			'Priority Mail Large Flat Rate Box' => 'Priority Mail Large Flat Rate Box',
			'Express Mail' => 'Express Mail',
			'Express Mail Flat Rate Envelope' => 'Express Mail Flat Rate Envelope'
			);
		$this->intl_types = array(
			'Global Express' => 'Global Express Guaranteed (GXG)**',
			'Global Express Non-Doc Rect' => 'Global Express Guaranteed Non-Document Rectangular',
			'Global Express Non-Doc Non-Rect' => 'Global Express Guaranteed Non-Document Non-Rectangular',
			'USPS GXG Envelopes' => 'USPS GXG tradmrk Envelopes**',
			'Express Mail Int' => 'Express Mail International',
			'Express Mail Int Flat Rate Env' => 'Express Mail International Flat Rate Envelope',
			'Priority Mail International' => 'Priority Mail International',
			'Priority Mail Int Flat Rate Lrg Box' => 'Priority Mail International Large Flat Rate Box',
			'Priority Mail Int Flat Rate Med Box' => 'Priority Mail International Medium Flat Rate Box',
			'Priority Mail Int Flat Rate Small Box' => 'Priority Mail International Small Flat Rate Box**',
			'Priority Mail Int Flat Rate Env' => 'Priority Mail International Flat Rate Envelope**',
			'First-Class Mail Int Lrg Env' => 'First-Class Mail International Large Envelope**',
			'First-Class Mail Int Package' => 'First-Class Mail International Package**',
			'First-Class Mail Int Letter' => 'First-Class Mail International Letter**'
			);
		}

	/////////////////////////////////////////
	///////////// Ends Variables ////////////
	/////////////////////////////////////////
 
	/////////////////////////////////////////
	/////////// Sets Country List ///////////
	/////////////////////////////////////////

	function country_list()
		{
		$list = array(
			'AF' => 'Afghanistan',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia-Herzegovina',
			'BW' => 'Botswana',
			'BR' => 'Brazil',
			'VG' => 'British Virgin Islands',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'MM' => 'Burma',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island (Australia)',
			'CC' => 'Cocos Island (Australia)',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo (Brazzaville),Republic of the',
			'ZR' => 'Congo, Democratic Republic of the',
			'CK' => 'Cook Islands (New Zealand)',
			'CR' => 'Costa Rica',
			'CI' => 'Cote d\'Ivoire (Ivory Coast)',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'TP' => 'East Timor (Indonesia)',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia, Republic of',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GB' => 'Great Britain and Northern Ireland',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GT' => 'Guatemala',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia, Republic of',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte (France)',
			'MX' => 'Mexico',
			'MD' => 'Moldova',
			'MC' => 'Monaco (France)',
			'MN' => 'Mongolia',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'AN' => 'Netherlands Antilles',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn Island',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'QA' => 'Qatar',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts (St. Christopher and Nevis)',
			'LC' => 'Saint Lucia',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'YU' => 'Serbia-Montenegro',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovak Republic',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia (Falkland Islands)',
			'KR' => 'South Korea (Korea, Republic of)',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TG' => 'Togo',
			'TK' => 'Tokelau (Union) Group (Western Samoa)',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican City',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'WF' => 'Wallis and Futuna Islands',
			'WS' => 'Western Samoa',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe');
			return $list;
		}
	
	/////////////////////////////////////////
	/////////// Ends Country List ///////////
	/////////////////////////////////////////

	/////////////////////////////////////////
	////////// Sets Quote ///////////////////
	/////////////////////////////////////////
	
	// MVS ADD
	function sort_order($vendors_id = '1') {
		$sort_order = @ constant('MODULE_SHIPPING_USPS_SORT_ORDER_' . $vendors_id);
		if (isset ($sort_order)) {
			$this->sort_order = $sort_order;
		} else {
			$this->sort_order = '0';
		}
		return $this->sort_order;
	}

	function tax_class($vendors_id = '1') {
		$this->tax_class = constant('MODULE_SHIPPING_USPS_TAX_CLASS_' . $vendors_id);
		return $this->tax_class;
	}

	function enabled($vendors_id = '1') {

		$this->enabled = false;

		$status = @ constant('MODULE_SHIPPING_USPS_STATUS_' . $vendors_id);

		if (isset ($status) && $status != '') {

			$this->enabled = (($status == 'True') ? true : false);

		}
		if (($this->enabled == true) && ((int) constant('MODULE_SHIPPING_USPS_ZONE_' . $vendors_id) > 0)) {
			$check_flag = false;
			$check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) constant('MODULE_SHIPPING_USPS_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $this->delivery_country_id . "' order by zone_id");
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

	function zones($vendors_id = '1') {
		
		if (($this->enabled == true) && ((int) constant('MODULE_SHIPPING_USPS_ZONE_' . $vendors_id) > 0)) {

			$check_flag = false;

			$check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) constant('MODULE_SHIPPING_USPS_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $this->delivery_country_id . "' order by zone_id");
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
	// EOADD MVS

	function quote($method = '', $module = '', $vendors_id = '1') {
		global $order, $shipping_weight, $shipping_num_boxes, $transittime, $dispinsure;
		if ( tep_not_null($method) && (in_array($method, $this->types) || in_array($method, $this->intl_types)) ) {
			$this->_setService($method);
		}
		if ($shipping_weight <= 0) {$shipping_weight = 0;}
		$shipping_weight = ($shipping_weight < 0.0625 ? 0.0625 : $shipping_weight);
		$shipping_pounds = floor ($shipping_weight);
		$shipping_ounces = $this->round_up((16 * ($shipping_weight - floor($shipping_weight))), 2);
		$this->_setWeight($shipping_pounds, $shipping_ounces, $shipping_weight);
		if (in_array('Display weight', explode(', ', constant('MODULE_SHIPPING_USPS_OPTIONS_' . $vendors_id)))) {
			$shiptitle = $shipping_pounds . ' lbs, ' . $shipping_ounces . ' oz';
		} else {
			$shiptitle = '';
		}
		$uspsQuote = $this->_getQuote($vendors_id);
		if (is_array($uspsQuote)) {
			if (isset($uspsQuote['error'])) {
				$this->quotes = array('id' => $this->code,
															'module' => $this->title,
															'error' => $uspsQuote['error']);
			} else {
				// Added MVS
				$vendors_data_query = tep_db_query("select handling_charge,
																							 handling_per_box,
																							 vendor_country,
																							 vendors_zipcode
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
				// EOADD MVS
						
						
				$this->quotes = array('id' => $this->code,
													'module' => $this->title . $shiptitle);
				
				$methods = array();
				$size = sizeof($uspsQuote);
				for ($i=0; $i<$size; $i++) {
					list($type, $cost) = each($uspsQuote[$i]);
					$title = ((isset($this->types[$type])) ? $this->types[$type] : $type);
					$title .= $transittime[$type] . $dispinsure[$type];
					if (constant('MODULE_SHIPPING_USPS_DMSTC_INSURANCE_OPTION_' . $vendors_id) == 'True') {
						$methods[] = array('id' => $type,
																'title' => $title,
																'cost' => ($cost * $shipping_num_boxes) + $insurance + $handling);
																//'cost' => ($cost + $insurance + $handling_cost[0]) * $shipping_num_boxes);
					} else {
						$methods[] = array('id' => $type,
																'title' => $title,
																'cost' => ($cost * $shipping_num_boxes) + $handling);
																//'cost' => ($cost + $handling_cost[0]) * $shipping_num_boxes);
					}
				}
				$this->quotes['methods'] = $methods;
				if ($this->tax_class > 0) {
					$this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
				}
			}
		} else {
			$this->quotes = array('id' => $this->code,
														'module' => $this->title,
														'error' => MODULE_SHIPPING_USPS_TEXT_ERROR);
		}
		if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);
		return $this->quotes;
	}
	
	
	function check($vendors_id = 1) {
		if (!isset($this->_check)) {
			$check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key = 'MODULE_SHIPPING_USPS_STATUS_" . $vendors_id . "'");
			$this->_check = tep_db_num_rows($check_query);
		}
		return $this->_check;
	}
	
	/////////////////////////////////////////
	////////// Ends Quote ///////////////////
	/////////////////////////////////////////

	/////////////////////////////////////////
	//////////// Install Module /////////////
	/////////////////////////////////////////

	function install($vendors_id = 1)
		{
		tep_db_query("ALTER TABLE `configuration` CHANGE `configuration_value` `configuration_value` TEXT NOT NULL, CHANGE `set_function` `set_function` TEXT NULL DEFAULT NULL");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable USPS Shipping', 'MODULE_SHIPPING_USPS_STATUS_" . $vendors_id . "', 'True', 'Do you want to offer USPS shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enter the USPS User ID', 'MODULE_SHIPPING_USPS_USERID_" . $vendors_id . "', 'NONE', 'Enter the USPS USERID assigned to you. <u>You must contact USPS to have them switch you to the Production server.</u>  Otherwise this module will not work!', '6', '3', 'tep_cfg_multiinput_list(array(\'ID\', \'Password\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort Order', 'MODULE_SHIPPING_USPS_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display.', '6', '0', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Tax Class', 'MODULE_SHIPPING_USPS_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_USPS_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Display Options', 'MODULE_SHIPPING_USPS_OPTIONS_" . $vendors_id . "', 'Display weight, Display transit time, Display insurance', 'Select display options', '6', '0', 'tep_cfg_select_multioption(array(\'Display weight\', \'Display transit time\', \'Display insurance\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Processing Time', 'MODULE_SHIPPING_USPS_PROCESSING_" . $vendors_id . "', '1', 'Days to Process Order', '6', '0', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Shipping Methods', 'MODULE_SHIPPING_USPS_DMSTC_TYPES_" . $vendors_id . "', 'First-Class Mail, Media Mail, Parcel Post, Priority Mail, Priority Mail Flat Rate Envelope, Priority Mail Small Flat Rate Box, Priority Mail Medium Flat Rate Box, Priority Mail Large Flat Rate Box, Express Mail, Express Mail Flat Rate Envelope', 'Select the domestic services to be offered:', '6', '4', 'tep_cfg_select_multioption(array(\'First-Class Mail\', \'Media Mail\', \'Parcel Post\', \'Priority Mail\', \'Priority Mail Flat Rate Envelope\', \'Priority Mail Small Flat Rate Box\', \'Priority Mail Medium Flat Rate Box\', \'Priority Mail Large Flat Rate Box\', \'Express Mail\', \'Express Mail Flat Rate Envelope\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Rates', 'MODULE_SHIPPING_USPS_DMSTC_RATE_" . $vendors_id . "', 'Retail', 'Charge retail pricing or internet pricing?', '6', '0', 'tep_cfg_select_option(array(\'Retail\', \'Internet\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Delivery Confirmation', 'MODULE_SHIPPING_USPS_DMST_DEL_CONF_" . $vendors_id . "', 'True', 'Automatically charge Delivery Confirmation for first class and parcel ($0.19)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Signature Confirmation', 'MODULE_SHIPPING_USPS_DMST_SIG_CONF_" . $vendors_id . "', 'True', 'Automatically charge Signature Confirmation when available ($1.95)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Signature Confirmation Threshold', 'MODULE_SHIPPING_USPS_SIG_THRESH_" . $vendors_id . "', '100', 'Order total required before Signature Confirmation is triggered?', '6', '0', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Insurance Options', 'MODULE_SHIPPING_USPS_DMSTC_INSURANCE_OPTION_" . $vendors_id . "', 'False', 'Force USPS Calculated Domestic Insurance?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Handling Fees', 'MODULE_SHIPPING_USPS_DMSTC_HANDLING_" . $vendors_id . "', '0, 0, 0, 0, 0, 0, 0, 0, 0, 0', 'Add a different handling fee for each shipping type.', '6', '0', 'tep_cfg_multiinput_list(array(\'First-Class\', \'Media\', \'Parcel\', \'Priority\', \'Priority Flat Env\', \'Priority Sm Flat Box\', \'Priority Med Flat Box\', \'Priority Lg Flat Box\', \'Express\', \'Express Flat Env\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic First-Class Threshold', 'MODULE_SHIPPING_USPS_DMSTC_FIRSTCLASS_THRESHOLD_" . $vendors_id . "', '0, 3.5, 3.5, 10, 10, 13', '<u>Maximums:</u><br>Letters 3.5oz<br>Large envelopes and parcels 13oz', '6', '0', 'tep_cfg_multiinput_duallist_oz(array(\'Letter\', \'Lg Env\', \'Package\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Domestic Other Mail Threshold', 'MODULE_SHIPPING_USPS_DMSTC_OTHER_THRESHOLD_" . $vendors_id . "', '0, 3, 0, 3, 3, 11, 11, 15, 0, 70, 0, 3, 0, 70, 0, 70, 0, 70', '<u>Maximums:</u><br>70 lb', '6', '0', 'tep_cfg_multiinput_duallist_lb(array(\'Flat Rate Envelope\', \'Sm Flat Rate Box\', \'Md Flat Rate Box\', \'Lg Flat Rate Box\', \'Standard Priority\', \'Express FltRt Env\', \'Express Standard\', \'Parcel Pst\', \'Media Mail\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Int\'l Shipping Methods', 'MODULE_SHIPPING_USPS_INTL_TYPES_" . $vendors_id . "', 'Global Express, Global Express Non-Doc Rect, Global Express Non-Doc Non-Rect, USPS GXG Envelopes, Express Mail Int, Express Mail Int Flat Rate Env, Priority Mail International, Priority Mail Int Flat Rate Env, Priority Mail Int Flat Rate Small Box, Priority Mail Int Flat Rate Med Box, Priority Mail Int Flat Rate Lrg Box, First-Class Mail Int Lrg Env, First-Class Mail Int Package, First-Class Mail Int Letter', 'Select the international services to be offered:', '6', '0', 'tep_cfg_select_multioption(array(\'Global Express\', \'Global Express Non-Doc Rect\', \'Global Express Non-Doc Non-Rect\', \'USPS GXG Envelopes\', \'Express Mail Int\', \'Express Mail Int Flat Rate Env\', \'Priority Mail International\', \'Priority Mail Int Flat Rate Env\', \'Priority Mail Int Flat Rate Small Box\', \'Priority Mail Int Flat Rate Med Box\', \'Priority Mail Int Flat Rate Lrg Box\', \'First-Class Mail Int Lrg Env\', \'First-Class Mail Int Package\', \'First-Class Mail Int Letter\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Int\'l Rates', 'MODULE_SHIPPING_USPS_INTL_RATE_" . $vendors_id . "', 'Retail', 'Charge retail pricing or internet pricing?', '6', '0', 'tep_cfg_select_option(array(\'Retail\', \'Internet\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Int\'l Insurance Options', 'MODULE_SHIPPING_USPS_INTL_INSURANCE_OPTION_" . $vendors_id . "', 'False', 'Force USPS Calculated International Insurance?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Int\'l Handling Fees', 'MODULE_SHIPPING_USPS_INTL_HANDLING_" . $vendors_id . "', '0', 'Add a flat fee international shipping.', '6', '0', 'tep_cfg_multiinput_list(array(\'\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Int\'l Package Sizes', 'MODULE_SHIPPING_USPS_INTL_SIZE_" . $vendors_id . "', '1, 1, 1, 0', 'Standard package dimensions required by USPS for international rates', '6', '0', 'tep_cfg_multiinput_list(array(\'Width\', \'Length\', \'Height\', \'Girth\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Non USPS Insurance - Domestic and international', 'MODULE_SHIPPING_USPS_INSURE_" . $vendors_id . "', 'False', 'Would you like to charge insurance for packages independent of USPS, i.e, merchant provided, Stamps.com, Endicia?  If used in conjunction with USPS calculated insurance, the higher of the two will apply.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, vendors_id) values ('Non USPS Insurance', 'MODULE_SHIPPING_USPS_INS1_" . $vendors_id . "', '1.75', 'Totals $.01-$50.00', '6', '0', 'currencies->format', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, vendors_id) values ('Non USPS Insurance', 'MODULE_SHIPPING_USPS_INS2_" . $vendors_id . "', '2.25', 'Totals $50.01-$100', '6', '0', 'currencies->format', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, vendors_id) values ('Non USPS Insurance', 'MODULE_SHIPPING_USPS_INS3_" . $vendors_id . "', '2.75', 'Totals $100.01-$200', '6', '0', 'currencies->format', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, vendors_id) values ('Non USPS Insurance', 'MODULE_SHIPPING_USPS_INS4_" . $vendors_id . "', '4.70', 'Totals $200.01-$300', '6', '0', 'currencies->format', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, vendors_id) values ('Non USPS Insurance', 'MODULE_SHIPPING_USPS_INS5_" . $vendors_id . "', '1.00', 'For every $100 over $300 (add)', '6', '0', 'currencies->format', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Insure Tax?', 'MODULE_SHIPPING_USPS_INSURE_TAX_" . $vendors_id . "', 'False', 'Would you like to insure sales tax paid by the customer?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Insure Shipping Cost?', 'MODULE_SHIPPING_USPS_INSURE_SHIPPING_" . $vendors_id . "', 'False', 'Would you like insure the shipping cost paid by the customer?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
		}
	
	function keys($vendors_id = 1) {
	return array('MODULE_SHIPPING_USPS_STATUS_' . $vendors_id, 'MODULE_SHIPPING_USPS_USERID_' . $vendors_id, 'MODULE_SHIPPING_USPS_SORT_ORDER_' . $vendors_id, 'MODULE_SHIPPING_USPS_TAX_CLASS_' . $vendors_id, 'MODULE_SHIPPING_USPS_ZONE_' . $vendors_id, 'MODULE_SHIPPING_USPS_OPTIONS_' . $vendors_id, 'MODULE_SHIPPING_USPS_PROCESSING_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMSTC_TYPES_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMSTC_RATE_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMST_DEL_CONF_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMST_SIG_CONF_' . $vendors_id, 'MODULE_SHIPPING_USPS_SIG_THRESH_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMSTC_INSURANCE_OPTION_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMSTC_HANDLING_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMSTC_FIRSTCLASS_THRESHOLD_' . $vendors_id, 'MODULE_SHIPPING_USPS_DMSTC_OTHER_THRESHOLD_' . $vendors_id, 'MODULE_SHIPPING_USPS_INTL_TYPES_' . $vendors_id, 'MODULE_SHIPPING_USPS_INTL_RATE_' . $vendors_id, 'MODULE_SHIPPING_USPS_INTL_INSURANCE_OPTION_' . $vendors_id, 'MODULE_SHIPPING_USPS_INTL_HANDLING_' . $vendors_id, 'MODULE_SHIPPING_USPS_INTL_SIZE_' . $vendors_id, 'MODULE_SHIPPING_USPS_INSURE_' . $vendors_id, 'MODULE_SHIPPING_USPS_INS1_' . $vendors_id, 'MODULE_SHIPPING_USPS_INS2_' . $vendors_id, 'MODULE_SHIPPING_USPS_INS3_' . $vendors_id,'MODULE_SHIPPING_USPS_INS4_' . $vendors_id, 'MODULE_SHIPPING_USPS_INS5_' . $vendors_id, 'MODULE_SHIPPING_USPS_INSURE_TAX_' . $vendors_id, 'MODULE_SHIPPING_USPS_INSURE_SHIPPING_' . $vendors_id);
	}
	
	/////////////////////////////////////////
	///////// End Install Module ////////////
	/////////////////////////////////////////

	/////////////////////////////////////////
	///////////// Remove Module /////////////
	/////////////////////////////////////////

	function remove($vendors_id) {
		tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
	}
	
	/////////////////////////////////////////
	////////// End Remove Module ////////////
	/////////////////////////////////////////

	/////////////////////////////////////////
	//////////// SET DEFAULTS ///////////////
	/////////////////////////////////////////

	function _setService($service) {
	$this->service = $service;
	}
	
	function _setWeight($pounds, $ounces=0, $weight) {
	$this->pounds = $pounds;
	$this->ounces = $ounces;
	$this->weight = $weight;
	}
	
	function _setMachinable($machinable) {
	$this->machinable = $machinable;
	}

	/////////////////////////////////////////
	//////////// END DEFAULTS ///////////////
	/////////////////////////////////////////
	
	/////////////////////////////////////////
	/////////// START RATE REQUEST //////////
	/////////////////////////////////////////

	function _getQuote($vendors_id)
	{
	global $order, $transittime, $dispinsure;
	
	$this->processing = constant('MODULE_SHIPPING_USPS_PROCESSING_' . $vendors_id);
	$this->dmstc_handling = explode( ", ", constant('MODULE_SHIPPING_USPS_DMSTC_HANDLING_' . $vendors_id));
	$this->intl_handling = explode( ", ", constant('MODULE_SHIPPING_USPS_INTL_HANDLING_' . $vendors_id));
	$this->sig_conf_thresh = constant('MODULE_SHIPPING_USPS_SIG_THRESH_' . $vendors_id);
	
	if (constant('MODULE_SHIPPING_USPS_INSURE_TAX_' . $vendors_id) == 'True' && constant('MODULE_SHIPPING_USPS_INSURE_SHIPPING_' . $vendors_id) == 'True') {$insurable = $order->info['total'];}
	elseif (constant('MODULE_SHIPPING_USPS_INSURE_TAX_' . $vendors_id)) {$insurable = $order->info['subtotal'] + $order->info['tax'];}
	elseif (constant('MODULE_SHIPPING_USPS_INSURE_SHIPPING_' . $vendors_id) == 'True') {$insurable = $order->info['total'] - $order->info['tax'];}
	else {$insurable = $order->info['subtotal'];}
	$transit = (in_array('Display transit time', explode(', ', constant('MODULE_SHIPPING_USPS_OPTIONS_' . $vendors_id))));
	$dispinsurance = (in_array('Display insurance', explode(', ', constant('MODULE_SHIPPING_USPS_OPTIONS_' . $vendors_id))));
	$Authentication = explode( ",", constant('MODULE_SHIPPING_USPS_USERID_' . $vendors_id));
	if ($order->delivery['country']['id'] == SHIPPING_ORIGIN_COUNTRY)
	
		/////////////////////////////////////////
		////// START USPS DOMESTIC REQUEST //////
		/////////////////////////////////////////
		
		{
		$request  = '<RateV4Request USERID="' . trim ($Authentication[0]) . '">' .
					'<Revision>2</Revision>';
		$services_count = 0;
		if (isset($this->service)) {
		$this->types = array($this->service => $this->types[$this->service]);
		}
		$dest_zip = str_replace(' ', '', $order->delivery['postcode']);
		if ($order->delivery['country']['iso_code_2'] == 'US') $dest_zip = substr($dest_zip, 0, 5);
		reset($this->types);
	
			/////////////////////////////////////////
			//// REQUEST IF WITHIN ALLOWED LIST /////
			/////////////////////////////////////////

			$allowed_types = explode(", ", constant('MODULE_SHIPPING_USPS_DMSTC_TYPES_' . $vendors_id));
			while (list($key, $value) = each($this->types)) {
			if ( !in_array($key, $allowed_types) ) continue;

				/////////////////////////////////////////
				// REQUEST IF WITHIN WEIGHT THRESHOLDS //
				/////////////////////////////////////////
				
				$FMT = explode(", ", constant('MODULE_SHIPPING_USPS_DMSTC_FIRSTCLASS_THRESHOLD_' . $vendors_id));
				if ($key == 'First-Class Mail'){
				$transid = $key;
				$key = 'First-Class Mail';
				if($this->pounds == 0) {
				if($FMT[0] < $this->ounces && $this->ounces <= $FMT[1]) {
				$transid = 'First-Class Mail Letter';
				$this->FirstClassMailType = 'LETTER';
				$this->machinable = 'TRUE';
				$this->size = 'REGULAR';
				$this->container = 'VARIABLE';
				} else if($FMT[2] < $this->ounces && $this->ounces <= $FMT[3]) {
				$transid = 'First-Class Mail Large Envelope';
				$this->FirstClassMailType = 'FLAT';
				$this->machinable = 'TRUE';
				$this->size = 'REGULAR';
				$this->container = 'VARIABLE';
				} else if($FMT[4] < $this->ounces && $this->ounces <= $FMT[5]) {
				$transid = 'First-Class Mail Package';
				$this->FirstClassMailType = 'PARCEL';
				$this->machinable = 'TRUE';
				$this->size = 'REGULAR';
				$this->container = 'VARIABLE';
				} else {
				$key = 'none';
				}
				}
				}

				$OMT = explode(", ", constant('MODULE_SHIPPING_USPS_DMSTC_OTHER_THRESHOLD_' . $vendors_id));
				if ($key == 'Priority Mail'){
				$transid = $key;
				if ($OMT[8] < $this->weight && $this->weight <= $OMT[9]) {
				$key = 'Priority Commercial';
				$this->container = '';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}

				if ($key == 'Priority Mail Flat Rate Envelope'){
				$transid = $key;
				if ($OMT[0] < $this->weight && $this->weight <= $OMT[1]) {
				$key = 'Priority Commercial';
				$this->container = 'FLAT RATE ENVELOPE';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}
				if ($key == 'Priority Mail Small Flat Rate Box'){
				$transid = $key;
				if ($OMT[2] < $this->weight && $this->weight <= $OMT[3]) {
				$key = 'Priority Commercial';
				$this->container = 'SM FLAT RATE BOX';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}
				if ($key == 'Priority Mail Medium Flat Rate Box'){
				$transid = $key;
				if ($OMT[4] < $this->weight && $this->weight <= $OMT[5]) {
				$key = 'Priority Commercial';
				$this->container = 'MD FLAT RATE BOX';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}
				if ($key == 'Priority Mail Large Flat Rate Box'){
				$transid = $key;
				if ($OMT[6] < $this->weight && $this->weight <= $OMT[7]) {
				$key = 'Priority Commercial';
				$this->container = 'LG FLAT RATE BOX';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}

				if ($key == 'Express Mail'){
				$transid = $key;
				if ($OMT[12] < $this->weight && $this->weight <= $OMT[13]) {
				$this->container = '';
				$key = 'Express Commercial';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}
				if ($key == 'Express Mail Flat Rate Envelope'){
				$transid = $key;
				if ($OMT[10] < $this->weight && $this->weight <= $OMT[11]) {
				$key = 'Express Commercial';
				$this->container = 'FLAT RATE ENVELOPE';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}
				if ($key == 'Parcel Post'){
				$transid = $key;
				$key = 'Parcel Post';
				if ($OMT[14] < $this->weight && $this->weight <= $OMT[15]){
				$this->machinable = 'TRUE';
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}
				
				if ($key == 'Media Mail'){
				$transid = $key;
				$key = 'Media Mail';
				if ($OMT[16] < $this->weight && $this->weight <= $OMT[17]){
				$this->size = 'REGULAR';
				} else {
				$key = 'none';
				}
				}

				/////////////////////////////////////////////
				// END REQUEST IF WITHIN WEIGHT THRESHOLDS //
				/////////////////////////////////////////////

			$request .= 
						'<Package ID="' . $services_count . '">' .
						'<Service>' . $key . '</Service>' .
						'<FirstClassMailType>' . $this->FirstClassMailType . '</FirstClassMailType>' .
						'<ZipOrigination>' . SHIPPING_ORIGIN_ZIP . '</ZipOrigination>' .
						'<ZipDestination>' . $dest_zip . '</ZipDestination>' .
						'<Pounds>' . $this->pounds . '</Pounds>' .
						'<Ounces>' . $this->ounces . '</Ounces>' .
						'<Container>' . $this->container . '</Container>' .
						'<Size>' . $this->size . '</Size>' .
						'<Value>' . $insurable . '</Value>' .
						'<Machinable>' . $this->machinable . '</Machinable>' .
						'<ShipDate>' . date('d-M-Y') . '</ShipDate>' .
						'</Package>';
 
				/////////////////////////////////////////
				////// START USPS TRANSIT REQUEST ///////
				/////////////////////////////////////////
				
				if($transit)
				{
				$transitreq  = 'USERID="' . trim ($Authentication[0]) . '">' .
				'<OriginZip>' . SHIPPING_ORIGIN_ZIP . '</OriginZip>' .
				'<DestinationZip>' . $dest_zip . '</DestinationZip>';
					switch ($key)
					{
					case 'First-Class Mail':   $transreq[$transid] = 'API=PriorityMail&XML=' . 
						urlencode( '<PriorityMailRequest ' . $transitreq . '</PriorityMailRequest>');
					break;
					case 'Media Mail':   $transreq[$transid] = 'API=StandardB&XML=' . 
						urlencode( '<StandardBRequest ' . $transitreq . '</StandardBRequest>');
					break;
					case 'Parcel Post':   $transreq[$transid] = 'API=StandardB&XML=' . 
						urlencode( '<StandardBRequest ' . $transitreq . '</StandardBRequest>');
					break;
					case 'Priority Commercial': $transreq[$transid] = 'API=PriorityMail&XML=' . 
						urlencode( '<PriorityMailRequest ' . $transitreq . '</PriorityMailRequest>');
					break;
					default:	$transreq[$transid] = '';
					break;
					}
				}
		
				/////////////////////////////////////////
				//////// END USPS TRANSIT REQUEST ///////
				/////////////////////////////////////////

			$services_count++;
			}

			/////////////////////////////////////////
			////// END IF WITHIN ALLOWED LIST ///////
			/////////////////////////////////////////

		$request .= '</RateV4Request>';
// echo $request;
		$request = 	'API=RateV4&XML=' . urlencode($request);
		}
		
		/////////////////////////////////////////
		/////// END USPS DOMESTIC REQUEST ///////
		/////////////////////////////////////////

	 	else
	
		/////////////////////////////////////////
		//// START USPS INTERNATIONAL REQUEST ///
		/////////////////////////////////////////
		
		{
		$request = 	'<IntlRateV2Request USERID="' . trim ($Authentication[0]) . '">' .
					'<Revision>2</Revision>' .
					'<Package ID="0">' .
					'<Pounds>' . $this->pounds . '</Pounds>' .
					'<Ounces>' . $this->ounces . '</Ounces>' .
					'<Machinable>True</Machinable>' .
					'<MailType>Package</MailType>' .
					'<GXG>' .
						'<POBoxFlag>N</POBoxFlag>' .
						'<GiftFlag>N</GiftFlag>' .
					'</GXG>' .
					'<ValueOfContents>' . $insurable . '</ValueOfContents>' .
					'<Country>' . $this->countries[$order->delivery['country']['iso_code_2']] . '</Country>' .
					'<Container>RECTANGULAR</Container>' .
					'<Size>REGULAR</Size>' ;
		$IPS = explode(", ", constant('MODULE_SHIPPING_USPS_INTL_SIZE_' . $vendors_id));
		$request .= '<Width>' . $IPS[0] . '</Width>' .
					'<Length>' . $IPS[1] . '</Length>' .
					'<Height>' . $IPS[2] . '</Height>' .
					'<Girth>' . $IPS[3] . '</Girth>' .
					'<OriginZip>' . SHIPPING_ORIGIN_ZIP . '</OriginZip>' .
					'<CommercialFlag>Y</CommercialFlag>' .
					'<ExtraServices>' .
						'<ExtraService>1</ExtraService>' .
						'<ExtraService>2</ExtraService>' .
					'</ExtraServices>' .
					'</Package>' .
					'</IntlRateV2Request>';
		$request = 	'API=IntlRateV2&XML=' . urlencode($request);
		}
		
		/////////////////////////////////////////
		//// END USPS INTERNATIONAL REQUEST /////
		/////////////////////////////////////////

		/////////////////////////////////////////
		/////// USPS HTTP COMMUNICATION /////////
		/////////////////////////////////////////
		
		$usps_server = 'production.shippingapis.com';
		$api_dll = 'shippingAPI.dll';
		$body = '';
		if (!class_exists('httpClient')) {
		include('includes/classes/http_client.php');
		}
		$http = new httpClient();
		if ($http->Connect($usps_server, 80)) {
		$http->addHeader('Host', $usps_server);
		$http->addHeader('User-Agent', 'osCommerce');
		$http->addHeader('Connection', 'Close');
		if ($http->Get('/' . $api_dll . '?' . $request)) $body = $http->getBody();
		//		mail('user@localhost.com','USPS Rate Quote response',$body,'From: <user@localhost.com>');  
		if ($transit && is_array($transreq) && ($order->delivery['country']['id'] == STORE_COUNTRY)) {
		while (list($key, $value) = each($transreq)) {
		if ($http->Get('/' . $api_dll . '?' . $value)) $transresp[$key] = $http->getBody();
		//		mail('user@localhost.com','USPS Transit Response',$transresp[$key],'From: <user@localhost.com>');
		}
		}
		$http->Disconnect();
		} else {
		return false;
		}
		$body = htmlspecialchars_decode($body);
		$body = preg_replace('/\&lt;sup\&gt;\&amp;reg;\&lt;\/sup\&gt;/', '', $body);
		$body = preg_replace('/\&lt;sup\&gt;\&amp;trade;\&lt;\/sup\&gt;/', ' tradmrk', $body);

		/////////////////////////////////////////
		/////END USPS HTTP COMMUNICATION ////////
		/////////////////////////////////////////

		/////////////////////////////////////////
		/////////// START RATE RESPONSE /////////
		/////////////////////////////////////////
		$response = array();
		while (true) {
		if ($start = strpos($body, '<Package ID=')) {

		$body = substr($body, $start);
		$end = strpos($body, '</Package>');
		$response[] = substr($body, 0, $end+10);
		$body = substr($body, $end+9);
		} else {
		break;
		}
		}
		$rates = array();
		$rates_sorter = array();
		if ($order->delivery['country']['id'] == SHIPPING_ORIGIN_COUNTRY) {

			/////////////////////////////////////////
			///////// START DOMESTIC RESPONSE ///////
			/////////////////////////////////////////
		
			if (sizeof($response) == '1') {
			if (preg_match('/<Error>/', $response[0])) {
			$number = preg_match('/<Number>(.*)<\/Number>/', $response[0], $regs);
			$number = $regs[1];
			$description = preg_match('/<Description>(.*)<\/Description>/', $response[0], $regs);
			$description = $regs[1];
			return array('id' => $this->code, 'module' => $this->title, 'error' => $number . ' - ' . $description);
			}
			}
			$n = sizeof($response);
			for ($i=0; $i<$n; $i++) {
			if (strpos($response[$i], '<Rate>'))
			{
				$service = preg_match('/<MailService>(.*)<\/MailService>/', $response[$i], $regs);
				$service = $regs[1];
				if ((constant('MODULE_SHIPPING_USPS_DMSTC_RATE_' . $vendors_id) == 'Internet') && preg_match('/CommercialRate/', $response[$i]))
					{	$postage = preg_match('/<CommercialRate>(.*)<\/CommercialRate>/', $response[$i], $regs);
						$postage = $regs[1];}
					else
		   			{	$postage = preg_match('/<Rate>(.*)<\/Rate>/', $response[$i], $regs);
						$postage = $regs[1];}
				if (preg_match('/Insurance<\/ServiceName><Available>true<\/Available><AvailableOnline>true/', $response[$i]))
					{	$insurance = preg_match('/Insurance<\/ServiceName><Available>true<\/Available><AvailableOnline>true<\/AvailableOnline><Price>(.*)<\/Price>/', $response[$i], $regs);
						$insurance = $regs[1];}
					elseif (preg_match('/Insurance<\/ServiceName><Available>true<\/Available><AvailableOnline>false/', $response[$i]))
						{	$insurance = preg_match('/Insurance<\/ServiceName><Available>true<\/Available><AvailableOnline>false<\/AvailableOnline><Price>(.*)<\/Price>/', $response[$i], $regs);
							$insurance = $regs[1];}
					else { $insurance = 0; }
				if ($insurable<=50)  {$uinsurance=constant('MODULE_SHIPPING_USPS_INS1_' . $vendors_id);}
	                else if ($insurable<=100) {$uinsurance=constant('MODULE_SHIPPING_USPS_INS2_' . $vendors_id);}
	            	else if ($insurable<=200) {$uinsurance=constant('MODULE_SHIPPING_USPS_INS3_' . $vendors_id);}
	                else if ($insurable<=300) {$uinsurance=constant('MODULE_SHIPPING_USPS_INS4_' . $vendors_id);}
	                else {$uinsurance = constant('MODULE_SHIPPING_USPS_INS4_' . $vendors_id) + ((ceil($insurable/100) -3) * constant('MODULE_SHIPPING_USPS_INS5_' . $vendors_id));}
				if (constant('MODULE_SHIPPING_USPS_DMSTC_INSURANCE_OPTION_' . $vendors_id) == 'True' && constant('MODULE_SHIPPING_USPS_INSURE_' . $vendors_id) == 'True')
					{$postage = $postage + max($insurance, $uinsurance);}
				elseif (constant('MODULE_SHIPPING_USPS_INSURE_' . $vendors_id) == 'True') 
					{$postage = $postage + $uinsurance;}	                
				elseif (constant('MODULE_SHIPPING_USPS_DMSTC_INSURANCE_OPTION_' . $vendors_id) == 'True' && $insurance > 0)
					{$postage = $postage + $insurance;}
				if ((constant('MODULE_SHIPPING_USPS_DMST_DEL_CONF_' . $vendors_id) == 'True') && (preg_match('/<\/ServiceName><Available>true<\/Available><AvailableOnline>true<\/AvailableOnline><Price>0.80<\/Price><PriceOnline>/', $response[$i])))
					{	$del_conf = preg_match('/<\/ServiceName><Available>true<\/Available><AvailableOnline>true<\/AvailableOnline><Price>0.80<\/Price><PriceOnline>(.*)<\/PriceOnline>/', $response[$i], $regs);
						$del_conf = $regs[1];
						$postage = $postage + $del_conf;
					}
				if ((constant('MODULE_SHIPPING_USPS_DMST_SIG_CONF_' . $vendors_id) == 'True') && ($this->sig_conf_thresh <= $order->info['subtotal'])&& (preg_match('/<\/ServiceName><Available>true<\/Available><AvailableOnline>true<\/AvailableOnline><Price>2.35<\/Price><PriceOnline>/', $response[$i])))
					{	$sig_conf = preg_match('/<\/ServiceName><Available>true<\/Available><AvailableOnline>true<\/AvailableOnline><Price>2.35<\/Price><PriceOnline>(.*)<\/PriceOnline>', $response[$i], $regs);
						$sig_conf = $regs[1];
						$postage = $postage + $sig_conf;
					}
			switch ($service) 
			{
				case 'First-Class Mail':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[0];
					break;
				case 'First-Class Mail Letter':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[0];
					break;
				case 'First-Class Mail Large Envelope':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[0];
					break;
				case 'First-Class Mail Package':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
						if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[0];
					break;
				case 'Media Mail':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[1];
					break;
				case 'Parcel Post':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[2];
					break;
				case 'Priority Mail':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[3];
					break;
				case 'Priority Mail Flat Rate Envelope':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[4];
					break;
				case 'Priority Mail Small Flat Rate Box':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[5];
					break;
				case 'Priority Mail Medium Flat Rate Box':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[6];
					break;
				case 'Priority Mail Large Flat Rate Box':
					$time = preg_match('/<Days>(.*)<\/Days>/', $transresp[$service], $tregs);
					$time = $tregs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($time + $this->processing);}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAY;
					} else {
					$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;
					}
					$postage = $postage + $this->dmstc_handling[7];
					break;
				case 'Express Mail':
					$time = preg_match('/<CommitmentDate>(.*)<\/CommitmentDate>/', $response[$i], $regs);
					$time = $regs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($time == 'Overnight to many areas' && $this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($this->processing + 1) . MODULE_SHIPPING_USPS_TEXT_DAYS;;}
					elseif ($time == 'Overnight to many areas') {$time = '---' . $time;}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {$time .= MODULE_SHIPPING_USPS_TEXT_DAY;}
					elseif ($time > '2' && $time <= '9') {$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;}
					$postage = $postage + $this->dmstc_handling[8];
					break;
				case 'Express Mail Flat Rate Envelope':
					$time = preg_match('/<CommitmentDate>(.*)<\/CommitmentDate>/', $response[$i], $regs);
					$time = $regs[1];
					if(strlen($time) > 2){
						$time = $this->days_diff($time);
					}
					if ($time == 'Overnight to many areas' && $this->processing > 0) {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . ($this->processing + 1) . MODULE_SHIPPING_USPS_TEXT_DAYS;;}
					elseif ($time == 'Overnight to many areas') {$time = '---' . $time;}
					else {$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $time;}
					if ($time == '1') {$time .= MODULE_SHIPPING_USPS_TEXT_DAY;}
					elseif ($time > '2' && $time <= '9') {$time .= MODULE_SHIPPING_USPS_TEXT_DAYS;}
					$postage = $postage + $this->dmstc_handling[8];
					break;
			}
			if (($dispinsurance) && ((constant('MODULE_SHIPPING_USPS_DMSTC_INSURANCE_OPTION_' . $vendors_id) == 'True' && $insurance > 0) || (constant('MODULE_SHIPPING_USPS_INSURE_' . $vendors_id) == 'True' && $uinsurance > 0)))
			{$dispinsure[$service] = '<br>' . MODULE_SHIPPING_USPS_TEXT_INSURED . '$' . tep_round_up($insurable, 2);}
			else {$dispinsure[$service] = '';}			
			if (($transit) && ($time != ''))
			{
				$transittime[$service] = ' (' . $time . ')';
			}
			else {$transittime[$service] = '';}			
			$rates[] = array($service => $postage);
			$rates_sorter[] = $postage;
			}
			}
			}

			/////////////////////////////////////////
			////////// END DOMESTIC RESPONSE ////////
			/////////////////////////////////////////
		
			else
			
			/////////////////////////////////////////
			////// START INTERNATIONAL RESPONSE /////
			/////////////////////////////////////////

			{
			if (preg_match('/<Error>/', $response[0])) {
			$number = preg_match('/<Number>(.*)<\/Number>/', $response[0], $regs);
			$number = $regs[1];
			$description = preg_match('/<Description>(.*)<\/Description>/', $response[0], $regs);
			$description = $regs[1];
			return array('id' => $this->code, 'module' => $this->title, 'error' => $number . ' - ' . $description);
			} else {
			$body = $response[0];
			$services = array();
			while (true) {
			if ($start = strpos($body, '<Service ID=')) {
			$body = substr($body, $start);
			$end = strpos($body, '</Service>');
			$services[] = substr($body, 0, $end+10);
			$body = substr($body, $end+9);
			} else {
			break;
			}
			}
			$allowed_types = array();
			foreach( explode(", ", constant('MODULE_SHIPPING_USPS_INTL_TYPES_' . $vendors_id)) as $value ) $allowed_types[$value] = $this->intl_types[$value];
			$size = sizeof($services);
			for ($i=0, $n=$size; $i<$n; $i++) {
			if (strpos($services[$i], '<Postage>')) {
			$service = preg_match('/<SvcDescription>(.*)<\/SvcDescription>/', $services[$i], $regs);
			$service = $regs[1];
			$CMP = preg_match('/<CommercialPostage>(.*)<\/CommercialPostage>/', $services[$i], $regs);
			$CMP = $regs[1];
			if ($CMP == 0)
			{
			$postage = preg_match('/<Postage>(.*)<\/Postage>/', $services[$i], $regs);
			$postage = $regs[1];
			}
			else{
			switch (constant('MODULE_SHIPPING_USPS_INTL_RATE_' . $vendors_id)) {
			case 'Internet':
			if (preg_match('/<CommercialPostage>/', $services[$i]))
			{
			$postage = preg_match('/<CommercialPostage>(.*)<\/CommercialPostage>/', $services[$i], $regs);
			$postage = $regs[1];
			}
			else
   			{
			$postage = preg_match('/<Postage>(.*)<\/Postage>/', $services[$i], $regs);
			$postage = $regs[1];
			}
			break;
			case 'Retail':
			$postage = preg_match('/<Postage>(.*)<\/Postage>/', $services[$i], $regs);
			$postage = $regs[1];
			break;
			}
			}
			$postage = $postage + $this->intl_handling[0];
			if (preg_match('/Insurance<\/ServiceName><Available>True/', $services[$i]))
				{	$iinsurance = preg_match('/Insurance<\/ServiceName><Available>True<\/Available><Price>(.*)<\/Price>/', $services[$i], $regs);
					$iinsurance = $regs[1];}
				else {$iinsurance = 0;}			
			if ($insurable<=50)  {$iuinsurance=constant('MODULE_SHIPPING_USPS_INS1_' . $vendors_id);}
                else if ($insurable<=100) {$iuinsurance=constant('MODULE_SHIPPING_USPS_INS2_' . $vendors_id);}
            	else if ($insurable<=200) {$iuinsurance=constant('MODULE_SHIPPING_USPS_INS3_' . $vendors_id);}
                else if ($insurable<=300) {$iuinsurance=constant('MODULE_SHIPPING_USPS_INS4_' . $vendors_id);}
                else {$iuinsurance = constant('MODULE_SHIPPING_USPS_INS4_' . $vendors_id) + ((ceil($insurable/100) -3) * constant('MODULE_SHIPPING_USPS_INS5_' . $vendors_id));}
			if (constant('MODULE_SHIPPING_USPS_INTL_INSURANCE_OPTION_' . $vendors_id) == 'True' && constant('MODULE_SHIPPING_USPS_INSURE_' . $vendors_id) == 'True')
					{$postage = $postage + max($iinsurance, $iuinsurance);}
				elseif (constant('MODULE_SHIPPING_USPS_INSURE_' . $vendors_id) == 'True') 
					{$postage = $postage + $iuinsurance;}	                
				elseif (constant('MODULE_SHIPPING_USPS_INTL_INSURANCE_OPTION_' . $vendors_id) == 'True')
					{$postage = $postage + $iinsurance;}	                
			$time = preg_match('/<SvcCommitments>(.*)<\/SvcCommitments>/', $services[$i], $tregs);
			$time = MODULE_SHIPPING_USPS_TEXT_ESTIMATED . $tregs[1];
			$time = preg_replace('/Weeks$/', MODULE_SHIPPING_USPS_TEXT_WEEKS, $time);
			$time = preg_replace('/Days$/', MODULE_SHIPPING_USPS_TEXT_DAYS, $time);
			$time = preg_replace('/Day$/', MODULE_SHIPPING_USPS_TEXT_DAY, $time);
			if( !in_array($service, $allowed_types) ) continue;
			if (isset($this->service) && ($service != $this->service) ) {
			continue;
			}
			if (($dispinsurance) && ((constant('MODULE_SHIPPING_USPS_INTL_INSURANCE_OPTION_' . $vendors_id) == 'True' && $iinsurance > 0) || (constant('MODULE_SHIPPING_USPS_INSURE_' . $vendors_id) == 'True' && $iuinsurance > 0)))
			{$dispinsure[$service] = '<br>' . MODULE_SHIPPING_USPS_TEXT_INSURED . '$' . tep_round_up($insurable, 2);}
			else {$dispinsure[$service] = '';}			
			if (($transit) && ($time != ''))
			{$transittime[$service] = ' (' . $time . ')';}
			else {$transittime[$service] = '';}			
			$rates[] = array($service => $postage);
			$rates_sorter[] = $postage;
			}
			}
			}
			}
			asort($rates_sorter);
			$sorted_rates = array();
			foreach (array_keys($rates_sorter) as $key){
			$sorted_rates[] = $rates[$key];
			}
		
			/////////////////////////////////////////
			/////// END INTERNATIONAL RESPONSE //////
			/////////////////////////////////////////

	return ((sizeof($sorted_rates) > 0) ? $sorted_rates : false);
	}

	/////////////////////////////////////////
	/////////// END RATE RESPONSE////////////
	/////////////////////////////////////////
	
	function days_diff($date,$from="now"){
		$day = 60*60*24;	
		$days_diff = round(($from - $date)/$day);
		if($days_diff <=0){
			$days_diff = 1;
		}
		return $days_diff;
	}
	
// Round up function for non whole numbers by GREG DEETH

// The value for the precision variable determines how many digits after the decimal and rounds the last digit up to the next value

// Precision = 0 -> xx.xxxx = x+

// Precision = 1 -> xx.xxxx = xx.+

// Precision = 2 -> xx.xxxx = xx.x+

  function round_up($number, $precision) {

            $number_whole = '';

            $num_left_dec = 0;

            $num_right_dec = 0;

            $num_digits = strlen($number);

            $number_out = '';

            $i = 0;

            while ($i + 1 <= strlen($number))

            {

                        $current_digit = substr($number, $i, ($i + 1) - $num_digits);

                        if ($current_digit == '.') {

                                    $i = $num_digits + 1;

                                    $num_left_dec = strlen($number_whole);

                                    $num_right_dec = ($num_left_dec + 1) - $num_digits;

                        } else {

                                    $number_whole = $number_whole . $current_digit;

                                    $i = $i + 1;

                        }

            }

            if ($num_digits > 3 && $precision < ($num_digits - $num_left_dec - 1) && $precision >= 0) {

                        $i = $precision;

                        $addable = 1;

                        while ($i > 0) {

                                    $addable = $addable * .1;

                                    $i = $i - 1;

                        }

                        $number_out = substr($number, 0, $num_right_dec + $precision) + $addable;

            } else {

                        $number_out = $number;

            }

            return $number_out;

  }
  

}

	
/////////////////////////////////////////
////////// Ends USPS Class ///////////
/////////////////////////////////////////
?>