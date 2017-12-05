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
    <li><a href="../QueryExecutioner/queryexecutioner.html">Query Executioner</a></li>
    <li><a href="../Categorization/categorization.html">Categorization</a></li>
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

  *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
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
    $path = str_replace("\\",'\\\\',$_SESSION["path"]);

    if(isImage($_SESSION["path"]) && $_SESSION["type"] == "path") {
      $file_info = image_local_metadata($_SESSION["path"]);
      $db_connection = new mysqli("localhost", "root", "", "mmda");
      if ($db_connection->connect_error) {
        die($db_connection->connect_error);
      }

      $result = $db_connection->query("INSERT INTO `image`
        VALUES ('{$file_info['guid']}','{$dagrguid}','{$file_info['name']}',
        '{$file_info['size']}','{$_POST["keywords"]}',{$file_info['timeCreated']},
        {$file_info['timeEntered']},'{$path}','{$file_info['type']}',
        {$file_info['width']},{$file_info['height']});");
    }
    else if(isText($_SESSION["path"]) != 0 && $_SESSION["type"] == "path") {
      $info = [];
      if(isText($_SESSION["path"]) == 1) {
        $info = DOCX_local_metadata($_SESSION["path"]);
      }
      else if(isText($_SESSION["path"]) == 2) {
        $info = TXT_XML_local_metadata($_SESSION["path"]);
      }

      $db_connection = new mysqli("localhost", "root", "", "mmda");
      if ($db_connection->connect_error) {
        die($db_connection->connect_error);
      }

      $result = $db_connection->query("INSERT INTO `text`
      VALUES ('{$info['guid']}','{$dagrguid}','{$info['name']}',{$info['size']},
      '{$_POST["keywords"]}',{$info['timeCreated']},{$info['timeEntered']},'{$info['path']}',
      '{$info['type']}','{$info['numberOfChars']}');");
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

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
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
    $path = str_replace("\\",'\\\\',$_SESSION["path"]);

    if(isImage($_SESSION["path"]) && $_SESSION["type"] == "path") {
      $file_info = image_local_metadata($_SESSION["path"]);
      $db_connection = new mysqli("localhost", "root", "", "mmda");
      if ($db_connection->connect_error) {
        die($db_connection->connect_error);
      }

      $result = $db_connection->query("INSERT INTO `image`
        VALUES ('{$file_info['guid']}','{$dagrguid}','{$file_info['name']}',
        '{$file_info['size']}','{$_POST["keywords"]}',{$file_info['timeCreated']},
        {$file_info['timeEntered']},'{$path}','{$file_info['type']}',
        {$file_info['width']},{$file_info['height']});");
    }
    else if(isText($_SESSION["path"]) != 0 && $_SESSION["type"] == "path") {
      $info = [];
      if(isText($_SESSION["path"]) == 1) {
        $info = DOCX_local_metadata($_SESSION["path"]);
      }
      else if(isText($_SESSION["path"]) == 2) {
        $info = TXT_XML_local_metadata($_SESSION["path"]);
      }

      $db_connection = new mysqli("localhost", "root", "", "mmda");
      if ($db_connection->connect_error) {
        die($db_connection->connect_error);
      }

      $result = $db_connection->query("INSERT INTO `text`
      VALUES ('{$info['guid']}','{$dagrguid}','{$info['name']}',{$info['size']},
      '{$_POST["keywords"]}',{$info['timeCreated']},{$info['timeEntered']},'{$info['path']}',
      '{$info['type']}','{$info['numberOfChars']}');");
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

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
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

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid path</h2>";
  }
}

if(isset($_POST["newu"])) {
  if(isset($_POST["URLInsert"]) && trim($_POST["URLInsert"]) != "") {
    $_SESSION["path"] = trim($_POST["URLInsert"]);
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p id="SingleInsert">
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <input type="text" id="DAGRNameField" name="newDAGRName">
        &emsp;
        <div id="DAGRInheritText">Existing DAGRs to Inherit: </div>
        <select name='children[]'multiple="multiple">
            <option value='freshman'>Freshman</option>
            <option value='sophomore'>Sophomore</option>
            <option value='junior'>Junior</option>
            <option value='senior'>Senior</option>
            <option value='senior'>Senior</option>
            <option value='senior'>Senior</option>
            <option value='senior'>Senior</option>
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert New DAGR" id="submitinsert" name="submitNew">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a URL</h2>";
  }
}

if(isset($_POST["newb"])) {
  if(isset($_POST["DirInsert"]) && trim($_POST["DirInsert"]) != "" && file_exists($_POST["DirInsert"])) {
    $_SESSION["path"] = trim($_POST["DirInsert"]);
    $body = $topPart.<<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <p id="SingleInsert">
        <h2>Inserting file(s) at '{$_SESSION["path"]}': </h2>
        <div id="DAGRNameText">DAGR Name: </div>
        <input type="text" id="DAGRNameField" name="newDAGRName">
        &emsp;
        <div id="DAGRInheritText">Existing DAGRs to Inherit: </div>
        <select name='children[]'multiple="multiple">
            <option value='freshman'>Freshman</option>
            <option value='sophomore'>Sophomore</option>
            <option value='junior'>Junior</option>
            <option value='senior'>Senior</option>
            <option value='senior'>Senior</option>
            <option value='senior'>Senior</option>
            <option value='senior'>Senior</option>
        </select>
        &emsp;
        <div id="KeywordText">Keywords: </div>
        <input type="text" id="KeywordField" name="keywords">
      </p>
      <p>
        <input type="submit" value="Insert New DAGR" id="submitinsert" name="submitNew">
      </p>
    </form>

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid directory</h2>";
  }
}

if(isset($_POST["oldl"])) {
  if(isset($_POST["singlePath"]) && trim($_POST["singlePath"]) != "" && file_exists($_POST["singlePath"])) {
    $_SESSION["path"] = trim($_POST["singlePath"]);
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

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid path</h2>";
  }
}

if(isset($_POST["oldu"])) {
  if(isset($_POST["URLInsert"]) && trim($_POST["URLInsert"]) != "") {
    $_SESSION["path"] = trim($_POST["URLInsert"]);
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

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid URL</h2>";
  }
}

if(isset($_POST["oldb"])) {
  if(isset($_POST["DirInsert"]) && trim($_POST["DirInsert"]) != "" && file_exists($_POST["DirInsert"])) {
    $_SESSION["path"] = trim($_POST["DirInsert"]);
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

    *Supported file types are: .docx, .xml, .txt, .mov, .wav, .jpg, .png, .gif, .mp3
EOBODY;
  }
  else {
    $body .= "<h2>Please enter a valid directory path</h2>";
  }
}

echo generatePage($body, "insert.css", "Insert and Bulk Insert");
?>
