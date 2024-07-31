<?php
	/*
	 * lorem-ipsum-generator.php tool test
	 *
	 * Note:
	 *  looks for a tool at ..
	 *  looks for a library at ../../lib
	 */

	$failed=false;

	echo ' -> Testing bytes option';
		$output=shell_exec('"'.PHP_BINARY.'" "'.__DIR__.'/../lorem-ipsum-generator.php" bytes '
		.	'20 '
		.	'"<p>" "</p>"'
		);
		if(strlen($output) === 27)
			echo ' [ OK ]';
		else
		{
			echo ' [FAIL]';
			$failed=true;
		}
		$output=trim($output);
		if(
			(substr($output, 0, 3) === '<p>') &&
			(substr($output, -4) === '</p>')
		)
			echo ' [ OK ]'.PHP_EOL;
		else
		{
			echo ' [FAIL]'.PHP_EOL;
			$failed=true;
		}

	echo ' -> Testing words option'.PHP_EOL;
		echo '  -> swl dn cr';
			$output=shell_exec('"'.PHP_BINARY.'" "'.__DIR__.'/../lorem-ipsum-generator.php" words '
			.	'20 4 '
			.	'swl '
			.	'dn '
			.	'"<p>" "</p>" '
			.	'cr'
			);
			$output_ex=explode("\r", $output);
			if(count($output_ex) === 7)
				echo ' [ OK ]';
			else
			{
				echo ' [FAIL]';
				$failed=true;
			}
			if(substr($output_ex[0], 0, 54) === '<p>Lorem ipsum dolor sit amet consectetuer adipiscing ')
				echo ' [ OK ]';
			else
			{
				echo ' [FAIL]';
				$failed=true;
			}
			if(
				($output_ex[1] === '') &&
				($output_ex[3] === '') &&
				($output_ex[5] === '')
			)
				echo ' [ OK ]';
			else
			{
				echo ' [FAIL]';
				$failed=true;
			}
			if(
				(substr($output_ex[0], 0, 3) === '<p>') &&
				(substr($output_ex[0], -4) === '</p>')
			)
				echo ' [ OK ]'.PHP_EOL;
			else
			{
				echo ' [FAIL]'.PHP_EOL;
				$failed=true;
			}
		echo '  -> nswl ndn lf';
			$output=shell_exec('"'.PHP_BINARY.'" "'.__DIR__.'/../lorem-ipsum-generator.php" words '
			.	'20 4 '
			.	'nswl '
			.	'ndn '
			.	'"<p>" "</p>" '
			.	'lf'
			);
			$output_ex=explode("\n", $output);
			if(count($output_ex) === 4)
				echo ' [ OK ]';
			else
			{
				echo ' [FAIL]';
				$failed=true;
			}
			if(substr($output_ex[0], 0, 54) === '<p>Lorem ipsum dolor sit amet consectetuer adipiscing ')
			{
				echo ' [FAIL]';
				$failed=true;
			}
			else
				echo ' [ OK ]';
			if(
				(substr($output_ex[0], 0, 3) === '<p>') &&
				(substr($output_ex[0], -4) === '</p>')
			)
				echo ' [ OK ]'.PHP_EOL;
			else
			{
				echo ' [FAIL]'.PHP_EOL;
				$failed=true;
			}
		echo '  -> nswl ndn crlf [SKIP]'.PHP_EOL;

	if($failed)
		exit(1);
?>