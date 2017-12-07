<?php
require_once("parserhelp.php");
require_once("../meta_helpers.php");
require_once("../meta_getters.php");


set_time_limit(300);


/*this function will be used to insert an HTML document to the database,
  and insert all its components */
function insertParsedHtml($infoArr, $check, $dagID, $htmlHostGuid) {
  $deepHostGuid = get_guid();
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }
  $componentArr = array();
  $componentArr = parseHTMLurl($infoArr['path']);

  //return $componentArr;
  //print_r($infoArr);
  //echo "<br />";
  //echo "<pre>";
  //print_r($componentArr);
  //echo "<pre>";
  if ($check == 0) {
    $htmlHostGuid = $infoArr['guid'];
    $htmlHostDagrid = $db_connection->query("SELECT DAGR_ID FROM `html` WHERE GUID ='{$infoArr['guid']}';");
    $htmlHostDagrid->data_seek(0);
    $row = $htmlHostDagrid->fetch_array(MYSQLI_ASSOC);
    $dagID = $row["DAGR_ID"];
    //echo $htmlHostGuid;

    $htmlArr = array();
    foreach ($componentArr as $val) {
      if (isHTML($val)) {
        $htmlArr[] = $val;
      }
      $compInfo = insertUrlFileFromParse($val, $dagID);
      //echo "<br />";
      //print_r($compInfo);
      $db_connection->query("INSERT INTO `html_component` VALUES ('{$htmlHostGuid}','{$compInfo['guid']}');");
    }
  }
  else if ($check == 1) {
    $htmlArr = array();
    foreach ($componentArr as $val) {
      if (isHTML($val)) {
        $htmlArr[] = $val;
      }
      $compInfo = insertUrlFileFromParse($val, $dagID);
      //echo "<br />";
      //print_r($compInfo);
      $db_connection->query("INSERT INTO `html_component` VALUES ('{$htmlHostGuid}','{$compInfo['guid']}');");
    }
  }

  if ($check == 0) {
    htmlDeepParse($htmlArr, 1, $dagID, $htmlHostGuid, $htmlHostGuid);
  }
  //print_r($htmlArr);

}

function htmlDeepParse($htmlArr, $check, $dagID, $htmlHostGuid) {
  foreach ($htmlArr as $val) {
    $valArr = HTML_URL_metadata($val);
    insertParsedHtml($valArr, 1, $dagID, $htmlHostGuid);
  }
}


//$url = 'https://originative.co/';
//echo "<pre>";
//print_r(insertParsedHtml($url));
//echo "<pre>";
 ?>
