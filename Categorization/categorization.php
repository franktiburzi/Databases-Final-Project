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
    <li><a href="categorization.php" class="active">Categorization</a></li>
  </ul>
EOBODY;

  $body = $topPart;
  $categories = CategoryNames();
  $dagrs = DAGRNames();
  $categorynameerror = FALSE;

  if (isset($_POST["createcategory"])) {
    if(!isset($_POST["newcategory"]) || $_POST["newcategory"] == "" || array_search($_POST["newcategory"],$categories) !== FALSE) {
      $categorynameerror = TRUE;
    }
    else {
      $db_connection = new mysqli("localhost", "root", "", "mmda");
      if ($db_connection->connect_error) {
        die($db_connection->connect_error);
      }
      $guid = get_guid();
      $result = $db_connection->query("INSERT INTO `categories`
        VALUES('{$guid}','{$_POST["newcategory"]}');");

      if(isset($_POST["children"])) {
        foreach($_POST["children"] as $c) {
          $result = $db_connection->query("SELECT GUID FROM `categories` WHERE NAME='{$c}';");
          $result->data_seek(0);
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $cguid = $row["GUID"];
          $result = $db_connection->query("INSERT INTO `belongs_to_category` VALUES ('{$guid}','{$cguid}');");
        }
      }
      $body .= "<h2>Congratulations, ".$_POST["newcategory"]." is now a category.</h2>";
    }

  }
  else if(isset($_POST["categorize"])) {
    $db_connection = new mysqli("localhost", "root", "", "mmda");
    if ($db_connection->connect_error) {
      die($db_connection->connect_error);
    }
    $result = $db_connection->query("SELECT GUID FROM `dagr` WHERE NAME='{$_POST["dagr"]}';");
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $dguid = $row["GUID"];

    $result = $db_connection->query("SELECT GUID FROM `categories` WHERE NAME='{$_POST["categoryinsert"]}';");
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $guid = $row["GUID"];

    $result = $db_connection->query("INSERT INTO `belongs_to_category` VALUES ('{$guid}','{$dguid}');");
    $body .= "<h2>Congratulations, ".$_POST["dagr"]." is now categorized as ".$_POST["categoryinsert"].".</h2>";
  }

if((!isset($_POST["createcategory"]) && !isset($_POST["categorize"])) || $categorynameerror)
  {$body .= <<<EOBODY
    <form action="categorization.php" method="POST">
    <h2>Create a Category:</h2>
    <div class="headertext">Category Name: </div>
    <input type="text" class="nametext" name="newcategory">
    &emsp;
EOBODY;
  if (count($categories) > 0){
    $body .= <<<EOBODY
    <div class="headertext">Categories to Inherit: </h2>
    <select name="children[]" multiple="multiple">
EOBODY;
  foreach($categories as $c) {
  $body .= "<option value='{$c}'>{$c}</option>";
  }
  $body .= <<<EOBODY
    </select>
EOBODY;
  }

  $body .= <<<EOBODY
    <br>
    <br>
    <input type="submit" id="newcategorybutton" value="Create Category" name="createcategory">
    </form>
    <br>
    <br>
EOBODY;

  if(count($categories) > 0 && count($dagrs) > 0){
    $body .= <<<EOBODY
    <form action="categorization.php" method="POST">
    <h2>Add DAGR to Existing Category: </h2>
    <br>
    <div class="headertext">DAGR: </div>
    <select name="dagr">
EOBODY;
    foreach($dagrs as $d) {
    $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
      </select>
      &emsp;
      <div class="headertext">Category: </div>
      <select name="categoryinsert">
EOBODY;
    foreach($categories as $c) {
    $body .= "<option value='{$c}'>{$c}</option>";
    }
    $body .= <<<EOBODY
      </select>
      <br>
      <br>
      <input type="submit" id="insertdagr" value="Categorize DAGR" name="categorize">
EOBODY;
  }}

  if($categorynameerror) {
    $body .= "<h2>Please enter a valid category name</h2>";
  }
  echo generatePage($body, "categorization.css", "Categorization");
?>
