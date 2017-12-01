<?php

$testpic = 'https://cdn.shopify.com/s/files/1/0267/1905/t/9/assets/slide-image-2.jpg?1603762906910313965';


#return file extension
function get_file_extension($file_name) {
    return substr(strrchr($file_name,'.'),1,3);
}

#converts GMT to UNIX timestamp
function convert_to_UNIX($date) {
  $datearr = explode(' ', $date);
  #return mktime($datearr, 24, 30, 12, $datearr[0], $datearr[2]);
  print_r($datearr);

}

#gets a numeric value for month
function get_month($month) {
  switch ($Month) {
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

#return file size of URLs
function remote_filesize($url){
	$data = get_headers($url, true);
	if (isset($data['Content-Length'])) {
		return $data['Content-Length'];
  }
}

#return date modified/created of URLs
function remote_time($url){
	$data = get_headers($url, true);
	if (isset($data['Last-Modified'])) {
		convert_to_UNIX(substr($data['Last-Modified'], 5, -4));
  }
  else {
    return 0;
  }
}

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

#print_r(get_headers($testpic, true));

#echo mktime(24, 24, 30, 11, 28, 2017);

remote_time($testpic);


 ?>
