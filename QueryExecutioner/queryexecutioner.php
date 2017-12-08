<?php
  session_start();

  require_once("../support.php");
  require_once("../dbLogin.php");
  require_once("../meta_helpers.php");
  require_once("../meta_getters.php");

  $topPart = <<<EOBODY
  <ul>
    <li><a href="../main.php">Home/About</a></li>
    <li><a href="../Insert/insert.php">Insert and Bulk Insert</a></li>
    <li><a href="queryexecutioner.php" class="active">Query Executioner</a></li>
    <li><a href="../HTMLComponents/htmlcomponents.php">HTML Components</a></li>
    <li><a href="../Categorization/categorization.php">Categorization</a></li>
    <li><a href="../Deletion/deletion.php">Deletion</a></li>
    <li><a href="../Misc/misc.php">Misc. Tasks</a></li>
  </ul>
EOBODY;

  $body = $topPart;
  $dagrs = DAGRNames();
  $categories = CategoryNames();
  $queryselection = "";
  $imagechecked = "";
  $textchecked = "";
  $audiochecked = "";
  $videochecked = "";
  $htmlchecked = "";
  $minsize = "value=\"0\"";
  $maxsize = "value=\"99999999999\"";
  $keyword = "";
  $startdateentered = "value=\"2017-12-01\"";
  $enddateentered = "value=\"2017-12-09\"";
  $startdatecreated = "value=\"2000-01-01\"";
  $enddatecreated = "value=\"2017-12-09\"";

  if(isset($_POST["filetype"])) {
    $values = $_POST["filetype"];

    if(array_search("text", $values) !== false) {
      $textchecked = "checked";
    }

    if(array_search("image", $values) !== false) {
      $imagechecked = "checked";
    }

    if(array_search("audio", $values) !== false) {
      $audiochecked = "checked";
    }

    if(array_search("video", $values) !== false) {
      $videochecked = "checked";
    }

    if(array_search("html", $values) !== false) {
      $htmlchecked = "checked";
    }
  }

  if(isset($_POST["minsize"])) {
    $minsize = "value=\"".$_POST["minsize"]."\"";
  }

  if(isset($_POST["maxsize"])) {
    $maxsize = "value=\"".$_POST["maxsize"]."\"";
  }

  if(isset($_POST["keyword"])) {
    $keyword = "value=\"".$_POST["keyword"]."\"";
  }

  if(isset($_POST["startdateentered"])) {
    $startdateentered = "value=\"".$_POST["startdateentered"]."\"";
  }

  if(isset($_POST["enddateentered"])) {
    $enddateentered = "value=\"".$_POST["enddateentered"]."\"";
  }

  if(isset($_POST["startdatecreated"])) {
    $startdatecreated = "value=\"".$_POST["startdatecreated"]."\"";
  }

  if(isset($_POST["enddatecreated"])) {
    $enddatecreated = "value=\"".$_POST["enddatecreated"]."\"";
  }

  $queryselection .= <<<EOBODY
    <form action="queryexecutioner.php" method="POST">
    <h2>Query Execution </h2>
    <br>
    <div class="querytext">Types of Files to Display: </div>
    <table>
    <tr>
    <td>
      <input type="checkbox" id="image" name="filetype[]" value="image" {$imagechecked}>
      <label for="image">Image</label>
    </td>
    <td>
      <input type="checkbox" id="text" name="filetype[]" value="text" {$textchecked}>
      <label for="text">Text</label>
    </td>
    <td>
      <input type="checkbox" id="audio" name="filetype[]" value="audio" {$audiochecked}>
      <label for="audio">Audio</label>
    </td>
    <td>
      <input type="checkbox" id="video" name="filetype[]" value="video" {$videochecked}>
      <label for="video">Video</label>
    </td>
    <td>
      <input type="checkbox" id="html" name="filetype[]" value="html" {$htmlchecked}>
      <label for="html">HTML</label>
    </td>
    </tr>
    </table>
    <br>
    <br>
    <div class="querytext">Search by DAGRs: </div>
    <select name="dagrs[]" multiple="multiple">
