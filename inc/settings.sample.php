<?php
// date_default_timezone_set('America/Los_Angeles'); // Set your timezone if it is not set in your php.ini

$config["harvest_user"]     = 'your@email.com'; // Your Harvest username, usually an email address
$config["harvest_pass"]     = 'password'; // Your Harvest password
$config["harvest_account"]  = 'accountname'; // The {myaccount} portion of your Harvest url: {myaccount}.harvestapp.com

// number of working hours a day you want to measure against
$config["working_hours_per_day"]["default"] = 7.5;
$config["working_hours_per_day"]["user@email.com"] = 6; // working other hours than default

// site title
$config["site_title"]       = 'Reloads Timetable King';

// wanna track with Google Analytics? Enter your Account ID
$config["ga_account_id"]    = '';