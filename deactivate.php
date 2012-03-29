<?php

$subtypes = array(
	'hjevent' => 'hjEvent'
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}
