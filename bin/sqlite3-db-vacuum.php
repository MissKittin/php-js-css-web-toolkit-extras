<?php
	/*
	 * Vacuum SQLite3 database
	 *
	 * Warning:
	 *  PDO extension is required
	 *  pdo_sqlite extension is required
	 * or
	 *  SQLite3 class is required
	 */

	if(
		class_exists('PDO') &&
		in_array('sqlite', PDO::getAvailableDrivers())
	)
		$driver='pdo';
	else if(class_exists('SQLite3'))
		$driver='sqlite3';
	else
	{
		echo 'PDO and pdo_sqlite nor sqlite3 extension is not loaded'.PHP_EOL;
		exit(1);
	}

	if($argc < 2)
	{
		echo 'Usage: '.$argv[0].' path/to/database.sqlite3'.PHP_EOL;
		exit(1);
	}

	if(!file_exists($argv[1]))
	{
		echo $argv[1].' does not exist'.PHP_EOL;
		exit(1);
	}

	try {
		switch($driver)
		{
			case 'pdo':
				echo 'Using PDO'.PHP_EOL;
				(new PDO('sqlite:'.$argv[1]))
				->	exec('VACUUM');
			break;
			case 'sqlite3':
				echo 'Using SQLite3'.PHP_EOL;
				$db=new SQLite3($argv[1]);
				$db->busyTimeout(5000);
				$db->exec('VACUUM');
		}

		echo 'Done'.PHP_EOL;
	} catch(Throwable $error) {
		echo 'Error: '.$error->getMessage().PHP_EOL;
		exit(1);
	}
?>