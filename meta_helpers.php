<?php

/*get the current working directory and add the getID3 path */
$cwd = getcwd();
require_once($cwd.'\getID3-master\getid3\getid3.php');

//return file extension
function get_file_extension($file_name) {
  $fileext = substr(strrchr($file_name,'.'),1,3);
  /*
  if (strcasecmp($fileext, 'doc')) {
    $fileext = 'docx';
  }
  else if (strcasecmp($fileext, 'htm')) {
    $fileext = 'html';
  }
  */
  return $fileext;

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
