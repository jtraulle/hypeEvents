<?php
if (elgg_is_xhr()) {
	$data = get_input('listdata');
	
	$guid = elgg_extract('items', $data, 0);
	if (is_array($guid)) {
		$guid = end($guid);
	}
	$options = elgg_extract('options', $data, array());
	array_walk_recursive($options, 'hj_framework_decode_options_array');

	$limit = elgg_extract('limit', $data['pagination'], 10);
	$offset = elgg_extract('offset', $data['pagination'], 0);
	$inverse_order = elgg_extract('inverse_order', $data['pagination'], false);

	$entity = get_entity($guid);
	if (!$calendar_start = $entity->calendar_start) {
		$calendar_start = time() - 24*60*60;
	}

	$db_prefix = elgg_get_config('dbprefix');
	$defaults = array(
		'offset' => (int) $offset,
		'limit' => (int) $limit,
		'class' => 'hj-syncable-list',
	);

	$options = array_merge($defaults, $options);
	
	if (!$inverse_order) {
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'calendar_start', 'value' => $calendar_start, 'operand' => '>'
		);
	} else {
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'calendar_start', 'value' => $calendar_start, 'operand' => '<'
		);
	}
	
	$items = elgg_get_entities_from_metadata($options);
	if (is_array($items) && count($items) > 0) {
		foreach ($items as $key => $item) {
			$id = "elgg-{$item->getType()}-{$item->guid}";
			$html = "<li id=\"$id\" class=\"elgg-item\">";
			$html .= elgg_view_list_item($item, $vars);
			$html .= '</li>';

			$output[] = array('guid' => $item->guid, 'html' => $html);
		}
	}
	header('Content-Type: application/json');
	print(json_encode($output));
	forward();

}
forward(REFERER);
