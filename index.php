<?php
include('inc/settings.php');
include('inc/functions.php');

// fetch dates
$dates  = getDateRange();

$year   = null;
$month  = null; 

if($validURLDates = getDateFromURL())
{
  $year   = date('Y',$validURLDates['start']);
  $month  = date('F',$validURLDates['start']);
}

$nextMonth  = strtotime(date('F',$dates['start']) . ' + 1 month');
$nextYear   = strtotime(date('Y',$dates['start']) . ' + 1 month');
$nextLink   = "?year=".date('Y',$nextYear)."&month=".date('F',$nextMonth);

if($nextMonth > time()) { $nextLink = null; }

$prevMonth  = strtotime(date('F',$dates['start']) . ' last month');
$prevYear   = strtotime(date('Y',$dates['start']) . ' last month');
$prevLink   = "?year=".date('Y',$prevYear)."&month=".date('F',$prevMonth);
?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title><?php echo $config["site_title"]; ?></title>
    <meta name="description" content="">

    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="favicon.ico">

    <link rel="stylesheet" href="css/main.css">
    <script src="js/vendor/modernizr-2.5.3.min.js"></script>
</head>
<body>
    <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
         chromium.org/developers/how-tos/chrome-frame-getting-started -->
    <!--[if lt IE 7]><p class="chromeframe">Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->

    <div id="loader">
      <h3><i class="thingy"></i> Snakker med Harvest</h3>
    </div>
          
  <div class="container">      
      <div id="report_overview">
          <div id="logo">Reload!</div>

          <div id="report_overview_hours">
          	<figure id="progress"></figure>
            <p class="logged_hours">?</p>
            <p class="logged_hours_desc">
              <span>Timer er logget</span>
              <span>denne m&aring;ned</span>
              <span class="date" year="<?php echo htmlspecialchars($year); ?>" month="<?php echo htmlspecialchars($month); ?>"><?php echo date('F Y', $dates['start']); ?> 
                <?php if(!is_null($prevLink)): ?><a href='<?php echo $prevLink; ?>'>&lt;</a><?php endif; ?>
                <?php if(!is_null($nextLink)): ?><a href='<?php echo $nextLink; ?>'>&gt;</a><?php endif; ?>
              </span>
            </p>
          </div>

          <div class="remaining">
            <p class="label">Det betyder, at der kun er</p>
            <p class="hours_togo"><span>?</span> timer tilbage!</p>
          </div>
        
        <div id="hours_productive">
          <h4>?</h4>
          <p class="percent">procent</p>
          <p class="description">rapporteret</p>
        </div>
        
      </div>
      
      <div id="user_ranking">
        <ul>
        
          <li class="hide">
            <div class="user_avatar">
              <span class="rank pink"></span>
              <figure class="user_avatar_holder"></figure>
            </div>
            <div class="user_info">
              <h2></h2>
              <p class="desc"></p>
              <p class="hours"></p>
            </div>
          </li>
          
        </ul>
      </div>
        
    </div>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.7.2.min.js"><\/script>')</script>

    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
    <!-- end scripts -->

    <?php if(!empty($config["ga_account_id"])): ?>
    <script>
        var _gaq=[['_setAccount','<?php echo $config["ga_account_id"]; ?>'],['_trackPageview']];
        (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>
    <?php endif; ?>

    <script type="text/javascript">
        window.onload = setupRefresh;

        function setupRefresh() {
          setTimeout("refreshPage();", 300000); // milliseconds - every 5 mins
        }
        function refreshPage() {
          window.location = location.href;
        }
    </script>

</body>
</html>