EOBODY;
  foreach($dagrs as $d) {
  $selected = "";
  if(isset($_POST["dagrs"]) && array_search($d,$_POST["dagrs"]) !== false) {
    $selected = "selected";
  }
  $queryselection .= "<option value='{$d}' {$selected}>{$d}</option>";
  }
  $queryselection .= <<<EOBODY
    </select>
    &ensp;
    <div class="querytext"> or by Category: </div>
    <select name="categories[]" multiple="multiple">
EOBODY;
  foreach($categories as $c) {
    $selected = "";
    if(isset($_POST["categories"]) && array_search($c,$_POST["categories"]) !== false) {
      $selected = "selected";
    }
  $queryselection .= "<option value='{$c}' {$selected}>{$c}</option>";
  }
  $queryselection .= <<<EOBODY
    </select>
    <br>
    <br>
    <br>
    <div class="querytext">Date Created: </div>
    <input type="date" class="textfield" name="startdatecreated" {$startdatecreated} required>
    &nbsp;<div class="querytext"> to </div>&nbsp;
    <input type="date" class="textfield" name="enddatecreated" {$enddatecreated} required>
    <br>
    <br>
    <br>
    <div class="querytext">Date Entered: </div>
    <input type="date" class="textfield" name="startdateentered" {$startdateentered} required>
    &nbsp;<div class="querytext"> to </div>&nbsp;
    <input type="date" class="textfield" name="enddateentered" {$enddateentered} required>
    <br>
    <br>
    <br>
    <div class="querytext">Size: </div>
    <input type="number" class="textfield" name="minsize" {$minsize} min="0" max = "99999999999" required>
    &nbsp;<div class="querytext"> to </div>&nbsp;
    <input type="number" class="textfield" name="maxsize" {$maxsize} min="0" max = "99999999999" required>
    <br>
    <br>
    <br>
    <div class="querytext">Keyword: </div>
    <input type="text" class="textfield" name="keyword" ${keyword}>
    <br>
    <br>
    <br>
    <div class="querytext">Order by: </div>
    <select name="order">
      <option value="dagrname">DAGR Name</option>
      <option value="NAME">File Name</option>
      <option value="FILE_SIZE">File Size</option>
      <option value="DATE_CREATED">Date Created</option>
      <option value="DATE_ENTERED">Date Entered</option>
      <option value="FILE_TYPE">File Type</option>
    </select>
    <br>
    <br>
    <input type="submit" id="submitquery" value="Execute Query" name="submitquery">
