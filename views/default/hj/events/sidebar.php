<?php

/**
 * Maps owner block
 */
$user = elgg_get_logged_in_user_entity();

elgg_load_js('hj.framework.ajax');
elgg_load_js('hj.framework.fieldcheck');

elgg_register_menu_item('page', array(
	'name' => 'allevents',
	'title' => elgg_echo('hj:events:allevents'),
	'text' => elgg_echo('hj:events:allevents'),
	'href' => "events/all",
	'priority' => 400
));

elgg_register_menu_item('page', array(
	'name' => 'myevents',
	'title' => elgg_echo('hj:events:myevents'),
	'text' => elgg_echo('hj:events:myevents'),
	'href' => "events/owner/$user->username",
	'priority' => 500
));

//elgg_register_menu_item('page', array(
//		'name' => 'friendsevents',
//		'title' => elgg_echo('hj:events:friendevents'),
//		'text' => elgg_echo('hj:events:friendevents'),
//		'href' => "events/friends/$user->username",
//		'priority' => 600
//	));

elgg_load_js('hj.framework.fieldcheck');
$form = hj_framework_get_data_pattern('object', 'hjevent');
$params = array(
	'form_guid' => $form->guid,
	'subtype' => 'hjevent',
	'owner_guid' => $user->guid,
	'target' => 'hj-upcoming-events-list',
	'dom_order' => 'prepend'
);
$params = hj_framework_extract_params_from_params($params);
$params = hj_framework_json_query($params);

elgg_register_menu_item('page', array(
	'name' => 'addevent',
	'title' => elgg_echo('hj:events:addnew'),
	'text' => elgg_echo('hj:events:addnew'),
	'href' => "action/framework/entities/edit",
	'is_action' => true,
	'rel' => 'fancybox',
	'id' => "hj-ajaxed-add-hjevent",
	'data-options' => htmlentities($params, ENT_QUOTES, 'UTF-8'),
	'class' => "hj-ajaxed-add",
	'target' => "",
	'priority' => 700
));

//elgg_register_menu_item('page', array(
//    'name' => 'addnewplace:details',
//    'parent_name' => 'addnewplace',
//    'text' => elgg_view_entity($form, array('params' => $params)),
//    'class' => 'hj-maps-menu-child',
//    'href' => false
//));

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

if (elgg_is_logged_in()) {
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

echo <<<HTML
        <div id="hj-events-owner-block-$user->guid" class="hj-events-owner-block">
                $content_menu
</div>

HTML;
