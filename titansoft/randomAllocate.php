<?php
function randomAllocateStaff() {
	$staffArr = array();
	foreach ($_SESSION['data'] as $key => $value) {
		array_push($staffArr, $value[0]);
	}
	
	for($i = 1; $i <=7; $i++) {
		$maxNum = ($i > 5) ? 3 : 2;
		$randKeys = array_rand($staffArr, $maxNum);
		for ($x = 0; $x < $maxNum; $x++) {
			$staffName = $staffArr[$randKeys[$x]];
			print_r($staffName);
		}
	}
}

?>