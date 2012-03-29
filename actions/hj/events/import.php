<?php

global $_FILES;

if (!empty($_FILES['calendar']['name']) && $_FILES['calendar']['error'] != 0) {
	register_error(elgg_echo('file:cannotload'));
	forward(REFERER);
}

$user = elgg_get_logged_in_user_entity();

if (!empty($_FILES['calendar']['name'])) {
	$filehandler = new hjFile();
	$filehandler->owner_guid = elgg_get_logged_in_user_guid();
	$filehandler->access_id = ACCESS_PRIVATE;

	$prefix = "hjfile/calendar/";

	$filestorename = elgg_strtolower($_FILES['calendar']['name']);

	$filehandler->setFilename($prefix . $filestorename);
	$filehandler->setMimeType($_FILES['calendar']['type']);
	$filehandler->originalfilename = $_FILES['calendar']['name'];
	$filehandler->simpletype = file_get_simple_type($_FILES['calendar']['type']);

	$filehandler->open("write");
	$filehandler->close();
	move_uploaded_file($_FILES['calendar']['tmp_name'], $filehandler->getFilenameOnFilestore());

	$cal_to_parse = $filehandler->grabFile();
	$cal_to_parse = explode('\n', $cal_to_parse);
	
} elseif ($url = get_input('url')) {
	$cal_to_parse = explode('\n', @file_get_contents($url));
	if (get_input('check_updates')) {
		$count = elgg_get_metadata(array(
			'guid' => $user->guid,
			'metadata_names' => 'ical_cron',
			'metadata_values' => $url,
			'count' => true
		));
		if ($count <= 0) {
			create_metadata($user->guid, 'ical_cron', $url, '', $user->guid, ACCESS_PUBLIC, true);
		}
	}
}

if (empty($cal_to_parse)) {
	register_error(elgg_echo('hj:events:nourltoparse'));
	forward(REFERER);
}

$imported = 0;
$updated = 0;
$failed = 0;

elgg_load_library('hj:ical');

$config = array('unique_id' => elgg_get_site_url());
$v = new vcalendar($config);
$v->parse($cal_to_parse);
$v->sort();
if (!$timezone = $v->getProperty('X-WR-TIMEZONE')) {
	$timezone = 'UCT';
} else {
	$timezone = $timezone[1];
}

for ($i = 1; $i <= sizeof($v->components); $i++) {
	$event = $v->getComponent($i);
	$uid = $event->getProperty('uid');
	
	$search = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'hjevent',
		'metadata_name' => 'cal_uid',
		'metadata_value' => $uid,
		'limit' => 1
			));

	if ($search && sizeof($search) > 0) {
		$guid = $search[0]->guid;
	} else {
		$guid = null;
	}
	$ev = new hjEvent($guid);
	$ev->owner_guid = elgg_get_logged_in_user_guid();
	$ev->access_id = ACCESS_PRIVATE;
	$ev->title = $event->getProperty("summary");
	$ev->description = $event->getProperty("description");
	$ev->cal_uid = $uid;

	$dt = new DateTime(implode('', $event->getProperty('dtstart')) . " $timezone");
	$ev->calendar_start = $dt->getTimestamp();

	$dt = new DateTime(implode('', $event->getProperty('dtend')) . " $timezone");
	$ev->calendar_end = $dt->getTimestamp();

	$summary = $event->getProperty("summary");
	$description = $event->getProperty("description");

	if ($location = $event->getProperty('location')) {
		$ev->location = $location;
	} else {
		$ev->location = null;
	}

	$ev->host = $event->getProperty('organizer');
	$ev->contact_details = $event->getProperty('contact');
	$ev->www = $event->getProperty('url');
	$ev->tags = $event->getProperty('categories');


	$result = $ev->save();

	if ($location && $result && elgg_is_active_plugin('hypeMaps')) {
		$location = new hjEntityLocation($ev->guid);
		$location->setAddressMetadata($location);
		$location->setEntityLocation($location);
	}

	if ($result && $guid) {
		$updated++;
	} else if ($result) {
		$imported++;
	} else {
		$failed++;
	}
}

system_message(elgg_echo('hj:events:import:status', array($imported, $updated, $failed)));


