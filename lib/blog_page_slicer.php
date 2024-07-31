<?php
	function blog_page_slicer(
		array $input_array,
		$current_page,
		int $entries_per_page,
		bool $preserve_keys=false
	){
		/*
		 * Select n elements from array at start point
		 * from simpleblog project
		 *
		 * Warning:
		 *  always use filter_var function if data comes from client!
		 *
		 * Usage:
			$sliced=blog_page_slicer(array_slice(scandir('/path'), 2), 1, 5);
			$sliced=blog_page_slicer(array_filter(scandir('/path'), function($input){
				if(substr($input, 0, 7) === 'public_')
					return $input;
			}), 1, 5);
		 */

		if(($current_page === false) || ($current_page < 1))
			$current_page=1;

		return array_slice(
			$input_array,
			($current_page-1)*$entries_per_page,
			$entries_per_page,
			$preserve_keys
		);
	}
?>