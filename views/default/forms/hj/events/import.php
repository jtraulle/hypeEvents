<?php

echo '<div>' . elgg_echo('hj:events:import:instructions') . '</div>';

echo '<label>' . elgg_echo('hj:events:import:file') . '</label><br />';
echo elgg_view('input/file', array(
	'name' => 'calendar'
));

echo '<label>' . elgg_echo('hj:events:import:url') . '</label><br />';
echo elgg_view('input/text', array(
	'name' => 'url'
));

echo '<label>' . elgg_echo('hj:events:import:checkupdates') . '</label><br />';
echo elgg_view('input/checkbox', array(
	'name' => 'check_updates',
	'default' => false
));

echo '<br />';

echo elgg_view('input/submit', array(
	'value' => elgg_echo('submit')
));

echo '<br /><br />';

$user = elgg_get_logged_in_user_entity();

$saved_feeds = elgg_get_metadata(array(
	'guids' => $user->guid,
	'metadata_names' => 'ical_cron',
	'limit' => 0
		));


$title = elgg_echo('hj:events:mysavedfeeds');

$content .= '<ul class="hj-view-list">';
if ($saved_feeds) {
	foreach ($saved_feeds as $feed) {
		$delete_link = elgg_view('output/url', array(
			'text' => elgg_echo('delete'),
			'href' => "action/events/deletefeed?id=$feed->id&guid=$user->guid",
			'is_action' => true
				));
		$parse_link = elgg_view('output/url', array(
			'text' => elgg_echo('hj:events:fetchfeed'),
			'href' => "action/hj/events/import?url=$feed->value",
			'is_action' => true
				));

		$content .= '<li>' . elgg_view_image_block($parse_link . '  ' . $delete_link, $feed->value) .'</li>';
	}
}
$content .= '</ul>';

echo elgg_view_module('info', $title, $content);

