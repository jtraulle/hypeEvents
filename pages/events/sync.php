<?php
if (elgg_is_xhr()) {
	$data = get_input('listdata');
	
	$guid = elgg_extract('items', $data, 0);

	$offset = sizeof($guid);

	if (is_array($guid)) {
		$guid = end($guid);
	}
	$options = elgg_extract('options', $data, array());
	array_walk_recursive($options, 'hj_framework_decode_options_array');

	$limit = elgg_extract('limit', $data['pagination'], 10);
	$inverse_order = elgg_extract('inverse_order', $data['pagination'], false);

	$entity = get_entity($guid);

	$db_prefix = elgg_get_config('dbprefix');
	$defaults = array(
		'offset' => (int) $offset,
		'limit' => (int) $limit,
		'class' => 'hj-syncable-list',
	);

	$options = array_merge($options, $defaults);
	
	$items = elgg_get_entities_from_metadata($options);
	$vars = array(
		'full_view' => elgg_extract('full_view', $data['pagination'], false)
	);
	if (is_array($items) && count($items) > 0) {
		foreach ($items as $key => $item) {
			if (!elgg_instanceof($item)) {
				continue;
			}
			$id = "elgg-{$item->getType()}-{$item->guid}";
			$html = "<li id=\"$id\" class=\"elgg-item\">";
			$html .= elgg_view_entity($item, $vars);
			$html .= '</li>';

			$output[] = array('guid' => $item->guid, 'html' => $html);
		}
	}
	header('Content-Type: application/json');
	print(json_encode(array('output' => $output)));
	exit;

}
forward(REFERER);
