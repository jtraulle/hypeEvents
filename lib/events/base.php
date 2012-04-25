<?php

/**
 * Create a new DateTime object with an offset defined by plugin setting or custom timezone
 *
 * @param str $timestamp
 * @param str $timezone
 * @return DateTime
 */
function hj_events_get_offset_date_time_object($timestamp = null, $timezone = null) {

	$dt = new DateTime();

	if ($timestamp)
		$dt->setTimestamp($timestamp);

	$dt->setTimezone(new DateTimeZone('GMT'));
	$utc = $dt->format(elgg_echo('hj:events:fulltimeformat'));

	if (elgg_get_plugin_setting('timezone_disable') == 'disable' && $default_timezone = elgg_get_plugin_setting('default_timezone', 'hypeEvents')) {
		$dt->setTimezone(new DateTimeZone($default_timezone));
	} elseif ($timezone) {
		$dt->setTimezone(new DateTimeZone($timezone));
	}

	return $dt;
}

/**
 * Prepare params for a getter function
 *
 * @param str $rel all|owner|friends  set owner_guid in $event_options for owner or friends
 * @param array $event_options
 * @return array
 */
function hj_events_prepare_params($rel = 'all', $event_options = array(), $getter_options = array()) {

	$calendar_start = elgg_extract('calendar_start', $event_options, false);
	$calendar_end = elgg_extract('calendar_end', $event_options, false);
	$location = elgg_extract('location', $event_options, false);
	$date = elgg_extract('date', $event_options, false);

	$defaults = array(
		'type' => 'object',
		'subtype' => 'hjevent',
		'limit' => 10,
		'offset' => 0,
		'count' => false,
		'order_by_metadata' => array('name' => 'calendar_start', 'direction' => 'asc', 'as' => 'integer')
	);

	$defaults = array_merge($defaults, $getter_options);

	if ($calendar_start) {
		$defaults['metadata_name_value_pairs'][] = array('name' => 'calendar_start', 'value' => $calendar_start, 'operand' => '>=');
	}

	if ($calendar_end) {
		$defaults['metadata_name_value_pairs'][] = array('name' => 'calendar_end', 'value' => $calendar_end, 'operand' => '<=');
	}

	if ($location) {
		$defaults['metadata_name_value_pairs'][] = array('name' => 'location', 'value' => '%' . $location . '%', 'operand' => 'LIKE');
	}

	if ($date) {
		$defaults['metadata_name_value_pairs'][] = array('name' => 'calendar_start', 'value' => $date, 'operand' => '<=');
		$defaults['metadata_name_value_pairs'][] = array('name' => 'calendar_end', 'value' => $date, 'operand' => '>=');
	}

	$owner_guid = elgg_extract('owner_guid', $event_options, elgg_get_logged_in_user_guid());

	switch ($rel) {

		case 'all' :
		default :
			// do nothing
			break;

		case 'owner' :
			$defaults['owner_guid'] = $owner_guid;
			break;

		case 'friends' :
			$defaults['owner_guids'] = array();
			$user = get_entity($owner_guid);
			if (elgg_instanceof($user, 'user')) {
				$friends = $user->getFriends("", 0, 0);
				if (sizeof($friends) > 0) {
					foreach ($friends as $friend) {
						$defaults['owner_guids'][] = $friend->guid;
					}
				} else {
					return false;
				}
			}
			break;
	}

	return $defaults;
}

/**
 * Get upcoming events
 *
 * @param str $rel all|owner|friends
 * @return Array of hjEvent|false
 */
function hj_events_get_upcoming_events($rel = 'all', $timestamp = null, $event_options = array(), $getter_options = array(), $return_options = false) {

	$options = hj_events_prepare_params($rel, $event_options, $getter_options);

	if (!$options) {
		return false;
	}
	if (!$timestamp) {
		$dt = hj_events_get_offset_date_time_object();
		$timestamp = $dt->getTimestamp();
	}

	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_start', 'value' => $timestamp, 'operand' => '>=');

	$upcoming_events = elgg_get_entities_from_metadata($options);

	if (!$return_options) {
		return $upcoming_events;
	} else {
		return $options;
	}
}

/**
 * List upcoming events
 *
 * @param str $rel
 * @return str
 */
