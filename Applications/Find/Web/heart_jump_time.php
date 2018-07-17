<?php
//namespace app\api\controller;
if(empty($_GET['pcid'])) {
	echo "pcid 没传";die;
}
$pcid = $_GET['pcid'];
$REDIS_HOST = '127.0.0.1';
$REDIS_HOST_PORT = 6379;
$REDIS_KEY_HEART_JUMP_PC_ID = "heart_";

$redis = new \Redis();
$redis->connect($REDIS_HOST, $REDIS_HOST_PORT);
$heart_jump_time = $redis->get($REDIS_KEY_HEART_JUMP_PC_ID . $pcid);
$redis->close();

$data = array('heart_time' => $heart_jump_time);

echo json_encode($data);