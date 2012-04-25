<?php

/* hypeEvents
 *
 * @package hypeJunction
 * @subpackage hypeEvents
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyrigh (c) 2011, Ismayil Khayredinov
 */
elgg_register_event_handler('init', 'system', 'hj_events_init', 502);

function hj_events_init() {

	$plugin = 'hypeEvents';

	if (!elgg_is_active_plugin('hypeFramework')) {
		register_error(elgg_echo('hj:framework:disabled', array($plugin, $plugin)));
		disable_plugin($plugin);
	}

	$shortcuts = hj_framework_path_shortcuts($plugin);

	// Helper Classes
	elgg_register_classes($shortcuts['classes']);

	// Register Libraries
	elgg_register_library('hj:events:base', $shortcuts['lib'] . 'events/base.php');
	elgg_load_library('hj:events:base');
	elgg_register_library('hj:events:setup', $shortcuts['lib'] . 'events/setup.php');
	elgg_register_library('hj:ical', $shortcuts['lib'] . 'ical/iCalcreator.php');

	//Check if the initial setup has been performed, if not porform it
	if (!elgg_get_plugin_setting('hj:events:setup')) {
		elgg_load_library('hj:events:setup');
		if (hj_events_setup() && hj_events_setup())
			system_message('hypeEvents was successfully configured');
	}

	// Register Actions
	elgg_register_action('events/rsvp', $shortcuts['actions'] . 'hj/events/rsvp.php');
	elgg_register_action('events/search', $shortcuts['actions'] . 'hj/events/search.php', 'public');
	elgg_register_action('hj/events/import', $shortcuts['actions'] . 'hj/events/import.php');
	elgg_register_action('events/deletefeed', $shortcuts['actions'] . 'hj/events/deletefeed.php');
	elgg_register_action('hypeEvents/settings/save', $shortcuts['actions'] . 'hj/events/plugin_settings.php', 'admin');

	// Register CSS and JS
	$css_url = elgg_get_simplecache_url('css', 'hj/events/base');
	elgg_register_css('hj.events.base', $css_url);

	$js_url = elgg_get_simplecache_url('js', 'hj/events/base');
	elgg_register_js('hj.events.base', $js_url);

	// Add hypeFormBuilder Field Types and processing algorithms
	elgg_register_plugin_hook_handler('hj:formbuilder:time_and_date', 'all', 'hj_events_time_input');
	elgg_register_plugin_hook_handler('hj:framework:field:process', 'all', 'hj_events_time_input_process');

	// Event URL
	elgg_register_entity_url_handler('object', 'hjevent', 'hj_events_url_handler');
	elgg_register_entity_type('object', 'hjevent');
	elgg_register_page_handler('events', 'hj_events_page_handler');

	// Register Site Menu Item
	elgg_register_menu_item('site', array(
		'name' => 'events',
		'text' => elgg_echo('hj:events'),
		'href' => 'events/all'
	));

	// Register new profile menu item
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'hj_events_owner_block_menu');

	elgg_register_plugin_hook_handler('register', 'menu:hjentityhead', 'hj_events_entity_head_menu');
	elgg_register_plugin_hook_handler('register', 'menu:rsvp', 'hj_events_rsvp_menu');

	elgg_register_event_handler('create', 'object', 'hj_events_compare_dates');
	elgg_register_event_handler('update', 'object', 'hj_events_compare_dates');

	elgg_extend_view('css/hj/portfolio/base', 'css/hj/events/base');
}

function hj_events_time_input($hook, $type, $return, $params) {
	$return[] = 'date_and_time';
	return $return;
}

function hj_events_time_input_process($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params, false);
	$field = elgg_extract('field', $params, false);
	if (!$entity || !$field) {
		return true;
	}

	switch ($field->input_type) {
		case 'date_and_time' :
			$field_name = $field->name;

			$event_time = get_input($field_name);
			$timestr = implode(' ', $event_time);

			$dt = new DateTime($timestr);
			$dt->setTimezone(new DateTimeZone('UTC'));
			$timestamp = $dt->getTimestamp();

			$field_name_str = "{$field_name}_str";
			$field_name_offset = "{$field_name}_offset";

			$entity->$field_name = $timestamp;
			$entity->$field_name_str = $timestr;
			$entity->$field_name_off = $dt->getOffset();
			$entity->event_timezone = $event_time['timezone'];

			if (!$entity->cal_uid) {
				$entity->cal_uid = elgg_get_site_url() . "-$entity->guid";
			}
			break;
	}
}

