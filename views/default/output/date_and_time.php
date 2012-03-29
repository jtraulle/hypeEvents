<?php

$value = elgg_extract('value', $vars);
$entity = elgg_extract('entity', $vars);

$dt = new DateTime();
$dt->setTimestamp($value);
$dt->setTimezone(new DateTimeZone('GMT'));
$utc = $dt->format(elgg_echo('hj:events:fulltimeformat'));

if (elgg_get_plugin_setting('timezone_disable') == 'disable' && $default_timezone = elgg_get_plugin_setting('default_timezone', 'hypeEvents')) {
	$def_tz = $dt->setTimezone(new DateTimeZone($default_timezone));
	$def_tm = $dt->format(elgg_echo('hj:events:fulltimeformat'));
	$html = "<acronym title=\"$utc\">" . $def_tm . '</acronym>';
} elseif ($default_timezone = $entity->event_timezone) {
	$def_tz = $dt->setTimezone(new DateTimeZone($default_timezone));
	$def_tm = $dt->format(elgg_echo('hj:events:fulltimeformat'));
	$html = "<acronym title=\"$utc\">" . $def_tm . '</acronym>';
} else {
	$html = "<acronym title=\"$utc\">" . date(elgg_echo('hj:events:fulltimeformat'), $value) . '</acronym>';
}

echo $html;

