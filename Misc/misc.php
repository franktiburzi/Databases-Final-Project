<?php
  // Starts the session
  session_start();

  // Includes
  require_once("../support.php");
  require_once("../dbLogin.php");
  require_once("../meta_helpers.php");
  require_once("../meta_getters.php");

  $topPart = <<<EOBODY
  <ul>
    <li><a href="../main.php">Home/About</a></li>
    <li><a href="../Insert/insert.php">Insert and Bulk Insert</a></li>
    <li><a href="../QueryExecutioner/queryexecutioner.php">Query Executioner</a></li>
    <li><a href="../HTMLComponents/htmlcomponents.php">HTML Components</a></li>
    <li><a href="../Categorization/categorization.php">Categorization</a></li>
    <li><a href="../Deletion/deletion.php">Deletion</a></li>
    <li><a href="misc.php" class="active">Misc. Tasks</a></li>
  </ul>
EOBODY;

  $body = $topPart;

  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }

  $orphans = [];
  $sterile = [];

  $result = $db_connection->query("SELECT DISTINCT NAME FROM `dagr` WHERE GUID NOT IN (SELECT CHILD_GUID FROM parent_relations);");
  $num_rows = $result->num_rows;
  for ($row_index = 0; $row_index < $num_rows; $row_index++) {
    $result->data_seek($row_index);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    array_push($orphans,$row['NAME']);
  }

  $result = $db_connection->query("SELECT DISTINCT NAME FROM `dagr` WHERE GUID NOT IN (SELECT PARENT_GUID FROM parent_relations);");
  $num_rows = $result->num_rows;
  for ($row_index = 0; $row_index < $num_rows; $row_index++) {
    $result->data_seek($row_index);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    array_push($sterile,$row['NAME']);
  }

  $body .= "<h2>Orphan/Sterile Reports</h2>";
  $body .= <<<EOBODY
    <table>
    <tr><th>Orphan DAGR(s)</th></tr>
EOBODY;
  foreach($orphans as $o) {
    $body .= "<tr><td>{$o}</td></tr>";
  }
  $body .= "</table>&emsp;";
  $body .= <<<EOBODY
    <table>
    <tr><th>Sterile DAGR(s)</th></tr>
EOBODY;
  foreach($sterile as $o) {
    $body .= "<tr><td>{$o}</td></tr>";
  }
  $body .= "</table>";
  $body .= "<hr>";
  $body .= "<h2>Reach Query</h2>";
  $body .= "<form action=\"misc.php\" method=\"POST\">";
  $body .= "<div class=\"querytext\">Select a DAGR:</div>&emsp;";
  $body .= "<select name=\"dagr\">";
  $dagrs = DAGRNames();
  foreach($dagrs as $d) {
    $selected = "";
    if(isset($_POST["dagr"]) && $_POST["dagr"] == $d) {
      $selected = "selected";
    }
    $body .= "<option value=\"{$d}\" {$selected}>{$d}</option>";
  }
  $body .= "<select>&emsp;";
  $body .= "<input type=\"submit\" class=\"button\" name=\"reach\" value=\"See ancestors/descendants\">";

  if(isset($_POST["reach"])) {
    $guid = getDAGRGUID($_POST["dagr"]);
    $ancestors = [];
    $descendants = [];

    $result = $db_connection->query("SELECT DISTINCT d.NAME FROM parent_relations p, dagr d WHERE CHILD_GUID='{$guid}' AND PARENT_GUID=d.GUID;");
    for ($row_index = 0; $row_index < $num_rows; $row_index++) {
      $result->data_seek($row_index);
      $row = $result->fetch_array(MYSQLI_ASSOC);

      array_push($ancestors, $row["NAME"]);
    }

    $result = $db_connection->query("SELECT DISTINCT d.NAME FROM parent_relations p, dagr d WHERE PARENT_GUID='{$guid}' AND CHILD_GUID=d.GUID;");
    for ($row_index = 0; $row_index < $num_rows; $row_index++) {
      $result->data_seek($row_index);
      $row = $result->fetch_array(MYSQLI_ASSOC);

      array_push($descendants, $row["NAME"]);
    }
    $body .= <<<EOBODY
      <br>
      <br>
      <br>
      <table>
      <tr><th>Ancestor DAGR(s)</th></tr>
EOBODY;
    foreach($ancestors as $o) {
      $body .= "<tr><td>{$o}</td></tr>";
    }
    $body .= "</table>&emsp;";
    $body .= <<<EOBODY
      <table>
      <tr><th>Descendant DAGR(s)</th></tr>
EOBODY;
    foreach($descendants as $o) {
      $body .= "<tr><td>{$o}</td></tr>";
    }
    $body .= "</table></form>";

  }

  $body .= "<hr>";
  $body .= "<div class=\"querytext\">Remove Duplicate Content: </div>&emsp;";
  $body .= "<form action=\"misc.php\" method=\"POST\">";
  $body .= "<input type=\"submit\" class=\"button\" name=\"removedups\" value=\"Go\"></form>";

  if(isset($_POST["removedups"])) {
    $result = $db_connection->query("SELECT i.GUID as `first`, ii2.GUID as `second` FROM image i, image i2 WHERE i.GUID > i2.GUID AND i.DAGR_ID=i2.DAGR_ID AND i.PATH_TO_RESOURCE=i2.PATH_TO_RESOURCE;");
    if($result->num_rows > 0) {
      $result->data_seek(0);
      $row = $result->fetch_array(MYSQLI_ASSOC);

      $guid = $row[]
    }
  }

  echo generatePage($body, "misc.css", "Misc. Tasks");
?>
