<?php

$user = elgg_get_logged_in_user_entity();

elgg_set_page_owner_guid($user->guid);
elgg_push_breadcrumb($user->name);

$getter_options = array(
	'limit' => get_input('limit', 0),
	'offset' => get_input('offset', 0)
);

$event_options = array(
	'calendar_start' => (int) get_input('calendar_start', false),
	'calendar_end' => (int) get_input('calendar_end', false),
	'location' => get_input('location', false),
	'date' => (int) get_input('date', false),
	'owner_guid' => $user->guid
);

$upcoming_events_list = hj_events_get_upcoming_events('owner', null, $event_options, $getter_options);
//$past_events_list = hj_events_get_past_events('owner', null, $event_options, $getter_options);
$rsvps_attending = hj_events_get_user_rsvps('attending', $user, null, $getter_options);
$rsvps_maybe_attending = hj_events_get_user_rsvps('maybe_attending', $user, null, $getter_options);

$events = array_merge($upcoming_events_list, $rsvps_attending, $rsvps_maybe_attending);

$target = 'hj-events-agenda-list';
$view_params = array(
	'full_view' => false,
	'list_id' => $target,
	'list_type' => get_input('list_type', 'agenda'),
);

$content = elgg_view_entity_list($events, $view_params);
$sidebar = elgg_view('hj/events/sidebar');

$title = elgg_echo("hj:events:agenda", array($user->name));

$layout_params = array(
	'title' => $title,
	'content' => $content,
	'sidebar' => $sidebar,
	'filter' => elgg_view('hj/events/filter', array(
		'filter_context' => ''
	))
);

$page = elgg_view_layout('content', $layout_params);

echo elgg_view_page($title, $page);