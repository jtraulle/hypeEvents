<?php

$q = get_input('search');

$limit = get_input('limit', 10);
$offset = get_input('offset', 0);

$calendar_start = (int) $q['calendar_start'];
$calendar_end = (int) $q['calendar_end'];
$location = $q['location'];

$date = (int) get_input('date');

$options = array(
	'type' => 'object',
	'subtype' => 'hjevent',
	'limit' => $limit,
	'offset' => $offset,
	'order_by_metadata' => array('name' => 'calendar_start', 'direction' => 'ASC', 'as' => 'integer')
);

if ($calendar_start) {
	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_start', 'value' => $calendar_start, 'operand' => '>=');
}

if ($calendar_end) {
	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_end', 'value' => $calendar_end, 'operand' => '<=');
}

if ($location) {
	$options['metadata_name_value_pairs'][] = array('name' => 'location', 'value' => '%' . $location . '%', 'operand' => 'LIKE');
}

if ($date) {
	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_start', 'value' => $date, 'operand' => '<=');
	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_end', 'value' => $date, 'operand' => '>=');
}

$events = elgg_get_entities_from_metadata($options);
$options['count'] = true;
$events_count = elgg_get_entities_from_metadata($options);
unset($options['count']);
$events_options = htmlentities(json_encode($options), ENT_QUOTES, 'UTF-8');

$target = 'hj-filtered-events-list';
$view_params = array(
	'full_view' => false,
	'list_id' => $target,
	'list_class' => 'hj-view-list',
	'item_class' => 'hj-view-entity elgg-state-draggable',
	'pagination' => true,
	'data-options' => $events_options,
	'limit' => $limit,
	'count' => $events_count,
	'base_url' => 'events/sync'
);

if ($events_count > 0) {
	$events_list = elgg_view_entity_list($events, $view_params);
} else {
	$events_list = elgg_echo('hj:events:search:notfound');
	system_message(elgg_echo('hj:events:search:notfound'));
}

$html = elgg_view_module('aside', elgg_echo('hj:events:filtered'), $events_list);

print(json_encode($html));
forward(REFERER);