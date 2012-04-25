<?php

$username = get_input('username');
$user = get_user_by_username($username);

if (!$user) {
	$user = elgg_get_logged_in_user_entity();
	forward("events/owner/$user->username");
}

$title = elgg_echo("hj:events:friends", array($user->name));

elgg_set_page_owner_guid($user->guid);
elgg_push_breadcrumb($title);

$getter_options = array(
	'limit' => get_input('limit', 10),
	'offset' => get_input('offset', 0)
);

$event_options = array(
	'calendar_start' => (int) get_input('calendar_start', false),
	'calendar_end' => (int) get_input('calendar_end', false),
	'location' => get_input('location', false),
	'date' => (int) get_input('date', false),
	'owner_guid' => $user->guid
);

$upcoming_events_list = hj_events_list_upcoming_events('friends', null, $event_options, $getter_options);
$past_events_list = hj_events_list_past_events('friends', null, $event_options, $getter_options);

$mod1 = elgg_view_module('aside', elgg_echo('hj:events:friends:upcoming'), $upcoming_events_list);
$mod2 = elgg_view_module('aside', elgg_echo('hj:events:friends:past'), $past_events_list);

$content = elgg_view_layout('hj/dynamic', array(
	'grid' => array(6,6),
	'content' => array($mod1, $mod2)
));

$sidebar = elgg_view('hj/events/sidebar');

$layout_params = array(
	'title' => $title,
	'content' => $content,
	'sidebar' => $sidebar
);

if ($user->guid == elgg_get_logged_in_user_guid()) {
	$layout_params['filter_context'] = 'friends';
} else {
	$layout_params['filter'] = false;
}

$page = elgg_view_layout('content', $layout_params);

echo elgg_view_page($title, $page);