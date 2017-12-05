<?php
  // Starts the session
  session_start();

  // Includes
  require_once("support.php");
  require_once("dbLogin.php");

  // $body is what we will end up displaying
  $body = "";

  // If the "Get Started" button has been pressed
  if(isset($_POST["start"]) || isset($_SESSION["start"])) {

    // HTML to display, provides links to other pages
    $body = <<<EOBODY
    <ul>
      <li><a class="active">Home/About</a></li>
      <li><a href="Insert/insert.php">Insert and Bulk Insert</a></li>
      <li><a href="QueryExecutioner/queryexecutioner.html">Query Executioner</a></li>
      <li><a href="Categorization/categorization.html">Categorization</a></li>
    </ul>
    <h1>The MMDA (Multi-Media Data Aggregator)</h1>
    <h2>Created by Austin Piel and Frank Tiburzi</h2>
    <br>
EOBODY;

    // Sets global session variable
    $_SESSION["start"] = "set";

    // Attempts to connect to the database
    $db_connection = new mysqli($host, $user, $password, $database);

    // If connection to the database fails
    if ($db_connection->connect_error) {
      die($db_connection->connect_error);
    }

    // Checks if the tables already exist in the database
    $table = "audio";
    $result = $db_connection->query("SHOW TABLES LIKE '".$table."'");
    if($result->num_rows == 1) {
      $body .= <<<EOBODY
      <div class="accepted">Your database is set up, use the menu bar to navigate
      the features of our application.</div>
EOBODY;
    }
    else {
      $audio = $db_connection->query("CREATE TABLE `audio` (
 `GUID` varchar(45) NOT NULL,
 `DAGR_ID` varchar(45) NOT NULL,
 `NAME` varchar(45) NOT NULL,
 `FILE_SIZE` int(11) NOT NULL,
 `KEYWORDS` varchar(500) NOT NULL,
 `DATE_CREATED` int(11) NOT NULL,
 `DATE_ENTERED` int(11) NOT NULL,
 `PATH_TO_RESOURCE` varchar(500) NOT NULL,
 `FILE_TYPE` varchar(4) NOT NULL,
 `AUDIO_LENGTH` int(11) NOT NULL,
 PRIMARY KEY (`GUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $belongs_to_category = $db_connection->query("CREATE TABLE `belongs_to_category` (
 `CATEGORY_ID` varchar(45) NOT NULL,
 `COMPONENT_ID` varchar(45) NOT NULL,
 PRIMARY KEY (`CATEGORY_ID`,`COMPONENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $categories = $db_connection->query("CREATE TABLE `categories` (
 `ID` varchar(45) NOT NULL,
 `NAME` varchar(45) NOT NULL,
 PRIMARY KEY (`ID`),
 UNIQUE KEY `NAME` (`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $html = $db_connection->query("CREATE TABLE `html` (
 `GUID` varchar(45) NOT NULL,
 `DAGR_ID` varchar(45) NOT NULL,
 `NAME` varchar(45) NOT NULL,
 `FILE_SIZE` int(11) NOT NULL,
 `KEYWORDS` varchar(500) NOT NULL,
 `DATE_CREATED` int(11) NOT NULL,
 `DATE_ENTERED` int(11) NOT NULL,
 `PATH_TO_RESOURCE` varchar(500) NOT NULL,
 PRIMARY KEY (`GUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $html_component = $db_connection->query("CREATE TABLE `html_component` (
 `HOST_GUID` varchar(45) NOT NULL,
 `COMPONENT_GUID` varchar(45) NOT NULL,
 PRIMARY KEY (`HOST_GUID`,`COMPONENT_GUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $image = $db_connection->query("CREATE TABLE `image` (
   `GUID` varchar(45) NOT NULL,
   `DAGR_ID` varchar(45) NOT NULL,
   `NAME` varchar(45) NOT NULL,
   `FILE_SIZE` int(11) NOT NULL,
   `KEYWORDS` varchar(500) NOT NULL,
   `DATE_CREATED` int(11) NOT NULL,
   `DATE_ENTERED` int(11) NOT NULL,
   `PATH_TO_RESOURCE` varchar(500) NOT NULL,
   `FILE_TYPE` varchar(4) NOT NULL,
   `IMAGE_WIDTH` int(11) NOT NULL,
   `IMAGE_HEIGHT` int(11) NOT NULL,
   PRIMARY KEY (`GUID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $parent_relations = $db_connection->query("CREATE TABLE `parent_relations` (
 `PARENT_GUID` varchar(45) NOT NULL,
 `CHILD_GUID` varchar(45) NOT NULL,
 PRIMARY KEY (`PARENT_GUID`,`CHILD_GUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $text = $db_connection->query("CREATE TABLE `text` (
 `GUID` varchar(45) NOT NULL,
 `DAGR_ID` varchar(45) NOT NULL,
 `NAME` varchar(45) NOT NULL,
 `FILE_SIZE` int(11) NOT NULL,
 `KEYWORDS` varchar(500) NOT NULL,
 `DATE_CREATED` int(11) NOT NULL,
 `DATE_ENTERED` int(11) NOT NULL,
 `PATH_TO_RESOURCE` varchar(500) NOT NULL,
 `FILE_TYPE` varchar(4) NOT NULL,
 `NUM_CHARS` int(11) NOT NULL,
 PRIMARY KEY (`GUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
          $video = $db_connection->query("CREATE TABLE `video` (
 `GUID` varchar(45) NOT NULL,
 `DAGR_ID` varchar(45) NOT NULL,
 `NAME` varchar(45) NOT NULL,
 `FILE_SIZE` int(11) NOT NULL,
 `KEYWORDS` varchar(500) NOT NULL,
 `DATE_CREATED` int(11) NOT NULL,
 `DATE_ENTERED` int(11) NOT NULL,
 `PATH_TO_RESOURCE` varchar(500) NOT NULL,
 `FILE_TYPE` varchar(4) NOT NULL,
 `VIDEO_LENGTH` int(11) NOT NULL,
 `RESOLUTION` varchar(45) NOT NULL,
 PRIMARY KEY (`GUID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
$dagr = $db_connection->query("CREATE TABLE `dagr` (
 `GUID` varchar(45) NOT NULL,
 `NAME` varchar(45) NOT NULL,
 `DATE_ENTERED` int(11) NOT NULL,
 PRIMARY KEY (`GUID`),
 UNIQUE KEY `NAME` (`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    }
  }

  // If the "Get Started" button has not yet been pressed
  else {

    // Menu does not contain links to other pages
    $body = <<<EOBODY
    <ul>
      <li><a class="active">Home/About</a></li>
      <li><a href="">Insert and Bulk Insert</a></li>
      <li><a href="">Query Executioner</a></li>
      <li><a href="">Categorization</a></li>
    </ul>
    <h1>The MMDA (Multi-Media Data Aggregator)</h1>
    <h2>Created by Austin Piel and Frank Tiburzi</h2>
    <br>
EOBODY;

    // Displays the "Get Started" button
    $body .= <<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
      <input type="submit" class="button" value="Get Started" id="start" name="start">
    </form>
EOBODY;
  }

  // Creates the page
  echo generatePage($body, "mainstyle.css");
 ?>
