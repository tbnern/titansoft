<?php
include ("common.php");

$submit = $_POST['submit'];
if (!isset($submit)) errorCode(99);

$dataArr = getOptionsData();
saveToScheduleFile($dataArr);
redirect();  

function getOptionsData() {
	if (!isset($_POST['schedule'])) errorCode(99);
	if ($_POST['schedule'] < 7) errorCode(99);
	
	$result = array();
	foreach ($_POST['schedule'] as $key => $valueArray) {
		$result[$key]['day'] = strtr($key, $GLOBALS['iDayToSDay']);
		foreach ($valueArray as $secondKey => $staff) {
			if (isset($result[$key]['staff']) && in_array($staff, $result[$key]['staff'])) errorCode(5);
			$result[$key]['staff'][] = $staff;
		}
	}
	return $result;
}

function saveToScheduleFile($dataArr) {
	$myfile = fopen($GLOBALS['scheduleFile'], "w") or die("Unable to open file!");
	$staffWorkArr = array();
	foreach ($dataArr as $key => $valueArray) {
		$txt = $valueArray['day'] . "\t";
		foreach ($valueArray['staff'] as $secondKey => $value) {
			$txt = $txt . $value . "\t";
			$staffWorkArr[$value][] = $valueArray['day'];
		}
		$txt = $txt . "\n";
		fwrite($myfile, $txt);
	}
	fclose($myfile);
	saveToIndividualFile($staffWorkArr);
}

function saveToIndividualFile($staffWorkArr) {
	foreach ($staffWorkArr as $key => $value) {
		$myfile = fopen($GLOBALS['scheduleFolder']."/".$key.".txt", "w") or die("Unable to open file!");
		foreach ($value as $secondKey => $day) {
			fwrite($myfile, $day."\n");
		}
		fclose($myfile);
	}
}
?>