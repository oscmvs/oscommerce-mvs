<?php
/*
    $Id: packaging.php,v 1.01 2004/11/16 torinwalker Exp $
    
    Copyright (c) 2003 Torin Walker
    
    This program is free software; you can redistribute it and/or modify it under the terms
    of the GNU General Public License as published by the Free Software Foundation; either
    version 2 of the License, or (at your option) any later version.
    
    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License along with this program;
    If not, you may obtain one by writing to and requesting one from
    
    The Free Software Foundation, Inc.,
    59 Temple Place, Suite 330,
    Boston, MA 02111-1307 USA
*/

require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
      </table>
        </td>
<!-- body_text //-->
        <td width="100%" valign="top">
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="dataTableContent" width="75%" valign="top">
<?php
$activeid = $_GET['id'];

//********** New Package
if(($_POST['name'] != "" && $_POST["Action"] == "newpackage") || ($_POST['id'] != "" && $_POST["Action"] == "updatepackage")) {
    if (number_format(trim($_POST['length']), 2, '.', '') <= 0) {
        $error = MIN_LENGTH_NOT_MET;
    } else if (number_format(trim($_POST['width']), 2, '.', '') <= 0) {
        $error = MIN_WIDTH_NOT_MET;
    } else if (number_format(trim($_POST['height']), 2, '.', '') <= 0) {
        $error = MIN_HEIGHT_NOT_MET;
    } else if (number_format(trim($_POST['empty_weight']), 2, '.', '') < 0) {	
        $error = MIN_EMPTY_WEIGHT_NOT_MET;
    } else if (number_format(trim($_POST['max_weight']), 2, '.', '') < 0) {	
        $error = MIN_MAX_WEIGHT_NOT_MET;
    } else {
        $sql_data_array = array(
            'package_name' => $_POST['name'],
            'package_description' => $_POST['description'],
            'package_length' => $_POST['length'],
            'package_width' => $_POST['width'],
            'package_height' => $_POST['height'],
            'package_empty_weight' => $_POST['empty_weight'],
            'package_max_weight' => $_POST['max_weight'],
            'package_cost' => $_POST['cost']
        );
        if ($_POST["Action"] == "newpackage") {
            tep_db_perform(TABLE_PACKAGING, $sql_data_array);
        } else {
            tep_db_perform(TABLE_PACKAGING, $sql_data_array, "update", "package_id = '" . $_POST['id'] . "'");
        }
    }
}

//********** Delete Package
if($_GET['id'] != "" && $_GET["Action"] == "deletepackage") {
    tep_db_query("delete from " . TABLE_PACKAGING . " where package_id = '" . $_GET['id'] . "'");
}

// ********* Display Packages
DisplayPackages($activeid, $error);
switch ($Action) {
    case "shownewpackageform":
    showNewPackageForm();
    break;
    case "showupdatepackageform":
    showUpdatePackageForm();
    break;
    case "showconfirmdeletepackageform":
    showConfirmDeletePackageForm();
    break;
    case "":
    default:
    showPackageInfoForm();
    break;
}

//*******************
function getPackages() {
    $packages = array();
    $packages_query = tep_db_query("select * from " . TABLE_PACKAGING . " order by package_cost;");
    while ($package = tep_db_fetch_array($packages_query)) {
        $packages[] = array(
            'id' => $package['package_id'],
            'name' => $package['package_name'],
            'description' => $package['package_description'],
            'length' => $package['package_length'],
            'width' => $package['package_width'],
            'height' => $package['package_height'],
            'empty_weight' => $package['package_empty_weight'],
            'max_weight' => $package['package_max_weight'],
            'cost' => $package['package_cost']
        );
    }
    return $packages;
}

