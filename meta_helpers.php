<?php

/*get the current working directory and add the getID3 path */
//$cwd = getcwd();
require_once('C:\xampp\htdocs\CMSC424\getID3-master\getid3\getid3.php');

//return file extension
function get_file_extension($file_name) {
    $ext = substr(strrchr($file_name,'.'),1,3);
    if($ext == "doc" && substr(strrchr($file_name,'.'),1,4) == "docx") {
      return "docx";
    }
    else if($ext == "htm" && substr(strrchr($file_name,'.'),1,4) == "html") {
      return "html";
    }
    else {
      return $ext;
    }
}

//Checks to make sure the file is of valid type
function valid_filetype($file_name) {
  return (get_file_extension($file_name) == "docx"
  || get_file_extension($file_name) == "xml"
  || get_file_extension($file_name) == "txt"
  || get_file_extension($file_name) == "mov"
  || get_file_extension($file_name) == "wav"
  || get_file_extension($file_name) == "jpg"
  || get_file_extension($file_name) == "png"
  || get_file_extension($file_name) == "gif"
  || get_file_extension($file_name) == "mp3");
}

//Returns whether a file is an image
function isImage($file_path) {
  $ext = get_file_extension($file_path);
  if($ext == "jpg" || $ext == "png" || $ext == "gif") {
    return true;
  }
  else {
    return false;
  }
}

//Returns whether a file is a text file
function isText($file_path) {
  $ext = get_file_extension($file_path);
  if($ext == "docx") {
    return 1;
  }
  else if($ext == "xml" || $ext == "txt") {
    return 2;
  }
  else {
    return 0;
  }
}

//Returns whether a file is an audio file
function isAudio($file_path) {
  $ext = get_file_extension($file_path);
  if($ext == "mp3" || $ext == "wav") {
    return true;
  }
  else {
    return false;
  }
}

//Returns whether a file is a video file
function isVideo($file_path) {
  $ext = get_file_extension($file_path);
  if($ext == "mov" || $ext == "mp4") {
    return true;
  }
  else {
    return false;
  }
}

//Returns true if $name is a valid dagr name to be entered
function DAGRValid($name) {
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }
  $result = $db_connection->query("SELECT * FROM `dagr` WHERE NAME='{$name}';");
  if($result->num_rows == 0) {
    return true;
  }
  else {
    return false;
  }
}

//Gets the GUID of a given DAGR
function getDAGRGUID($name) {
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }
  $result = $db_connection->query("SELECT GUID FROM `dagr` WHERE NAME='{$name}';");
  $result->data_seek(0);
  $row = $result->fetch_array(MYSQLI_ASSOC);

  return $row["GUID"];
}

//Returns array of all DAGR names
function DAGRNames() {
  $arr = [];
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }
  $result = $db_connection->query("SELECT NAME FROM `dagr`;");
  $num_rows = $result->num_rows;
  for ($row_index = 0; $row_index < $num_rows; $row_index++) {
    $result->data_seek($row_index);
    $row = $result->fetch_array(MYSQLI_ASSOC);

    array_push($arr, $row["NAME"]);
  }
  return $arr;
}

//Returns array of all Categories
function CategoryNames() {
  $arr = [];
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }
  $result = $db_connection->query("SELECT NAME FROM `categories`;");
  $num_rows = $result->num_rows;
  for ($row_index = 0; $row_index < $num_rows; $row_index++) {
    $result->data_seek($row_index);
    $row = $result->fetch_array(MYSQLI_ASSOC);

    array_push($arr, $row["NAME"]);
  }
  return $arr;
}

//Creates a DAGR
function createDAGR($name, $arr, $guid) {
  $db_connection = new mysqli("localhost", "root", "", "mmda");
  if ($db_connection->connect_error) {
    die($db_connection->connect_error);
  }

  $time = time();
  $result = $db_connection->query("INSERT INTO `dagr` VALUES ('{$guid}','{$name}',{$time});");

  foreach($arr as $d) {
    $result = $db_connection->query("SELECT GUID FROM `dagr` WHERE NAME='{$d}';");
    $result->data_seek(0);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $dguid = $row["GUID"];
    $result = $db_connection->query("INSERT INTO `parent_relations` VALUES ('{$guid}','{$dguid}');");
  }
}

