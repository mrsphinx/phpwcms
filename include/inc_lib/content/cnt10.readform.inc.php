<?php
/*************************************************************************************
   Copyright notice
   
   (c) 2002-2012 Oliver Georgi <oliver@phpwcms.de> // All rights reserved.
 
   This script is part of PHPWCMS. The PHPWCMS web content management system is
   free software; you can redistribute it and/or modify it under the terms of
   the GNU General Public License as published by the Free Software Foundation;
   either version 2 of the License, or (at your option) any later version.
  
   The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html
   A copy is found in the textfile GPL.txt and important notices to the license 
   from the author is found in LICENSE.txt distributed with these scripts.
  
   This script is distributed in the hope that it will be useful, but WITHOUT ANY 
   WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
   PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 
   This copyright notice MUST APPEAR in all copies of the script!
*************************************************************************************/

// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
   die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------



// Content Type Form Email
$content["mailform"] = explode("\n", slweg($_POST["cmailform"]));
$content["mailform"] = array_diff($content["mailform"], array(''));
        $content["mailsubject"] = clean_slweg($_POST["cmailsubject"]);
        if (isEmpty($content["mailsubject"])) $content["mailsubject"] = "online webform email message";
        $content["mailrecipient"] = clean_slweg($_POST["cmailrecipient"]);
        if (!is_valid_email($content["mailrecipient"])) $content["error"]["mailrecipient"] = "proof recipient - email format error";
        $content["mailbutton"] = clean_slweg($_POST["cmailbutton"]);
        if (isEmpty($content["mailbutton"])) $content["mailbutton"] = "send";
        $content["mailhtml"] = isset($_POST["cmailhtml"]) ? intval($_POST["cmailhtml"]) : 0;

        if (is_array($content["mailform"]) && count($content["mailform"])) {
            foreach($content["mailform"] as $key => $value) {
                $content["mailform"][$key] = explode("|", chop($value)); 
                // Field-Code
                $content["mailform"][$key][0] = strtoupper(trim($content["mailform"][$key][0]));
                if (		$content["mailform"][$key][0] == "IT" 
						||	$content["mailform"][$key][0] == "IP" 
						||	$content["mailform"][$key][0] == "IH" 
						||	$content["mailform"][$key][0] == "TA"
						||	$content["mailform"][$key][0] == "SM"
						||	$content["mailform"][$key][0] == "SL" 
						||	$content["mailform"][$key][0] == "IC" 
						||	$content["mailform"][$key][0] == "IR" 
						||	$content["mailform"][$key][0] == "SC"
						||	$content["mailform"][$key][0] == "IN"
						||	$content["mailform"][$key][0] == "CA"
					)
				{
                    $content["mailform"][$key][1] = isset($content["mailform"][$key][1]) ? trim($content["mailform"][$key][1]) : '';
                    $content["mailform"][$key][1] = ($content["mailform"][$key][1]) ? $content["mailform"][$key][1] : "field_" . randpassword(3);

                    $content["mailform"][$key][2] = isset($content["mailform"][$key][2]) ? intval($content["mailform"][$key][2]) : 0;
                    $content["mailform"][$key][3] = isset($content["mailform"][$key][3]) ? trim($content["mailform"][$key][3]) : '';

					if(isset($content["mailform"][$key][4])) {
						$field_length = explode(",", $content["mailform"][$key][4]);
						$field_max_height = isset($field_length[1]) ? intval($field_length[1]) : 0;
						$field_length = intval($field_length[0]);
					} else {
						$field_length = 0;
						$field_max_height = 0;
					}
                    $field_length = ($field_length) ? $field_length : 10;
                    switch ($content["mailform"][$key][0]) {
                        case "TA": $field_max_height = ($field_max_height) ? $field_max_height : 3;
                            break;
                        case "SL": $field_max_height = ($field_max_height) ? $field_max_height : 0;
                            break;
                        case "IC": $field_max_height = ($field_max_height) ? $field_max_height : 0;
                            break;
                        case "IR": $field_max_height = ($field_max_height) ? $field_max_height : 0;
                            break;
						case "CA": $content["mailform"][$key][1] = 'Captcha_Validation';
								   $content["mailform"][$key][2] = 1;
							break;
                        default: $field_max_height = ($field_max_height) ? $field_max_height : 100;
                    } 

                    $content["mailform"][$key][5] = isset($content["mailform"][$key][5]) ? trim($content["mailform"][$key][5]) : '';
                    $content["mailform"][$key][6] = isset($content["mailform"][$key][6]) ? intval($content["mailform"][$key][6]) : 0;

                    $content["mailform"][$key]["field"] = $content["mailform"][$key][0] . "|";
                    $content["mailform"][$key]["field"] .= $content["mailform"][$key][1] . "|";
                    $content["mailform"][$key]["field"] .= $content["mailform"][$key][2] . "|";
                    $content["mailform"][$key]["field"] .= $content["mailform"][$key][3] . "|";
                    $content["mailform"][$key]["field"] .= $field_length . ",";
                    $content["mailform"][$key]["field"] .= $field_max_height . "|";
                    $content["mailform"][$key]["field"] .= $content["mailform"][$key][5] . "|";
                    $content["mailform"][$key]["field"] .= $content["mailform"][$key][6];
                } else {
                    unset($content["mailform"][$key]);
                } 
            } 
        } 

        if (is_array($content["mailform"]) && count($content["mailform"])) {
            $content["form"] = "";
            $mft = 0;
            foreach($content["mailform"] as $key => $value) {
                $content["form"] .= (($mft) ? "\n" : "") . $content["mailform"][$key]["field"];
                $mft++;
            } 
        } else {
			$content["mailform"] = "";
		}

		if(isset($content["form"])) {
			if(!is_array($content["form"])) {
        		$content["form"]  = base64_encode($content["form"]) . "#:#" . $content["mailsubject"] . "#:#";
        		$content["form"] .= $content["mailrecipient"] . "#:#" . $content["mailbutton"] . "#:#" . $content["mailhtml"];
			} else {
				$content["form"] = implode('#:#', $content["form"]);
			}
		} else {
			$content["form"] = '';
		}
?>