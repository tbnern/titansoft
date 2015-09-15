<?php
session_start();

$GLOBALS['iDayToSDay'] = array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 7 => "Sunday");

$GLOBALS['sDayToiDay'] = array("monday" => 1, "tuesday" => 2, "wednesday" => 3, "thursday" => 4, "friday" => 5, "saturday" => 6, "sunday" => 7); 

$GLOBALS['scheduleFolder'] = "schedule";
$GLOBALS['scheduleFile'] = $GLOBALS['scheduleFolder']."/schedule.txt";

function errorCode($code) {
	switch ($code) {
		case 1:
			$_SESSION['error'] = "Wrong file type. Please upload the correct file. Only txt is allowed!";
			break;
		case 2:
			$_SESSION['error'] = "Exceed file size limit. Please upload the file less than 2MB!";
			break;
		case 3:
			$_SESSION['error'] = "More than 4 staff. Please make sure it's not than 4 staff!";
			break;
		case 4:
			$_SESSION['error'] = "Wrong file format. Please make sure the format is correct!";
			break;	
		case 5:
			$_SESSION['error'] = "Same staff at the same day is not allowed!";
			break;	
		default:
			$_SESSION['error'] = "Something is not right here. Please try again!";
	}
	redirect();
	exit;
}

function redirect() {
	header('location:index.php');
}

function setGlobalStaff() {
	setGlobalStaffName();
	setGlobalStaffOff();
}

function setGlobalStaffName() {
	$GLOBALS['staff'] = array();
	foreach ($_SESSION['data'] as $key => $value) {
		array_push($GLOBALS['staff'], $value[0]);
	}
}

function setGlobalStaffOff() {
	foreach ($_SESSION['data'] as $key => $value) {
		$GLOBALS['staffOff'][$value[0]] = array();
		foreach ($value[1] as $day) {
			array_push($GLOBALS['staffOff'][$value[0]], $day);
		}
	}
}

function randomAllocateStaff() {
	$staffArr = $GLOBALS['staff'];
	
	for($i = 7; $i >= 1; $i--) {
		$maxNum = ($i > 5) ? 3 : 2;
		$randKeys = array_rand($staffArr, $maxNum);
		for ($x = 0; $x < $maxNum; $x++) {
			$staffName = $staffArr[$randKeys[$x]];
			$result[$i][] = $staffName;
		}
		$staffArr = removeStaff($result,$staffArr);
	}
	
	return $result;
}

function removeStaff($result,$staffArr) {
	$out = array();
	foreach($result as $key => $value) {
		foreach($value as $secondKey => $secondValue) {
			if (array_key_exists($secondValue, $out)) {
				$out[$secondValue]++;
			} else {
				$out[$secondValue] = 1;
			}
			if ($out[$secondValue] >= 5 && in_array($secondValue, $staffArr)) {
				$staffArrPopKey = array_search($secondValue, $staffArr);
				unset($staffArr[$staffArrPopKey]);
			}
		}
	}
	
	return $staffArr;
}

function readScheduleFile() {
	$myfile = fopen($GLOBALS['scheduleFile'], "r") or errorCode(99);
	$result = array();
	while (!feof($myfile)) {
		$input = fgets($myfile);
		$input = explode("\t", $input);
		array_push($result, $input);
	}
	fclose($myfile);
	return convertScheduleFileToArr($result);;
}

function convertScheduleFileToArr($fileArr) {
	$result = array();
	foreach ($fileArr as $key) {
		$date = strtr(strtolower($key[0]),$GLOBALS['sDayToiDay']);
		foreach ($key as $k => $name) {
			if ($k != 0) $result[$date][] = $name;
		}
	}
	return $result;
}

function checkScheduleFileExist() {
	return file_exists($GLOBALS['scheduleFile']);
}

function checkIndividualFileExist($staffName) {
	return file_exists($GLOBALS['scheduleFolder']."/".$staffName.".txt");
}
?>