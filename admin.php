<?php
define("INDEX", "yes");
include 'config.php';
@set_time_limit(0);
$start = microtime(true);
if($error_log == 1){
	@ini_set('log_errors', 1);
	@ini_set('error_log', 'php_errors.log');
}
if($display_errors == 1){
	@ini_set('display_errors', 1);
	@error_reporting(-1);
}
else{
	@ini_set('display_errors', 0);
	@error_reporting(0);
}
if($stat_uniq == 1){
	$sqluniq = "AND uniq = 'yes'";
}
else{
	$sqluniq = '';
}
$trans = parse_ini_file('template/language/'.$language.'.ini', true);
include 'template/function.php';
include 'template/login.php';
include 'template/code.php';
include 'template/log.php';
include 'template/sources.php';
include 'template/countries.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>zTDS <?php echo $version; ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="template/style.css">
<link rel="stylesheet" type="text/css" href="template/codemirror/codemirror.css">
<?php
if($stat_rm == 0){echo"<style type=\"text/css\">#center{width:86%;}#right{width:0%;}</style>
";}
?>
<link rel="shortcut icon" href="template/img/favicon.ico">
<script type="text/javascript" src="template/js/jquery-latest.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="template/codemirror/codemirror.js"></script>
<script type="text/javascript" src="template/codemirror/javascript.js"></script>
<script type="text/javascript" src="template/js/jquery.responsiveTabs.js"></script>
</head>
<body>
<div class="header">
<div class="logo align_left">zTDS <?php echo $version; ?></div>
</div>
<div class="content">
<div id="left">
<div class="left_block">
<?php include 'template/menu.php'; ?>
</div>
</div>
<div id="center">
<div class="center_block align_left">
<?php include 'template/group.php'; ?>
<?php include 'template/stream.php'; ?>
<?php include 'template/editor.php'; ?>
</div>
</div>
<div id="right">
<div class="right_block align_left">
<?php if($stat_rm == 1){include 'template/stat.php';} ?>
</div>
</div>
</div>
<div style="clear:both;"></div>
<div class="bottom">&copy; root</div>
<?php
if(empty($dg)){$dg = '[0,0,0,0,0]';}
if(empty($dg_se)){$dg_se = '[0,0,0,0,0,0]';}
?>
<script type="text/javascript">
      google.charts.load('44', {'packages':['corechart']});
      google.charts.setOnLoadCallback(init);
	  function init () {drawChart();<?php if($q == 'se'){echo 'drawChart_se();';} ?>}
      function drawChart(){
        var data = google.visualization.arrayToDataTable([['Day', '<?php echo $trans['chart']['ch1']; ?>', '<?php echo $trans['chart']['ch2']; ?>', '<?php echo $trans['chart']['ch3']; ?>', '<?php echo $trans['chart']['ch4']; ?>'], <?php echo $dg; ?>]);
        var options = {
          title: 'Statistics',
          curveType: 'none',
          legend:{ position: 'bottom' },
		  chartArea:{left:60,right:20,top:20,bottom:40,width:'100%',height:'100%'},
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
      }
	  function drawChart_se(){
        var data = google.visualization.arrayToDataTable([['Day', 'Google', 'Yandex', 'Mail.ru', 'Yahoo', 'Bing'], <?php echo $dg_se; ?>]);
        var options = {
          title: 'Search engines',
          curveType: 'none',
          legend:{ position: 'bottom' },
		  chartArea:{left:60,right:20,top:20,bottom:40,width:'100%',height:'100%'},
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart_se'));
        chart.draw(data, options);
      }
</script>
<script type="text/javascript">
$(function(){
	var $tabs = $('.tab-container').accordionToTabs({
		breakpoint: '600px'
	});
});
</script>
<?php if($debug == 1){echo '<div class="debug">'.(microtime(true) - $start).' s.</div>';} ?>
</body>
</html>