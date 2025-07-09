<?php
	if(class_exists(
		'\Anper\PredisCollector\PredisCollector'
	)){
		class anper_predis_collector
		extends Anper\PredisCollector\PredisCollector
		{
			/*
			 * anper/predis-collector patch
			 *
			 * The PredisCollector class has an incorrectly written getAssets method
			 * The original implementation does not allow for correct asset routing
			 * This class correctly implements this method, providing compatibility
			 *  with the maximebf_debugbar.php library
			 *
			 * Warning:
			 *  anper/predis-collector package is required
			 *
			 * Usage (with maximebf_debugbar.php):
				require './lib/maximebf_debugbar.php';

				maximebf_debugbar
				::	add_resources_dir('anper/predis-collector', 'resources')
				::	collectors([ // merge this array
						'predis'=>(class_exists('anper_predis_collector')) ? new anper_predis_collector() : new maximebf_debugbar_dummy()
					]);

				$predis_handle=new Predis\Client([
					'scheme'=>'tcp',
					'host'=>'127.0.0.1',
					'port'=>6379,
					'database'=>0
				]);

				maximebf_debugbar
				::	get_collector('predis')
				->	addClient($predis_handle);
			 */

			public function getAssets()
			{
				return [
					'css'=>'css/widget.css',
					'js'=>'js/widget.js'
				];
			}
		}
	}
?>