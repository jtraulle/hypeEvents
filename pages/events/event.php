<?php

$event_guid = get_input('e');
$event = get_entity($event_guid);

if (!elgg_instanceof($event, 'object', 'hjevent')) {
	forward();
}

$owner = get_entity($event->owner_guid);
elgg_set_page_owner_guid($owner->guid);

elgg_push_breadcrumb($owner->name, "events/owner/$owner->username");
elgg_push_breadcrumb($event->title);

$content = elgg_view_entity($event, array('full_view' => true));
$sidebar = elgg_view('hj/events/sidebar', array('events' => array($event)));

$page = elgg_view_layout('one_sidebar', array(
	'content' => $content,
	'sidebar' => $sidebar
));

echo elgg_view_page($event->title, $page);

