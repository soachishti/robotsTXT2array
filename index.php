<?php
//Main Functions File
require('robotstxt2array.php');

//Url host
$host = "google.com";

//Complete Url
$url = "http://" . $host . "/robots.txt";	

//Convert relative url to absolute urls
$rel2abs = false;

//Fetch text from URL
$txt = file_get_contents_curl($url);

//Convert Robottxt to Array
$value = robotstxt_parse($txt);

//Print out the robottxt array
echo '<pre>';
	print_r($value);
echo '</pre>';

//Check for link that whether it is allowed or not.
$link = "/search";
echo "/search". " is " . allowed($link,$value,'*') . '<br />';
$link = "/catalogs/about";
echo $link . " is " . allowed($link,$value,'*'); 
?>