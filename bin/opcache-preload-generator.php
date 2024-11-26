<?php
	/*
	 * Opcache preload script generator
	 *
	 * Warning:
	 *  relative_path.php library is required
	 *  strip_php_comments.php library is required
	 *
	 * Usage:
	 *  opcache-preload-generator.php project-main-path application-path output-file-path [--debug]
	 *  php ./bin/opcache-preload-generator.php . ./app ./var/lib/app_preload.php
	 *  php ./opcache-preload-generator.php .. ./app ./var/lib/app_preload.php
	 *
	 * Note:
	 *  add ./var/lib/app_preload.php to the opcache.preload config entry
	 *  use absolute path!
	 *
	 * Configuration:
	 *  create opcache-preload-generator.config.php in the app directory
	 *  and define the blacklist, eg:
		<?php
			$blacklist[]='./app/opcache-preload-generator.config.php';
			$blacklist[]='./app/assets';
			$blacklist[]='./app/databases';
			$blacklist[]='./app/views/samples/default/default.js';
		?>
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
			'relative_path.php',
			'strip_php_comments.php'
		]);
	} catch(Exception $error) {
		echo 'Error: '.$error->getMessage().PHP_EOL;
		exit(1);
	}

	if($argc < 3)
	{
		echo 'Usage:'.PHP_EOL;
		echo ' opcache-preload-generator.php project-main-path application-path output-file-path [--debug]'.PHP_EOL;
		echo PHP_EOL;
		echo 'Eg:'.PHP_EOL;
		echo ' '.$argv[0].' . ./app ./var/lib/app_preload.php'.PHP_EOL;
		echo ' '.$argv[0].' .. ./app ./var/lib/app_preload.php'.PHP_EOL;
		echo PHP_EOL;
		echo 'For more info see this file'.PHP_EOL;
		exit(1);
	}

	if(!is_dir($argv[1]))
	{
		echo $argv[1].' is not a directory'.PHP_EOL;
		exit(1);
	}

	chdir($argv[1]);

	if(!is_dir($argv[2]))
	{
		echo $argv[2].' is not a directory (from '.$argv[1].')'.PHP_EOL;
		exit(1);
	}

	if(!is_dir(dirname($argv[3])))
	{
		echo dirname($argv[3]).' is not a directory (from '.$argv[1].')'.PHP_EOL;
		exit(1);
	}

	if(@file_put_contents($argv[3], '') === false)
	{
		echo $argv[3].' write error'.PHP_EOL;
		exit(1);
	}

	$blacklist=[];
	$add_to_list__already_added=[];
	@include $argv[2].'/opcache-preload-generator.config.php';

	if(
		isset($argv[4]) &&
		($argv[4] === '--debug')
	){
		function _debug($message)
		{
			file_put_contents(
				$GLOBALS['argv'][3],
				' /* '.$message.' */ '."\n",
				FILE_APPEND
			);
		}
	}
	else
	{
		function _debug(){}
	}
	function is_in_blacklist($file_name, $blacklist)
	{
		_debug('scanning blacklist for '.$file_name);

		if(in_array($file_name, $blacklist))
		{
			_debug($file_name.' is blacklisted');
			return true;
		}

		return false;
	}
	function scan_for_includes($file, $blacklist)
	{
		preg_match_all(
			'/(include|include_once|require|require_once) *\(? *[\'"](.*?)[\'"] *\)? *;/',
			strip_php_comments(file_get_contents($file)),
			$output
		);

		if(isset($output[2]))
			foreach($output[2] as $include_file)
				if(strtolower(substr(
					$include_file,
					strrpos(
						$include_file,
						'.'
					)+1
				)) === 'php'){
					_debug('[:] scanning '.$include_file);

					if(!is_file($include_file))
						_debug($include_file.' does not exists or is not a file');
					else if(is_in_blacklist($include_file, $blacklist))
						_debug($include_file.' is blacklisted');
					else
					{
						add_to_list($include_file);
						scan_for_includes($include_file, $blacklist);
					}

					_debug('[:] ended '.$include_file);
				}
	}
	function add_to_list($file)
	{
		if(!in_array(
			$file,
			$GLOBALS['add_to_list__already_added']
		)){
			_debug('adding '.$file);

			file_put_contents(
				$GLOBALS['argv'][3]
				,'opcache_compile_file(\''
				.	relative_path(
						$GLOBALS['argv'][3],
						$file
					)
				."');\n",
				FILE_APPEND
			);

			$GLOBALS['add_to_list__already_added'][]=$file;
		}
		else
			_debug($file.' already added');
	}

	file_put_contents(
		$argv[3]
		,'<?php '."\n"
		.	'chdir(__DIR__);'."\n",
		FILE_APPEND
	);

		foreach(new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$argv[2],
				RecursiveDirectoryIterator::SKIP_DOTS
			)
		) as $file){
			$file_name=strtr(
				$file->getPathname(),
				'\\', '/'
			);

			if(
				(!is_in_blacklist($file_name, $blacklist)) &&
				(strtolower($file->getExtension()) === 'php')
			){
				_debug('[1] scanning '.$file_name);
				add_to_list($file_name);
				scan_for_includes($file_name, $blacklist);
				_debug('[1] ended '.$file_name);
			}
		}

	file_put_contents(
		$argv[3],
		'?>',
		FILE_APPEND
	);
?>