<?php

elgg_push_breadcrumb(elgg_echo('hj:events:import'));

$form_vars = array('enctype' => 'multipart/form-data');
$form = elgg_view_form('hj/events/import', $form_vars);

$sidebar = elgg_view('hj/events/sidebar');


$content = elgg_view_layout('one_sidebar', array(
	'sidebar' => $sidebar,
	'content' => $form
));

echo elgg_view_page(elgg_echo('hj:events:import'), $content);

