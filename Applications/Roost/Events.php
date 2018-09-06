<?php

require_once __DIR__ . '/DB/StatLog.php';

use \GatewayWorker\Lib\Gateway;


class Events
{
    static $REDIS_KEY_PENDING_PROGRAM= "pp_";
    static $REDIS_KEY_HEART_JUMP_PC_ID = "heart_";

    static $TRIGER_PREFIX = "trg_";

    static $REDIS_HOST = '127.0.0.1';
    static $REDIS_HOST_PORT = 6379;
//
    static $redis = null;

    public static function onWorkerStart($businessWorker) {
        echo "onWorkerStart\n";
        $output = shell_exec('ifconfig');
        preg_match("/\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}/",$output,$realip);
        $GLOBALS['host_ip'] = $realip[0];

        self::redisConnect();
    }

    public static function redisConnect() {
        self::$redis = new Redis();
        $result = self::$redis->connect(self::$REDIS_HOST, self::$REDIS_HOST_PORT);
//        echo "connect redis result = $result";
    }

    public static function redisDisconnect () {
        self::$redis->close();
    }

    public static function onWorkerStop($businessWorker) {
        echo "onWorkerStop\n";
        self::redisDisconnect();
    }

    static function redisSetPendingSheetFlag ($Uid) {
        return self::$redis->set(self::$REDIS_KEY_PENDING_PROGRAM . $Uid, 'u');
    }

    static function redisClearPendingSheetFlag ($Uid) {
        return self::$redis->del(self::$REDIS_KEY_PENDING_PROGRAM . $Uid);
    }

    static function redisGetPendingSheetFlag ($Uid) {
        return self::$redis->get(self::$REDIS_KEY_PENDING_PROGRAM . $Uid);
    }


    public static function onConnect($client_id) {
        echo "onConnect: new client connected ($client_id)\n";

    }

    static function onPCRegister($client_id, $message) {

        $session = Gateway::getSession($client_id);

        if (!isset($client_data)) {
            $session = array();
        }

        $arr = explode('@', $message);

        $session['cnmid'] = $arr[1];
        $session['pcid'] = $arr[2];

        if(empty($session['cnmid']) or empty($session['pcid'])){
            return;
        }

        $pcUid = $session['cnmid'] . '_' . $session['pcid'];

        echo "onPCRegister ($message)! Uid=$pcUid\n";

        $session['uid'] = $pcUid;
        $session['stat'] = new StatLog($message);

        Gateway::setSession($client_id, $session);
        Gateway::bindUid($client_id, $pcUid);
        $sheet = self::redisGetPendingSheetFlag($pcUid);
        if (!isset($sheet)) {
            echo 'pending sheet found!';

        }
    }

    static function onPCUpdated ($client_id) {
        $session = Gateway::getSession($client_id);
        $cinema_id = $session['cnmid'];

        if (!isset($cinema_id)) {
            echo 'cinema_id is missing !';
            return;
        }

        $pc_id = $session['pcid'];

        if (!isset($pc_id)) {
            echo "pcid is missing (cnmid = $cinema_id)!";
            return;
        }

        $Uid = $cinema_id . '_' . $pc_id;

        self::redisClearPendingSheetFlag($Uid);
    }

    static function onChangeTriger ($message) {
        $Uid = substr($message, 4, strlen($message) -4);
        echo "trigger Uid = $Uid \n";

        $client_id_array = Gateway::getClientIdByUid($Uid);

        if (count($client_id_array) == 0) {
            echo "box Uid $Uid not online, send to it later\n";

        } else {
            $client_id = $client_id_array[0];
            Gateway::sendToClient($client_id, "u");
        }
    }

    public static function onMessage($client_id, $message) {
        echo "onMessage: $message\n";
        if (strpos($message,"pc_reg") === 0) {
            self::onPCRegister($client_id, $message);
        } else if (strpos($message, self::$TRIGER_PREFIX) === 0) { // 节目单变更触发
            self::onChangeTriger ($message);
        } else if (strpos($message,"upd") === 0) { // 收到pc确认消息: 已更新
            self::onPCUpdated();
        }
    }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
       echo "onClose ()\n";

       $session = Gateway::getSession($client_id);
       $cinema_id = $session['cnmid'];
       $pc_id = $session['pcid'];

       echo "$cinema_id _ $pc_id disconnected\n";


   }
}
