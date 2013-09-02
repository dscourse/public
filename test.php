<?php 

		// Split course dates into weeks
		$beginDate = strtotime('2013-1-18'); 		// Get the course begin date
		$endDate   = strtotime('2013-3-1');			// Get the course end date 
		$datediff = $endDate - $beginDate;					// Get difference between dates
		echo $datediff;  
		$totalDays = floor($datediff/(60*60*24));			// Count the total number of days in between
		if(($totalDays % 7) > 0) { $totalWeeks = ($totalDays/7)+1; } else { $totalWeeks = ($totalDays/7); };  // Get how many weeks there are in those days. 
		$allWeeks = array();
		for ($i1 = 0; $i1 < $totalWeeks; $i1++){
			array_push($allWeeks, 0);				
		} 
		print_r($allWeeks); 
