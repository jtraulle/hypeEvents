<?php

$calendar_name_label = 'Set a name for your calendar (for exports)';
$calendar_name = elgg_view('input/text', array(
	'name' => 'params[calendar_name]',
	'value' => $vars['entity']->calendar_name,
		));

$calendar_description_label = 'Set a description of your calendar (for exports)';
$calendar_description = elgg_view('input/text', array(
	'name' => 'params[calendar_description]',
	'value' => $vars['entity']->calendar_description,
		));

$default_timezone_options = hj_events_default_timezones();

$timezone_label = 'Disable Timezone selection and set the default to:';
$timezone_disable = elgg_view('input/dropdown', array(
	'name' => "params[timezone_disable]",
	'value' => $vars['entity']->timezone_disable,
	'options_values' => array(
		'disable' => elgg_echo('disable'),
		'enable' => elgg_echo('enable')
	)
		));
$timezone = elgg_view('input/dropdown', array(
	'name' => "params[default_timezone]",
	'value' => $vars['entity']->default_timezone,
	'options_values' => $default_timezone_options
		));

$default_timezone_options = hj_events_format_array($default_timezone_options, true, true);

$timezones_to_show_label = 'If timezone selection is enabled, display the following timezones:';
$timezones_to_show = elgg_view('input/checkboxes', array(
	'options' => $default_timezone_options,
	'name' => "params[custom_timezones]",
	'value' => json_decode($vars['entity']->custom_timezones),
	'default' => true
));

$settings = <<<__HTML

    <h3>Exports</h3>
    <div>
        <p><i>$calendar_name_label</i><br>$calendar_name</p>
        <p><i>$calendar_description_label</i><br>$calendar_description</p>
    </div>
    <hr>
	<h3>Timezone Settings</h3>
	<p><i>$timezone_label</i><br>$timezone_disable<br>$timezone</p>
	<p><i>$timezones_to_show_label</i>$timezones_to_show</p>
    <hr>
    
</div>
__HTML;

echo $settings;