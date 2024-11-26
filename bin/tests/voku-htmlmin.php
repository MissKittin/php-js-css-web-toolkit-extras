<?php
	/*
	 * voku-htmlmin.php tool test
	 *
	 * Note:
	 *  looks for a tool at ..
	 *  also looks for tools at TK_BIN env variable
	 *  looks for a library at ../../lib
	 *  also looks for libraries at TK_LIB env variable
	 *
	 * Warning:
	 *  rmdir_recursive.php library is required
	 */

	function test_copy_lib($lib, $env='TK_LIB', $in_dir='lib', $out_dir='lib/')
	{
		if(is_file(__DIR__.'/../../'.$in_dir.$lib))
		{
			copy(__DIR__.'/../../'.$in_dir.'/'.$lib, __DIR__.'/tmp/voku-htmlmin/'.$out_dir.$lib);
			return;
		}

		if(getenv('TK_LIB') !== false)
		{
			foreach(explode("\n", getenv($env)) as $_tk_dir)
				if(is_file($_tk_dir.'/'.$lib))
				{
					copy($_tk_dir.'/'.$lib, __DIR__.'/tmp/voku-htmlmin/'.$out_dir.$lib);
					return;
				}
		}

		echo ' [FAIL]'.PHP_EOL;
		exit(1);
	}

	if(!is_file(__DIR__.'/../'.basename(__FILE__)))
	{
		echo 'Error: '.basename(__FILE__).' tool does not exist'.PHP_EOL;
		exit(1);
	}

	echo ' -> Including rmdir_recursive.php';
		if(is_file(__DIR__.'/../../lib/rmdir_recursive.php'))
		{
			if(@(include __DIR__.'/../../lib/rmdir_recursive.php') === false)
			{
				echo ' [FAIL]'.PHP_EOL;
				exit(1);
			}
		}
		else if(getenv('TK_LIB') !== false)
		{
			foreach(explode("\n", getenv('TK_LIB')) as $_tk_dir)
				if(is_file($_tk_dir.'/rmdir_recursive.php'))
				{
					if(@(include $_tk_dir.'/rmdir_recursive.php') === false)
					{
						echo ' [FAIL]'.PHP_EOL;
						exit(1);
					}

					break;
				}
		}
		else
		{
			echo ' [FAIL]'.PHP_EOL;
			exit(1);
		}
	echo ' [ OK ]'.PHP_EOL;

	echo ' -> Removing temporary files';
		@mkdir(__DIR__.'/tmp');
		@mkdir(__DIR__.'/tmp/voku-htmlmin');
		@rmdir_recursive(__DIR__.'/tmp/voku-htmlmin/static');
		@unlink(__DIR__.'/tmp/voku-htmlmin/onefile.html');
		@unlink(__DIR__.'/tmp/voku-htmlmin/voku-htmlmin.php');
	echo ' [ OK ]'.PHP_EOL;

	echo ' -> Creating test directory';
		if(!file_exists(__DIR__.'/tmp/voku-htmlmin/lib'))
		{
			mkdir(__DIR__.'/tmp/voku-htmlmin/lib');
			test_copy_lib('check_var.php');
			test_copy_lib('curl_file_updown.php');
			test_copy_lib('get-composer.php', 'TK_BIN', 'bin', '');
		}

		copy(__DIR__.'/../voku-htmlmin.php', __DIR__.'/tmp/voku-htmlmin/voku-htmlmin.php');

		mkdir(__DIR__.'/tmp/voku-htmlmin/static');
		file_put_contents(__DIR__.'/tmp/voku-htmlmin/static/index.html', '
			<!DOCTYPE html>
			<html lang="en">
				<head>
					<style type="text/css">
						body {
							color: #ffffff;
							background-color: #000000;
						}
					</style>
					<script type="text/javascript">
						alert("ok");
					</script>
				</head>
				<body>
					<h1>Test</h1>
				</body>
			</html>
		');
		copy(__DIR__.'/tmp/voku-htmlmin/static/index.html', __DIR__.'/tmp/voku-htmlmin/static/about.html');
		copy(__DIR__.'/tmp/voku-htmlmin/static/index.html', __DIR__.'/tmp/voku-htmlmin/onefile.html');
	echo ' [ OK ]'.PHP_EOL;

	$failed=false;

	if(file_exists(__DIR__.'/tmp/voku-htmlmin/composer.phar'))
		echo ' -> Downloading composer [SKIP]'.PHP_EOL;
	else
	{
		echo ' -> Downloading composer'.PHP_EOL.PHP_EOL;
			system('"'.PHP_BINARY.'" "'.__DIR__.'/tmp/voku-htmlmin/get-composer.php"');

			if(!file_exists(__DIR__.'/tmp/voku-htmlmin/composer.phar'))
			{
				echo PHP_EOL;
				exit(1);
			}
		echo PHP_EOL;
	}

	if(file_exists(__DIR__.'/tmp/voku-htmlmin/composer.json'))
		echo ' -> Downloading voku/html-min package [SKIP]'.PHP_EOL;
	else
	{
		echo ' -> Downloading voku/html-min package'.PHP_EOL.PHP_EOL;
			system('"'.PHP_BINARY.'" "'.__DIR__.'/tmp/voku-htmlmin/composer.phar" '
			.	'--no-cache '
			.	'"--working-dir='.__DIR__.'/tmp/voku-htmlmin" '
			.	'require voku/html-min'
			);

			if(!file_exists(__DIR__.'/tmp/voku-htmlmin/vendor/voku/html-min'))
			{
				echo PHP_EOL;
				exit(1);
			}
		echo PHP_EOL;
	}

	echo ' -> Starting tool'.PHP_EOL.PHP_EOL;
		system('"'.PHP_BINARY.'" "'.__DIR__.'/tmp/voku-htmlmin/voku-htmlmin.php" --dir "'.__DIR__.'/tmp/voku-htmlmin/static"');
		system('"'.PHP_BINARY.'" "'.__DIR__.'/tmp/voku-htmlmin/voku-htmlmin.php" --file "'.__DIR__.'/tmp/voku-htmlmin/onefile.html"');
	echo PHP_EOL;

	echo ' -> Testing output files';
		//echo ' ('.md5(file_get_contents(__DIR__.'/tmp/voku-htmlmin/static/index.html')).')';
		if(md5(file_get_contents(__DIR__.'/tmp/voku-htmlmin/static/index.html')) === '6d5cfdf7fb1301c8276dff41ec0a041a')
			echo ' [ OK ]';
		else
		{
			echo ' [FAIL]';
			$failed=true;
		}
		if(md5(file_get_contents(__DIR__.'/tmp/voku-htmlmin/static/about.html')) === '6d5cfdf7fb1301c8276dff41ec0a041a')
			echo ' [ OK ]';
		else
		{
			echo ' [FAIL]';
			$failed=true;
		}
		if(md5(file_get_contents(__DIR__.'/tmp/voku-htmlmin/onefile.html')) === '6d5cfdf7fb1301c8276dff41ec0a041a')
			echo ' [ OK ]'.PHP_EOL;
		else
		{
			echo ' [FAIL]'.PHP_EOL;
			$failed=true;
		}

	if($failed)
		exit(1);
?>