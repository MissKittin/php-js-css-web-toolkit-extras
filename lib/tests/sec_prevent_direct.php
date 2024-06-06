<?php
	/*
	 * sec_prevent_direct.php library test
	 *
	 * Note:
	 *  looks for a library at ../lib
	 *  looks for a library at ..
	 *  also looks for additional libraries at TK_LIB env variable
	 *
	 * Warning:
	 *  has_php_close_tag.php library is required
	 *  include_into_namespace.php library is required
	 */

	namespace Test
	{
		function _include_tested_library($namespace, $file)
		{
			if(!is_file($file))
				return false;

			$code=file_get_contents($file);

			if($code === false)
				return false;

			include_into_namespace($namespace, $code, has_php_close_tag($code));

			return true;
		}

		echo ' -> Mocking classes';
			class Exception extends \Exception {}
		echo ' [ OK ]'.PHP_EOL;

		echo ' -> Mocking functions';
			function http_response_code($code)
			{
				$GLOBALS['http_response_code']=$code;
			}
		echo ' [ OK ]'.PHP_EOL;

		echo ' -> Mocking superglobals';
			$_SERVER['REQUEST_URI']='/goodscript?trash';
		echo ' [ OK ]'.PHP_EOL;

		foreach(['has_php_close_tag.php', 'include_into_namespace.php'] as $library)
		{
			echo ' -> Including '.$library;
				if(is_file(__DIR__.'/../lib/'.$library))
				{
					if(@(include __DIR__.'/../lib/'.$library) === false)
					{
						echo ' [FAIL]'.PHP_EOL;
						exit(1);
					}
				}
				else if(is_file(__DIR__.'/../'.$library))
				{
					if(@(include __DIR__.'/../'.$library) === false)
					{
						echo ' [FAIL]'.PHP_EOL;
						exit(1);
					}
				}
				else if(getenv('TK_LIB') !== false)
				{
					foreach(explode("\n", getenv('TK_LIB')) as $_tk_dir)
						if(is_file($_tk_dir.'/'.$library))
						{
							if(@(include $_tk_dir.'/'.$library) === false)
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
		}

		echo ' -> Including '.basename(__FILE__);
			if(is_file(__DIR__.'/../lib/'.basename(__FILE__)))
			{
				if(!_include_tested_library(
					__NAMESPACE__,
					__DIR__.'/../lib/'.basename(__FILE__)
				)){
					echo ' [FAIL]'.PHP_EOL;
					exit(1);
				}
			}
			else if(is_file(__DIR__.'/../'.basename(__FILE__)))
			{
				if(!_include_tested_library(
					__NAMESPACE__,
					__DIR__.'/../'.basename(__FILE__)
				)){
					echo ' [FAIL]'.PHP_EOL;
					exit(1);
				}
			}
			else
			{
				echo ' [FAIL]'.PHP_EOL;
				exit(1);
			}
		echo ' [ OK ]'.PHP_EOL;

		$failed=false;

		echo ' -> Testing prevent_index';
			$GLOBALS['http_response_code']='';
			ob_start();
			prevent_index('good value', 'echo');
			if((ob_get_clean() === 'good value') && ($GLOBALS['http_response_code'] === 404))
				echo ' [ OK ]'.PHP_EOL;
			else
			{
				echo ' [FAIL]'.PHP_EOL;
				$failed=true;
			}

		echo ' -> Testing prevent_direct'.PHP_EOL;
		echo '  -> checking failed';
			$GLOBALS['http_response_code']='';
			prevent_direct('badscript');
			if($GLOBALS['http_response_code'] === '')
				echo ' [ OK ]'.PHP_EOL;
			else
			{
				echo ' [FAIL]'.PHP_EOL;
				$failed=true;
			}

		if($failed)
		{
			echo PHP_EOL.'Exiting due to previous errors'.PHP_EOL;
			exit(1);
		}

		echo '  -> checking success (now exit)'.PHP_EOL;
			prevent_direct('goodscript', '', 'echo');
			echo '  -> checking success [FAIL]'.PHP_EOL;
			exit(1);
	}
?>