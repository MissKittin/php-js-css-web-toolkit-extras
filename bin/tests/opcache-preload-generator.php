<?php
	/*
	 * opcache-preload-generator.php tool test
	 *
	 * Note:
	 *  looks for a tool at ..
	 *  looks for a library at ../../lib
	 *  also looks for additional libraries at TK_LIB env variable
	 *
	 * Warning:
	 *  rmdir_recursive.php library is required
	 */

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
		@rmdir_recursive(__DIR__.'/tmp/opcache-preload-generator');
		mkdir(__DIR__.'/tmp/opcache-preload-generator');
	echo ' [ OK ]'.PHP_EOL;

	echo ' -> Creating test directory';
		mkdir(__DIR__.'/tmp/opcache-preload-generator/src');
		file_put_contents(__DIR__.'/tmp/opcache-preload-generator/src/main.php', '<?php
			echo "main";
			include "./liba.php";
			echo "liba ok";
			include "./libb.php";
			echo "libb ok";
		?>');
		file_put_contents(__DIR__.'/tmp/opcache-preload-generator/src/liba.php', '<?php
			echo "liba";
			include "./libc.php";
			echo "libc ok";
		?>');
		file_put_contents(__DIR__.'/tmp/opcache-preload-generator/src/libb.php', '<?php
			echo "libb";
		?>');
		file_put_contents(__DIR__.'/tmp/opcache-preload-generator/src/libc.php', '<?php
			echo "libc";
		?>');
	echo ' [ OK ]'.PHP_EOL;

	echo ' -> Starting tool'.PHP_EOL.PHP_EOL;
		chdir(__DIR__.'/tmp/opcache-preload-generator/src');
		system('"'.PHP_BINARY.'" "'.__DIR__.'/../'.basename(__FILE__).'" '
		.	'. '
		.	'. '
		.	'../preload.php'
		//.	' --debug'
		);
	echo PHP_EOL;

	echo ' -> Testing preload script';
		$data=file('../preload.php');
		natsort($data);
		$md5sum=md5(implode($data));
		//echo ' ['.$md5sum.']';
		if($md5sum === 'f9d842c6b59f86534fcf3c5ed0de4389')
			echo ' [ OK ]'.PHP_EOL;
		else
		{
			echo ' [FAIL]'.PHP_EOL;
			exit(1);
		}
?>