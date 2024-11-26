<?php
	/*
	 * webdev.sh client
	 *
	 * Warning:
	 *  check_var.php library is required
	 *  webdevsh.php library is required
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
			'webdevsh.php'
		]);
	} catch(Exception $error) {
		echo 'Error: '.$error->getMessage().PHP_EOL;
		exit(1);
	}

	$input_directory=check_argv_next_param('--dir');
	$minify_styles=true;
	$minify_scripts=true;
	$ignore_https=false;

	if(check_argv('--no-css'))
		$minify_styles=false;

	if(check_argv('--no-js'))
		$minify_scripts=false;

	if(check_argv('--no-check-certificate'))
		$ignore_https=true;

	if(
		($input_directory === null) ||
		check_argv('--help') || check_argv('-h')
	){
		echo 'Usage: '.$argv[0].' --dir ./public/assets [--no-css] [--no-js] [--no-check-certificate]'.PHP_EOL;
		echo 'where ./public/assets is a directory'.PHP_EOL;
		echo ' --no-css disables CSS minification'.PHP_EOL;
		echo ' --no-js disables Javascript minification'.PHP_EOL;
		echo ' --no-check-certificate disables HTTP certificate check in the curl'.PHP_EOL;
		exit(1);
	}

	if(!is_dir($input_directory))
	{
		echo $input_directory.' is not a directory'.PHP_EOL;
		exit(1);
	}

	foreach(new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(
			$input_directory,
			RecursiveDirectoryIterator::SKIP_DOTS
		)
	) as $asset)
		try {
			switch(pathinfo($asset, PATHINFO_EXTENSION))
			{
				case 'css':
					if($minify_styles)
					{
						echo 'Processing '.$asset.PHP_EOL;

						if(file_put_contents(
							$asset,
							webdevsh_css_minifier(
								file_get_contents($asset),
								$ignore_https
							)
						) === false)
							echo ' failed: file cannot be saved'.PHP_EOL;
					}
				break;
				case 'js':
					if($minify_scripts)
					{
						echo 'Processing '.$asset.PHP_EOL;

						if(file_put_contents(
							$asset,
							webdevsh_js_minifier(
								file_get_contents($asset),
								$ignore_https
							)
						) === false)
							echo ' failed: file cannot be saved'.PHP_EOL;
					}
			}
		} catch(Throwable $error) {
			echo ' failed: '.$error->getMessage().PHP_EOL;
		}
?>