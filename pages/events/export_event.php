<?php

$event_guids = get_input('e');
$event_guids = explode(',', $event_guids);

elgg_load_library('hj:ical');

$config = array('unique_id' => elgg_get_site_url());

$v = new vcalendar($config);
$v->setProperty('method', 'PUBLISH');

$cal_name = elgg_get_plugin_setting('calendar_name', 'hypeEvents');
$cal_desc = elgg_get_plugin_setting('calendar_description', 'hypeEvents');
$v->setProperty("x-wr-calname", "$cal_name");
$v->setProperty("X-WR-CALDESC", "$cal_desc");
$v->setProperty("X-WR-TIMEZONE", "UTC");

foreach ($event_guids as $guid) {
	$event = get_entity($guid);

	$vevent = & $v->newComponent('vevent');
	
	$cal = getdate((int) $event->calendar_start);
	$start = array('year' => $cal['year'], 'month' => $cal['mon'], 'day' => $cal['mday'], 'hour' => $cal['hours'], 'min' => $cal['minutes'], 'sec' => $cal['seconds']);
	$vevent->setProperty('dtstart', $start);

	if ($event->calendar_end) {
		$cal = getdate((int) $event->calendar_end);
		$end = array('year' => $cal['year'], 'month' => $cal['mon'], 'day' => $cal['mday'], 'hour' => $cal['hours'], 'min' => $cal['minutes'], 'sec' => $cal['seconds']);
		$vevent->setProperty('dtend', $end);
	}

	$tags = $event->getTags();
	if ($tags) {
		$vevent->setProperty('categories', $tags);
	}

	$vevent->setProperty('class', 'PRIVATE');
	$vevent->setProperty('organizer', $event->host);
	$vevent->setProperty('uid', $event->cal_uid);
	$vevent->setProperty('url', $event->getURL());
	
	$vevent->setProperty('location', $event->venue . ', ' . $event->location);
	$vevent->setProperty('summary', $event->title);
	$vevent->setProperty('description', elgg_strip_tags($event->description));
}

$v->returnCalendar();
