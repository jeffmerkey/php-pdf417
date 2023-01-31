<?php
/***************************************************************************
*
*   Copyright(c) Jeff V. Merkey 1997-2023.  All rights reserved.
*   pdf417 decoding PHP class for 2D barcode scanners
*
*   Licensed under the Lesser GNU Public License (LGPL) v2.1.
*
*   Permission to use, copy, modify, distribute, and sell this software and its
*   documentation for any purpose is hereby granted without fee, provided that
*   the above copyright notice appear in all copies and that both that
*   copyright notice and this permission notice appear in supporting
*   documentation.  No representations are made about the suitability of
*   this software for any purpose.  It is provided "as is" without express or
*   implied warranty.
*
*   DESCRIPTION:  pdf417 decoding PHP class for 2D barcode scanners
* 
*   Simple, down and dirty, easy to use PHP class for parsing pdf417 data 
*   returned by 2D barcode scanners when scanning driver's license and ID
*   card PDF417 data.  This class returns the scanned and parsed data as an
*   indexed PHP array or a JSON encoded object.
*
*   Common pdf417 tags and field content used on driver's licenses and id cards:
*
*   "DAA" => "Full Name",
*   "DAB" => "Family Name",
*   "DAC" => "Given Name",
*   "DAD" => "Middle Name",
*   "DAE" => "Name Suffix",
*   "DAF" => "Name Prefix",
*   "DAG" => "Mailing Street Address1",
*   "DAH" => "Mailing Street Address2",
*   "DAI" => "Mailing City",
*   "DAJ" => "Mailing Jurisdiction Code (State)",
*   "DAK" => "Mailing Postal Code",
*   "DAL" => "Residence Street Address1",
*   "DAM" => "Residence Street Address2",
*   "DAN" => "Residence City",
*   "DAO" => "Residence Jurisdiction Code",
*   "DAP" => "Residence Postal Code",
*   "DAQ" => "License or ID Number",
*   "DAR" => "License Classification Code",
*   "DAS" => "License Restriction Code",
*   "DAT" => "License Endorsements Code",
*   "DAU" => "Height in FT_IN",
*   "DAV" => "Height in CM",
*   "DAW" => "Weight in LBS",
*   "DAX" => "Weight in KG",
*   "DAY" => "Eye Color",
*   "DAZ" => "Hair Color",
*   "DBA" => "License Expiration Date",
*   "DBB" => "Date of Birth",
*   "DBC" => "Sex",
*   "DBD" => "License or ID Document Issue Date",
*   "DBE" => "Issue Timestamp",
*   "DBF" => "Number of Duplicates",
*   "DBG" => "Medical Indicator Codes",
*   "DBH" => "Organ Donor",
*   "DBI" => "Non-Resident Indicator",
*   "DBJ" => "Unique Customer Identifier",
*   "DBK" => "Social Security Number",
*   "DBL" => "Date Of Birth",
*   "DBM" => "Social Security Number",
*   "DBN" => "Full Name",
*   "DBO" => "Family Name",
*   "DBP" => "Given Name",
*   "DBQ" => "Middle Name or Initial",
*   "DBR" => "Suffix",
*   "DBS" => "Prefix",
*   "DCA" => "Specific Class",
*   "DCB" => "Specific Restrictions",
*   "DCD" => "Specific Endorsements",
*   "DCE" => "Physical Description Weight Range",
*   "DCF" => "Document Discriminator",
*   "DCG" => "Country territory of issuance",
*   "DCH" => "Federal Commercial Vehicle Codes",
*   "DCI" => "Place of birth",
*   "DCJ" => "Audit information",
*   "DCK" => "Inventory Control Number",
*   "DCL" => "Race Ethnicity",
*   "DCM" => "Standard vehicle classification",
*   "DCN" => "Standard endorsement code",
*   "DCO" => "Standard restriction code",
*   "DCP" => "Jurisdiction specific vehicle classification description",
*   "DCQ" => "Jurisdiction-specific",
*   "DCR" => "Jurisdiction specific restriction code description",
*   "DCS" => "Last Name",
*   "DCT" => "First Name",
*   "DCU" => "Suffix",
*   "DDA" => "Compliance Type",
*   "DDB" => "Card Revision Date",
*   "DDC" => "HazMat Endorsement Expiry Date",
*   "DDD" => "Limited Duration Document Indicator",
*   "DDE" => "Family Name Truncation",
*   "DDF" => "First Names Truncation",
*   "DDG" => "Middle Names Truncation",
*   "DDH" => "Under 18 Until",
*   "DDI" => "Under 19 Until",
*   "DDJ" => "Under 21 Until",
*   "DDK" => "Organ Donor Indicator",
*   "DDL" => "Veteran Indicator",
*   "PAA" => "Permit Classification Code",
*   "PAB" => "Permit Expiration Date",
*   "PAC" => "Permit Identifier",
*   "PAD" => "Permit IssueDate",
*   "PAE" => "Permit Restriction Code",
*   "PAF" => "Permit Endorsement Code",
*   "ZVA" => "Court Restriction Code",
*   "ZxZ" => "State Specific fields where 'x' is a variable value",
*   "Zxx" => "State Specific fields where 'xx' is a variable value",
*
**************************************************************************/

