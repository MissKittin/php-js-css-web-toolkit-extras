#!/usr/bin/env php
<?php
	/*
	 * Easily create a brand new project
	 *
	 * Note:
	 *  copy this file to /usr/local/bin
	 *  and give it executable permissions
	 */

	$git_bin='git';
	$app_version='stable';
	$extras_version=null;
	$project_dir=null;
	$app_repo='https://github.com/MissKittin/php-js-css-web-toolkit-app.git';
	$extras_repo='https://github.com/MissKittin/php-js-css-web-toolkit-extras.git';
	$result_code=null;

	foreach(array_slice($argv, 1) as $arg)
	{
		$arg=explode('=', $arg);

		switch($arg[0])
		{
			case '--help':
			case '-h':
				echo $argv[0].' [--app=branch] [--extras=branch] [--app-repo=git-repo-url] [--extras-repo=git-repo-url] [--git=path/to/git] project-dir'.PHP_EOL;
				exit();
			break;
			case '--app':
				echo 'Selected app version: '.$arg[1].PHP_EOL;
				$app_version=$arg[1];
			break;
			case '--extras':
				echo 'Selected extras version: '.$arg[1].PHP_EOL;
				$extras_version=$arg[1];
			break;
			case '--app-repo':
				echo 'Selected app repo: '.$arg[1].PHP_EOL;
				$app_repo=$arg[1];
			break;
			case '--extras-repo':
				echo 'Selected extras repo: '.$arg[1].PHP_EOL;
				$extras_repo=$arg[1];
			break;
			case '--git':
				$git_bin=$arg[1];
			break;
			default:
				echo 'Project directory: '.$arg[0].PHP_EOL;
				$project_dir=$arg[0];
		}
	}

	if($project_dir === null)
	{
		echo 'Error: no project directory given'.PHP_EOL;
		echo 'See '.$argv[0].' --help'.PHP_EOL;
		exit(1);
	}

	passthru($git_bin.' --version', $result_code);

	if($result_code !== 0)
	{
		echo 'Error: git not found'.PHP_EOL;
		echo 'See '.$argv[0].' --help'.PHP_EOL;
		exit(1);
	}

	system('"'.$git_bin.'" clone '
	.	'--recursive '
	.	'--depth 1 '
	.	'--shallow-submodules '
	.	'-b '.$app_version.' '
	.	'"'.$app_repo.'" '
	.	'"'.$project_dir.'"'
	);

	if(!is_file($project_dir.'/app/bin/git-init.php'))
	{
		echo 'Error: git clone failed'.PHP_EOL;
		exit(1);
	}

	system('"'.PHP_BINARY.'" '
	.	'"'.$project_dir.'/app/bin/git-init.php" '
	.	'"'.$git_bin.'"'
	);

	if($extras_version !== null)
		system('"'.$git_bin.'" '
		.	'-C "'.$project_dir.'" '
		.	'submodule add '
		.	'--depth=1 '
		.	'-b '.$extras_version.' '
		.	'"'.$extras_repo.'" '
		.	'tke'
		);
?>