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
    <li><a href="deletion.php" class="active">Deletion</a></li>
    <li><a href="../Misc/misc.php">Misc. Tasks</a></li>
  </ul>
EOBODY;

  $body = $topPart;

if(!isset($_POST["finalize"]))
  {if(!isset($_POST["continue"])){
    $body .= "<h2>Choose a DAGR to delete:</h2>";
  }
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }

  $result = $db_connection->query("SELECT DISTINCT NAME FROM `dagr`;");

  $arr = [];
  $num_rows = $result->num_rows;
  for ($row_index = 0; $row_index < $num_rows; $row_index++) {
    $result->data_seek($row_index);
    $row = $result->fetch_array(MYSQLI_ASSOC);

    array_push($arr, $row["NAME"]);
  }
  if(!isset($_POST["continue"])){
    $body .= "<form action=\"deletion.php\" method=\"POST\">";
    $body .= "<select name=\"name\">";
    foreach($arr as $name) {
      $selected = "";
      if(isset($_POST["name"]) && $_POST["name"] == $name) {
        $selected = "selected";
      }
      $body .= "<option value='{$name}' {$selected}>{$name}</option>";
    }
    $body .= "</select>&emsp;";
    if(!isset($_POST["continue"])){
      $body .= "<input type=\"submit\" class=\"button\" value=\"Go\" name=\"submit\"></form>";
    }
  }

  if(isset($_POST["submit"])) {
      $_SESSION["dagrdeletename"] = $_POST["name"];
  }


  if(isset($_POST["submit"]) || isset($_POST["continue"])) {
    $body .= "<hr>";
    $body .= "<h2>Parent DAGRs that will be affected: </h2>";
    $body .= "<table class=\"parents\"><tr><th>Parent DAGR(s)</th></tr>";

    $result = $db_connection->query("SELECT GUID FROM `dagr` WHERE NAME='{$_SESSION["dagrdeletename"]}';");
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $guid = $row["GUID"];

    $result = $db_connection->query("SELECT d.NAME FROM dagr d, parent_relations p WHERE d.GUID=p.PARENT_GUID AND p.CHILD_GUID='{$guid}';");
    $num_rows = $result->num_rows;

    if($num_rows == 0) {
      $body .= "<tr><td>None</td></tr>";
    }
    for ($row_index = 0; $row_index < $num_rows; $row_index++) {
      $result->data_seek($row_index);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $body .= <<<EOBODY
      <tr>
      <td>-{$row['NAME']}</td>
      </tr>
EOBODY;
    }
    $body .= "</table><br><br>";
    if(!isset($_POST["continue"])){
      $body .= <<<EOBODY
        <form action="deletion.php" method="POST">
        <div class="querytext">Are you sure you would like to proceed?</div>
        <input type="submit" class="button" value="Continue" name="continue">
        </form>
EOBODY;
    }
  }

  if(isset($_POST["continue"])) {
    $body.= "<hr>";
    $body .= "<h2>Child DAGR(s) Deletion: </h2>";
    $body .= "<form action=\"deletion.php\" method=\"POST\"><table class=\"parents\"><tr><th>Child DAGR(s)</th></tr>";

    $result = $db_connection->query("SELECT GUID FROM `dagr` WHERE NAME='{$_SESSION["dagrdeletename"]}';");
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $guid = $row["GUID"];

    $result = $db_connection->query("SELECT d.NAME FROM dagr d, parent_relations p WHERE d.GUID=p.CHILD_GUID AND p.PARENT_GUID='{$guid}';");
    $num_rows = $result->num_rows;

    if($num_rows == 0) {
      $body .= "<tr><td>None</td></tr>";
    }

    for ($row_index = 0; $row_index < $num_rows; $row_index++) {
      $result->data_seek($row_index);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      $body .= <<<EOBODY
      <tr>
      <td>{$row['NAME']}</td>
      <td><input type="radio" name="child{$row['NAME']}" value="shallow" required> Shallow Delete</td>
      <td><input type="radio" name="child{$row['NAME']}" value="deep" required> Deep Delete</td>
      </tr>
EOBODY;
    }

    $body .= "</table><br><br>";
    $body .= <<<EOBODY
      <input type="submit" class="button" value="Finalize Deletion" name="finalize"></form>
EOBODY;

  }}

  if(isset($_POST["finalize"])) {
    $name = $_SESSION["dagrdeletename"];
    $db_connection = new mysqli("localhost", "root", "", "mmda");
    if ($db_connection->connect_error) {
      die($db_connection->connect_error);
    }
    $result = $db_connection->query("SELECT GUID FROM `dagr` WHERE NAME='{$name}';");
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $guid = $row["GUID"];

    $children = [];
    $result = $db_connection->query("SELECT d.NAME FROM dagr d, parent_relations p WHERE d.GUID=p.CHILD_GUID AND p.PARENT_GUID='{$guid}';");
    $num_rows = $result->num_rows;
    for ($row_index = 0; $row_index < $num_rows; $row_index++) {
      $result->data_seek($row_index);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      array_push($children,$row['NAME']);
    }

    deleteDAGR($guid);
    foreach($children as $c) {
      if($_POST["child{$c}"] == "deep") {
        $result = $db_connection->query("SELECT GUID FROM `dagr` WHERE NAME='{$c}';");
        $result->data_seek(0);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        deleteDAGR($row["GUID"]);
      }
    }
    $body .= "<h2>Congratulations, {$name} and selected children have been deleted.</h2>";
  }



  echo generatePage($body, "deletion.css", "Deletion");
?>