class pdf417
{
   var $result = array();
   var $work = array();
   var $debug = 0;
   var $pdf417_sort=array(
      "idnumber",  "fullname", "firstname", "middle", "lastname", "suffix",
      "sex",       "race",     "veteran",   "height", "heightcm", "weightlbs",
      "weightkg", "eyecolor",  "haircolor", "address", "address2", "city",
      "state", "zip", "country", "birthdate", "expiredate",  "issuedate", 
      "revisiondate", "class",  "restrictions", "endorsements", "compliance",
      "organdonor", "organdonorindicator", "idexpired",
   );

   var $pdf417_map=array(
      "DAA" => "fullname",     "DBN" => "fullname",   "DAQ" => "idnumber",
      "DAC" => "firstname",    "DCT" => "firstname",  "DBP" => "firstname",
      "DAD" => "middle",       "DBQ" => "middle",     "DCS" => "lastname",
      "DBO" => "lastname",     "DAF" => "suffix",     "DBR" => "suffix",
      "DBS" => "prefix",       "DAG" => "address",    "DAH" => "address2",
      "DAI" => "city",         "DAJ" => "state",      "DAK" => "zip",
      "DAL" => "address",      "DAM" => "address2",   "DBB" => "birthdate", 
      "DAU" => "height",       "DAV" => "heightcm",   "DAW" => "weightlbs",
      "DAX" => "weightkg",     "DAY" => "eyecolor",   "DAZ" => "haircolor",
      "DBD" => "issuedate",    "DBA" => "expiredate", "DCG" => "country",
      "DBH" => "organdonor",   "DDK" => "organdonorindicator", "DDL" => "veteran", 
      "DDB" => "revisiondate", "DBC" => "sex",        "DCA" => "class",
      "DCB" => "restrictions", "DCD" => "endorsements", "DDA" => "compliance",
      "DCL" => "race",
   );

   var $pdf417_match=array(
      "DAA","DAB","DAC","DAD","DAE","DAF","DAG","DAH","DAI","DAJ","DAK","DAL","DAM",
      "DAN","DAO","DAP","DAQ","DAR","DAS","DAT","DAU","DAV","DAW","DAX","DAY","DAZ",
      "DBA","DBB","DBC","DBD","DBE","DBF","DBG","DBH","DBI","DBJ","DBK","DBL","DBM",
      "DBN","DBO","DBP","DBQ","DBR","DBS","DCA","DCB","DCD","DCE","DCF","DCG","DCH",
      "DCI","DCJ","DCK","DCL","DCM","DCN","DCO","DCP","DCQ","DCR","DCS","DCT","DCU",
      "DDB","DDC","DDD","DDA","DDE","DDF","DDG","DDH","DDI","DDJ","DDK","DDL","PAA",
      "PAB","PAC","PAD","PAE","PAF","ZVA","ZUZ","ZUA","ZUB","ZOZ","ZNA","ZNB","ZNC",
      "ZND","ZKZ","DDAM","DDAF","DDAN",
   );

