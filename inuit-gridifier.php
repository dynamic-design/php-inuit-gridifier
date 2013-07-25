<?php 

/*
Plugin Name: inuit-gridifier
Plugin URI: http://#
Description: Nude gal, a plugin that strips post gallery images of formating with hook option.
Version: 1.0
Author: volontarresor
Author URI: http://volontarresor.se
*/

/* ****************
	Grid generator
******************* */


function get_grid_item_classes($spans) {
	$classes = array("", "lap-", "desk-");

	foreach ($spans as $i => $span) {
		$classes[$i] .= get_grid_item_class($spans[$i]);
	}

	return join(" ", $classes);
}

/**
* 
*/
class Grid
{
	private $grid_items;
	function __construct($grid_items)
	{
		$this->grid_items = $grid_items;
	}

	private function get_items() {
		return join("<!-- -->", $this->grid_items);
	}

	public function __toString() {
		return "<div class=\"grid\">" . $this->get_items() . "</div>";
	}
}

/**
* 
*/
class Grid_item
{
	private $header;
	private $contents;
	
	function __construct($contents)
	{
		$this->contents = $contents;
	}

	public function set_header($header) {
		$this->header = $header;
	}

	private function get_header(){
		return "<div class=\"grid__item " . $this->header . "\">";
	}

	public function __toString() {
		return $this->get_header() . $this->contents . "</div>";
	}
}


function gridify($grid_items, $preffered_item_span_per_row) {
	$number_of_items = sizeof($grid_items);
	$grid_item_headers = get_grid_headers($number_of_items, $preffered_item_span_per_row);

	$expected_ordered_a = array();

	foreach ($grid_item_headers as $grid_item_header) {
		$expected = $grid_item_header->get_expected_items_in_grid();
		$tmp = array_key_exists($expected, $expected_ordered_a) ? $expected_ordered_a[$expected] : array();
		array_push($tmp, $grid_item_header);
		$expected_ordered_a[$expected] = $tmp;
	}


	$grids = array();
	foreach ($expected_ordered_a as $expexted_ordered => $header_array) {

		$rows_with_these_headers = ($expexted_ordered == 0) ? 0 : sizeof($header_array)/$expexted_ordered;

		for ($i = 0; $i < $rows_with_these_headers ; $i++) {
			$grid_items_with_headers = array();

			for ($j=0; $j < $expexted_ordered; $j++) {
				$finished_grid_item = array_pop($grid_items); 
				$finished_grid_item->set_header($header_array[0]);

				array_push( $grid_items_with_headers, $finished_grid_item );
			}

			array_push($grids, new Grid($grid_items_with_headers));
		}
	}

	return join("",$grids);
}

/**
* 
*/
class Grid_header
{
	private $header;
	private $expected_items_in_grid;
	
	function __construct($header, $expected_items_in_grid)
	{
		$this->header = $header;
		$this->expected_items_in_grid = $expected_items_in_grid;
	}

	public function get_header() {
		return $this->header;
	}

	public function get_expected_items_in_grid() {
		return $this->expected_items_in_grid;
	}

	public function __toString() {

		return $this->header;
	}
}

function get_grid_headers($number_of_items, $preffered_item_span_per_row) {
	$rests = array(0,0,0);
	foreach ($preffered_item_span_per_row as $i => $span) {
		$rests[$i] = $number_of_items % (1/$preffered_item_span_per_row[$i]);
	}

	$grid_headers = array();

	for ($i=0; $i < $number_of_items ; $i++) {
		if($i < $number_of_items - max($rests)) {
			array_push($grid_headers, new Grid_header(get_grid_item_classes($preffered_item_span_per_row), intval(1/min($preffered_item_span_per_row))));
		}else {
			$rests_span = array();
			foreach ($rests as $j => $rest) {
				if($rests[$j] == 0) {
					$rests_span[$j] = 0;
				}else {
					$rests_span[$j] = round(1/$rests[$j], 2);
				}
			}

			$min_rest = min($rests_span);
			$min_rest = ($min_rest == 0) ? 0 : intval(1/$min_rest);

			array_push($grid_headers, new Grid_header(get_grid_item_classes($rests_span), $min_rest));				
		}

	}

	return array_reverse($grid_headers);
}

function get_grid_item_class($span) {
	switch ($span) {
		case 0:
		return "no";
		break;
		case 0.25:
		return "one-quarter";
		break;
		case 0.33:
		return "one-third";
		break;
		case 0.5:
		return "one-half";
		break;
		case 0.66:
		return "two-thirds";
		break;
		case 0.75:
		return "three-quarters";
		break;
		case 1:
		return "one-whole";
		break;
		default:
		return "one-whole";
		break;
	}
}