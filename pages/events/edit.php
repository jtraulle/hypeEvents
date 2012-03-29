<?php

elgg_load_css('hj.events.base');
elgg_load_js('hj.events.base');

$form = hj_framework_get_data_pattern('object', 'hjevent');
$event_guid = get_input('e');

if ($event_guid) {
	$event = get_entity($event_guid);
	elgg_push_breadcrumb($event->title, $event->getURL());
	elgg_push_breadcrumb(elgg_echo('edit'));
	$params = hj_framework_extract_params_from_entity($event);
} else {
	$params['form_guid'] = $form->guid;
	elgg_push_breadcrumb(elgg_echo('create'));
	$params = hj_framework_extract_params_from_params($params);
}
$params['ajaxify'] = false;

$body = elgg_view_entity($form, $params);
$layout = elgg_view_layout('one_sidebar', array(
	'content' => $body
));
echo elgg_view_page($form->title, $layout);