function hj_events_page_handler($page) {
	elgg_load_js('hj.framework.ajax');


	elgg_load_js('hj.events.base');
	elgg_load_css('hj.events.base');

	if (elgg_is_active_plugin('hypeMaps')) {
		elgg_load_js('hj.maps.base');
		elgg_load_js('hj.maps.google');
		elgg_load_js('hj.maps.googlegears');
	}

	$plugin = 'hypeEvents';
	$shortcuts = hj_framework_path_shortcuts($plugin);
	$pages = $shortcuts['pages'] . 'events/';

	elgg_push_breadcrumb(elgg_echo('hj:events'), 'events/all');

	$type = elgg_extract(0, $page, 'owner');

	switch ($type) {
		case 'all' :
			hj_events_register_title_buttons();
			include "{$pages}all.php";
			break;

		case 'event' :
			hj_events_register_title_buttons();
			$event = elgg_extract(1, $page);
			set_input('e', $event);
			include "{$pages}event.php";
			break;

		case 'edit' :
			gatekeeper();
			$event = elgg_extract(1, $page, false);
			if ($event) {
				set_input('e', $event);
			}
			include "{$pages}edit.php";
			break;

		case 'owner' :
			gatekeeper();
			hj_events_register_title_buttons();
			$owner = elgg_extract(1, $page, elgg_get_logged_in_user_entity()->username);
			set_input('username', $owner);
			include "{$pages}owner.php";
			break;

		case 'friends' :
			gatekeeper();
			hj_events_register_title_buttons();
			$owner = elgg_extract(1, $page, elgg_get_logged_in_user_entity()->username);
			set_input('username', $owner);
			include "{$pages}friends.php";
			break;

		case 'export' :
			switch ($page[1]) {
				case 'event' :
				default :
					set_input('e', $page[2]);
					//set_input('format', $page[3]);
					include "{$pages}export_event.php";
					break;

				case 'calendar' :
					set_input('calendar_type', $page[2]);
					include "{$pages}ics.php";
					break;

				case 'feed' :

					break;
			}
			break;

		case 'import' :
			gatekeeper();
			include "{$pages}import_event.php";
			break;

		case 'sync' :
			gatekeeper();
			include "{$pages}sync.php";
			break;
	}
	return true;
}

function hj_events_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "events/owner/{$params['entity']->username}";
		$return[] = new ElggMenuItem('events', elgg_echo('hj:events:menu:owner_block'), $url);
		return $return;
	}
	return false;
}

function hj_events_url_handler($entity) {
	return "events/event/$entity->guid";
}

function hj_events_entity_head_menu($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params);
	$handler = elgg_extract('handler', $params);
	$data = hj_framework_json_query($params);

	if (elgg_instanceof($entity, 'object', 'hjevent')) {
		$ical = array(
			'name' => 'ical',
			'title' => elgg_echo('hj:events:ical'),
			'text' => elgg_echo('hj:events:ical'),
			'href' => "events/export/event/$entity->guid/",
			'section' => 'dropdown',
			'priority' => 100
		);
		$return[] = ElggMenuItem::factory($ical);
	}
	return $return;
}

function hj_events_rsvp_menu($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params);
	$handler = elgg_extract('handler', $params);
	$data = hj_framework_json_query($params);

	if (elgg_in_context('print') || elgg_in_context('activity')) {
		return $return;
	}

	if (elgg_instanceof($entity, 'object', 'hjevent')) {

		if (!$rsvp_types = elgg_get_plugin_setting('rsvp_types', 'hypeEvents')) {
			$rsvp_types = 'attending,maybe_attending,not_attending';
			elgg_set_plugin_setting('rsvp_types', $rsvp_types);
		}
		$rsvp_types = explode(',', $rsvp_types);

		if (is_array($rsvp_types)) {

//			$rsvp = array(
//				'name' => 'rsvp',
//				'text' => elgg_echo('hj:events:rsvp'),
//				'href' => false,
//				'priority' => 100
//			);
//			$return[] = ElggMenuItem::factory($rsvp);

			foreach ($rsvp_types as $key => $rsvp_type) {
				$rsvp_type = trim($rsvp_type);
				$class = '';
				if (check_entity_relationship(elgg_get_logged_in_user_guid(), $rsvp_type, $entity->guid)) {
					$class = 'hj-rsvp-confirmed';
				}
				$item = array(
					'name' => $rsvp_type,
					'title' => elgg_echo('hj:events:rsvp:' . $rsvp_type),
					'text' => elgg_echo('hj:events:rsvp:' . $rsvp_type),
					'href' => "action/events/rsvp?response=$rsvp_type&e=$entity->guid",
					'is_action' => true,
					'data-options' => $data,
					'class' => "hj-ajaxed-rsvp $class",
					'priority' => ($key + 1) * 100
				);
				$return[] = ElggMenuItem::factory($item);
			}
		}
	}
	return $return;
}

function hj_events_compare_dates($event, $type, $entity) {

	if (!elgg_instanceof($entity, 'object', 'hjevent')) {
		return true;
	}

	if ($entity->calendar_end < $entity->calendar_start) {
		register_error(elgg_echo('hj:events:endbeforestart'));
		return false;
	}

	return true;
}

function hj_events_register_title_buttons() {
	$form = hj_framework_get_data_pattern('object', 'hjevent');
	$params = array(
		'form_guid' => $form->guid,
		'subtype' => 'hjevent',
		'owner_guid' => $user->guid,
		'target' => 'hj-upcoming-events-list',
		'dom_order' => 'prepend',
		'ajaxify' => false
	);
	$params = hj_framework_extract_params_from_params($params);
	$params = hj_framework_json_query($params);

	if (elgg_is_logged_in()) {
		elgg_register_menu_item('title', array(
			'name' => 'addevent',
			'title' => elgg_echo('hj:events:addnew'),
			'text' => elgg_view('input/button', array(
				'value' => elgg_echo('hj:events:addnew'),
				'class' => 'elgg-button-action'
			)),
			'href' => "action/framework/entities/edit",
			'is_action' => true,
			'rel' => 'fancybox',
			'id' => "hj-ajaxed-add-hjevent",
			'data-options' => htmlentities($params, ENT_QUOTES, 'UTF-8'),
			'class' => "hj-ajaxed-add",
			'target' => ""
		));
	}
}