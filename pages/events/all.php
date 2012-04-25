<?php
$getter_options = array(
	'limit' => get_input('limit', 10),
	'offset' => get_input('offset', 0)
);

$event_options = array(
	'calendar_start' => (int) get_input('calendar_start', false),
	'calendar_end' => (int) get_input('calendar_end', false),
	'location' => get_input('location', false),
	'date' => (int) get_input('date', false)
);

$upcoming_events_list = hj_events_list_upcoming_events('all', null, $event_options, $getter_options);
$past_events_list = hj_events_list_past_events('all', null, $event_options, $getter_options);

$mod1 = elgg_view_module('aside', elgg_echo('hj:events:upcoming'), $upcoming_events_list);
$mod2 = elgg_view_module('aside', elgg_echo('hj:events:past'), $past_events_list);

$content .= elgg_view_layout('hj/dynamic', array(
	'grid' => array(6, 6),
	'content' => array($mod1, $mod2)
		));

$content = elgg_view_module('info', '', $content, array('id' => 'hj-events-module'));
$search_box = elgg_view('hj/events/search');

$sidebar = elgg_view('hj/events/sidebar');
$sidebar .= $search_box;

$title = elgg_echo('hj:events:all');
$page = elgg_view_layout('content', array(
	'title' => $title,
	'sidebar' => $sidebar,
	'content' => $content,
	'filter_context' => 'all'
		));

echo elgg_view_page($title, $page);