//************************  DisplayPackages()
// shows the main menu, lists the admins
function DisplayPackages($activeid,$error) {
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2" width="100%">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent"><?php echo HEADING_NAME; ?></td>
        <td class="dataTableHeadingContent" align="left"><?php echo HEADING_DESCRIPTION; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo HEADING_LENGTH; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo HEADING_WIDTH; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo HEADING_HEIGHT; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo HEADING_EMPTY_WEIGHT; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo HEADING_MAX_WEIGHT; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo HEADING_COST; ?></td>
        <td class="dataTableHeadingContent" align="left"><?php echo HEADING_ACTION; ?></td>
      </tr>

    <?php
    $packages = getPackages();
    if (count($packages) == 0) {
        echo                         '<tr><td colspan="8">' . NO_PACKAGES_DEFINED . '</td></tr>';
    }
    if ($error != "") {
        echo '<SPAN class="errorText">'.$error.'</SPAN>';
    }
    for ($i = 0; $i < count($packages); $i++) {
        if (($_GET["Action"] != "shownewpackageform") && ($error == "")) {
            if ($activeid == "") {
                $activeid = $packages[0]['id'];
            }
        }
        if ($activeid == $packages[$i]['id']) {
            echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_PACKAGING, 'id=' . $packages[$i]['id'] ).'\'">' . "\n";
        } else {
            echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_PACKAGING, 'id=' .$packages[$i]['id'] ). '\'">' . "\n";
        }
        echo '<td class="dataTableContent" align="left"><br>' . $packages[$i]['name'] . '</td>';
        echo '<td class="dataTableContent" align="left"><br>' . $packages[$i]['description'] . '</td>';
        echo '<td class="dataTableContent" align="center"><br>' . $packages[$i]['length'] . '</td>';
        echo '<td class="dataTableContent" align="center"><br>' . $packages[$i]['width'] . '</td>';
        echo '<td class="dataTableContent" align="center"><br>' . $packages[$i]['height'] . '</td>';
        echo '<td class="dataTableContent" align="center"><br>' . $packages[$i]['empty_weight'] . '</td>';
        echo '<td class="dataTableContent" align="center"><br>' . $packages[$i]['max_weight'] . '</td>';
        echo '<td class="dataTableContent" align="center"><br>' . $packages[$i]['cost'] . '</td>';

        if ($activeid == $packages[$i]['id'] ) { 
            echo '<td>' . tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
        } else { 
            echo '<td><a href="' . tep_href_link(FILENAME_PACKAGING, 'id=' . $packages[$i]['id'] ). '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', ICON_INFO) . '</a>'; 
        }
    }
    echo '</td></tr></table><br>';
    echo '<a href="' . tep_href_link( FILENAME_PACKAGING ) . '?Action=shownewpackageform&id=' . $_GET['id'] . '">'.tep_image_button('button_new_package.gif', NEW_ADMIN).'</a>&nbsp;';
    echo '<a href="' . tep_href_link( FILENAME_PACKAGING ) . '?Action=showupdatepackageform&id=' . $_GET['id'] . '">'.tep_image_button('button_edit.gif', NEW_ADMIN).'</a>&nbsp;' ;	 
    echo '<a href="' . tep_href_link( FILENAME_PACKAGING ) . '?Action=showconfirmdeletepackageform&id=' . $_GET['id'] . '">'.tep_image_button('button_delete.gif', NEW_ADMIN).'</a>' ;	 
    echo '</td><td class="infoBoxContent" valign="top">';
}

//******************************   showNewPackageForm()
// Show the form to create a new package
function showNewPackageForm() {
    $packages = getPackages();
    $cost = 0;
    for ($i = 0; $i < count($packages); $i++) {
        if ($packages[$i]['cost'] > $cost) {
            $cost = $packages[$i]['cost'] + 1;
        }
    }

    echo "<table cellspacing='0' width='100%' cellpadding='0'> <tr><td colspan='2' class='infoBoxHeading'>". CREATE_NEW_PACKAGE."</td></tr></table>";
    
    echo tep_draw_form("newpackage", FILENAME_PACKAGING);
    echo tep_draw_hidden_field("Action", "newpackage");
    echo '<table><tr><td class="infoBoxContent"><b>'.HEADING_NAME.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_NAME_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("name").'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_DESCRIPTION.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_DESCRIPTION_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("description").'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_LENGTH.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_LENGTH_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("length").'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_WIDTH.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_WIDTH_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("width").'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_HEIGHT.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_HEIGHT_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("height").'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_EMPTY_WEIGHT.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_EMPTY_WEIGHT_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("empty_weight").'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_MAX_WEIGHT.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_MAX_WEIGHT_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("max_weight").'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_COST.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_COST_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("cost", $cost).'</td></tr>';
    echo '<tr><td colspan="2">'. tep_image_submit('button_confirm.gif', 'Save these values as a new package.') ;
    echo '&nbsp;&nbsp;<a href="' . tep_href_link( FILENAME_PACKAGING,'id='.$_GET["id"] ) . '">'.tep_image_button('button_cancel.gif', IMAGE_CANCEL) .'</A>' ;	 
    echo "</td></tr></table>" ;
    echo ("</form>");
}