   var $pdf417_regex=(
      'ANSI|DL|'.
      'DAA|DAB|DAC|DAD|DAE|DAF|DAG|DAH|DAI|DAJ|DAK|DAL|DAM|DAN|DAO|DAP|DAQ|DAR|'.
      'DAS|DAT|DAU|DAV|DAW|DAX|DAY|DAZ|DBA|DBB|DBC|DBD|DBE|DBF|DBG|DBH|DBI|DBJ|'.
      'DBK|DBL|DBM|DBN|DBO|DBP|DBQ|DBR|DBS|DCA|DCB|DCD|DCE|DCF|DCG|DCH|DCI|DCJ|'.
      'DCK|DCL|DCM|DCN|DCO|DCP|DCQ|DCR|DCS|DCT|DCU|DDB|DDC|DDD|DDE|DDF|DDG|DDH|'.
      'DDI|DDJ|DDK|DDL|PAA|PAB|PAC|PAD|PAE|PAF|ZVA|ZUZ|ZUA|ZUB|ZOZ|ZNA|ZNB|ZNC|'.
      'ZND|ZKZ|'.
      // the DDA field is a special parsing case and can only contain 
      // M,F, and N values.  This case can fail if the city name ends with
      // a 'D' character when parsing a match for any 'DAx' pdf417 tag.  i.e.
      // 'DAIPORTLANDDAJOR' will erroneously parse as "[DAI]=>PORTLAN, [DDA]=>JOR"
      // instead of "[DAI]=>PORTLAND, [DAJ]=OR".  The solution is to map 
      // all posible values of the DDA tag and brute force search for M,F, 
      // and N values.
      'DDAM|DDAF|DDAN'
   );

   function pdf417_detect($rawdata) 
   {
      // these pdf417 checks pertain to drivers licenses
      $pdf417 = $rawdata;
      $offset = strpos($pdf417, "ANSI", 0);
      if ($offset === false) {}
      else
      {
         $offset += 4;
         $offset = strpos($pdf417, "DL", $offset);
         if ($offset === false) {} 
         else
         {
            $offset += 2;
            $offset = strpos($pdf417, "DL", $offset);
            if ($offset === false) {} 
            else
               return true;
         }
      }      
      return false;
   }