function hj_events_list_upcoming_events($rel = 'all', $timestamp = null, $event_options = array(), $getter_options = array()) {

	$options = hj_events_get_upcoming_events($rel, $timestamp, $event_options, $getter_options, true);
	$upcoming_events_count = hj_events_get_upcoming_events($rel, $timestamp, $event_options, array('count' => true));
	$upcoming_events = hj_events_get_upcoming_events($rel, $timestamp, $event_options);

	$target = 'hj-upcoming-events-list';
	$view_params = array(
		'full_view' => false,
		'list_id' => $target,
		'list_class' => 'hj-view-list',
		'item_class' => 'hj-view-entity elgg-state-draggable',
		'pagination' => true,
		'data-options' => $options,
		'limit' => $options['limit'],
		'count' => $upcoming_events_count,
		'base_url' => 'events/sync',
		'dom_order' => 'append'
	);

	$upcoming_events_list = elgg_view_entity_list($upcoming_events, $view_params);

	return $upcoming_events_list;
}

/**
 * Get past events
 *
 * @param str $rel all|owner|friends
 * @return Array of hjEvent|false
 */
function hj_events_get_past_events($rel = 'all', $timestamp = null, $event_options = array(), $getter_options = array(), $return_options = false) {

	$options = hj_events_prepare_params($rel, $event_options, $getter_options);

	if (!$options) {
		return false;
	}
	
	if (!$timestamp) {
		$dt = hj_events_get_offset_date_time_object();
		$timestamp = $dt->getTimestamp();
	}

	$options['order_by_metadata'] = array('name' => 'calendar_start', 'direction' => 'desc', 'as' => 'integer');
	$options['metadata_name_value_pairs'][] = array('name' => 'calendar_end', 'value' => $timestamp, 'operand' => '<');

	$upcoming_events = elgg_get_entities_from_metadata($options);

	if (!$return_options) {
		return $upcoming_events;
	} else {
		return $options;
	}
}

/**
 * List upcoming events
 *
 * @param str $rel
 * @return str
 */
function hj_events_list_past_events($rel = 'all', $timestamp = null, $event_options = array(), $getter_options = array()) {

	$options = hj_events_get_past_events($rel, $timestamp, $event_options, $getter_options, true);
	$past_events_count = hj_events_get_past_events($rel, $timestamp, $event_options, array('count' => true));
	$past_events = hj_events_get_past_events($rel, $timestamp, $event_options);

	$target = 'hj-past-events-list';
	$view_params = array(
		'full_view' => false,
		'list_id' => $target,
		'list_class' => 'hj-view-list',
		'item_class' => 'hj-view-entity elgg-state-draggable',
		'pagination' => true,
		'data-options' => $options,
		'limit' => $options['limit'],
		'count' => $past_events_count,
		'base_url' => 'events/sync',
		'dom_order' => 'append'
	);

	$past_events_list = elgg_view_entity_list($past_events, $view_params);

	return $past_events_list;
}

/**
 * 	Get events that user rsvp'ed to
 *
 * @param str $type attending|maybe_attending|not_attending
 * @param ElggUser $user
 * @param str $timestamp
 * @param array $getter_options
 * @param bool $return_options
 * @return Array of hjEvent
 */
function hj_events_get_user_rsvps($type = 'attending', $user = null, $timestamp = null, $getter_options = array(), $return_options = false) {

	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}
	if (!$timestamp) {
		$dt = hj_events_get_offset_date_time_object();
		$timestamp = $dt->getTimestamp();
	}

	$db_prefix = elgg_get_config('dbprefix');
	$rsvp_options = array(
		'relationship' => $type,
		'relationship_guid' => $user->guid,
		'inverse_relationship' => false,
		'type' => 'object',
		'subtype' => 'hjevent',
		'limit' => 10,
		'offset' => 0,
		'joins' => array("JOIN {$db_prefix}metadata as mtx on e.guid = mtx.entity_guid
                      JOIN {$db_prefix}metastrings as msnx on mtx.name_id = msnx.id
                      JOIN {$db_prefix}metastrings as msvx on mtx.value_id = msvx.id"
		),
		'wheres' => array("((msnx.string = 'calendar_end') AND (msvx.string > $timestamp))"),
			//'order_by_metadata' => array('name' => 'calendar_start', 'direction' => 'asc', 'as' => 'integer')
	);

	$options = array_merge($rsvp_options, $getter_options);

	if (!$return_options) {
		return elgg_get_entities_from_relationship($options);
	} else {
		return $options;
	}
}