//Inserts local file
function insertLocalFile($path,$dagrguid) {
  if(isImage($path)) {
    $file_info = image_local_metadata($path);
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
  else if(isText($path) != 0) {
    $info = [];
    if(isText($path) == 1) {
      $info = DOCX_local_metadata($path);
    }
    else if(isText($path) == 2) {
      $info = text_local_metadata($path);
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
  else if(isAudio($path)) {
    $file_info = audio_local_metadata($path);
    $db_connection = new mysqli("localhost", "root", "", "mmda");
    if ($db_connection->connect_error) {
      die($db_connection->connect_error);
    }

    $result = $db_connection->query("INSERT INTO `audio`
      VALUES ('{$file_info['guid']}','{$dagrguid}','{$file_info['name']}',
      '{$file_info['size']}','{$_POST["keywords"]}',{$file_info['timeCreated']},
      {$file_info['timeEntered']},'{$path}','{$file_info['type']}',
      {$file_info['audioLength']});");
  }
  else if(isVideo($path)) {
    $file_info = video_local_metadata($path);
    $db_connection = new mysqli("localhost", "root", "", "mmda");
    if ($db_connection->connect_error) {
      die($db_connection->connect_error);
    }

    $result = $db_connection->query("INSERT INTO `video`
      VALUES ('{$file_info['guid']}','{$dagrguid}','{$file_info['name']}',
      '{$file_info['size']}','{$_POST["keywords"]}',{$file_info['timeCreated']},
      {$file_info['timeEntered']},'{$path}','{$file_info['type']}',
      {$file_info['videoLength']}, '{$file_info['videoResolution']}');");
  }
}

//Inserts from directory
function insertFromDir($path,$dagrguid) {
  if(substr($path, -1) != "/" && substr($path, -1) != "\\") {
    $path .= "\\";
  }
  $arr = glob($path."*");
  foreach($arr as $file) {
    if(is_dir($file)) {
      insertFromDir($file,$dagrguid);
    }
    else {
      $newpath = str_replace("\\",'\\\\',$file);
      insertLocalFile($newpath,$dagrguid);
    }
  }
}

//Generates a guid
function get_guid(){
      if (function_exists('com_create_guid')){
          return com_create_guid();
      }else{
          mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
          $charid = strtoupper(md5(uniqid(rand(), true)));
          $hyphen = chr(45);// "-"
          $uuid = substr($charid, 0, 8).$hyphen
              .substr($charid, 8, 4).$hyphen
              .substr($charid,12, 4).$hyphen
              .substr($charid,16, 4).$hyphen
              .substr($charid,20,12);
          return $uuid;
      }
  }

/* Functions below are used for converting HTTP headers into unix timestamps
  Invoked by calling remote_time($URL)*/

//gets a numeric value for month
function get_month($month) {
  switch ($month) {
    case "Jan":
        return 1;
        break;
    case "Feb":
        return 2;
        break;
    case "Mar":
        return 3;
        break;
    case "Apr":
        return 4;
        break;
    case "May":
        return 5;
        break;
    case "Jun":
        return 6;
        break;
    case "Jul":
        return 7;
        break;
    case "Aug":
        return 8;
        break;
    case "Sep":
        return 9;
        break;
    case "Oct":
        return 10;
        break;
    case "Nov":
        return 11;
        break;
    case "Dec":
        return 12;
        break;
    default:
        echo "err";
      }
}

//converts GMT to UNIX timestamp
function convert_to_UNIX($date) {
  $datearr = explode(' ', $date);
  $timearr = explode(':', $datearr[3]);
  $month = get_month($datearr[1]);
  $utime = mktime(($timearr[0] + 1), $timearr[1], $timearr[2], $month, $datearr[0], $datearr[2]);
  if (date("I",$utime) == 0) {
    return $utime;
  }
  else {
    $utime = mktime(($timearr[0] + 2), $timearr[1], $timearr[2], $month, $datearr[0], $datearr[2]);
    return $utime;
  }
}

//return date modified/created of URLs
function remote_time($url){
	$data = get_headers($url, true);
	if (isset($data['Last-Modified'])) {
		$date = convert_to_UNIX(substr($data['Last-Modified'], 5, -4));
    return $date;
  }
  else {
    return 0;
  }
}


/* return file size of URLs based on HTTP header */
function remote_filesize($url){
	$data = get_headers($url, true);
	if (isset($data['Content-Length'])) {
		return $data['Content-Length'];
  }
}

/*The below functions are used for getting information about DOCX files */

//Function to extract text  - takes in a file path
function extract_DOCX_text($filename) {
  //Check for extension
  $exploded = explode('.', $filename);
  $ext = end($exploded);

  //if its docx file
  if($ext == 'docx') {
    $dataFile = "word/document.xml";
  }
  else {
    $dataFile = "content.xml";
  }

  //Create a new ZIP archive object
  $zip = new ZipArchive;

  // Open the archive file
  if (true === $zip->open($filename)) {
      // search for the data file in the archive and return tagless XML
      if (($index = $zip->locateName($dataFile)) !== false) {
          $text = $zip->getFromIndex($index);
          $xml = DOMDocument::loadXML($text, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
          return (strlen(strip_tags($xml->saveXML())) - 2);
      }
      $zip->close();
  }
  // error case
  return "File not found";
}

/*this function returns the length of MP3 and WAV audio files */
function get_local_audio_length($filename) {
  $getID3 = new getID3;
  $fileinfo = $getID3->analyze($filename);

  return round($fileinfo['playtime_seconds'], 2);
}

/*this function returns the length and resolution(in a string of numxnum)
of local MP4 and MOV video files */
function get_local_video_data($filename) {
  $getID3 = new getID3;
  $fileinfo = $getID3->analyze($filename);

  $vidarr = array();
  $vidarr[0] = ($fileinfo['video']['resolution_x'] . 'x' . $fileinfo['video']['resolution_y']);
  $vidarr[1] = round($fileinfo['playtime_seconds'], 2);

  return $vidarr;
}

/*this function returns the filesize, length and resolution(in a string of form numxnum)
of URL based MP4 and MOV video files */
function get_URL_video_data($remotefilename) {
  if ($fp_remote = fopen($remotefilename, 'rb')) {
      @$localtempfilename = tempnam('/tmp', 'getID3');
      if ($fp_local = fopen($localtempfilename, 'wb')) {
          while ($buffer = fread($fp_remote, 8192)) {
              fwrite($fp_local, $buffer);
          }
          fclose($fp_local);
          // Initialize getID3 engine
          $getID3 = new getID3;
          $fileinfo = $getID3->analyze($localtempfilename);
          unlink($localtempfilename);
      }
      fclose($fp_remote);
    }

    $vidarr = array();
    $vidarr[0] = ($fileinfo['video']['resolution_x'] . 'x' . $fileinfo['video']['resolution_y']);
    $vidarr[1] = round($fileinfo['playtime_seconds'], 2);
    $vidarr[2] = $fileinfo['filesize'];

    return $vidarr;

}

/*this function returns the filesize and audio length and
of URL based MP4 and MOV video files */
function get_URL_audio_data($remotefilename) {
  if ($fp_remote = fopen($remotefilename, 'rb')) {
      @$localtempfilename = tempnam('/tmp', 'getID3');
      if ($fp_local = fopen($localtempfilename, 'wb')) {
          while ($buffer = fread($fp_remote, 8192)) {
              fwrite($fp_local, $buffer);
          }
          fclose($fp_local);
          // Initialize getID3 engine
          $getID3 = new getID3;
          $fileinfo = $getID3->analyze($localtempfilename);
          unlink($localtempfilename);
      }
      fclose($fp_remote);
    }

    $audioarr = array();
    $audioarr[0] = round($fileinfo['playtime_seconds'], 2);
    $audioarr[1] = $fileinfo['filesize'];

    return $audioarr;

}


 ?>
