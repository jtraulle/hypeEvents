<?php

$event_guid = get_input('e');
$event = get_entity($event_guid);

if (!elgg_instanceof($event, 'object', 'hjevent')) {
	forward();
}

elgg_push_breadcrumb($event->title, $event->getURL());

$content = elgg_view_entity($event, array('full_view' => true));
$sidebar = elgg_view('hj/events/sidebar', array('events' => array($event)));

$page = elgg_view_layout('hj/profile', array(
	'content' => $content,
	'sidebar' => $sidebar
));

$page = elgg_view_layout('one_column', array('content' => $page));

echo elgg_view_page($event->title, $page);

