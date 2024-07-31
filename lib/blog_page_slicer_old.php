<?php
	function blog_page_slicer_old(array $input_array, $current_page, int $entries_per_page)
	{
		/*
		 * Select n elements from array at start point
		 * from simpleblog project
		 * old version - for historical purposes only
		 *
		 * Usage:
			$sliced=blog_page_slicer_old(array_slice(scandir('/path'), 2), 1, 5);
			$sliced=blog_page_slicer_old(array_slice(scandir('/path'), 2), $_GET['page_id'], 5); // $_GET['page_id'] will be filtered
			$sliced=blog_page_slicer_old(array_filter(scandir('/path'), function($input){
				if(substr($input, 0, 7) === 'public_')
					return $input;
			}), 1, 5);
		 */

		$current_page=filter_var($current_page, FILTER_VALIDATE_INT);

		if(($current_page === false) || ($current_page < 1))
			$current_page=1;

		$return_array=[];
		$foreach_cache_a=($current_page*$entries_per_page)-($entries_per_page-1);
		$foreach_cache_b=$current_page*$entries_per_page;
		$foreach_loop_ind=1;

		foreach($input_array as $input_array_element)
		{
			if($foreach_loop_ind >= $foreach_cache_a)
				$return_array[]=$input_array_element;

			if($foreach_loop_ind === $foreach_cache_b)
				break;

			++$foreach_loop_ind;
		}

		return $return_array;
	}
?>