<?php

/*Name of the document file*/
$document = 'C:\Users\Frank\Documents\phptest.docx';

/**Function to extract text  - takes in a file path */
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
          return strip_tags($xml->saveXML());
      }
      $zip->close();
  }
  // error case
  return "File not found";
}

echo gettype(extracttext($document));

?>
