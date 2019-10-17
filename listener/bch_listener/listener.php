<?php

require_once 'vendor/autoload.php';

use BCH_Listener\Classes\Listener;

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

try
{
	$redis = new Redis();
	$redis->connect('redis',  getenv('REDIS_PORT'));
}
catch (\Exception $e)
{
	echo 'Thrown exception: ',  $e->getMessage(), "\n";
}

try
{
	$listener = new Listener();
}
catch (\Exception $e)
{
	echo 'Thrown exception: ',  $e->getMessage(), "\n";
}

// Listening Etherium block and publish to redis channel
$listener->listen('block', function ($response) use ($redis){
	if ( ! empty($response))
	{
		$redis->publish(
			'etherium.blocks',
			json_encode($response)
		);
	}
});

pcntl_exec($_SERVER['_'], $argv);