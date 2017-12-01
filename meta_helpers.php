<?php
#return file extension
function get_file_extension($file_name) {
    return substr(strrchr($file_name,'.'),1,3);
}

$testpic = 'C:\Users\Frank\Pictures\testgif.gif';

function image_local_size($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['width'] = getimagesize($file_path)[0];
  $image_info['height'] = getimagesize($file_path)[1];
  $image_info['size'] = filesize($file_path);
  $image_info['timecreated'] = filectime($file_path);
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
print_r(image_local_size($testpic));

 ?>
