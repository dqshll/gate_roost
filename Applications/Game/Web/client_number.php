<?php
//namespace app\api\controller;

$REDIS_HOST = '127.0.0.1';
$REDIS_HOST_PORT = 6379;
$REDIS_KEY_CLIENT_NUM = "client_number";

$redis = new \Redis();
$redis->connect($REDIS_HOST, $REDIS_HOST_PORT);
$client_number = $redis->get($REDIS_KEY_CLIENT_NUM);
$redis->close();

$data = array('client_number' => $client_number);

echo json_encode($data);




