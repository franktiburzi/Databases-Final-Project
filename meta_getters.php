<?php

//Get the helper functions for metadata getters
require_once("meta_helpers.php");

/* files for testing images */
$testpic = 'https://cdn.shopify.com/s/files/1/1369/4793/products/r60_00_568x.jpg?v=1511413379';
//$testpic = 'C:\Users\Frank\Pictures\Fall-Colors-Tokyo-2011-G8975.jpg';

/*file for testing text files*/
//$document = 'C:\Users\Frank\Documents\phptest.docx';
//$document = 'C:\Users\Frank\Documents\txttest.txt';
//$document = 'C:\Users\Frank\Documents\xmltest.xml';
//$document = 'http://www.terpconnect.umd.edu/~ftiburzi/sgc/portfolio.html';
$document = 'C:\Users\Frank\Documents\sgc\portfolio.html';

/*files for testing audio files */
//$audiofilename = 'C:\Users\Frank\Documents\wavtest.wav';
$audiofilename = 'https://tutorialehtml.com/assets_tutorials/media/Loreena_Mckennitt_Snow_56bit.mp3';

/*files for testing audio files */
//$videofilename = 'C:\Users\Frank\Documents\mp4test.mp4';
//$videofilename = 'C:\Users\Frank\Documents\movtest.mov';
$videofilename = 'https://www.w3schools.com/html/mov_bbb.mp4';


/*Returns an array of image metadata for PNG, JPG and GIF for locally stored files */
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
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['size'] = filesize($file_path);
  $image_info['timeCreated'] = filemtime($file_path);
  $image_info['timeEntered'] = time();
  $image_info['numberOfChars'] = extract_DOCX_text($file_path);
  $image_info['path'] = $file_path;

  return $image_info;
}

/*Returns an array of metadata for XML and TXT files */
function text_local_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['size'] = filesize($file_path);
  $image_info['timeCreated'] = filemtime($file_path);
  $image_info['timeEntered'] = time();
  $image_info['numberOfChars'] = strlen(file_get_contents(($file_path)));
  $image_info['path'] = $file_path;

  return $image_info;
}

/*Returns an array of metadata for XML and TXT files */
function text_URL_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['size'] = remote_filesize($file_path);
  $image_info['timeCreated'] = remote_time($file_path);
  $image_info['timeEntered'] = time();
  $image_info['numberOfChars'] = strlen(file_get_contents(($file_path)));
  $image_info['path'] = $file_path;

  return $image_info;
}

/*Returns an array of metadata for local MP3 and WAV audio files */
function audio_local_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['size'] = filesize($file_path);
  $image_info['timeCreated'] = filemtime($file_path);
  $image_info['timeEntered'] = time();
  $image_info['audioLength'] = get_local_audio_length($file_path);
  $image_info['path'] = $file_path;

  return $image_info;
}

/*Returns an array of metadata for local MP4 and MOV video files */
function video_local_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['size'] = filesize($file_path);
  $image_info['timeCreated'] = filemtime($file_path);
  $image_info['timeEntered'] = time();
  $image_info['videoLength'] = get_local_video_data($file_path)[1];
  $image_info['videoResolution'] = get_local_video_data($file_path)[0];
  $image_info['path'] = $file_path;

  return $image_info;
}

/*Returns an array of metadata for URL based MP4 and MOV video files */
function video_URL_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['size'] = get_URL_video_data($file_path)[2];
  $image_info['timeCreated'] = remote_time($file_path);
  $image_info['timeEntered'] = time();
  $image_info['videoLength'] = get_URL_video_data($file_path)[1];
  $image_info['videoResolution'] = get_URL_video_data($file_path)[0];
  $image_info['path'] = $file_path;

  return $image_info;
}

/*Returns an array of metadata for URL based MP3 and WAV audio files */
function audio_URL_metadata($file_path) {
  $image_info = array();
  $image_info['type'] = get_file_extension($file_path);
  $image_info['size'] = get_URL_audio_data($file_path)[1];
  $image_info['timeCreated'] = remote_time($file_path);
  $image_info['timeEntered'] = time();
  $image_info['audioLength'] = get_URL_audio_data($file_path)[0];
  $image_info['path'] = $file_path;

  return $image_info;
}


//print_r(image_URL_metadata($testpic));

//echo extract_DOCX_text($document);

print_r(text_local_metadata($document));


 ?>
