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

$display = get_input('display', 'upcoming');

switch ($display) {

	case 'upcoming' :
	default :
		$content = hj_events_list_upcoming_events('all', null, $event_options, $getter_options);
		$title = elgg_echo('hj:events:upcoming');
		$filter_context = 'upcoming';
		break;

	case 'past' :
		$content = hj_events_list_past_events('all', null, $event_options, $getter_options);
		$title = elgg_echo('hj:events:past');
		$filter_context = 'past';
		break;
}

elgg_push_breadcrumb($title);

$filter = elgg_view('hj/events/filter', array(
	'filter_context' => $filter_context
		));

$sidebar = elgg_view('hj/events/sidebar');
//$sidebar .= elgg_view('hj/events/search');

$page = elgg_view_layout('content', array(
	'title' => $title,
	'sidebar' => $sidebar,
	'content' => $content,
	'filter' => $filter
		));

echo elgg_view_page($title, $page);