EOBODY;

  $body .= $queryselection;

  if(isset($_POST["submitquery"])) {
    if(!empty($_POST["dagrs"]) && !empty($_POST["categories"])) {
      $body .= "<h2>Please only select dagrs OR categories</h2>";
    }
    else if(empty($_POST["dagrs"]) && empty($_POST["categories"])) {
      $body .= "<h2>Please select DAGR(s) or Category(s)</h2>";
    }
    else {
      if($imagechecked == "checked") {
        $body .= "<hr>";
        $body .= "<h2 class=\"typeheader\">Images</h2>";

        $table = <<<EOBODY
        <div class="centered">
        <table align="center"><tr>
        <th>DAGR Name</th>
        <th>Name</th>
        <th>File Size</th>
        <th>Keywords</th>
        <th>Date Created</th>
        <th>Date Entered</th>
        <th class="path">Path</th>
        <th class="filetype">File Type</th>
        <th>Image Width</th>
        <th>Image Height</th>
        </tr>
EOBODY;
        $query = "SELECT distinct i.*, d.name as 'dagrname' FROM image i, dagr d";
        $datecreated1 = strtotime($_POST["startdatecreated"]);
        $datecreated2 = strtotime($_POST["enddatecreated"]);
        $dateentered1 = strtotime($_POST["startdateentered"]);
        $dateentered2 = strtotime($_POST["enddateentered"]);

        $db_connection = new mysqli("localhost", "root", "", "mmda");
        if ($db_connection->connect_error) {
          die($db_connection->connect_error);
        }

        if(!empty($_POST["dagrs"])) {
          $empty = true;

          $result = $db_connection->query("SELECT * FROM `parent_relations`;");
          if($result->num_rows > 0) {
            $empty = false;
          }
          if(!$empty){
            $query .= ", dagr d2, parent_relations p";
          }
          $query .= " WHERE (((";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID))";
          if(!$empty){
          $query .= "OR (d.GUID=p.CHILD_GUID AND p.PARENT_GUID=d2.GUID AND d.GUID=i.DAGR_ID AND (";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d2.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          }
          else {
            $query .= ")";
          }
          $query .= " AND i.DATE_CREATED > {$datecreated1} AND i.DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }
        else if(!empty($_POST["categories"])) {
          $query .= ", categories c, belongs_to_category b, belongs_to_category b2
          WHERE (((";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID) AND (c.GUID=b.CATEGORY_ID) AND (i.DAGR_ID=b.COMPONENT_ID) AND (i.DAGR_ID=d.GUID))
          OR (d.GUID=i.DAGR_ID AND i.DAGR_ID=b.COMPONENT_ID AND b.CATEGORY_ID=b2.COMPONENT_ID AND b2.CATEGORY_ID=c.GUID AND (";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          $query .= " AND DATE_CREATED > {$datecreated1} AND DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }


        $result = $db_connection->query($query);
        $num_rows = $result->num_rows;
        for ($row_index = 0; $row_index < $num_rows; $row_index++) {
          $result->data_seek($row_index);
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $datecreated = gmdate("Y-m-d", $row['DATE_CREATED']);
          $dateentered = gmdate("Y-m-d", $row['DATE_ENTERED']);
          $table .= <<<EOBODY
          <tr>
          <td>{$row['dagrname']}</td>
          <td>{$row['NAME']}</td>
          <td>{$row['FILE_SIZE']}</td>
          <td>{$row['KEYWORDS']}</td>
          <td>{$datecreated}</td>
          <td>{$dateentered}</td>
          <td>{$row['PATH_TO_RESOURCE']}</td>
          <td>{$row['FILE_TYPE']}</td>
          <td>{$row['IMAGE_WIDTH']}</td>
          <td>{$row['IMAGE_HEIGHT']}</td>
          </tr>
EOBODY;
        }

        $table .= "</table></div>";
        $body .= $table;
      }
      if($textchecked == "checked") {
        $body .= "<hr>";
        $body .= "<h2 class=\"typeheader\">Text</h2>";

        $table = <<<EOBODY
        <div class="centered">
        <table align="center"><tr>
        <th>DAGR Name</th>
        <th>Name</th>
        <th>File Size</th>
        <th>Keywords</th>
        <th>Date Created</th>
        <th>Date Entered</th>
        <th class="path">Path</th>
        <th class="filetype">File Type</th>
        <th># of Chars</th>
        </tr>
EOBODY;
        $query = "SELECT distinct i.*, d.name as 'dagrname' FROM text i, dagr d";
        $datecreated1 = strtotime($_POST["startdatecreated"]);
        $datecreated2 = strtotime($_POST["enddatecreated"]);
        $dateentered1 = strtotime($_POST["startdateentered"]);
        $dateentered2 = strtotime($_POST["enddateentered"]);
        $db_connection = new mysqli("localhost", "root", "", "mmda");
        if ($db_connection->connect_error) {
          die($db_connection->connect_error);
        }

        if(!empty($_POST["dagrs"])) {
          $empty = true;

          $result = $db_connection->query("SELECT * FROM `parent_relations`;");
          if($result->num_rows > 0) {
            $empty = false;
          }
          if(!$empty){
            $query .= ", dagr d2, parent_relations p";
          }
          $query .= " WHERE (((";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID))";
          if(!$empty){
          $query .= "OR (d.GUID=p.CHILD_GUID AND p.PARENT_GUID=d2.GUID AND d.GUID=i.DAGR_ID AND (";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d2.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          }
          else {
            $query .= ")";
          }
          $query .= " AND i.DATE_CREATED > {$datecreated1} AND i.DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }
        else if(!empty($_POST["categories"])) {
          $query .= ", categories c, belongs_to_category b, belongs_to_category b2
          WHERE (((";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID) AND (c.GUID=b.CATEGORY_ID) AND (i.DAGR_ID=b.COMPONENT_ID) AND (i.DAGR_ID=d.GUID))
          OR (d.GUID=i.DAGR_ID AND i.DAGR_ID=b.COMPONENT_ID AND b.CATEGORY_ID=b2.COMPONENT_ID AND b2.CATEGORY_ID=c.GUID AND (";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          $query .= " AND DATE_CREATED > {$datecreated1} AND DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }


        $result = $db_connection->query($query);
        $num_rows = $result->num_rows;
        for ($row_index = 0; $row_index < $num_rows; $row_index++) {
          $result->data_seek($row_index);
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $datecreated = gmdate("Y-m-d", $row['DATE_CREATED']);
          $dateentered = gmdate("Y-m-d", $row['DATE_ENTERED']);
          $table .= <<<EOBODY
          <tr>
          <td>{$row['dagrname']}</td>
          <td>{$row['NAME']}</td>
          <td>{$row['FILE_SIZE']}</td>
          <td>{$row['KEYWORDS']}</td>
          <td>{$datecreated}</td>
          <td>{$dateentered}</td>
          <td>{$row['PATH_TO_RESOURCE']}</td>
          <td>{$row['FILE_TYPE']}</td>
          <td>{$row['NUM_CHARS']}</td>
          </tr>
EOBODY;
        }

        $table .= "</table></div>";
        $body .= $table;
      }
      if($audiochecked == "checked") {
        $body .= "<hr>";
        $body .= "<h2 class=\"typeheader\">Audio</h2>";

        $table = <<<EOBODY
        <div class="centered">
        <table align="center"><tr>
        <th>DAGR Name</th>
        <th>Name</th>
        <th>File Size</th>
        <th>Keywords</th>
        <th>Date Created</th>
        <th>Date Entered</th>
        <th class="path">Path</th>
        <th class="filetype">File Type</th>
        <th>Audio Length</th>
        </tr>
EOBODY;
        $query = "SELECT distinct i.*, d.name as 'dagrname' FROM audio i, dagr d";
        $datecreated1 = strtotime($_POST["startdatecreated"]);
        $datecreated2 = strtotime($_POST["enddatecreated"]);
        $dateentered1 = strtotime($_POST["startdateentered"]);
        $dateentered2 = strtotime($_POST["enddateentered"]);
        $db_connection = new mysqli("localhost", "root", "", "mmda");
        if ($db_connection->connect_error) {
          die($db_connection->connect_error);
        }

        if(!empty($_POST["dagrs"])) {
          $empty = true;

          $result = $db_connection->query("SELECT * FROM `parent_relations`;");
          if($result->num_rows > 0) {
            $empty = false;
          }
          if(!$empty){
            $query .= ", dagr d2, parent_relations p";
          }
          $query .= " WHERE (((";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID))";
          if(!$empty){
          $query .= "OR (d.GUID=p.CHILD_GUID AND p.PARENT_GUID=d2.GUID AND d.GUID=i.DAGR_ID AND (";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d2.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          }
          else {
            $query .= ")";
          }
          $query .= " AND i.DATE_CREATED > {$datecreated1} AND i.DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }
        else if(!empty($_POST["categories"])) {
          $query .= ", categories c, belongs_to_category b, belongs_to_category b2
          WHERE (((";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID) AND (c.GUID=b.CATEGORY_ID) AND (i.DAGR_ID=b.COMPONENT_ID) AND (i.DAGR_ID=d.GUID))
          OR (d.GUID=i.DAGR_ID AND i.DAGR_ID=b.COMPONENT_ID AND b.CATEGORY_ID=b2.COMPONENT_ID AND b2.CATEGORY_ID=c.GUID AND (";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          $query .= " AND DATE_CREATED > {$datecreated1} AND DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }


        $result = $db_connection->query($query);
        $num_rows = $result->num_rows;
        for ($row_index = 0; $row_index < $num_rows; $row_index++) {
          $result->data_seek($row_index);
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $datecreated = gmdate("Y-m-d", $row['DATE_CREATED']);
          $dateentered = gmdate("Y-m-d", $row['DATE_ENTERED']);
          $table .= <<<EOBODY
          <tr>
          <td>{$row['dagrname']}</td>
          <td>{$row['NAME']}</td>
          <td>{$row['FILE_SIZE']}</td>
          <td>{$row['KEYWORDS']}</td>
          <td>{$datecreated}</td>
          <td>{$dateentered}</td>
          <td>{$row['PATH_TO_RESOURCE']}</td>
          <td>{$row['FILE_TYPE']}</td>
          <td>{$row['AUDIO_LENGTH']}</td>
          </tr>
EOBODY;
        }

        $table .= "</table></div>";
        $body .= $table;
      }
      if($videochecked == "checked") {
        $body .= "<hr>";
        $body .= "<h2 class=\"typeheader\">Video</h2>";

        $table = <<<EOBODY
        <div class="centered">
        <table align="center"><tr>
        <th>DAGR Name</th>
        <th>Name</th>
        <th>File Size</th>
        <th>Keywords</th>
        <th>Date Created</th>
        <th>Date Entered</th>
        <th class="path">Path</th>
        <th class="filetype">File Type</th>
        <th>Video Length</th>
        <th>Resolution</th>
        </tr>
EOBODY;
        $query = "SELECT distinct i.*, d.name as 'dagrname' FROM video i, dagr d";
        $datecreated1 = strtotime($_POST["startdatecreated"]);
        $datecreated2 = strtotime($_POST["enddatecreated"]);
        $dateentered1 = strtotime($_POST["startdateentered"]);
        $dateentered2 = strtotime($_POST["enddateentered"]);
        $db_connection = new mysqli("localhost", "root", "", "mmda");
        if ($db_connection->connect_error) {
          die($db_connection->connect_error);
        }

        if(!empty($_POST["dagrs"])) {
          $empty = true;

          $result = $db_connection->query("SELECT * FROM `parent_relations`;");
          if($result->num_rows > 0) {
            $empty = false;
          }
          if(!$empty){
            $query .= ", dagr d2, parent_relations p";
          }
          $query .= " WHERE (((";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID))";
          if(!$empty){
          $query .= "OR (d.GUID=p.CHILD_GUID AND p.PARENT_GUID=d2.GUID AND d.GUID=i.DAGR_ID AND (";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d2.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          }
          else {
            $query .= ")";
          }
          $query .= " AND i.DATE_CREATED > {$datecreated1} AND i.DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }
        else if(!empty($_POST["categories"])) {
          $query .= ", categories c, belongs_to_category b, belongs_to_category b2
          WHERE (((";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID) AND (c.GUID=b.CATEGORY_ID) AND (i.DAGR_ID=b.COMPONENT_ID) AND (i.DAGR_ID=d.GUID))
          OR (d.GUID=i.DAGR_ID AND i.DAGR_ID=b.COMPONENT_ID AND b.CATEGORY_ID=b2.COMPONENT_ID AND b2.CATEGORY_ID=c.GUID AND (";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          $query .= " AND DATE_CREATED > {$datecreated1} AND DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }


        $result = $db_connection->query($query);
        $num_rows = $result->num_rows;
        for ($row_index = 0; $row_index < $num_rows; $row_index++) {
          $result->data_seek($row_index);
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $datecreated = gmdate("Y-m-d", $row['DATE_CREATED']);
          $dateentered = gmdate("Y-m-d", $row['DATE_ENTERED']);
          $table .= <<<EOBODY
          <tr>
          <td>{$row['dagrname']}</td>
          <td>{$row['NAME']}</td>
          <td>{$row['FILE_SIZE']}</td>
          <td>{$row['KEYWORDS']}</td>
          <td>{$datecreated}</td>
          <td>{$dateentered}</td>
          <td>{$row['PATH_TO_RESOURCE']}</td>
          <td>{$row['FILE_TYPE']}</td>
          <td>{$row['VIDEO_LENGTH']}</td>
          <td>{$row['RESOLUTION']}</td>
          </tr>
EOBODY;
        }

        $table .= "</table></div>";
        $body .= $table;
      }
      if($htmlchecked == "checked") {
        $body .= "<hr>";
        $body .= "<h2 class=\"typeheader\">HTML</h2>";

        $table = <<<EOBODY
        <div class="centered">
        <table align="center"><tr>
        <th>DAGR Name</th>
        <th>Name</th>
        <th>File Size</th>
        <th>Keywords</th>
        <th>Date Created</th>
        <th>Date Entered</th>
        <th class="path">Path</th>
        </tr>
EOBODY;
        $query = "SELECT distinct i.*, d.name as 'dagrname' FROM html i, dagr d";
        $datecreated1 = strtotime($_POST["startdatecreated"]);
        $datecreated2 = strtotime($_POST["enddatecreated"]);
        $dateentered1 = strtotime($_POST["startdateentered"]);
        $dateentered2 = strtotime($_POST["enddateentered"]);
        $db_connection = new mysqli("localhost", "root", "", "mmda");
        if ($db_connection->connect_error) {
          die($db_connection->connect_error);
        }

        if(!empty($_POST["dagrs"])) {
          $empty = true;

          $result = $db_connection->query("SELECT * FROM `parent_relations`;");
          if($result->num_rows > 0) {
            $empty = false;
          }
          if(!$empty){
            $query .= ", dagr d2, parent_relations p";
          }
          $query .= " WHERE (((";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID))";
          if(!$empty){
          $query .= "OR (d.GUID=p.CHILD_GUID AND p.PARENT_GUID=d2.GUID AND d.GUID=i.DAGR_ID AND (";
          foreach($_POST["dagrs"] as $d) {
            $query .= "d2.NAME = '{$d}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          }
          else {
            $query .= ")";
          }
          $query .= " AND i.DATE_CREATED > {$datecreated1} AND i.DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }
        else if(!empty($_POST["categories"])) {
          $query .= ", categories c, belongs_to_category b, belongs_to_category b2
          WHERE (((";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ") AND (d.GUID=i.DAGR_ID) AND (c.GUID=b.CATEGORY_ID) AND (i.DAGR_ID=b.COMPONENT_ID) AND (i.DAGR_ID=d.GUID))
          OR (d.GUID=i.DAGR_ID AND i.DAGR_ID=b.COMPONENT_ID AND b.CATEGORY_ID=b2.COMPONENT_ID AND b2.CATEGORY_ID=c.GUID AND (";
          foreach($_POST["categories"] as $c) {
            $query .= "c.NAME = '{$c}' OR ";
          }
          $query = substr($query, 0, -4);
          $query .= ")))";
          $query .= " AND DATE_CREATED > {$datecreated1} AND DATE_CREATED < {$datecreated2}";
          $query .= " AND i.DATE_ENTERED > {$dateentered1} AND i.DATE_ENTERED < {$dateentered2}";
          $query .= " AND FILE_SIZE >= {$_POST["minsize"]} AND FILE_SIZE <= {$_POST["maxsize"]}";
          $query .= " AND KEYWORDS LIKE '%{$_POST["keyword"]}%'";
          $query .= " ORDER BY {$_POST["order"]}";
        }


        $result = $db_connection->query($query);
        $num_rows = $result->num_rows;
        for ($row_index = 0; $row_index < $num_rows; $row_index++) {
          $result->data_seek($row_index);
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $datecreated = gmdate("Y-m-d", $row['DATE_CREATED']);
          $dateentered = gmdate("Y-m-d", $row['DATE_ENTERED']);
          $table .= <<<EOBODY
          <tr>
          <td>{$row['dagrname']}</td>
          <td>{$row['NAME']}</td>
          <td>{$row['FILE_SIZE']}</td>
          <td>{$row['KEYWORDS']}</td>
          <td>{$datecreated}</td>
          <td>{$dateentered}</td>
          <td>{$row['PATH_TO_RESOURCE']}</td>
          </tr>
EOBODY;
        }

        $table .= "</table></div>";
        $body .= $table;
      }
    }
  }
  echo generatePage($body,"query.css", "Query Executioner");
 ?>