function hj_events_list_user_rsvps($type = 'attending', $user = null, $timestamp = null, $getter_options = array()) {

	$options = hj_events_get_user_rsvps($type, $user, $timestamp, $getter_options, true);
	$events = hj_events_get_user_rsvps($type, $user, $timestamp, $getter_options, false);
	$getter_options['count'] = true;
	$events_count = hj_events_get_user_rsvps($type, $user, $timestamp, $getter_options, false);

	$target = "hj-rsvps-$type-$user->guid";
	$view_params = array(
		'full_view' => false,
		'list_id' => $target,
		'list_class' => 'hj-view-list',
		'item_class' => 'hj-view-entity elgg-state-draggable',
		'pagination' => true,
		'data-options' => $options,
		'limit' => $options['limit'],
		'count' => $events_count,
		'base_url' => 'hj/sync/relationship',
		'dom_order' => 'append'
	);

	$events_list = elgg_view_entity_list($events, $view_params);

	return $events_list;
}

/**
 * Helper function for looking up timezones
 *
 * @param array $associated_array
 * @return array
 */
function hj_events_default_timezones($associated_array = true, $reverse_association) {

	$defaults = array(
		'Pacific/Kwajalein' => elgg_echo('Pacific/Kwajalein'),
		'Pacific/Midway' => elgg_echo('Pacific/Midway'),
		'Pacific/Samoa' => elgg_echo('Pacific/Samoa'),
		'Pacific/Honolulu' => elgg_echo('Pacific/Honolulu'),
		'America/Anchorage' => elgg_echo('America/Anchorage'),
		'America/Los_Angeles' => elgg_echo('America/Los_Angeles'),
		'America/Tijuana' => elgg_echo('America/Tijuana'),
		'America/Denver' => elgg_echo('America/Denver'),
		'America/Chihuahua' => elgg_echo('America/Chihuahua'),
		'America/Mazatlan' => elgg_echo('America/Mazatlan'),
		'America/Phoenix' => elgg_echo('America/Phoenix'),
		'America/Regina' => elgg_echo('America/Regina'),
		'America/Tegucigalpa' => elgg_echo('America/Tegucigalpa'),
		'America/Chicago' => elgg_echo('America/Chicago'),
		'America/Mexico_City' => elgg_echo('America/Mexico_City'),
		'America/Monterrey' => elgg_echo('America/Monterrey'),
		'America/New_York' => elgg_echo('America/New_York'),
		'America/Bogota' => elgg_echo('America/Bogota'),
		'America/Lima' => elgg_echo('America/Lima'),
		'America/Rio_Branco' => elgg_echo('America/Rio_Branco'),
		'America/Indiana/Indianapolis' => elgg_echo('America/Indiana/Indianapolis'),
		'America/Caracas' => elgg_echo('America/Caracas'),
		'America/Halifax' => elgg_echo('America/Halifax'),
		'America/Manaus' => elgg_echo('America/Manaus'),
		'America/Santiago' => elgg_echo('America/Santiago'),
		'America/La_Paz' => elgg_echo('America/La_Paz'),
		'America/St_Johns' => elgg_echo('America/St_Johns'),
		'America/Argentina/Buenos_Aires' => elgg_echo('America/Argentina/Buenos_Aires'),
		'America/Sao_Paulo' => elgg_echo('America/Sao_Paulo'),
		'America/Godthab' => elgg_echo('America/Godthab'),
		'America/Montevideo' => elgg_echo('America/Montevideo'),
		'Atlantic/South_Georgia' => elgg_echo('Atlantic/South_Georgia'),
		'Atlantic/Azores' => elgg_echo('Atlantic/Azores'),
		'Atlantic/Cape_Verde' => elgg_echo('Atlantic/Cape_Verde'),
		'Europe/Dublin' => elgg_echo('Europe/Dublin'),
		'Europe/Lisbon' => elgg_echo('Europe/Lisbon'),
		'Europe/London' => elgg_echo('Europe/London'),
		'Africa/Monrovia' => elgg_echo('Africa/Monrovia'),
		'Atlantic/Reykjavik' => elgg_echo('Atlantic/Reykjavik'),
		'Africa/Casablanca' => elgg_echo('Africa/Casablanca'),
		'Europe/Belgrade' => elgg_echo('Europe/Belgrade'),
		'Europe/Bratislava' => elgg_echo('Europe/Bratislava'),
		'Europe/Budapest' => elgg_echo('Europe/Budapest'),
		'Europe/Ljubljana' => elgg_echo('Europe/Ljubljana'),
		'Europe/Prague' => elgg_echo('Europe/Prague'),
		'Europe/Sarajevo' => elgg_echo('Europe/Sarajevo'),
		'Europe/Skopje' => elgg_echo('Europe/Skopje'),
		'Europe/Warsaw' => elgg_echo('Europe/Warsaw'),
		'Europe/Zagreb' => elgg_echo('Europe/Zagreb'),
		'Europe/Brussels' => elgg_echo('Europe/Brussels'),
		'Europe/Copenhagen' => elgg_echo('Europe/Copenhagen'),
		'Europe/Madrid' => elgg_echo('Europe/Madrid'),
		'Europe/Paris' => elgg_echo('Europe/Paris'),
		'Africa/Algiers' => elgg_echo('Africa/Algiers'),
		'Europe/Amsterdam' => elgg_echo('Europe/Amsterdam'),
		'Europe/Berlin' => elgg_echo('Europe/Berlin'),
		'Europe/Rome' => elgg_echo('Europe/Rome'),
		'Europe/Stockholm' => elgg_echo('Europe/Stockholm'),
		'Europe/Vienna' => elgg_echo('Europe/Vienna'),
		'Europe/Minsk' => elgg_echo('Europe/Minsk'),
		'Africa/Cairo' => elgg_echo('Africa/Cairo'),
		'Europe/Helsinki' => elgg_echo('Europe/Helsinki'),
		'Europe/Riga' => elgg_echo('Europe/Riga'),
		'Europe/Sofia' => elgg_echo('Europe/Sofia'),
		'Europe/Tallinn' => elgg_echo('Europe/Tallinn'),
		'Europe/Vilnius' => elgg_echo('Europe/Vilnius'),
		'Europe/Athens' => elgg_echo('Europe/Athens'),
		'Europe/Bucharest' => elgg_echo('Europe/Bucharest'),
		'Europe/Istanbul' => elgg_echo('Europe/Istanbul'),
		'Asia/Jerusalem' => elgg_echo('Asia/Jerusalem'),
		'Asia/Amman' => elgg_echo('Asia/Amman'),
		'Asia/Beirut' => elgg_echo('Asia/Beirut'),
		'Africa/Windhoek' => elgg_echo('Africa/Windhoek'),
		'Africa/Harare' => elgg_echo('Africa/Harare'),
		'Asia/Kuwait' => elgg_echo('Asia/Kuwait'),
		'Asia/Riyadh' => elgg_echo('Asia/Riyadh'),
		'Asia/Baghdad' => elgg_echo('Asia/Baghdad'),
		'Africa/Nairobi' => elgg_echo('Africa/Nairobi'),
		'Asia/Tbilisi' => elgg_echo('Asia/Tbilisi'),
		'Europe/Moscow' => elgg_echo('Europe/Moscow'),
		'Europe/Volgograd' => elgg_echo('Europe/Volgograd'),
		'Asia/Tehran' => elgg_echo('Asia/Tehran'),
		'Asia/Muscat' => elgg_echo('Asia/Muscat'),
		'Asia/Baku' => elgg_echo('Asia/Baku'),
		'Asia/Yerevan' => elgg_echo('Asia/Yerevan'),
		'Asia/Yekaterinburg' => elgg_echo('Asia/Yekaterinburg'),
		'Asia/Karachi' => elgg_echo('Asia/Karachi'),
		'Asia/Tashkent' => elgg_echo('Asia/Tashkent'),
		'Asia/Kolkata' => elgg_echo('Asia/Kolkata'),
		'Asia/Colombo' => elgg_echo('Asia/Colombo'),
		'Asia/Katmandu' => elgg_echo('Asia/Katmandu'),
		'Asia/Dhaka' => elgg_echo('Asia/Dhaka'),
		'Asia/Almaty' => elgg_echo('Asia/Almaty'),
		'Asia/Novosibirsk' => elgg_echo('Asia/Novosibirsk'),
		'Asia/Rangoon' => elgg_echo('Asia/Rangoon'),
		'Asia/Krasnoyarsk' => elgg_echo('Asia/Krasnoyarsk'),
		'Asia/Bangkok' => elgg_echo('Asia/Bangkok'),
		'Asia/Jakarta' => elgg_echo('Asia/Jakarta'),
		'Asia/Brunei' => elgg_echo('Asia/Brunei'),
		'Asia/Chongqing' => elgg_echo('Asia/Chongqing'),
		'Asia/Hong_Kong' => elgg_echo('Asia/Hong_Kong'),
		'Asia/Urumqi' => elgg_echo('Asia/Urumqi'),
		'Asia/Irkutsk' => elgg_echo('Asia/Irkutsk'),
		'Asia/Ulaanbaatar' => elgg_echo('Asia/Ulaanbaatar'),
		'Asia/Kuala_Lumpur' => elgg_echo('Asia/Kuala_Lumpur'),
		'Asia/Singapore' => elgg_echo('Asia/Singapore'),
		'Asia/Taipei' => elgg_echo('Asia/Taipei'),
		'Australia/Perth' => elgg_echo('Australia/Perth'),
		'Asia/Seoul' => elgg_echo('Asia/Seoul'),
		'Asia/Tokyo' => elgg_echo('Asia/Tokyo'),
		'Asia/Yakutsk' => elgg_echo('Asia/Yakutsk'),
		'Australia/Darwin' => elgg_echo('Australia/Darwin'),
		'Australia/Adelaide' => elgg_echo('Australia/Adelaide'),
		'Australia/Canberra' => elgg_echo('Australia/Canberra'),
		'Australia/Melbourne' => elgg_echo('Australia/Melbourne'),
		'Australia/Sydney' => elgg_echo('Australia/Sydney'),
		'Australia/Brisbane' => elgg_echo('Australia/Brisbane'),
		'Australia/Hobart' => elgg_echo('Australia/Hobart'),
		'Asia/Vladivostok' => elgg_echo('Asia/Vladivostok'),
		'Pacific/Guam' => elgg_echo('Pacific/Guam'),
		'Pacific/Port_Moresby' => elgg_echo('Pacific/Port_Moresby'),
		'Asia/Magadan' => elgg_echo('Asia/Magadan'),
		'Pacific/Fiji' => elgg_echo('Pacific/Fiji'),
		'Asia/Kamchatka' => elgg_echo('Asia/Kamchatka'),
		'Pacific/Auckland' => elgg_echo('Pacific/Auckland'),
		'Pacific/Tongatapu' => elgg_echo('Pacific/Tongatapu')
	);


	return elgg_trigger_plugin_hook('default_timezones', 'events', null, $defaults);
}

/**
 * Helper function to retrieve data from arrays
 *
 * @param array $array
 * @param bool $associated_array
 * @param bool $reverse_association
 * @return array
 */
function hj_events_format_array($array = array(), $associated_array = true, $reverse_association = false) {
	if ($associated_array && !$reverse_association) {
		$return = $array;
	} else {
		foreach ($array as $key => $value) {
			if (!$associated_array) {
				$return[] = $key;
			} elseif ($reverse_association) {
				$return[$value] = $key;
			}
		}
	}
	return $return;
}

/**
 * Overwrite default timezones
 *
 * @return array
 */
function hj_events_custom_timezones() {

	$defaults = hj_events_default_timezones();
	if (!$custom_timezones = elgg_get_plugin_setting('custom_timezones', 'hypeEvents')) {
		return $defaults;
	}

	$custom_timezones = json_decode($custom_timezones);

	foreach ($custom_timezones as $custom_timezone) {
		$return[$custom_timezone] = $defaults[$custom_timezone];
	}

	if (empty($return)) {
		$return = $defaults;
	}
	return $return;
}