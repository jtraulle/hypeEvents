<?php

$entity = $vars['entity'];
$sizes = array('small', 'medium', 'large', 'tiny', 'master', 'topbar');
// Get size
if (!in_array($vars['size'], $sizes)) {
	$vars['size'] = "medium";
}

if ($entity->icon) {

	$class = elgg_extract('img_class', $vars, '');

	if (isset($entity->name)) {
		$title = $entity->name;
	} else {
		$title = $entity->title;
	}
	$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8', false);

	$url = $entity->getURL();
	if (isset($vars['href'])) {
		$url = $vars['href'];
	}

	$img = elgg_view('output/img', array(
		'src' => $entity->getIconURL($vars['size']),
		'alt' => $title,
		'class' => $class,
			));

	if ($url) {
		$params = array(
			'href' => $url,
			'text' => $img,
			'is_trusted' => true,
		);
		$class = elgg_extract('link_class', $vars, '');
		if ($class) {
			$params['class'] = $class;
		}

		echo elgg_view('output/url', $params);
	} else {
		echo $img;
	}
} else {
	$dt = new DateTime();
	$dt->setTimestamp($entity->calendar_start);
	$dt->setTimezone(new DateTimeZone('GMT'));
	$utc = $dt->format(elgg_echo('hj:events:fulltimeformat'));

	if (elgg_get_plugin_setting('timezone_disable') == 'disable' && $default_timezone = elgg_get_plugin_setting('default_timezone', 'hypeEvents')) {
		$def_tz = $dt->setTimezone(new DateTimeZone($default_timezone));
		$def_tm = $dt->format(elgg_echo('hj:events:fulltimeformat'));
		$html = elgg_view('input/hidden', array(
			'value' => $def_tm,
			'class' => 'hj-dt-to-img'
				));
		$html .= '<div class="hj-dt-to-img-div"></div>';
	} elseif ($default_timezone = $entity->event_timezone) {
		$def_tz = $dt->setTimezone(new DateTimeZone($default_timezone));
		$def_tm = $dt->format(elgg_echo('hj:events:fulltimeformat'));
		$html = elgg_view('input/hidden', array(
			'value' => $def_tm,
			'class' => 'hj-dt-to-img'
				));
		$html .= '<div class="hj-dt-to-img-div"></div>';
	} else {
		$html = elgg_view('input/hidden', array(
			'value' => date(elgg_echo('hj:events:fulltimeformat'), $entity->calendar_start),
			'class' => 'hj-dt-to-img'
				));
		$html .= '<div class="hj-dt-to-img-div"></div>';
	}
	echo $html;
}