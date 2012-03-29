<?php

$entity = elgg_extract('entity', $vars, false);
$value = elgg_extract('value', $vars, false);

$name = elgg_extract('name', $vars);

if (!$value && !$entity) {
	if ($entity = get_entity(get_input('event_guid', null))) {
		$value = $entity->$name;
	}

	if (!$value) {
		$value = time();
	}
} else if (!$value && $entity) {
	$value = $entity->$name;
}

$dt = new DateTime();
$dt->setTimestamp($value);

if (elgg_get_plugin_setting('timezone_disable') == 'disable' && $default_timezone = elgg_get_plugin_setting('default_timezone', 'hypeEvents')) {
	$def_tz = $dt->setTimezone(new DateTimeZone($default_timezone));
} elseif ($default_timezone = $entity->event_timezone) {
	$def_tz = $dt->setTimezone(new DateTimeZone($default_timezone));
} else {
	$def_tz = $dt->setTimezone(new DateTimeZone('GMT'));
}

$value = $dt->getTimestamp();

$date = elgg_view('input/text', array(
	'id' => "{$name}_datepicker",
	'value' => gmdate('d-m-Y', $value),
	//'timestamp' => true,
	'name' => "{$name}[date]"
		));


$time_options = array();

for ($i = 0; $i <= 23; $i++) {
	$timestamp00 = $i * 60 * 60;
	$timestamp30 = $i * 60 * 60 + 30 * 60;

	$time_options[] = gmdate(elgg_echo('hj:events:timeformat'), $timestamp00);
	$time_options[] = gmdate(elgg_echo('hj:events:timeformat'), $timestamp30);
}


$time_value = $dt->format('H:i');
$time = elgg_view('input/dropdown', array(
	'name' => "{$name}[time]",
	'value' => $time_value,
	'options' => $time_options,
		));


if (elgg_get_plugin_setting('timezone_disable', 'hypeEvents') == 'disable' && $default_timezone = elgg_get_plugin_setting('default_timezone', 'hypeEvents')) {
	$timezone = elgg_view('input/hidden', array(
		'name' => "{$name}[timezone]",
		'value' => $default_timezone
			));
} else {
	$timezone_options = hj_events_custom_timezones();
	if (!$entity) {
		$timezone_value = date('e', $value);
	} else {
		$timezone_value = $entity->event_timezone;
	}

	$timezone = elgg_view('input/dropdown', array(
		'name' => "{$name}[timezone]",
		'value' => "$timezone_value",
		'options_values' => $timezone_options
			));
}

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
