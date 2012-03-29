<?php

$entity = elgg_extract('entity', $vars, false);
$full = elgg_extract('full_view', $vars, false);

if (!$entity) {
	return true;
}

elgg_load_css('hj.events.base');
elgg_load_js('hj.events.base');

$form = hj_framework_get_data_pattern('object', 'hjevent');
$fields = $form->getFields();

$owner = $entity->getOwnerEntity();

// Short View of the Entity
$title = elgg_view('output/url', array('text' => $entity->title, 'href' => $entity->getURL()));

$time_icon = elgg_view_icon('hj hj-icon-time');

if ($entity->calendar_start) {
	$starttime = elgg_view('output/date_and_time', array('value' => $entity->calendar_start));
	$starttime_str = elgg_echo('hj:events:starttime', array($starttime));
}

if ($entity->calendar_end) {
	$endtime = elgg_view('output/date_and_time', array('value' => $entity->calendar_end));
	$endtime_str = elgg_echo('hj:events:endtime', array($endtime));
}

$subtitle .= elgg_view_image_block($time_icon, $starttime_str . '<br />' . $endtime_str);

$location_icon = elgg_view_icon('hj hj-icon-location');

if ($entity->venue) {
	$location[] = $entity->venue;
}
if ($entity->location) {
	$location[] = elgg_view('output/location', array('entity' => $entity));
	$subtitle .= elgg_view_image_block($location_icon, implode(', ', $location));
}

$params = elgg_clean_vars($vars);
$params = hj_framework_extract_params_from_entity($entity, $params);

if (!$full || (elgg_is_xhr() && !elgg_in_context('fancybox'))) {
	//$short_description = elgg_get_excerpt($entity->description);
	$icon = elgg_view_entity_icon($entity, 'small');
} else {
	$icon = elgg_view_entity_icon($entity, 'medium');

	if (is_array($fields)) {
		foreach ($fields as $field) {
			$field_name = $field->name;
			if ($entity->$field_name != '' && !in_array($field_name, array('title', 'tags')) && !in_array($field->input_type, array('access', 'hidden'))) {
				$output_type = $field->input_type;

				if ($output_type == 'dropdown') {
					$output_value = elgg_echo("{$field->name}:value:{$entity->$field_name}");
				} else {
					$output_value = $entity->$field_name;
				}
				$output_label = elgg_echo($field->getLabel());
				$output_text = elgg_view("output/$output_type", array('value' => $output_value, 'entity' => $entity));
				//$output_icon = elgg_view_icon($entity->$field_name);
				if (!empty($output_value)) {
					$full_description .= elgg_view_module('aside', $output_label, $output_text);
				}
			}
		}
	}
}

$params['target'] = "elgg-object-$entity->guid";
$params['fbox_x'] = '900';
$header_menu = elgg_view_menu('hjentityhead', array(
	'entity' => $entity,
	'current_view' => $full,
	'handler' => 'hjevent',
	'class' => 'elgg-menu-hz hj-menu-hz',
	'sort_by' => 'priority',
	'params' => $params
		));

if (!elgg_in_context('print') && !elgg_in_context('activity') && $full) {
	$rsvp_menu = elgg_view_menu('rsvp', array(
		'entity' => $entity,
		'current_view' => $full,
		'handler' => 'hjevent',
		'class' => 'elgg-menu-hz hj-menu-hz hj-events-rsvp-menu',
		'sort_by' => 'priority',
		'params' => $params
			));
}

$params = array(
	'entity' => $entity,
	'title' => $title,
	'metadata' => $header_menu,
	'subtitle' => $subtitle,
	'content' => $short_description,
	'class' => 'hj-portfolio-widget'
);

$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);
$summary = elgg_view_image_block($icon, $list_body);

echo "<div id=\"elgg-object-$entity->guid\">";
if (!$full || (elgg_is_xhr() && !elgg_in_context('fancybox'))) {
	echo $summary;
} else {

	$sidebar = elgg_view_module('info', elgg_echo('hj:events:rsvp'), $rsvp_menu);

	if (elgg_is_active_plugin('hypeMaps')) {
		$map = '<div style="margin:0 auto">' . elgg_view('hj/maps/map', array(
					'entity' => $entity,
					'height' => '250px'
				)) . '</div>';

		$sidebar .= elgg_view_module('info', elgg_echo('hj:events:map'), $map);
	}
	$rsvps = array();
	$rsvps['attending'] = $entity->getRSVPs('attending');
	$rsvps['maybe_attending'] = $entity->getRSVPs('maybe_attending');
	$rsvps['not_attending'] = $entity->getRSVPs('not_attending');

	foreach ($rsvps as $rsvp_type => $attendees) {
		if (sizeof($attendees) > 0) {
			$attendee_list = elgg_view_entity_list($attendees, array(
				'list_type' => 'gallery',
				'size' => 'small'
					));
			$sidebar .= elgg_view_module('info', elgg_echo('hj:events:rsvp:' . $rsvp_type), $attendee_list);
		}
	}

	$full_description = elgg_view_module('info', elgg_echo('hj:events:event:details'), $full_description);

	$comments .= elgg_view_module('info', elgg_echo('comments'), elgg_view_comments($entity));

	echo $summary;
	echo elgg_view_layout('hj/dynamic', array(
		'grid' => array(8, 4),
		'content' => array($full_description . $comments, $sidebar)
	));
}
echo '</div>';