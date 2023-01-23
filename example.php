<?php
include "pdf417-class.php";
/*
   It's important to disable the shortcut keys Ctrl-J and Ctrl-D if using
   the 2D scanner in Chrome or the scanning process will cause the browser to 
   jump into either the Downloads screen (Ctrl-J) or the bookmark dropdown 
   dialog (Ctrl-D).  Download the chrome extension from Chrome Store
   called "disable keyboard shortcuts" then after it installs, type
   "chrome://extensions/shortcuts" in the url bar and hit enter.  Enter
   Ctrl-J and Ctrl-D into the fields in the extension to disable these 
   shortcuts and your 2D bar code scanner should work properly.  Firefox 
   maps Ctrl-J to the dropdown url dialog and unlike chrome, there is no 
   way to easily disable shortcut keys which may be in a PDF417 data stream.  
   This behavior occurs because a bar code scanner basically works like a
   keyboard in how it interfaces to a web browser.
*/
/*
   Sample scan data for illustration purposes.  Normally this type of
   datastream is what gets returned from a 2D scanner when you scan
   the pdf417 2D barcode on the back of the id or driver's license
   while the cursor is active in a web browser field.  The 2D scanner
   will behave as a keyboard amd input the pdf417 datastream into 
   the current field on a web browser form.
*/
$scandata = "
@ANSI 6360290102DL00390178ZO02170031DLDAQC1234567DAASMITH, JOHN ROBERTDAGDAL123 WEED ROADDAIPORTLANDDAJORDAK97206      DARA   DASD         DATM     DAU601DAW240DBA20501225DBB19600713DBC1DBD20220713ZOZOAFIRST LICENSED 07-15-2022";

// create new pdf417 class object
$pdfh = new pdf417;

// parse the pdf417 datastream 
$pdf417data = $pdfh->pdf417_parse($scandata); 

// check if a pdf417 datastream was detected and print it
if ($pdf417data)
   $pdfh->pdf417_printf($pdf417data);
else {
   echo 'Not a pdf417 datastream';
   exit;
}

// covert pdf417 datastream to json object
$jsondata = $pdfh->pdf417_json_encode($pdf417data);

// covert pdf417 json object to php array
if ($jsondata)
   $jsonarray = $pdfh->pdf417_json_decode($jsondata);

if ($jsondata) {
   echo "--- JSON ENCODE ---\n";
   print_r($jsondata);
   echo "\n";

   echo "--- JSON DECODE ---\n";
   print_r($jsonarray);
   echo "\n";
}

?>
