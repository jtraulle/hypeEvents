<?php

/**
 * Maps owner block
 */
$user = elgg_get_logged_in_user_entity();

elgg_load_js('hj.framework.ajax');


elgg_register_menu_item('page', array(
	'name' => 'allevents',
	'title' => elgg_echo('hj:events:allevents'),
	'text' => elgg_echo('hj:events:allevents'),
	'href' => "events/all",
	'priority' => 400
));

if (elgg_is_logged_in()) {
	elgg_register_menu_item('page', array(
		'name' => 'import',
		'title' => elgg_echo('hj:events:import'),
		'text' => elgg_echo('hj:events:import'),
		'href' => "events/import",
		'priority' => 800
	));

	elgg_register_menu_item('page', array(
		'name' => 'export',
		'title' => elgg_echo('hj:events:export:site'),
		'text' => elgg_echo('hj:events:export:site'),
		'href' => "events/export/calendar",
		'priority' => 900
	));

	elgg_register_menu_item('page', array(
		'name' => 'exportattending',
		'title' => elgg_echo('hj:events:export:attending'),
		'text' => elgg_echo('hj:events:export:attending'),
		'href' => "events/export/calendar/attending",
		'priority' => 950
	));
}

$content_menu = elgg_view_menu('page', array(
	'entity' => $user,
	'class' => 'profile-content-menu',
	'context' => elgg_get_context(),
	'sort_by' => 'priority'
		));
