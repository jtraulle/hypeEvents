<?php

$username = get_input('username');
$user = get_user_by_username($username);

if (!$user) {
	$user = elgg_get_logged_in_user_entity();
	forward("events/owner/$user->username");
}

$limit = get_input('limit', 10);
$offset = get_input('offset', 0);

$options = array(
	'type' => 'object',
	'subtype' => 'hjevent',
	'limit' => $limit,
	'offset' => $offset,
	'owner_guid' => $user->guid,
	'order_by_metadata' => array('name' => 'calendar_start', 'direction' => 'ASC', 'as' => 'integer')
);

$events = elgg_get_entities_from_metadata($options);
$options['count'] = true;
$events_count = elgg_get_entities_from_metadata($options);
unset($options['count']);
$events_options = htmlentities(json_encode($options), ENT_QUOTES, 'UTF-8');

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

$col1 = elgg_view_module('info', elgg_echo('hj:events:owned', array($user->name)), $events_list);

$db_prefix = elgg_get_config('dbprefix');
$time = time();
$attending = elgg_list_entities_from_relationship(array(
	'relationship' => 'attending',
	'relationship_guid' => $user->guid,
	'inverse_relationship' => false,
	'limit' => 0,
	'full_view' => false,
	'joins' => array("JOIN {$db_prefix}metadata as mtx on e.guid = mtx.entity_guid
                      JOIN {$db_prefix}metastrings as msnx on mtx.name_id = msnx.id
                      JOIN {$db_prefix}metastrings as msvx on mtx.value_id = msvx.id"
	),
	'wheres' => array("((msnx.string = 'calendar_end') AND (msvx.string > $time))"),
		));

$col2 = elgg_view_module('info', elgg_echo('hj:events:user:attending', array($user->name)), $attending);

$maybe_attending = elgg_list_entities_from_relationship(array(
	'relationship' => 'maybe_attending',
	'relationship_guid' => $user->guid,
	'inverse_relationship' => false,
	'limit' => 0,
	'full_view' => false,
	'joins' => array("JOIN {$db_prefix}metadata as mtx on e.guid = mtx.entity_guid
                      JOIN {$db_prefix}metastrings as msnx on mtx.name_id = msnx.id
                      JOIN {$db_prefix}metastrings as msvx on mtx.value_id = msvx.id"
	),
	'wheres' => array("((msnx.string = 'calendar_end') AND (msvx.string > $time))"),
		));

$col2 .= elgg_view_module('info', elgg_echo('hj:events:user:maybeattending', array($user->name)), $maybe_attending);

$content = elgg_view_layout('hj/dynamic', array(
	'grid' => array(6,6),
	'content' => array($col1, $col2)
));

$sidebar = elgg_view('hj/events/sidebar');

$page = elgg_view_layout('hj/profile', array(
	'content' => $content,
	'sidebar' => $sidebar
));

echo elgg_view_page(elgg_echo("hj:events:owned", array($user->name)), $page);