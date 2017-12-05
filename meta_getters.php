<?php

//Get the helper functions for metadata getters
require_once("meta_helpers.php");

/* files for testing images */
$testpic = 'https://cdn.shopify.com/s/files/1/1369/4793/products/r60_00_568x.jpg?v=1511413379';
//$testpic = 'C:\Users\Frank\Pictures\Fall-Colors-Tokyo-2011-G8975.jpg';

/*file for testing text files*/
//$document = 'C:\Users\Frank\Documents\phptest.docx';
//$document = 'C:\Users\Frank\Documents\txttest.txt';
$document = 'C:\Users\Frank\Documents\xmltest.xml';

/*Returns an array of image metadata for PNG, JPG and GIF for locally stored files */
function image_local_metadata($file_path) {
  $image_info = array();
  $image_info['guid'] = get_guid();
  $image_info['name'] = basename($file_path, ".".$_SESSION["filetype"]);
  $image_info['type'] = get_file_extension($file_path);
  $image_info['width'] = getimagesize($file_path)[0];
  $image_info['height'] = getimagesize($file_path)[1];
  $image_info['size'] = filesize($file_path);
  $image_info['timeCreated'] = filemtime($file_path);
  $image_info['timeEntered'] = time();
  $image_info['path'] = $file_path;

  return $image_info;
}


/*Returns an array of image metadata for PNG, JPG and GIF for URL based files */
function image_URL_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['width'] = getimagesize($file_path)[0];
  $image_info['height'] = getimagesize($file_path)[1];
  $image_info['size'] = remote_filesize($file_path);
  $image_info['timeCreated'] = remote_time($file_path);
  $image_info['timeEntered'] = time();
  $image_info['path'] = $file_path;

  return $image_info;
}

/*Returns an array of metadata for MS Word files */
function DOCX_local_metadata($file_path) {
  $text_info = array();
  $text_info['guid'] = get_guid();
  $text_info['name'] = basename($file_path, ".".$_SESSION["filetype"]);
  $text_info['type'] = get_file_extension($file_path);
  $text_info['size'] = filesize($file_path);
  $text_info['timeCreated'] = filemtime($file_path);
  $text_info['timeEntered'] = time();
  $text_info['numberOfChars'] = extract_DOCX_text($file_path);
  $text_info['path'] = $file_path;

  return $text_info;
}

/*Returns an array of metadata for XML and TXT files */
function TXT_XML_local_metadata($file_path) {
  $text_info = array();
  $text_info['guid'] = get_guid();
  $text_info['name'] = basename($file_path, ".".$_SESSION["filetype"]);
  $text_info['type'] = get_file_extension($file_path);
  $text_info['size'] = filesize($file_path);
  $text_info['timeCreated'] = filemtime($file_path);
  $text_info['timeEntered'] = time();
  $text_info['numberOfChars'] = strlen(file_get_contents(($file_path)));
  $text_info['path'] = $file_path;

  return $text_info;
}

//print_r(image_URL_metadata($testpic));

//echo extract_DOCX_text($document);

//print_r(TXT_XML_local_metadata($document));


 ?>
