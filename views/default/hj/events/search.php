<?php

$calendar = "<div style=\"margin:0 auto;\"><div id=\"hj-events-calendar-datepicker\"></div></div>";

$form_body .= '<label>' . elgg_echo('hj:events:search:after') . '</label>';
$form_body .= elgg_view('input/date', array(
	'value' => (int)get_input('calendar_start', time()),
	'name' => 'search[calendar_start]',
	'timestamp' => true
));

$form_body .= '<label>' . elgg_echo('hj:events:search:before') . '</label>';
$form_body .= elgg_view('input/date', array(
	'value' => (int)get_input('calendar_end', time()),
	'name' => 'search[calendar_end]',
	'timestamp' => true
));

$form_body .= '<label>' . elgg_echo('hj:events:search:location') . '</label>';
$form_body .= elgg_view('input/text', array(
	'value' => get_input('location'),
	'name' => 'search[location]'
));

$form_body .= elgg_view('input/submit', array(
	'value' => elgg_echo('search')
));

$form = elgg_view('input/form', array(
	'id' => 'hj-events-search',
	'action' => 'action/events/search',
	'body' => $form_body
));

$calendar_module = elgg_view_module('info', elgg_echo('hj:events:calendar'), $calendar);
$search_module = elgg_view_module('info', elgg_echo('hj:events:search'), $form);

echo $calendar_module;
echo $search_module;