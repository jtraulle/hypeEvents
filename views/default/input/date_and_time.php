<?php

$entity = elgg_extract('entity', $vars);
$value = elgg_extract('value', $vars);
$name = elgg_extract('name', $vars);

$date = elgg_view('input/date', array(
	'value' => $value,
	//'timestamp' => true,
	'name' => "{$name}[date]"
));


$time_options = array();

for ($i = 0;$i <= 23; $i++) {
	$timestamp00 = $i * 60 * 60;
	$timestamp30 = $i * 60 *60 + 30 * 60;

	$time_options[] = gmdate(elgg_echo('hj:events:timeformat'), $timestamp00);
	$time_options[] = gmdate(elgg_echo('hj:events:timeformat'), $timestamp30);
}


$time_value = date('H:i', $value);
$time = elgg_view('input/dropdown', array(
	'name' => "{$name}[time]",
	'value' => $time_value,
	'options' => $time_options,
));

$timezone_options = array();
for ($i=-12;$i <= 12; $i++) {
	$offset = $i * 60 * 60;
	if ($i < 0) {
		$option = "GMT$i";
	} else{
		$option = "GMT+$i";
	}
	$timezone_options["$offset"] = $option;
}

$timezone_value = date('Z', $value);

$timezone = elgg_view('input/dropdown', array(
	'name' => "{$name}[timezone]",
	'value' => "$timezone_value",
	'options_values' => $timezone_options
));

echo <<<HTML
   <div class="clearfix">
	<div class="hj-left">
		$date
	</div>
	<div class="hj-left">
		$time
	</div>
	<div class="hj-left">
		$timezone
	</div>
HTML;
