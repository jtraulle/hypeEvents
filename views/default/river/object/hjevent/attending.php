<?php

/**
 * Default Form Submission River
 */
$subject = $vars['item']->getSubjectEntity();
$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->excerpt);
$excerpt = elgg_get_excerpt($excerpt);

$attendees = $object->getRSVPs('attending', 10);
if (sizeof($attendees) > 1) {
	$attachment .= '<div>' . elgg_echo('hj:events:guestlist') . '</div>';
	$attachment .= '<ul class="elgg-gallery">';
	foreach ($attendees as $attendee) {
		if ($subject->guid !== $attendee->guid) {
			$attachment .= '<li class="elgg-list-entity">' . elgg_view_entity_icon($attendee, 'small');
		}
	}
	$attachment .= '</ul>';
}
echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'message' => $excerpt,
	'attachments' => $attachment
));