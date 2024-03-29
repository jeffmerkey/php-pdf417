
# php-pdf417 <!-- omit in toc -->

Simple PHP class for decoding Drivers License and ID Card PDF417 Datastreams 
from Web Browser 2D Barcode Scanners

## Table of Contents <!-- omit in toc -->
- [Copyright and Licensing](#copyright-and-licensing)
- [Description](#description)
- [Using the PDF417 PHP Class](#using-the-pdf427-php-class)
- [Using the Example Program](#using-the-example-program)
- [Issues / Problems / Help](#issues--problems--help)

## Copyright and Licensing

Copyright(c) Jeff V. Merkey 1997-2023.  All rights reserved.
pdf417 decoding PHP class for 2D barcode scanners

Licensed under the Lesser GNU Public License (LGPL) v2.1.

Permission to use, copy, modify, distribute, and sell this software and its
documentation for any purpose is hereby granted without fee, provided that
the above copyright notice appear in all copies and that both that
copyright notice and this permission notice appear in supporting
documentation.  No representations are made about the suitability of
this software for any purpose.  It is provided "as is" without express or
implied warranty.

## Description

This is a Simple, down and dirty, easy to use PHP class for parsing
pdf417 data returned by 2D barcode scanners when scanning driver’s
license and ID card PDF417 data. This class returns the scanned and
parsed data as an indexed PHP array or a JSON encoded object.

It’s important to disable the shortcut keys Ctrl-J and Ctrl-D if using
the 2D scanner in Chrome or the scanning process will cause the browser
to jump into either the Downloads screen (Ctrl-J) or the bookmark
dropdown dialog (Ctrl-D) after scanning a driver’s license or ID card.
This occurs because the PDF417 datastream will include these keystroke
sequences as part of it's normal operation since Ctrl-J and Ctrl-D 
are valid data sequences for encoding PDF417 data and often appear
in PDF417 datastreams.  

To disable these shortcuts on Chrome, Download the chrome extension from 
the online Chrome Store called “disable keyboard shortcuts” then after it 
installs, type “chrome://extensions/shortcuts” in the url bar and hit enter. 

Enter Ctrl-J and Ctrl-D into the fields in the extension to disable these 
shortcuts and your 2D bar code scanner should work properly. Firefox maps 
Ctrl-J to the dropdown url dialog and unlike chrome, there is no way to 
easily disable shortcut keys which may be in a PDF417 data stream with 
the Firefox browser. It is recommended to only use this PDF417 decoder 
on Chrome or Windows based web browsers which support disabling these 
shortcut keystrokes.

This behavior occurs because a bar code scanner basically works like a
keyboard in how it interfaces to a web browser.

## Using the PDF417 PHP Class

Include the pdf417-class.php in your PHP program to use the PDF417 
decoder then create a class object and then you can parse a PDF417 
datastream returned from a 2D barcode scanner.  Position the cursor
over the web browser field your PHP program will use to process the 
datastream then scan the driver's license or id card with the 2D 
scanner.  PDF417 is only supported by
2D and 3D barcode scanners.

```sh
include "pdf417-class.php";

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
```

You can also convert the PDF417 array returned by the pdf417_parse() 
method into a json object.

```sh
// convert pdf417 returned array to json object
$jsondata = $pdfh->pdf417_json_encode($pdf417data);

// convert pdf417 json object to php array
if ($jsondata)
   $jsonarray = $pdfh->pdf417_json_decode($jsondata);
```

# Using the Example Program

You can invoke the example program from the command line with php command
line utility.

```sh
# 
# php example.php
#
```

The program should parse the example pdf417 datastream and print out the 
following:

```sh
IDNUMBER : C1234567
FULLNAME : SMITH, JOHN ROBERT
FIRSTNAME : JOHN
MIDDLE : ROBERT
LASTNAME : SMITH
SEX : MALE
HEIGHT : 601
WEIGHTLBS : 240
ADDRESS : 123 WEED ROAD
CITY : PORTLAND
STATE : OR
ZIP : 97206
BIRTHDATE : 07/13/1960
EXPIREDATE : 12/25/2050
ISSUEDATE : 07/13/2022
--- JSON ENCODE ---
{"idnumber":"C1234567","fullname":"SMITH, JOHN ROBERT","firstname":"JOHN","middle":"ROBERT","lastname":"SMITH","sex":"MALE","height":"601","weightlbs":"240","address":"123 WEED ROAD","city":"PORTLAND","state":"OR","zip":"97206","birthdate":"07\/13\/1960","expiredate":"12\/25\/2050","issuedate":"07\/13\/2022"}
--- JSON DECODE ---
stdClass Object
(
    [idnumber] => C1234567
    [fullname] => SMITH, JOHN ROBERT
    [firstname] => JOHN
    [middle] => ROBERT
    [lastname] => SMITH
    [sex] => MALE
    [height] => 601
    [weightlbs] => 240
    [address] => 123 WEED ROAD
    [city] => PORTLAND
    [state] => OR
    [zip] => 97206
    [birthdate] => 07/13/1960
    [expiredate] => 12/25/2050
    [issuedate] => 07/13/2022
)

```

## Issues / problems / help

If you have any issues, please log them at <https://github.com/jeffmerkey/php-pdf417/issues>

If you have any suggestions for improvements then pull requests are
welcomed, or raise an issue.
