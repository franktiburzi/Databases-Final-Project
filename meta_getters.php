<?php

//Get the helper functions for metadata getters
require_once("meta_helpers.php");

$testpic = 'https://cdn.shopify.com/s/files/1/1369/4793/products/r60_00_568x.jpg?v=1511413379';
//$testpic = 'C:\Users\Frank\Pictures\Fall-Colors-Tokyo-2011-G8975.jpg';

//Returns an array of image metadata for PNG, JPG and GIF for locally stored files
function image_local_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['width'] = getimagesize($file_path)[0];
  $image_info['height'] = getimagesize($file_path)[1];
  $image_info['size'] = filesize($file_path);
  $image_info['timeCreated'] = filemtime($file_path);
  $image_info['timeEntered'] = time();
  $image_info['path'] = $file_path;

  return $image_info;
}


//Returns an array of image metadata for PNG, JPG and GIF for URL based files
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

print_r(image_URL_metadata($testpic));


 ?>
