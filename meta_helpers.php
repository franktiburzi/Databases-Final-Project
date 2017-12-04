<?php


//return file extension
function get_file_extension($file_name) {
    return substr(strrchr($file_name,'.'),1,3);
}

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

 ?>
