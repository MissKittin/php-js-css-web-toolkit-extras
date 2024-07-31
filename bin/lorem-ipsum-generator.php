<?php
	/*
	 * Interface for lorem_ipsum_generator.php library
	 *
	 * Warning:
	 *  lorem_ipsum_generator.php library is required
	 *
	 * lib directory path:
	 *  __DIR__/lib
	 *  __DIR__/../lib
	 *  getenv(TK_LIB)
	 */

	function load_library($libraries, $required=true)
	{
		foreach($libraries as $library)
			if(file_exists(__DIR__.'/lib/'.$library))
				require __DIR__.'/lib/'.$library;
			else if(file_exists(__DIR__.'/../lib/'.$library))
				require __DIR__.'/../lib/'.$library;
			else if(getenv('TK_LIB') !== false)
			{
				foreach(explode("\n", getenv('TK_LIB')) as $_tk_dir)
					if(is_file($_tk_dir.'/'.$library))
					{
						require $_tk_dir.'/'.$library;
						break;
					}
			}
			else
				if($required)
					throw new Exception($library.' library not found');
	}

	try {
		load_library(['lorem_ipsum_generator.php']);
	} catch(Exception $error) {
		echo 'Error: '.$error->getMessage().PHP_EOL;
		exit(1);
	}

	function convert_eol($input, $eol)
	{
		switch($eol)
		{
			case 'cr':
				return str_replace("\n", "\r", $input);
			case 'crlf':
				return str_replace("\n", "\r\n", $input);
		}

		return $input;
	}

	$exit=0;

	if(!isset($argv[1]))
	{
		$argv[1]='--help';
		$exit=1;
	}

	switch($argv[1])
	{
		case '-h':
		case '--help':
			echo 'Usage:'.PHP_EOL;
			echo ' '.$argv[0].' bytes int-bytes [string-start-tag] [string-end-tag] [cr|lf|crlf]'.PHP_EOL;
			echo ' '.$argv[0].' words [int-words] [int-paragraphs] [swl|nswl] [dn|ndn] [string-start-tag] [string-end-tag] [cr|lf|crlf]'.PHP_EOL;
			echo PHP_EOL;
			echo 'Where:'.PHP_EOL;
			echo ' string-start-tag -> add before each line (default: empty)'.PHP_EOL;
			echo ' string-end-tag -> add after each line (default: empty)'.PHP_EOL;
			echo ' int-words -> default: 30'.PHP_EOL;
			echo ' int-paragraphs -> default: 4'.PHP_EOL;
			echo ' swl -> start with Lorem ipsum'.PHP_EOL;
			echo ' nswl -> do not start with Lorem ipsum (default)'.PHP_EOL;
			echo ' dn -> use double newline'.PHP_EOL;
			echo ' ndn -> do not use double newline (default)'.PHP_EOL;
			echo ' cr -> use CR as the end-of-line character (MAC)'.PHP_EOL;
			echo ' lf -> use LF as the end-of-line character (UNIX) (default)'.PHP_EOL;
			echo ' crlf -> use CR-LF as the end-of-line character (WINDOWS)'.PHP_EOL;
			exit($exit);
		break;
		case 'bytes':
			if(!isset($argv[2]))
			{
				echo 'Error: int-bytes was not specified'.PHP_EOL;
				echo 'See '.$argv[0].' --help'.PHP_EOL;
				exit(1);
			}

			$start_tag='';
			$end_tag='';
			$eol='lf';

			if(isset($argv[3]))
				$start_tag=$argv[3];
			if(isset($argv[4]))
				$end_tag=$argv[4];
			if(isset($argv[5]))
				$eol=$argv[5];

			echo convert_eol(generate_lorem_ipsum_b($argv[2], $start_tag, $end_tag), $eol);
		break;
		case 'words':
			$words=30;
			$paragraphs=4;
			$start_with_lipsum=false;
			$double_newline=false;
			$start_tag='';
			$end_tag='';
			$eol='lf';

			if(isset($argv[2]))
				$words=$argv[2];
			if(isset($argv[3]))
				$paragraphs=$argv[3];
			if(isset($argv[4]) && ($argv[4] === 'swl'))
				$start_with_lipsum=true;
			if(isset($argv[5]) && ($argv[5] === 'dn'))
				$double_newline=true;
			if(isset($argv[6]))
				$start_tag=$argv[6];
			if(isset($argv[7]))
				$end_tag=$argv[7];
			if(isset($argv[8]))
				$eol=$argv[8];

			echo convert_eol(generate_lorem_ipsum_wp(
				$words,
				$paragraphs,
				$start_with_lipsum,
				$double_newline,
				$start_tag,
				$end_tag
			), $eol);
		break;
	}
?>