<?php

$limit = get_input('limit', 10);
$offset = get_input('offset', 0);

$calendar_start = (int) get_input('calendar_start', false);
$calendar_end = (int) get_input('calendar_end', false);
$location = get_input('location', false);

$date = (int) get_input('date');

$options = array(
	'type' => 'object',
	'subtype' => 'hjevent',
	'limit' => $limit,
	'offset' => $offset,
	'order_by_metadata' => array('name' => 'calendar_start', 'direction' => 'asc', 'as' => 'integer')
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

$current_time = date(elgg_echo('hj:events:fulltimeformat'));
$dt = new DateTime($current_time);
$dt = $dt->setTimezone(new DateTimeZone('UTC'));
$timestamp = $dt->getTimestamp();

if (!$calendar_start && !$calendar_end && !$location && !$date) {
	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_start', 'value' => $timestamp, 'operand' => '>');

	$upcoming_events = elgg_get_entities_from_metadata($options);
	$options['count'] = true;
	$upcoming_events_count = elgg_get_entities_from_metadata($options);
	unset($options['count']);
	$upcoming_events_options = $options;
	unset($options['metadata_name_value_pairs']);

	$target = 'hj-upcoming-events-list';
	$view_params = array(
		'full_view' => false,
		'list_id' => $target,
		'list_class' => 'hj-view-list',
		'item_class' => 'hj-view-entity elgg-state-draggable',
		'pagination' => true,
		'data-options' => $upcoming_events_options,
		'limit' => $limit,
		'count' => $upcoming_events_count,
		'base_url' => 'events/sync',
		'dom_order' => 'append'
	);

	$upcoming_events_list = elgg_view_entity_list($upcoming_events, $view_params);

	$options['order_by_metadata'] = array('name' => 'calendar_start', 'direction' => 'desc', 'as' => 'integer');
	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_end', 'value' => $timestamp, 'operand' => '<');

	$past_events = elgg_get_entities_from_metadata($options);
	$options['count'] = true;
	$past_events_count = elgg_get_entities_from_metadata($options);
	unset($options['count']);
	$past_events_options = $options;

	$target = 'hj-past-events-list';
	$view_params = array(
		'full_view' => false,
		'list_id' => $target,
		'list_class' => 'hj-view-list',
		'item_class' => 'hj-view-entity elgg-state-draggable',
		'pagination' => true,
		'data-options' => $past_events_options,
		'limit' => $limit,
		'count' => $past_events_count,
		'base_url' => 'events/sync',
		'dom_order' => 'append'
	);

	$past_events_list = elgg_view_entity_list($past_events, $view_params);

	$content .= elgg_view_module('aside', elgg_echo('hj:events:upcoming'), $upcoming_events_list);

	$content .= elgg_view_module('aside', elgg_echo('hj:events:past'), $past_events_list);
} else {
	$events = elgg_get_entities_from_metadata($options);
	$options['count'] = true;
	$events_count = elgg_get_entities_from_metadata($options);
	unset($options['count']);
	$events_options = $options;

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

	$events_list = elgg_view_entity_list($events, $view_params);

	$content .= elgg_view_module('aside', elgg_echo('hj:events:filtered'), $events_list);
}

$content = elgg_view_module('info', elgg_echo('hj:events'), $content, array('id' => 'hj-events-module'));
$search_box = elgg_view('hj/events/filter');

$sidebar = elgg_view('hj/events/sidebar');
$sidebar .= $search_box;

$page = elgg_view_layout('content', array(
	'sidebar' => $sidebar,
	'content' => $content
		));

echo elgg_view_page(elgg_echo('hj:events'), $page);