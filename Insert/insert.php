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
    <li><a href="insert.php" class = "active">Insert and Bulk Insert</a></li>
    <li><a href="../QueryExecutioner/queryexecutioner.php">Query Executioner</a></li>
    <li><a href="../Categorization/categorization.php">Categorization</a></li>
  </ul>
EOBODY;

  $body = $topPart.<<<EOBODY

  <form action="{$_SERVER['PHP_SELF']}" method="post">
    <p>
      <h2>Insert via local path: </h2>
      <input type="text" name="singlePath" class="maintext">
      <input type="submit" class="button" value="Enter as new DAGR" name="newl">
      <input type="submit" class="button" value="Enter into existing DAGR" name="oldl">
    </p>

    <p>
      <h2>Insert via URL: </h2>
      <input type="text" class="maintext" name="URLInsert">
      <input type="submit" class="button" value="Enter as new DAGR" name="newu">
      <input type="submit" class="button" value="Enter into existing DAGR" name="oldu">
    </p>

    <p>
      <h2>Bulk Insert (enter directory path): </h2>
      <input type="text" class="maintext" name="DirInsert">
      <input type="submit" class="button" value="Enter as new DAGR" name="newb">
      <input type="submit" class="button" value="Enter into existing DAGR" name="oldb">
    </p>
  </form>

  *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
EOBODY;

if(isset($_POST["submitNew"])) {
  if(isset($_POST["newDAGRName"]) && $_POST["newDAGRName"] != ""
&& DAGRValid($_POST["newDAGRName"])){
    $body = $topPart.<<<EOBODY
    <h2>Congratulations, you have inserted your document(s).</h2>
EOBODY;
    $dagrs = DAGRNames();
    $subdagrs = [];
    foreach($dagrs as $d) {
      if(!empty($_POST["children"]) && in_array($d,$_POST["children"])) {
        array_push($subdagrs, $d);
      }
    }
    $dagrguid = get_guid();
    createDAGR($_POST["newDAGRName"], $subdagrs, $dagrguid);

    if($_SESSION["type"] == "path") {
      $path = str_replace("\\",'\\\\',$_SESSION["path"]);
      insertLocalFile($path,$dagrguid);
    }
    else if($_SESSION["type"] == "directory") {
      insertFromDir($_SESSION["path"],$dagrguid);
    }
    else if($_SESSION["type"] == "url") {
      insertUrlFile($_SESSION["path"],$dagrguid);
    }
  }
  else {
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p id="SingleInsert">
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <input type="text" id="DAGRNameField" name="newDAGRName">
        &emsp;
        <div id="DAGRInheritText">Existing DAGRs to Inherit: </div>
        <select name="children[]" multiple="multiple">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert New DAGR" id="submitinsert" name="submitNew">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
    <h2>Please enter a valid and unused DAGR Name</h2>
EOBODY;
  }
}

if(isset($_POST["submitExisting"])) {
  if(isset($_POST["ExistingDAGR"])){
    $body = $topPart.<<<EOBODY
    <h2>Congratulations, you have inserted your document(s).</h2>
EOBODY;
    $dagrguid = getDAGRGUID($_POST["ExistingDAGR"]);


    if($_SESSION["type"] == "path") {
      $path = str_replace("\\",'\\\\',$_SESSION["path"]);
      insertLocalFile($path,$dagrguid);
    }
    else if($_SESSION["type"] == "directory") {
      insertFromDir($_SESSION["path"],$dagrguid);
    }
    else if($_SESSION["type"] == "url") {
      insertUrlFile($_SESSION["path"],$dagrguid);
    }
  }
  else {
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p>
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <select name="children[]" multiple="multiple">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert Into Existing DAGR" id="submitinsert" name="submitExisting">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
    <h2>Please enter a valid DAGR Name</h2>
EOBODY;
  }
}

if(isset($_POST["newl"])) {
  if(isset($_POST["singlePath"]) && trim($_POST["singlePath"]) != "" && file_exists(trim($_POST["singlePath"])) && valid_filetype(trim($_POST["singlePath"]))) {
    $_SESSION["path"] = trim($_POST["singlePath"]);
    $_SESSION["type"] = "path";

    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p id="SingleInsert">
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <input type="text" id="DAGRNameField" name="newDAGRName">
        &emsp;
        <div id="DAGRInheritText">Existing DAGRs to Inherit: </div>
        <select name="children[]" multiple="multiple">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert New DAGR" id="submitinsert" name="submitNew">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid path</h2>";
  }
}

if(isset($_POST["newu"])) {
  if(isset($_POST["URLInsert"]) && trim($_POST["URLInsert"]) != "") {
    $_SESSION["path"] = trim($_POST["URLInsert"]);
    $_SESSION["type"] = "url";
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p id="SingleInsert">
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <input type="text" id="DAGRNameField" name="newDAGRName">
        &emsp;
        <div id="DAGRInheritText">Existing DAGRs to Inherit: </div>
        <select name="children[]" multiple="multiple">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert New DAGR" id="submitinsert" name="submitNew">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a URL</h2>";
  }
}

if(isset($_POST["newb"])) {
  if(isset($_POST["DirInsert"]) && trim($_POST["DirInsert"]) != "" && is_dir($_POST["DirInsert"])) {
    $_SESSION["path"] = trim($_POST["DirInsert"]);
    $_SESSION["type"] = "directory";
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p id="SingleInsert">
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <input type="text" id="DAGRNameField" name="newDAGRName">
        &emsp;
        <div id="DAGRInheritText">Existing DAGRs to Inherit: </div>
        <select name="children[]" multiple="multiple">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert New DAGR" id="submitinsert" name="submitNew">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid directory</h2>";
  }
}

if(isset($_POST["oldl"])) {
  if(isset($_POST["singlePath"]) && trim($_POST["singlePath"]) != "" && file_exists($_POST["singlePath"])) {
    $_SESSION["path"] = trim($_POST["singlePath"]);
    $_SESSION["type"] = "path";
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p>
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <select name="ExistingDAGR">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert Into Existing DAGR" id="submitinsert" name="submitExisting">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid path</h2>";
  }
}

if(isset($_POST["oldu"])) {
  if(isset($_POST["URLInsert"]) && trim($_POST["URLInsert"]) != "") {
    $_SESSION["path"] = trim($_POST["URLInsert"]);
    $_SESSION["type"] = "url";
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p>
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <select name="ExistingDAGR">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert Into Existing DAGR" id="submitinsert" name="submitExisting">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid URL</h2>";
  }
}

if(isset($_POST["oldb"])) {
  if(isset($_POST["DirInsert"]) && trim($_POST["DirInsert"]) != "" && is_dir($_POST["DirInsert"])) {
    $_SESSION["path"] = trim($_POST["DirInsert"]);
    $_SESSION["type"] = "directory";
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p>
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <select name="ExistingDAGR">
EOBODY;
    $dagrs = DAGRNames();
    foreach($dagrs as $d) {
      $body .= "<option value='{$d}'>{$d}</option>";
    }
    $body .= <<<EOBODY
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert Into Existing DAGR" id="submitinsert" name="submitExisting">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mp3, .wav, .jpg, .png, .gif, .mp4, .mov
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid directory path</h2>";
  }
}

echo generatePage($body, "insert.css", "Insert and Bulk Insert");
?>
