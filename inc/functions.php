<?php

  function getDateFromURL() {
    $dates  = false;
    $year   = null;
    $month  = null;

    if(isset($_GET["year"]) && is_numeric($_GET["year"])) {
      $year = intval($_GET["year"]);
    }

    if(isset($_GET["month"]) && !empty($_GET["month"])) {
      $month = $_GET["month"];
    }

    if(!is_null($year) && !is_null($month))
    {
      $date_start = strtotime(sprintf("first day of %s %s", $month, $year));
      $date_end   = strtotime(sprintf("last day of %s %s", $month, $year));

      // failsafe for end date
      if($date_end > strtotime("yesterday")) {
        $date_end   = strtotime("yesterday");
      }

      $dates['start'] = $date_start;
      $dates['end']   = $date_end;

    }

    return $dates;
  }

  function getDateRange($period = null) {

    // default values
    $date_start = strtotime('first day of this month');
    $date_end   = strtotime("yesterday");

    // overruled by url?
    $dates = getDateFromURL();
    if($dates != false && is_array($dates))
    {
      $date_start   = $dates['start'];
      $date_end     = $dates['end'];
    }
    else
    {
      // working with current month by default
      // check for first day of month (and if it's a monday), then show last months data
      if(
          $date_end < $date_start || 
            (date("w") == 1 && date("j") < 4 && // today is a monday in the first 3 days of the month
              (date("w", $date_start == 6) || date("w", $date_start == 0)) // and the startdate is sunday or saturday
            )
        ) {
        // lets set the start date to the first day of last month
        $date_start = strtotime('first day of last month');
        $date_end = strtotime('last day of last month');
      }     
    }

    if($period == 'month') {
      $date_end   = strtotime(sprintf("last day of %s",date("F",$date_start)));
    }

    $dates['start'] = $date_start;
    $dates['end']   = $date_end;
    return $dates;
  }

  function getWorkingDays($period = null)
  {

    $dates = getDateRange($period);
    $date_start = $dates['start'];
    $date_end   = $dates['end'];

    // Sets the Count
    $count = 0;
    // iterates through each day till the end day is back at the start date
    while (date("Y-m-d", $date_start) <= date("Y-m-d",$date_end)){
      $count = (date("w", $date_end) != 0 && date("w", $date_end) != 6) ? $count +1 : $count;
      $date_end = $date_end - 86400;
    }

    return $count;
  }
  
  function getFirstWorkDay($date_start, $date_end, $format = 'Ymd')
  {
    // Sets the Count
    $count = 0;
    $date_first = 0;
    // iterates through each day till the end day is back at the start date
    while (date("Y-m-d", $date_start) <= date("Y-m-d",$date_end)){
      $count = (date("w", $date_end) != 0 && date("w", $date_end) != 6) ? $count +1 : $count;
      $date_start = $date_start + 86400;
      if($count > 0) {
        $date_first = $date_start;
        break;
      }
    }

    return date($format, $date_start);
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

    if($performance >= 110) {
      $group = "A-karmahunter";
    } elseif ($performance < 110 && $performance >= 98) {
      $group = "B-goalie";
    } elseif ($performance < 98 && $performance >= 70) {
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

  function getEntries($harvestAPI, $config) {

    $dates = getDateRange();

    $date_start = $dates['start'];
    $date_end   = $dates['end'];

    $range = new Harvest_Range(date('Ymd', $date_start), date('Ymd', $date_end));

    $users = $harvestAPI->getActiveUsers();
    $return = array();

    $total_hours        = 0;
    $workdays_in_range  = getWorkingDays(); // until today
    $employees          = array();
    $first_workday      = getFirstWorkDay($date_start, $date_end, "Ymd");

    foreach ($users->data as $user) {

      // ignore contractors, they do not play our game :-)
      if($user->get("is-contractor") == "true")
      {
        continue;
      }
      
      // ignore newly created users, this game will not be fair for them
      if(date("Ymd",strtotime($user->get("created-at"))) > $first_workday)  // timestamp 2012-12-02T09:57:33Z
      {
        continue;
      }

      // this is a real user, count him in!
      $employees[] = $user;

      // determine optimal hours logged for this user
      $hours_goal = $workdays_in_range * $config["working_hours_per_day"]["default"];
      if(isset($config["working_hours_per_day"][$user->email]) && $config["working_hours_per_day"][$user->email] > 0) {
        // we have a user defined daily working schedule
         $hours_goal = $workdays_in_range * $config["working_hours_per_day"][$user->email];
      }

      $activity = $harvestAPI->getUserEntries($user->id, $range);
      $hours_registered = 0;
      
      foreach ($activity->data as $entry) {
        $hours_registered += $entry->hours;
      }

      // Getting user Harvest user id.
      // Splitting it up to get retrieve Harvest avatar.
      $user_id = strval($user->id);
      $user_id = str_pad($user_id, 9, "0", STR_PAD_LEFT);

      $user_id_parts = str_split($user_id, 3);
      
      $return[1][] = array(
        'user_id_first_part' => (string)$user_id_parts[0],
        'user_id_second_part' => (string)$user_id_parts[1],
        'user_id_third_part' => (string)$user_id_parts[2],
        'name' => $user->first_name, 
        'hours_registered' => $hours_registered,  
        'hours_goal' => $hours_goal,
        'performance' => round($hours_registered/$hours_goal*100),
        'group' => determineRankingGroup($hours_registered, $hours_goal)
        );
      $total_hours += $hours_registered;
      
    }
    
    $return[0] = round($total_hours);
    $return[2] = $employees;
    
    return $return;
    
  }

?>