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

//return file size of URLs
function remote_filesize($url){
	$data = get_headers($url, true);
	if (isset($data['Content-Length'])) {
		return $data['Content-Length'];
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

<<<<<<< HEAD
#Returns an array of image metadata for PNG, JPG and GIF for locally stored files
function image_local_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['width'] = getimagesize($file_path)[0];
  $image_info['height'] = getimagesize($file_path)[1];
  $image_info['size'] = filesize($file_path);
  $image_info['timecreated'] = filemtime($file_path);
  $image_info['path'] = $file_path;

  return $image_info;
}

function image_URL_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['width'] = getimagesize($file_path)[0];
  $image_info['height'] = getimagesize($file_path)[1];
  $image_info['size'] = remote_filesize($file_path);
  $image_info['timecreated'] = remote_time($file_path);
  $image_info['path'] = $file_path;

  return $image_info;
}
/*
$file = fopen($testpic,"r");
print_r(fstat($file));
fclose($file);
echo "<br>";
echo get_file_extension($testpic);
*/
#print_r(image_local_metadata($testpic));
#$fp = fopen($testpic, 'r');
#$contents = stream_get_contents($fp);
#fclose($fp);

#print_r(image_URL_metadata($testpic));

#echo date("I",1511907870);

#print_r(get_headers($testpic, true));

#echo mktime(24, 24, 30, 11, 28, 2017);

#echo remote_time($testpic);

=======
>>>>>>> e9cea62b732e9c0b4affea5346c5764005eb5302
 ?>
