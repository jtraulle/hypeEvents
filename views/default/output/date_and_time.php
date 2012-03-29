<?php

$value = elgg_extract('value', $vars);
$entity = elgg_extract('entity', $vars);

$dt = new DateTime();
$dt->setTimestamp($value);
$dt->setTimezone(new DateTimeZone('GMT'));
$utc = $dt->format(elgg_echo('hj:events:fulltimeformat'));

echo "<acronym title=\"$utc\">" . date(elgg_echo('hj:events:fulltimeformat'), $value) . '</acronym>';



