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
    <li><a href="../Categorization/categorization.php">Categorization</a></li>
  </ul>
EOBODY;

  $body = $topPart;
  $dagrs = DAGRNames();
  $categories = CategoryNames();

  $body .= <<<EOBODY
    <form action="queryexecutioner.php" method="POST">
    <h2>Query Execution </h2>
    <br>
    <div class="querytext">Types of Files to Display: </div>
    <table>
    <tr>
    <td>
      <input type="checkbox" id="image" name="filetype" value="image">
      <label for="image">Image</label>
    </td>
    <td>
      <input type="checkbox" id="text" name="filetype" value="text">
      <label for="text">Text</label>
    </td>
    <td>
      <input type="checkbox" id="audio" name="filetype" value="audio">
      <label for="audio">Audio</label>
    </td>
    <td>
      <input type="checkbox" id="video" name="filetype" value="video">
      <label for="video">Video</label>
    </td>
    <td>
      <input type="checkbox" id="html" name="filetype" value="html">
      <label for="html">HTML</label>
    </td>
    </tr>
    </table>
    <br>
    <br>
    <div class="querytext">Search by DAGRs: </div>
    <select name="dagr" multiple="multiple">
EOBODY;
  foreach($dagrs as $d) {
  $body .= "<option value='{$d}'>{$d}</option>";
  }
  $body .= <<<EOBODY
    </select>
    &ensp;
    <div class="querytext"> or by Category: </div>
    <select name="categoryinsert" multiple="multiple">
EOBODY;
  foreach($categories as $c) {
  $body .= "<option value='{$c}'>{$c}</option>";
  }
  $body .= <<<EOBODY
    </select>
    <br>
    <br>
    <br>
    <div class="querytext">Date Entered: </div>
    <input type="date" name="startdate">
    &nbsp;<div class="querytext"> to </div>&nbsp;
    <input type="date" name="enddate">
    <br>
    <br>
    <br>
    <div class="querytext">Size: </div>
    <input type="number" name="minsize">
    &nbsp;<div class="querytext"> to </div>&nbsp;
    <input type="number" name="maxsize">
    <br>
    <br>
    <br>
    <div class="querytext">Keyword: </div>
    <input type="text" name="keyword">
    <br>
    <br>
    <input type="submit" id="submitquery" value="Execute Query" name="submitquery">

EOBODY;
  echo generatePage($body,"query.css", "Query Executioner");
 ?>
