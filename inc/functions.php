<?php
  
  function getWorkingDays($period = null)
  {
    $startDate = date('Y-m-01');
    
    if($period == 'month') $endDate = date('Y-m-t');
    else $endDate = date('Y-m-d');

    // Sets the Count
    $count = 0;
    $startStt = strtotime($startDate);
    $endStt = strtotime($endDate);
    // iterates through each day till the end day is back at the start date
    while (date("Y-m-d", $startStt) <= date("Y-m-d",$endStt)){
      $count = (date("w", $endStt) != 0 && date("w", $endStt) != 6) ? $count +1 : $count;
      $endStt = $endStt - 86400;
    }
    return $count;
  }

?>