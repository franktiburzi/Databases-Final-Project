<?php
//$filename = 'https://www.w3schools.com/html/mov_bbb.mp4';

//get the current working directory and add the getID3 path
$cwd = getcwd();
require_once($cwd.'\getID3-master\getid3\getid3.php');


// Copy remote file locally to scan with getID3()
$remotefilename = 'https://tutorialehtml.com/assets_tutorials/media/Loreena_Mckennitt_Snow_56bit.mp3';
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


//echo round($ThisFileInfo['playtime_seconds'], 2);


//print_r($fileinfo);
//print_r($fileinfo['video']);
//$vidarr = array();
//$vidarr[0] = ($fileinfo['video']['resolution_x'] . 'x' . $fileinfo['video']['resolution_y']);
//$vidarr[1] = round($fileinfo['playtime_seconds'], 2);
//$vidarr[2] = $fileinfo['filesize'];
//echo $fileinfo['video']['resolution_x'];
//echo "<br />";
//echo $fileinfo['video']['resolution_y'];
//echo "<br />";
//echo round($fileinfo['playtime_seconds'], 2);

//print_r($vidarr[0]);


 ?>
