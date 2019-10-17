<?php

namespace BCH_Listener\Classes;
use BCH_Listener\Classes\Providers\Provider;


/**
 * Class Listener
 * @package BCH_Listener\Classes
 */
class Listener {

	/**
	 * Listen Provider changes
	 * @param $type
	 * @param $callback
	 */
	public function listen($type, $callback)
	{
		$provider = new Provider(getenv('INFURA_BASE_URL'));

		// set new filter
		// TODO need make delete filter
		$provider->set_filter('eth_newBlockFilter');

		$action = 'get_'.$type.'_by_hash';

		while (TRUE)
		{
			$callback($provider->get_filter_changes($action));
			sleep(1);
		}
	}
}