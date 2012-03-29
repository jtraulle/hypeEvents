<?php

$entity = $vars['entity'];

if ($entity->icon) {

	$sizes = array('small', 'medium', 'large', 'tiny', 'master', 'topbar');
// Get size
	if (!in_array($vars['size'], $sizes)) {
		$vars['size'] = "medium";
	}

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
	$mon = $dt->format("M");
	$day = $dt->format("j");

	$title = htmlspecialchars($entity->title, ENT_QUOTES, 'UTF-8', false);

	$url = $entity->getURL();
	if (isset($vars['href'])) {
		$url = $vars['href'];
	}

	$img = elgg_view_module('eventicon', $mon, $day);

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
}