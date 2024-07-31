<?php
	/*
	 * lorem_ipsum_generator.php library test
	 *
	 * Note:
	 *  looks for a library at ../lib
	 *  looks for a library at ..
	 */

	echo ' -> Including '.basename(__FILE__);
		if(is_file(__DIR__.'/../lib/'.basename(__FILE__)))
		{
			if(@(include __DIR__.'/../lib/'.basename(__FILE__)) === false)
			{
				echo ' [FAIL]'.PHP_EOL;
				exit(1);
			}
		}
		else if(is_file(__DIR__.'/../'.basename(__FILE__)))
		{
			if(@(include __DIR__.'/../'.basename(__FILE__)) === false)
			{
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

	echo ' -> Testing generate_lorem_ipsum_wp'.PHP_EOL;
		echo '  -> single newline';
			$content=explode("\n", generate_lorem_ipsum_wp(30, 4, false, false, '<p>', '</p>'));
			if(count($content) === 4)
				echo ' [ OK ]'.PHP_EOL;
			else
			{
				echo ' [FAIL]'.PHP_EOL;
				$failed=true;
			}
			foreach($content as $paragraph_i=>$paragraph)
			{
				echo '   -> paragraph '.$paragraph_i;

				if(
					(substr($paragraph, 0, 3) === '<p>') &&
					(substr($paragraph, -4) === '</p>')
				)
					echo ' [ OK ]';
				else
				{
					echo ' [FAIL]';
					$failed=true;
				}

				if(str_word_count($paragraph) === 32)
					echo ' [ OK ]'.PHP_EOL;
				else
				{
					echo ' [FAIL]'.PHP_EOL;
					$failed=true;
				}
			}
		echo '  -> double newline/start with lipsum';
			$content=explode("\n", generate_lorem_ipsum_wp(30, 4, true, true, '<p>', '</p>'));
			if(count($content) === 7)
				echo ' [ OK ]'.PHP_EOL;
			else
			{
				echo ' [FAIL]'.PHP_EOL;
				$failed=true;
			}
			foreach($content as $paragraph_i=>$paragraph)
			{
				echo '   -> paragraph '.$paragraph_i;

				if($paragraph_i === 0)
				{
					echo ' (Lorem ipsum...)';

					if(substr($paragraph, 0, 54) === '<p>Lorem ipsum dolor sit amet consectetuer adipiscing ')
						echo ' [ OK ]';
					else
					{
						echo ' [FAIL]';
						$failed=true;
					}
				}

				if($paragraph_i%2 === 0)
				{
					if(
						(substr($paragraph, 0, 3) === '<p>') &&
						(substr($paragraph, -4) === '</p>')
					)
						echo ' [ OK ]';
					else
					{
						echo ' [FAIL]';
						$failed=true;
					}

					if(str_word_count($paragraph) === 32)
						echo ' [ OK ]'.PHP_EOL;
					else
					{
						echo ' [FAIL]'.PHP_EOL;
						$failed=true;
					}
				}
				else
				{
					echo ' (empty line)';

					if($paragraph === '')
						echo ' [ OK ]'.PHP_EOL;
					else
					{
						echo ' [FAIL]'.PHP_EOL;
						$failed=true;
					}
				}
			}

	echo ' -> Testing generate_lorem_ipsum_b';
		$content=generate_lorem_ipsum_b(400, '<p>', '</p>');
		if(
			(substr($content, 0, 3) === '<p>') &&
			(substr($content, -4) === '</p>')
		)
			echo ' [ OK ]';
		else
		{
			echo ' [FAIL]';
			$failed=true;
		}
		if(strlen($content) === 407)
			echo ' [ OK ]'.PHP_EOL;
		else
		{
			echo ' [FAIL]'.PHP_EOL;
			$failed=true;
		}

	if($failed)
		exit(1);
?>