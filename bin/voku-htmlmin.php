<?php
	/*
	 * Interface for the voku/html-min package
	 *
	 * Warning:
	 *  check_var.php library is required
	 *  voku/html-min package is required
	 *
	 * Composer directory path:
	 *  __DIR__/composer/vendor
	 *  __DIR__/vendor
	 *  __DIR__/../composer/vendor
	 *  __DIR__/../vendor
	 *  getenv(TK_COMPOSER)
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
	function find_composer_autoloader()
	{
		foreach([
			'composer/vendor/autoload.php',
			'vendor/autoload.php',
			'../composer/vendor/autoload.php',
			'../vendor/autoload.php'
		] as $composer_path)
			if(is_file(__DIR__.'/'.$composer_path))
				return __DIR__.'/'.$composer_path;

		$TK_COMPOSER=getenv('TK_COMPOSER');

		if(
			($TK_COMPOSER !== false) &&
			is_file($TK_COMPOSER.'/autoload.php')
		)
			return $TK_COMPOSER.'/autoload.php';

		throw new Exception('Composer autoloader not found');
	}

	try {
		load_library(['check_var.php']);
		require find_composer_autoloader();

		if(!class_exists('\voku\helper\HtmlMin'))
			throw new Exception('voku/html-min package not installed');
	} catch(Exception $error) {
		echo 'Error: '.$error->getMessage().PHP_EOL;
		exit(1);
	}

	$input_file=check_argv_next_param('--file');
	$input_directory=check_argv_next_param('--dir');

	if(
		(
			($input_file === null) &&
			($input_directory === null)
		) ||
		check_argv('--help') || check_argv('-h')
	){
		echo 'Usage:'.PHP_EOL;
		echo ' '.$argv[0].' --dir ./public/static'.PHP_EOL;
		echo ' '.$argv[0].' --file ./public/static/index.html'.PHP_EOL;
		echo 'Where'.PHP_EOL;
		echo ' --dir minifies all *.htm and *.html files'.PHP_EOL;
		echo '  in a directory and subdirectories'.PHP_EOL;
		echo ' --file minifies a specific file'.PHP_EOL;
		exit(1);
	}

	if(
		($input_file !== null) &&
		(!is_file($input_file))
	){
		echo $input_file.' is not a file'.PHP_EOL;
		exit(1);
	}

	if(
		($input_directory !== null) &&
		(!is_dir($input_directory))
	){
		echo $input_directory.' is not a directory'.PHP_EOL;
		exit(1);
	}

	$html_min=(new voku\helper\HtmlMin())
	->	doOptimizeViaHtmlDomParser()                   // optimize html via "HtmlDomParser()"
	->	doRemoveComments()                             // remove default HTML comments (depends on "doOptimizeViaHtmlDomParser(true)")
	->	doSumUpWhitespace()                            // sum-up extra whitespace from the Dom (depends on "doOptimizeViaHtmlDomParser(true)")
	->	doRemoveWhitespaceAroundTags()                 // remove whitespace around tags (depends on "doOptimizeViaHtmlDomParser(true)")
	->	doOptimizeAttributes()                         // optimize html attributes (depends on "doOptimizeViaHtmlDomParser(true)")
	->	doRemoveHttpPrefixFromAttributes()             // remove optional "http:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
	->	doRemoveHttpsPrefixFromAttributes()            // remove optional "https:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
	->	doKeepHttpAndHttpsPrefixOnExternalAttributes() // keep "http:"- and "https:"-prefix for all external links
	->	doRemoveDefaultAttributes()                    // remove defaults (depends on "doOptimizeAttributes(true)" | disabled by default)
	->	doRemoveDeprecatedAnchorName()                 // remove deprecated anchor-jump (depends on "doOptimizeAttributes(true)")
	->	doRemoveDeprecatedScriptCharsetAttribute()     // remove deprecated charset-attribute - the browser will use the charset from the HTTP-Header, anyway (depends on "doOptimizeAttributes(true)")
	->	doRemoveDeprecatedTypeFromScriptTag()          // remove deprecated script-mime-types (depends on "doOptimizeAttributes(true)")
	->	doRemoveDeprecatedTypeFromStylesheetLink()     // remove "type=text/css" for css links (depends on "doOptimizeAttributes(true)")
	->	doRemoveDeprecatedTypeFromStyleAndLinkTag()    // remove "type=text/css" from all links and styles
	->	doRemoveDefaultMediaTypeFromStyleAndLinkTag()  // remove "media="all" from all links and styles
	->	doRemoveDefaultTypeFromButton()                // remove type="submit" from button tags
	->	doRemoveEmptyAttributes()                      // remove some empty attributes (depends on "doOptimizeAttributes(true)")
	->	doRemoveValueFromEmptyInput()                  // remove 'value=""' from empty <input> (depends on "doOptimizeAttributes(true)")
	->	doSortCssClassNames()                          // sort css-class-names, for better gzip results (depends on "doOptimizeAttributes(true)")
	->	doSortHtmlAttributes()                         // sort html-attributes, for better gzip results (depends on "doOptimizeAttributes(true)")
	->	doRemoveSpacesBetweenTags()                    // remove more (aggressive) spaces in the dom (disabled by default)
	->	doRemoveOmittedQuotes()                        // remove quotes e.g. class="lall" => class=lall
	->	doRemoveOmittedHtmlTags();

	if($input_file !== null)
	{
		echo 'Processing '.$input_file.PHP_EOL;

		file_put_contents(
			$input_file,
			$html_min->minify(
				file_get_contents($input_file)
			)
		);

		exit();
	}

	foreach(new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(
			$input_directory,
			RecursiveDirectoryIterator::SKIP_DOTS
		)
	) as $asset)
		switch(pathinfo($asset, PATHINFO_EXTENSION))
		{
			case 'htm':
			case 'html':
				echo 'Processing '.$asset.PHP_EOL;

				file_put_contents(
					$asset,
					$html_min->minify(
						file_get_contents($asset)
					)
				);
		}
?>