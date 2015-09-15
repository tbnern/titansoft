<?php include ("common.php"); ?>
<html>
	<header>
		<title>
			Welcome to Work Arrangement Program
		</title>
	</header>
	<body>
		<?php 
			if (isset($_SESSION['error']) && $_SESSION['error']) {
				echo "<font color='red'>".($_SESSION['error'])."</font>"; 
				unset($_SESSION['error']);
			}
		?>
		<form action="fileProcess.php" method="post" enctype="multipart/form-data" >
			Upload data file:
			<input type="file" name="fileToUpload" id="fileToUpload" accept=".txt" />
			<input type="submit" value="Upload" name="submit" />
		</form>
		<?php if (isset($_SESSION['data']) && $_SESSION['data']) : ?>
			<form action="updateSchedule.php" method="post" enctype="multipart/form-data" >
			<table>
			<?php
				setGlobalStaff();
				$preAllocate = array();
				if (isset($_REQUEST['random'])) {
					$preAllocate = randomAllocateStaff();
					echo "<h3>We have allocated the staff randomly!</h3>";
				} elseif (checkScheduleFileExist()) {
					$preAllocate = readScheduleFile();
				}
				for ($i = 1; $i <= 7; $i++) {
					$count = 0;
					echo "<tr><td>".strtr($i, $GLOBALS['iDayToSDay'])."</td>";
					$maxLoop = ($i >= 6) ? 3 : 2;
						for ($x = 1; $x <= $maxLoop; $x++) {
							echo "<td><select name='schedule[$i][$x]'>";
							foreach ($GLOBALS['staff'] as $key => $value) {
								$selected = ($count < $maxLoop && $preAllocate[$i][$count] == $value) ? "selected" : "";
								//check unhappy
								$unhappy = (in_array($i, $GLOBALS['staffOff'][$value])) ? " :(" : "";
								echo "<option value='".$value."'".$selected.">".$value.$unhappy."</option>";
							}
							$count++;
							echo"</select></td>";
					}
					
				}
				echo "</tr>";
			?>
		</table>
		<input type="submit" value="Save" name="submit" />&nbsp;&nbsp;&nbsp;
		<a href='?random=true' >Random Allocate</a><br><br>
		</form>
		
		<?php 
			if (checkScheduleFileExist()) echo "<a href='".$GLOBALS['scheduleFile'] ."'target='_blank'>Show Weekly Schedule</a>";
		
			foreach ($GLOBALS['staff'] as $key => $value) {
				if(checkIndividualFileExist($value)) echo "<br><a href='".$GLOBALS['scheduleFolder']."/".$value.".txt"."'target='_blank'>".$value." Schedule</a>";
			}		
		?>
		<?php endif; ?>
	</body>
</html>