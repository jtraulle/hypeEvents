<?php
/**
 * Default Form Submission River
 */

$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->excerpt);
$excerpt = elgg_get_excerpt($excerpt);

$attendees = $object->getRSVPs('attending', 5);
echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'message' => $excerpt,
    'attachments' => elgg_echo('hj:events:guestlist') . '<br />' . elgg_view_entity_list($attendees, array('list_type' => 'gallery', 'size' => 'small'))
));
