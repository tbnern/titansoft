<?php
include ("common.php");

$submit = $_POST['submit'];
if (!isset($submit)) errorCode(99);

if (!isset($_FILES['fileToUpload']['name'])) echo errorCode(5);
$filename = $_FILES['fileToUpload']['name'];
$tempFilename = $_FILES['fileToUpload']['tmp_name'];

checkFileExt($filename);
checkFileSize($tempFilename);
readFileContent($tempFilename);
removeOldFiles();
redirect();  
 
function checkFileExt($file) {
	$allowedExt = array('txt');
	$ext = pathinfo($file, PATHINFO_EXTENSION);
	if(!in_array($ext,$allowedExt) ) errorCode(1);
}

function checkFileSize($file) {
	if(filesize($file) > 2048) errorCode(2);
}

function readFileContent($file) {
	$_SESSION['data'] = array();
	$myfile = fopen($file, "r") or errorCode(99);
	$count = 0;
	while (!feof($myfile)) {
		$count++;
		$input = fgets($myfile);
		if ($count > 2) storeData($input);
		if ($count > 6) {
			unset($_SESSION['data']);
			errorCode(3);
			break;
		}
	}
	fclose($myfile);
	if (count($_SESSION['data']) < 1) errorCode(4);
	
}

function storeData($input) {
	$input = explode("\t\t", $input);
	$input[1] = convertDayToArr($input[1]);
	array_push($_SESSION['data'], $input);
}

function convertDayToArr($dayStr) {
	$dayArr = explode(",", strtolower($dayStr));
	
	foreach ($dayArr as $key => $value) {
		$dayArr[$key] = strtr($value,$GLOBALS['sDayToiDay']);
	}
	return $dayArr; 
}

function removeOldFiles() {
	$files = glob($GLOBALS['scheduleFolder']."/*"); 
	foreach ($files as $file){ 
	  if (is_file($file)) unlink($file); 
	}
}

?>