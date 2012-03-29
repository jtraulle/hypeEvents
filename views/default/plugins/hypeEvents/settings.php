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

$settings = <<<__HTML

    <h3>Exports</h3>
    <div>
        <p><i>$calendar_name_label</i><br>$calendar_name</p>
        <p><i>$calendar_description_label</i><br>$calendar_description</p>
    </div>
    
    <hr>
    
</div>
__HTML;

echo $settings;