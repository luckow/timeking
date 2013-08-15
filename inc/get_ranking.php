<?php
include('settings.php'); // config 
include('functions.php'); 

// Include the HarvestAPI class
require_once 'HarvestAPI.php';
// Register the HarvestAPI autoloader
spl_autoload_register(array('HarvestAPI', 'autoload'));

// fetch date range
$dates    = getDateRange();
$dateStart  = date('Ymd', $dates['start']);
$dateEnd    = date('Ymd', $dates['end']);

// check for cache
$cacheFile = getcwd() . '/cache/json_'.$dateStart.'-'.$dateEnd.'.cache';

if(file_exists($cacheFile) && filemtime($cacheFile) > (time()-600))
{
  // cache is valid
  $encodedJson = file_get_contents($cacheFile);
  if(!is_null($encodedJson) && !empty($encodedJson)) {
    echo $encodedJson;
    die();
  }
}

// Cache did not check out or is too old
// fetch new data
$harvestAPI = new HarvestReports();
$harvestAPI->setUser($config["harvest_user"]);
$harvestAPI->setPassword($config["harvest_pass"]);
$harvestAPI->setAccount($config["harvest_account"]);

$entries = getEntries($harvestAPI, $config);  

$total = $entries[0];
$employees = $entries[2];
$ranking = sortByOneKey($entries[1], 'group');
//shuffle($ranking);

$json = array();
$json['succes'] = true;
//$json['debug'] = date("Ymd",strtotime('first day of this month'));
$json['hours_total_registered'] = $total;
$json['hours_total_month']      = getActualWorkingHoursInRange($config,$employees,"month");
$json['hours_until_today']      = getActualWorkingHoursInRange($config,$employees,null);
$json['date_start']             = $dates['start'];
$json['date_end']               = $dates['end'];
$json['ranking']                = $ranking;
$json['timestamp']              = date("Ymd H:i:s",time());

$encodedJson = json_encode($json);

// cache the result
if(!file_put_contents($cacheFile,$encodedJson))
{
  error_log('Failed writing to json cache file: ' . $cacheFile);
}

echo $encodedJson;
?>