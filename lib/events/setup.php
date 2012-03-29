<?php

function hj_events_setup() {
    if (elgg_is_logged_in()) {
        hj_events_setup_event_form();
        elgg_set_plugin_setting('hj:events:setup', true);
		elgg_set_plugin_setting('default_timezone_offset', '0');
        return true;
    }
    return false;
}

function hj_events_setup_event_form() {
    elgg_load_library('hj:events:base');

    $form = new hjForm();
    $form->title = 'hypeEvents:event';
    $form->label = 'Add New Event';
    $form->description = '';
    $form->subject_entity_subtype = 'hjevent';
    $form->notify_admins = false;
    $form->add_to_river = true;
    $form->comments_on = true;
    $form->ajaxify = true;

    if ($form->save()) {

		$form->addField(array(
            'title' => 'Image',
            'name' => 'icon',
			'input_type' => 'entity_icon'
        ));

        $form->addField(array(
            'title' => 'Title',
            'name' => 'title',
            'mandatory' => true
        ));

		$form->addField(array(
			'title' => 'Description',
			'name' => 'description',
			'input_type' => 'longtext'
		));

		$form->addField(array(
			'title' => 'Start Time',
			'name' => 'calendar_start',
			'input_type' => 'date_and_time',
			'mandatory' => true
		));

		$form->addField(array(
			'title' => 'End Time',
			'name' => 'calendar_end',
			'input_type' => 'date_and_time',
			'mandatory' => true
		));
		
		$form->addField(array(
			'title' => 'Host',
			'name' => 'host'
		));

		$form->addField(array(
			'title' => 'Venue',
			'name' => 'venue'
		));

		$form->addField(array(
			'title' => 'Location',
			'name' => 'location',
			'input_type' => 'location',
			'mandatory' => true
		));

		$form->addField(array(
			'title' => 'Contant Details',
			'name' => 'contact_details'
		));

		$form->addField(array(
			'title' => 'Website',
			'name' => 'www',
			'input_type' => 'url'
		));

		$form->addField(array(
			'title' => 'Costs',
			'name' => 'costs',
			'input_type' => 'longtext'
		));

        $form->addField(array(
            'title' => 'Tags',
            'input_type' => 'tags',
            'name' => 'tags'
        ));

        $form->addField(array(
            'title' => 'Access Level',
            'input_type' => 'access',
            'name' => 'access_id'
        ));

        return true;
    }
    return false;
}