<?php
	/*
	 * A toy that converts inclusion to a single file blob
	 * and prints it to stdout
	 *
	 * Note:
	 *  this toy also processes include () in child files
	 *  supports include, include_once, require and require_once
	 *  supports include 'file'; include('file'); and include ( 'file' ) ;
	 *
	 * Warning:
	 *  this toy is super stupid and you must prepare your code
	 *   so that after running through include2blob,
	 *   the target code is syntactically correct
	 *  include $variable; is not supported
	 *   only include 'string'; or include "string";
	 *   include $variable; will be ignored
	 *  _once is treated as a normal inclusion
	 *  you must change to the correct directory for the relative paths to be valid
	 *  --debug will also enable --no-compress
	 *  check_var.php library is required
	 *  global_variable_streamer.php library is required
	 *  strip_php_comments.php library is required
	 *
	 * lib directory path:
	 *  __DIR__/lib
	 *  __DIR__/../lib
	 *  getenv(TK_LIB)
	 */

	function load_library($libraries, $required=true)
	{
		foreach($libraries as $library)
		{
			if(file_exists(__DIR__.'/lib/'.$library))
			{
				require __DIR__.'/lib/'.$library;
				continue;
			}

			if(file_exists(__DIR__.'/../lib/'.$library))
			{
				require __DIR__.'/../lib/'.$library;
				continue;
			}

			if(getenv('TK_LIB') !== false)
				foreach(explode("\n", getenv('TK_LIB')) as $_tk_dir)
					if(is_file($_tk_dir.'/'.$library))
					{
						require $_tk_dir.'/'.$library;
						continue 2;
					}

			if($required)
				throw new Exception($library.' library not found');
		}
	}

	try {
		load_library([
			'check_var.php',
			'global_variable_streamer.php',
			'strip_php_comments.php'
		]);
	} catch(Exception $error) {
		echo 'Error: '.$error->getMessage().PHP_EOL;
		exit(1);
	}

	$ignore_include_errors=false;

	if(check_argv('--ignore-include-errors'))
		$ignore_include_errors=true;

	if(check_argv('--debug'))
	{
		function open_file($matches)
		{
			if(!file_exists($matches[2]))
			{
				if($GLOBALS['ignore_include_errors'])
					return '/* include file '.$matches[2].' not found */'."\n";

				echo $matches[2].' not exists'.PHP_EOL;
				exit(1);
			}

			return ''
			.	' /* start include file '.$matches[2].' */ '."\n".'?>'
			.	include2blob(
					strip_php_comments(
						file_get_contents($matches[2])
					)
				)
			.	'<?php /* end include file '.$matches[2].' */ '."\n";
		}

		$_SERVER['argv'][]='--no-compress';
	}
	else
	{
		function open_file($matches)
		{
			if(!file_exists($matches[2]))
			{
				if($GLOBALS['ignore_include_errors'])
					return '/* include file '.$matches[2].' not found */'."\n";

				echo $matches[2].' not exists'.PHP_EOL;
				exit(1);
			}

			return ''
			.	'?>'
			.	include2blob(
					strip_php_comments(
						file_get_contents($matches[2])
					)
				)
			.	'<?php ';
		}
	}
	function include2blob($file_content)
	{
		return preg_replace_callback(
			'/require_once\s*\(?\s*(\'|")(.*)(\'|")\s*\)?\s*;/',
			'open_file',
			preg_replace_callback(
				'/require\s*\(?\s*(\'|")(.*)(\'|")\s*\)?\s*;/',
				'open_file',
				preg_replace_callback(
					'/include_once\s*\(?\s*(\'|")(.*)(\'|")\s*\)?\s*;/',
					'open_file',
					preg_replace_callback(
						'/include\s*\(?\s*(\'|")(.*)(\'|")\s*\)?\s*;/',
						'open_file',
						$file_content
					)
				)
			)
		);
	}

	if(!isset($argv[1]))
	{
		echo 'No file name given'.PHP_EOL;
		echo 'Usage: '.$argv[0].' path/to/file.php [--debug] [--no-compress] [--ignore-include-errors]'.PHP_EOL;
		exit(1);
	}

	if(!file_exists($argv[1]))
	{
		echo $argv[1].' not exists'.PHP_EOL;
		exit(1);
	}

	global_variable_streamer::register_wrapper('gvs');

	$file_content=include2blob(
		strip_php_comments(
			file_get_contents($argv[1])
		)
	);

	if(!check_argv('--no-compress'))
		$file_content=preg_replace(
			'/<\?php\s*\?>/',
			'',
			preg_replace(
				'/<\?php\s+/',
				'<?php ',
				preg_replace(
					'/ \?><\?php\s*/',
					' ',
					php_strip_whitespace(
						'gvs://file_content'
					)
				)
			)
		);

	echo $file_content;
?>