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
    <li><a href="../HTMLComponents/htmlcomponents.php" class="active">HTML Components</a></li>
    <li><a href="../Categorization/categorization.php">Categorization</a></li>
    <li><a href="../Deletion/deletion.php">Deletion</a></li>
    <li><a href="../Misc/misc.php">Misc. Tasks</a></li>
  </ul>
EOBODY;

  $body = $topPart;

  $body .= "<h2>View an HTML file's components:</h2>";
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }

  $result = $db_connection->query("SELECT DISTINCT NAME FROM `html`;");

  $arr = [];
  $num_rows = $result->num_rows;
  for ($row_index = 0; $row_index < $num_rows; $row_index++) {
    $result->data_seek($row_index);
    $row = $result->fetch_array(MYSQLI_ASSOC);

    array_push($arr, $row["NAME"]);
  }

  $body .= "<form action=\"htmlcomponents.php\" method=\"POST\">";
  $body .= "<select name=\"name\">";
  foreach($arr as $name) {
    $selected = "";
    if(isset($_POST["name"]) && $_POST["name"] == $name) {
      $selected = "selected";
    }
    $body .= "<option value='{$name}' {$selected}>{$name}</option>";
  }
  $body .= "</select>&emsp;";
  $body .= "<input type=\"submit\" class=\"button\" value=\"Go\" name=\"submit\"></form>";

  if(isset($_POST["submit"])) {
    $result = $db_connection->query("SELECT GUID FROM `html` WHERE NAME='{$_POST["name"]}';");
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $guid = $row["GUID"];

    $result = $db_connection->query("SELECT h.* FROM html h, html_component hc WHERE hc.HOST_GUID='{$guid}' AND h.GUID=hc.COMPONENT_GUID;");
    $body .= "<hr>";
    $table = <<<EOBODY
    <div class="centered">
    <table align="center"><tr>
    <th>Host Name</th>
    <th>Component Name</th>
    <th>File Size</th>
    <th>Keywords</th>
    <th>Date Created</th>
    <th>Date Entered</th>
    <th class="path">Path</th>
    </tr>
EOBODY;

    $num_rows = $result->num_rows;
    for ($row_index = 0; $row_index < $num_rows; $row_index++) {
      $result->data_seek($row_index);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $datecreated = gmdate("Y-m-d", $row['DATE_CREATED']);
      $dateentered = gmdate("Y-m-d", $row['DATE_ENTERED']);
      $table .= <<<EOBODY
      <tr>
      <td>{$_POST["name"]}</td>
      <td>{$row['NAME']}</td>
      <td>{$row['FILE_SIZE']}</td>
      <td>{$row['KEYWORDS']}</td>
      <td>{$datecreated}</td>
      <td>{$dateentered}</td>
      <td>{$row['PATH_TO_RESOURCE']}</td>
      </tr>
EOBODY;
    }

    $table .= "</table>";
    $body .= $table;
  }

  echo generatePage($body, "html.css", "HTML Components");

?>
