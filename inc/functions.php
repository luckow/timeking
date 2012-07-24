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

  function getActualWorkingHoursInRange($config,$employees,$range = null) {

      $days = getWorkingDays($range);
      $hours = 0;

      foreach ($employees as $user) {
        if(isset($config["working_hours_per_day"][$user->email]) && $config["working_hours_per_day"][$user->email] >= 0) {
          $hours += $config["working_hours_per_day"][$user->email] * $days;
        }
        else {
          $hours += $config["working_hours_per_day"]["default"] * $days;
        }
      }

      return round($hours);
  }

  function determineRankingGroup($hours_registered, $hours_goal) {
    $performance = round($hours_registered/$hours_goal*100);

    if($performance >= 105) {
      $group = "A-karmahunter";
    } elseif ($performance < 105 && $performance >= 95) {
      $group = "B-goalie";
    } elseif ($performance < 95 && $performance >= 70) {
      $group = "C-karmauser";
    } else {
      $group = "D-slacker";
    }
    return $group;
  }

  function sortByOneKey(array $array, $key) {

      $result = array();
      $values = array();

      foreach ($array as $id => $value) {
          $values[$id] = isset($value[$key]) ? $value[$key] : '';
      }

      asort($values);

      foreach ($values as $key => $value) {
          $result[] = $array[$key];
      }

      return $result;
  }

?>