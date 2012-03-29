<?php

$id = (int)get_input('id');

if (elgg_delete_metadata(array('metadata_id' => $id, 'guid' => get_input('guid')))) {
	system_message(elgg_echo('hj:framework:success'));
} else {
	register_error(elgg_echo('hj:framework:error'));
}

forward(REFERER);

