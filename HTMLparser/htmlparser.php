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
  if (substr($url, 0, 1) == '/' && substr($url, 1, 2) != '/') {
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


//$url = 'https://originative.co/';
//$url = 'C:\Users\Frank\Documents\vankeeb.html';
//$url2 = 'https://thevankeyboards.com/';

/*returns an array of all links from an HTML webpage */
function parseHTMLurl($url) {

  $baseUrl = getBaseUrl($url);
  $protocol = getProtocol($baseUrl);
  $urlToParse = getCurl($url);
  $dom = new domDocument;
  @$dom->loadHTML($urlToParse);
  $HTMLcomponents = array();

  //get links
  foreach($dom->getElementsByTagName('a') as $link) {
      //Show the <a href>
      $templink = $link->getAttribute('href');
      $linktype = get_file_extension($templink);
      if (!empty($linktype)) {
        $linkstart = getLinkStart($templink);
        if ($linkstart == 0) {
          $HTMLcomponents[] = ($baseUrl . $templink);
        }
        else if ($linkstart == 1) {
          $HTMLcomponents[] = ($protocol . $templink);
        }
        else {
          if (substr($templink, 0, 4) == 'http') {
            $HTMLcomponents[] = $templink;
          }
        }
      }
  }
  //get images
  foreach($dom->getElementsByTagName('img') as $image) {
      $tempimage = $image->getAttribute('src');
      $imagetype = get_file_extension($tempimage);
      $imagestart = getLinkStart($tempimage);
      if ($imagestart == 0) {
        $HTMLcomponents[] = ($baseUrl . $tempimage);
      }
      else if ($imagestart == 1) {
        $HTMLcomponents[] = ($protocol . $tempimage);
      }
      else {
        if (substr($tempimage, 0, 4) == 'http') {
        $HTMLcomponents[] = $tempimage;
        }
      }
  }
  return $HTMLcomponents;
}

/*returns an array of all links from an local HTML file */
function parseHTMLlocal($url) {
  $dom = new domDocument;
  @$dom->loadHTMLFILE($url);
  $HTMLcomponents = array();
  //get links
  foreach($dom->getElementsByTagName('a') as $link) {
      //Show the <a href>
      $templink = $link->getAttribute('href');
      $linktype = get_file_extension($templink);
      if (substr($templink, 0, 2) == '//') {
        $templink = 'https:' . $templink;
      }
      if (substr($templink, 0, 4) == 'http') {
      $HTMLcomponents[] = $templink;
      }
  }
  //get images
  foreach($dom->getElementsByTagName('img') as $image) {
      $tempimage = $image->getAttribute('src');
      $imagetype = get_file_extension($tempimage);
      $imagestart = getLinkStart($tempimage);
      if (substr($tempimage, 0, 2) == '//') {
        $tempimage = 'https:' . $tempimage;
      }
      if (substr($tempimage, 0, 4) == 'http') {
      $HTMLcomponents[] = $tempimage;
      }
  }
  return $HTMLcomponents;
}
//debug statements
/*
echo "<pre>";
print_r(parseHTMLlocal($url));
echo "<pre>";
echo "<br />";
echo "<pre>";
print_r(parseHTMLurl($url2));
echo "<pre>";
*/

//debug functions
/*
foreach($dom->getElementsByTagName('a') as $link) {
    # Show the <a href>
    $templink = $link->getAttribute('href');
    $linktype = get_file_extension($templink);
    if (!empty($linktype)) {
      echo $templink;
      echo "<br />";
    }
}

foreach($dom->getElementsByTagName('img') as $image) {
    $tempimage = $image->getAttribute('src');
    $imagetype = get_file_extension($tempimage);
    echo $tempimage;
    echo "<br />";
}
*/


 ?>
