#!/usr/bin/env php
<?php

$server = new swoole_websocket_server("0.0.0.0", 9501);


$server->on("workerStart", function ($server, $workerId) {
	$client = new \swoole_redis;
	$client->on("message", function (\swoole_redis $client, $data) use ($server) {
		if ($data[0] === 'message')
		{
			foreach($server->connections as $fd) {
				$server->push($fd, json_encode($data));
			}
		}
	});
	$client->connect("redis", 6379, function (swoole_redis $client, $result) {
		$client->subscribe("etherium.blocks");
	});
});

$server->on('open', function (swoole_websocket_server $server, $request){
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame){
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, 'Message from ws');
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();
