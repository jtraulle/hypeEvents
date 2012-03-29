<?php

$form_vars = array('enctype' => 'multipart/form-data');
$form = elgg_view_form('hj/events/import', $form_vars);

$sidebar = elgg_view('hj/events/sidebar');

$content = elgg_view_layout('hj/profile', array(
	'sidebar' => $sidebar,
	'content' => $form
));

echo elgg_view_page(elgg_echo('hj:events:import'), $content);