//******************************   showUpdatePackageForm()
// Show the form to update a package
function showUpdatePackageForm() {
    $packages = getPackages();
    $activepackage = $packages[0];
    if ($_GET['id'] != "") {
        for ($i = 0; $i < count($packages); $i++) {
            if ($_GET['id'] == $packages[$i]['id']) {
                $activepackage = $packages[$i];
            }
        }
    }
    echo "<table cellspacing='0' width='100%' cellpadding='0'> <tr><td colspan='2' class='infoBoxHeading'>". UPDATE_PACKAGE."</td></tr></table>";
    echo tep_draw_form("updatepackage", FILENAME_PACKAGING.'?id='.$_GET['id']);
    echo tep_draw_hidden_field("Action", "updatepackage");
    echo tep_draw_hidden_field("id", $activepackage['id']);
    echo '<table><tr><td class="infoBoxContent"><b>'.HEADING_NAME.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_NAME_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("name", $activepackage['name']).'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_DESCRIPTION.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_DESCRIPTION_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("description", $activepackage['description']).'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_LENGTH.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_LENGTH_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("length", $activepackage['length']).'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_WIDTH.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_WIDTH_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("width", $activepackage['width']).'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_HEIGHT.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_HEIGHT_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("height", $activepackage['height']).'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_EMPTY_WEIGHT.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_EMPTY_WEIGHT_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("empty_weight", $activepackage['empty_weight']).'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_MAX_WEIGHT.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_MAX_WEIGHT_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("max_weight", $activepackage['max_weight']).'</td></tr>';
    echo '<tr><td class="infoBoxContent"><b>'.HEADING_COST.'</b></td></tr><tr><td class="infoBoxContent">'.HEADING_COST_TEXT.'</td></tr><tr><td class="infoBoxContent">'.tep_draw_input_field("cost", $activepackage['cost']).'</td></tr>';
    echo '<tr><td colspan="2">'. tep_image_submit('button_confirm.gif', 'Update the package with these values.') ;
    echo '&nbsp;&nbsp;<a href="' . tep_href_link( FILENAME_PACKAGING,'id='.$_GET["id"] ) . '">'.tep_image_button('button_cancel.gif', IMAGE_CANCEL) .'</a>' ;	 
    echo "</td></tr></table>" ;
    echo ("</form>");
}

//*************************** showConfirmDeletePackageForm()
// Shows the form to confirm package deletion
function showConfirmDeletePackageForm() {
    $packages = getPackages();
    $package_name = "";
    for ($i = 0; $i < count($packages); $i++) {
        if ($packages[$i]['id'] == $_GET['id']) {
            $package_name = $packages[$i]['name'];
        }
    }
    echo "<table cellspacing='0' width='100%' cellpadding='0'> <tr><td colspan='2' class='infoBoxHeading'>". DELETE_PACKAGE."</td></tr></table>";
    echo '<table cellpadding="5"><tr><td class="infoBoxContent">'.CONFIRM_DELETE.'</td></tr>' ;
    echo "<tr><td>".$package_name."</td></td>";
    echo "<tr><td>";
    //echo tep_image_submit('button_confirm.gif', 'ok') ;
    echo '<a href="' . tep_href_link( FILENAME_PACKAGING,'Action=deletepackage&id='.$_GET["id"] ) . '">'.tep_image_button('button_confirm.gif', IMAGE_CONFIRM) .'</a>' ;	 
    echo '&nbsp;&nbsp;<a href="' . tep_href_link( FILENAME_PACKAGING,'id='.$_GET["id"] ) . '">'.tep_image_button('button_cancel.gif', IMAGE_CANCEL) .'</a>' ;	 
    echo '</td></tr></table>';
}

//************************  ShowPackageInfo()
// Shows the info a package
function showPackageInfoForm() {
    $packages = getPackages();
    $activepackage = $packages[0];
    $activeid = $_GET["id"];
    if ($activeid != "") {
        for ($i = 0; $i < count($packages); $i++) {
            if ($activeid == $packages[$i]['id']) {
                $activepackage = $packages[$i];
            }
        }
    }
    if ($error != "") {
        echo '<SPAN class="errorText">'.$error.'</SPAN>';
    }

    echo                         "<table cellspacing='0' width='100%' cellpadding='0'><tr><td colspan='2' class='infoBoxHeading'>". HEADING_INFO ."</td></tr></table>";
    if (count($packages) != 0) {
        echo '<table>';
        echo '<tr><td class="infoBoxContent"><b>'.    HEADING_NAME    .'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['name'].'</td></tr>';
        echo '<tr><td class="infoBoxContent"><b>'. HEADING_DESCRIPTION.'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['description'].'</td></tr>';
        echo '<tr><td class="infoBoxContent"><b>'.   HEADING_LENGTH   .'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['length'].'</td></tr>';
        echo '<tr><td class="infoBoxContent"><b>'.   HEADING_WIDTH    .'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['width'].'</td></tr>';
        echo '<tr><td class="infoBoxContent"><b>'.   HEADING_HEIGHT   .'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['height'].'</td></tr>';
        echo '<tr><td class="infoBoxContent"><b>'.HEADING_EMPTY_WEIGHT.'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['empty_weight'].'</td></tr>';
        echo '<tr><td class="infoBoxContent"><b>'. HEADING_MAX_WEIGHT .'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['max_weight'].'</td></tr>';
        echo '<tr><td class="infoBoxContent"><b>'.    HEADING_COST    .'</b></td></tr><tr><td class="infoBoxContent">'.$activepackage['cost'].'</td></tr>';
        echo "</table>";
    }
    echo "";
}
?>
        </td>
      </tr>
    </table> 
  </td>
<!-- body_text_eof //--></tr>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>