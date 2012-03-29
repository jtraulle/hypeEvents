<?php

$e = get_input('e');
$event = get_entity($e);

if (elgg_instanceof($event, 'object', 'hjevent')) {
	$rsvp = get_input('response');
	$user = elgg_get_logged_in_user_entity();
	if ($event->addRSVP($user, $rsvp)) {
		system_message(elgg_echo('hj:events:rsvp:success'));
		if ($rsvp == 'attending') {
			$view = "river/object/hjevent/attending";
            if (!elgg_view_exists($view)) {
                $view = "river/object/hjformsubmission/create";
            }
            add_to_river($view, "attending", $user->guid, $event->guid);
		}
	} else {
		register_error(elgg_echo('hj:events:rsvp:error'));
	}
} else {
	system_message(elgg_echo('hj:events:rsvp:error'));
}

forward(REFERER);

