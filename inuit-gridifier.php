<?php 
/**
 * Class to easily generate Inuit CSS classes with fallback if a row is not filled up
 * with the preferred number of columns
 *
 * Usage:
 * 1. Create the class and pass your total number of items and your preferred amount 
 *    of columns per row.
 * 2. Iterate over your collection and use the start_loop() method where you want to
 *    output your rows and end_loop() where you want to close the rows.
 * 3. In between these two methods, use the get_class() method to get the calculated
 *    CSS grid class for the current iterated item.
 *
 * Example:
 * 	// Five items and preferred 2 columns per row
 * 	$items = array('one', 'two', 'three', 'four', 'five');
 *  $gridifier = new Inuit_Gridifier($items, 2);
 *
 * 	foreach($items as $item) {
 *  	$gridifier->start_loop();
 *   		echo '<div class="' . $gridifier->get_class() . '">' . $item . '</div>';
 * 		$gridifier->end_loop();
 * 	}
 *  
 */
class Inuit_Gridifier {

	private $numberOfItems;
	private $currentItem;
	private $colsCurrentRow;
	private $numberOfPreferredCols;

	private $classes = array(
		1 => 'one-whole',
		2 => 'one-half',
		3 => 'one-third',
		4 => 'one-quarter',
		5 => 'one-fifth'
		);

	/**
	 * Create a new instance
	 * @param integer $numberOfItems         Total number of items in iterated collection
	 * @param integer $numberOfPreferredCols Preferred number of columns for each row
	 */
	public function __construct( $numberOfItems = 0, $numberOfPreferredCols = 3 )
	{
		// Make sure the number of columns are supported
		if ( ! array_key_exists( $numberOfItems, $this->classes ) ) {
			throw new InvalidArgumentException( $numberOfItems . ' columns are not supported');
		}

		$this->numberOfItems = $numberOfItems;
		$this->numberOfPreferredCols = $numberOfPreferredCols;
		
		$this->currentItem = 0;
	}

	public function start_loop()
	{
		// Open row if even muliplier with cols per row
		if ( ($this->currentItem % $this->numberOfPreferredCols) === 0 ) {
			echo '<div class="grid">';

			// Calculate number of items on this row
			$itemsLeft = $this->numberOfItems - $this->currentItem;
			$this->colsCurrentRow = ($itemsLeft > $this->numberOfPreferredCols) ? $this->numberOfPreferredCols : $itemsLeft;
		}
	}

	/**
	 * Gets the grid item CSS classes for the iterated item
	 * @param  string $extraClasses Extra CSS classes for each grid item
	 * @return string               CSS classes
	 */
	public function get_class($extraClasses = '')
	{
		$grid_class = 'grid__item lap-' . $this->classes[$this->colsCurrentRow];
		
		// If optional extra
		if ( ! empty( $extraClasses ) > 0 ) {
			$grid_class .= ' ' . $extraClasses;
		}
		return $grid_class; 
	}

	public function end_loop()
	{
		$this->currentItem++;

		// Close row when filled up AND on the very last item
		if ( ( ( $this->currentItem ) % $this->numberOfPreferredCols) === 0
			|| $this->currentItem === $this->numberOfItems ) {
			echo '</div><!-- .grid -->';
		}
	}
}