   function pdf417_parse($rawdata) 
   {
      // these pdf417 checks pertain to drivers licenses
      $pdf417 = $rawdata;
      $offset = strpos($pdf417, "ANSI", 0);
      if ($offset === false) {}
      else
      {
          $offset += 4;
          $offset = strpos($pdf417, "DL", $offset);
          if ($offset === false) {} 
          else
          {
             $offset += 2;
             $offset = strpos($pdf417, "DL", $offset);
             if ($offset === false) {} 
             else
             {
                $offset += 2;
                $pdf417 = substr($pdf417, $offset);

                $pdf417array = preg_split('/('.$this->pdf417_regex.')/',$pdf417, -1, 
                            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                if ($this->debug) {
                   print_r($pdf417array);
                   echo '<br/>';
                }

                for ($i=0; $i < count($pdf417array); $i++) {
                    if (!strncmp($pdf417array[$i], "DDA", 3) and 
                        strlen($pdf417array[$i]) == 4)
                    {
                       $this->work[$this->pdf417_map['DDA']] = $pdf417array[$i][3];  
                       continue;
                    }                          

                    if (!in_array($pdf417array[$i+1], $this->pdf417_match)) {
                       if (array_key_exists($pdf417array[$i], $this->pdf417_map)) {
                          $this->work[$this->pdf417_map[$pdf417array[$i]]] = 
                                        $pdf417array[$i + 1];  
                       }
                       $i++;
                    }
                }

                foreach ($this->work as $label => $value) {
                   switch ($label) {

                   case "fullname":
                      if (strstr($value, ',')) {
                         $arr1 = explode(',', trim($value));
                         $last = trim($arr1[0]);   
                         if (isset($arr1[1]) and $arr1[1]) { 
                            $arr2 = explode(' ', trim($arr1[1]));
                            $first = trim($arr2[0]);   
                            if (isset($arr2[1]) and $arr2[1]) { 
                               $middle = trim($arr2[1]);
                               for ($i=2; $i < count($arr2); $i++) {
                                  if (isset($arr2[$i]) and $arr2[$i]) 
                                     $middle .= ' '.$arr2[$i]; 
                               }
                            }
                         }  
                      }
                      else
                      {
                         $arr1 = explode(' ', trim($value));
                         $first = trim($arr1[0]);   
                         if (isset($arr1[1]) and $arr1[1]) { 
                            $middle = trim($arr1[1]);   
                            if (isset($arr1[2]) and $arr1[2]) { 
                               $last = trim(end($arr1));
                            }
                         }  
                      }
                      if (isset($first) and $first) 
                         $this->work['firstname'] = str_replace(',', '', $first);
                      if (isset($last) and $last)
                         $this->work['lastname'] = str_replace(',', '', $last);
                      if (isset($middle) and $middle)
                         $this->work['middle'] = str_replace(',', '', $middle);
                      break;

                   case "sex":
                      switch ($value) {
                      case 3: 
                         $this->work[$label] = 'NONBINARY';
                         break;  
                      case 2: 
                         $this->work[$label] = 'FEMALE';
                         break;  
                      case 1: 
                         $this->work[$label] = 'MALE';
                         break;  
                      default: 
                         break;  
                      }
                      break;

                   case "veteran":
                      switch ($value) {
                      case 1: 
                         $this->work[$label] = 'YES';
                         break;  
                      default: 
                         break;  
                      }
                      break;

                   case "organdonor":
                      $this->work[$label] = 'YES';
                      break;

                   case "organdonorindicator":
                      $this->work[$label] = 'YES';
                      break;

                   case "zip":
                      $this->work[$label] = $this->format_zip_code($value);
                      break;

   	 	   case "birthdate":
		   case "issuedate":
		   case "revisiondate": 
                      $current = strtotime($value);
                      if (!$current) {
      		         $s1 = substr($value, 0, 4);
			 $s2 = substr($value, 4);
                         $current = strtotime($s2.$s1);
                      }
                      if ($current) 
                         $this->work[$label] = date('m/d/Y', $current);
                      break;

                   case "expiredate":
                      $current = strtotime($value);
                      if (!$current) {
      		         $s1 = substr($value, 0, 4);
			 $s2 = substr($value, 4);
                         $current = strtotime($s2.$s1);
                      }
                      if ($current) 
                      {
                         if ($current >= time()) 
                            $this->work[$label] = date('m/d/Y', $current);
                         else {
                            $this->work['idexpired'] = 'YES';
                            $this->work[$label] = date('m/d/Y', $current).
                                                  ' <b> *** EXPIRED *** </b>';
                         }
                      }
                      break;

                   default:
                      break;
                   }                   
                }

                // only output field tags in the pdf417 sort array and do so in order
                foreach ($this->pdf417_sort as $label => $value) {
                   if (array_key_exists($value, $this->work)) 
                      $this->result[$value] = $this->work[$value];
                } 
                return $this->result;
            }
         }
      }
      return NULL;
   }

   function pdf417_print_html($result) 
   {
      if (!isset($result)) {
         if ($this->debug)
            echo 'pdfarray was not set in pdf417_print_html<br/>';
         return;
      }
      foreach ($result as $label => $value) {  
            echo strtoupper($label).' : '.$value.'<br/>';
      }
   }

   function pdf417_printf($result) 
   {
      if (!isset($result)) {
         if ($this->debug)
            echo "pdfarray was not set in pdf417_printf\n";
         return;
      }
      foreach ($result as $label => $value) {  
            echo strtoupper($label).' : '.$value."\n";
      }
   }

   function format_zip_code($zip) 
   {
      $zipnum = preg_replace('/\D/', '', $zip);
      $newzip = substr($zipnum, 0, 5);
      $tail = substr($zipnum, 5);
      if ($tail) $newzip .= '-' . $tail;
      return $newzip;
   }

   function pdf417_json_encode($jsonarray) 
   {
      return json_encode($jsonarray);
   }

   function pdf417_json_decode($jsondata) 
   {
      return json_decode($jsondata);
   }
}

?>
