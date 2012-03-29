<?php
if (elgg_is_xhr()) {
	$last = get_entity(get_input('guid'));
	$calendar_start = $last->calendar_start;

	$options = get_input('options');
	$options = array_map('htmlspecialcharsdecode', $options);
	
	$pagination_options = get_input('pagination');

	$db_prefix = elgg_get_config('dbprefix');
	$defaults = array(
		'offset' => (int) max(get_input('offset', 0), 0),
		'limit' => (int) max(get_input('limit', 10), 0),
		'class' => 'hj-syncable-list',
	);

	$options = array_merge($defaults, $options);
	$options['metadata_name_value_pairs'][] = array(
		'name' => 'calendar_start', 'value' => $calendar_start, 'operand' => '>'
	);

	$items = elgg_get_entities_from_metadata($options);
	if (is_array($items) && count($items) > 0) {
		foreach ($items as $key => $item) {
			$id = "elgg-{$item->getType()}-{$item->guid}";
			$time = $item->time_created;

			$html = "<li id=\"$id\" class=\"elgg-item\" data-timestamp=\"$time\">";
			$html .= elgg_view_list_item($item, $vars);
			$html .= '</li>';

			$output[] = $html;
		}
	}
		print(json_encode($output));
	return true;

}
forward(REFERER);
