<?php

/*returns the file extension of a link or file */
function get_file_extension($file_name) {
  $fileext = substr(strrchr($file_name,'.'),1,3);
  if (strcasecmp($fileext, 'doc') == 0) {
    $fileext = 'docx';
  }
  else if (strcasecmp($fileext, 'htm') == 0) {
    $fileext = 'html';
  }
  return $fileext;
}

function getBaseUrl($url) {
  $result = parse_url($url);
  return $result['scheme']."://".$result['host'];
}

function getProtocol($url) {
  $result = parse_url($url);
  return $result['scheme'].":";
}

function getLinkStart($url) {
  if ($url[0] == '/' && $url[1] != '/') {
    return 0;
  }
  else if (substr($url, 0, 2) == '//') {
    return 1;
  }
  else {
    return 2;
  }
}


/* uses curl to simulate a request to the page */
function getCurl($url) {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_REFERER, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
  $curledUrl = curl_exec($curl);
  curl_close($curl);

  return $curledUrl;
}


$url = "https://originative.co/";
$baseUrl = getBaseUrl($url);
$protocol = getProtocol($baseUrl);
echo $baseUrl;
echo "<br />";
echo $protocol;
echo "<br />";


$urlToParse = getCurl($url);

$dom = new domDocument;
//libxml_use_internal_errors(true);
@$dom->loadHTML($urlToParse);

$HTMLcomponents = array();

//get links
foreach($dom->getElementsByTagName('a') as $link) {
    # Show the <a href>
    $templink = $link->getAttribute('href');
    $linktype = get_file_extension($templink);
    if (!empty($linktype)) {
      $linkstart = getLinkStart($templink);
      if ($linkstart == 0) {
        $HTMLcomponents[] = ($baseUrl . $templink);
        //echo $baseUrl . $templink . "------" . $linktype;
        //echo "<br />";
      }
      else if ($linkstart == 1) {
        $HTMLcomponents[] = ($protocol . $templink);
        //echo $protocol . $templink . "------" . $linktype;
        //echo "<br />";
      }
      else {
        $HTMLcomponents[] = $templink;
        //echo $templink . "------" . $linktype;
        //echo "<br />";
      }
    }
}
echo "<br />";

//get images
foreach($dom->getElementsByTagName('img') as $image) {
    $tempimage = $image->getAttribute('src');
    $imagetype = get_file_extension($tempimage);
    $imagestart = getLinkStart($tempimage);
    if ($imagestart == 0) {
      $HTMLcomponents[] = ($baseUrl . $tempimage);
      //echo $baseUrl . $tempimage . "------" . $imagetype;
      //echo "<br />";
    }
    else if ($imagestart == 1) {
      $HTMLcomponents[] = ($protocol . $tempimage);
      //echo $protocol . $tempimage . "------" . $imagetype;
      //echo "<br />";
    }
    else {
      $HTMLcomponents[] = $tempimage;
      //echo $tempimage . "------" . $imagetype;
      //echo "<br />";
    }
}
echo "<pre>";
print_r($HTMLcomponents);
echo "<pre>";
 ?>
