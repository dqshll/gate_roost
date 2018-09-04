<?php
/**
 * box, mobile 的UID 就是socketId
 * 每个mobile的session中记录了对应的box_id (就是box的socket_id)
 * 通过uid->client_id的映射方式找到client_id和box进行通讯
 *
 */

require_once __DIR__ . '/DB/PlayEvent.php';
require_once __DIR__ . '/Buzz/BuzzGame.php';

//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;


class Events
{
    static $REDIS_KEY_CLIENT_NUM = "client_number";
    static $REDIS_KEY_SOCKET_ID_SEED = "socket_id_seed";
    static $REDIS_KEY_HEART_JUMP_PC_ID = "heart_";

    static $REDIS_HOST = '127.0.0.1';
    static $REDIS_HOST_PORT = 6379;

    static $redis = null;

    public static function onWorkerStart($businessWorker) {
        echo "onWorkerStart\n";
        $output = shell_exec('ifconfig');
        preg_match("/\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}/",$output,$realip);
        $GLOBALS['host_ip'] = $realip[0];
        self::$redis = new Redis();
        self::$redis->connect(self::$REDIS_HOST, self::$REDIS_HOST_PORT);

    }

    public static function onWorkerStop($businessWorker) {
        echo "onWorkerStop\n";
        self::$redis->close();

    }

    static function redisNewSocketId() {
        $newId = self::$redis->incr(self::$REDIS_KEY_SOCKET_ID_SEED);
        return $newId;
    }

    static function redisUpdateClientNumber () {
        $n = Gateway::getAllClientCount();
        echo "cur connections num = {$n}" .
        self::$redis->set(self::$REDIS_KEY_CLIENT_NUM, $n);
    }


    // socket id 等价于 uid
    static function getSocketId($client_id) {
        $client_data = Gateway::getSession($client_id);
        $socket_id = $client_data['socket_id'];
        return $socket_id;
    }

//    static function markBox($client_id) {
//        $client_data = Gateway::getSession($client_id);
//        if (!isset($client_data)) {
//            $client_data = array();
//        }
//
//        $client_data['im_box'] = true;
//        Gateway::setSession($client_id, $client_data);
//    }

    static function getBoxBuzz ($client_id) {
//        echo "getBoxBuzz \n";
        $buzz = null;
        $session = Gateway::getSession($client_id);
        if (isset($session) and array_key_exists('buzz', $session)) {
            $buzz = $session['buzz'];
        }
        return $buzz;
    }

    static function setBoxIdOfMobile($client_id, $box_id) {
        $client_data = Gateway::getSession($client_id);
        if (!isset($client_data)) {
            $client_data = array();
        }

        $client_data['box_id'] = $box_id;
        Gateway::setSession($client_id, $client_data);
    }

    static function getBoxIdOfMobile($client_id) {
        $session = Gateway::getSession($client_id);
        $box_id = null;
        if(isset($session) && array_key_exists('box_id', $session)) {
            $box_id = $session['box_id'];
        }
        echo "getBoxIdOfMobile = $box_id \n";
        return $box_id;
    }

    static function getClientIdByUid($uid) {
        $tmp = Gateway::getClientIdByUid($uid);
        if(count($tmp) != 1) {
            echo "sth wrong with uid($uid)\n";
            var_dump($tmp);
        }
        return $tmp[0];
    }

    static function forward2MyBox($client_id, $message) {
        $box_socket_id = self::getBoxIdOfMobile($client_id);
        if (empty($box_socket_id)) {
            return;
        }
        $box_client_id = self::getClientIdByUid($box_socket_id);

        $forward = self::getSocketId($client_id) . "-" . $message;
        Gateway::sendToClient($box_client_id, $forward);
        echo "forwarding to box ($box_socket_id):" . $forward . "\n";
    }

    //根据手机端client_id 获取盒子信息
    static function getBoxSessionByMobile($client_id) {
        $box_socket_id = self::getBoxIdOfMobile($client_id);
        if (empty($box_socket_id)) {
            return;
        }
        $box_client_id = self::getClientIdByUid($box_socket_id);
        return Gateway::getSession($box_client_id);
    }

    static function createBuzz($msg) {
        $n = strpos($msg, ":");
        if ($n != false) {
            $buzz_name = substr($msg, $n + 1, strlen($msg) - $n - 1);
            echo ("buzz = $buzz_name\n");
            if ($buzz_name === "fd") {
                return new BuzzGame();
            }
//            else if ($buzz === "rn") {
//                $this->mBuzz = new BuzzRunner();
//            } else if ($buzz === "jy") {
//                $this->mBuzz = new JyVote();
//            }
        }
        return null;
    }

    public static function onConnect($client_id) {
//        echo "new client connected ($client_id)\n";

        $new_socket_id = self::redisNewSocketId();

        echo "onConnect ($new_socket_id)\n";

        $client_data = Gateway::getSession($client_id);

        if (!isset($client_data)) {
            $client_data = array();
        }

        $client_data['socket_id'] = $new_socket_id;
        Gateway::setSession($client_id, $client_data);
        Gateway::bindUid($client_id, $new_socket_id);

        self::redisUpdateClientNumber();
    }

    public static function ImpOnBoxConnected($client_id, $message) {
        $box_socket_id = self::getSocketId($client_id);
        echo "new box connected ($box_socket_id)! \n";

//           self::markBox($client_id); // socket_id作为盒子的id和群组名, 方便通信
        Gateway::joinGroup($client_id, $box_socket_id);
        Gateway::sendToClient($client_id, "scid_" . $box_socket_id);

        $session = Gateway::getSession($client_id);
        $arr = explode('@', $message);
        if(count($arr) > 1) {
            $session['pcid'] = $arr[1];
        }
        $session ['buzz'] = self::createBuzz ($arr[0]);
        Gateway::setSession($client_id, $session);
    }

    public static function ImpOnMobileConnected($client_id, $message) {
        $box_socket_id = self::getSocketId($client_id);
        echo "new mobile connected ($box_socket_id) \n";

        $startWithC = (strpos($message, 'prof_c') === 0);
        $startWithR = (strpos($message, 'prof_r') === 0);

        if ($startWithC || $startWithR) {
            $splits = explode(';', $message);
            $n = count($splits);
            if($n > 2) {
                $box_id = $splits[1];
                echo "box_id =  $box_id \n";
                $box_client_id = self::getClientIdByUid($box_id);
                if (isset($box_client_id)) {
                    Gateway::sendToClient($client_id,'ack');
                    self::setBoxIdOfMobile($client_id, $box_id);
                    Gateway::joinGroup($client_id, $box_id);

                    $session = Gateway::getSession($client_id);
                    $session['stat_play'] = new PlayEvent($message, $startWithR);
                    $box_session = self::getBoxSessionByMobile($client_id);
                    if(!empty($box_session['pcid'])) {
                        $session['stat_play']->pcid = $box_session['pcid'];
                    }
                    Gateway::setSession($client_id, $session);

                    self::forward2MyBox($client_id, $message);
                } else {
                    echo "box is not available!!!";
                }
            } else {
                echo "not enough info to join game";
            }
        }
    }

    public static function ImpOnBoxMessage($client_id, $message) {
        echo "msg from box: $message\n";

        if (isset(self::$buzz)) {
            if (self::$buzz->onBoxMessage($client_id, $message)) {
                echo "handled by box buzz\n";
                return;
            }
        }

        $splits = explode('-', $message);

        if (!empty($splits)) {
            $n = count($splits);

            if ($n == 2) {
                $mobileId = $splits[0];
                $command = $splits[1];
                $mobile_client_id = self::getClientIdByUid($mobileId);
                if ($command == 'kick' || $command == 'end') { // kick表示用户死命, end表示游戏结束
                    echo "$command $mobileId\n";
                    $mobile_session = Gateway::getSession($mobile_client_id);
                    $mobile_session ['stat_play']->setEndTime(0);
                    $mobile_session ['kick'] = true;
                    Gateway::setSession($mobile_client_id, $mobile_session);
                    Gateway::closeClient($mobile_client_id, $command);
                } else { // 其他直接转发
                    echo "forward to $mobileId: $command\n";
                    $session = Gateway::getSession($mobile_client_id);
                    Gateway::getSession($client_id) ['buzz']->pareMsgForLog($mobile_client_id, $command, $session);
                    Gateway::setSession($mobile_client_id, $session);
                    Gateway::sendToClient($mobile_client_id, $command);
                }
            } else if ($n == 1) {
                if ($message == 'heart') {
                    $showtime = date("Y-m-d H:i:s");
                    $session = Gateway::getSession($client_id);
                    if(isset($session['pcid'])) {
                        self::$redis->set(self::$REDIS_KEY_HEART_JUMP_PC_ID . $session['pcid'], time());
                    }
                    echo "[$showtime box heart beating]\n";
                    Gateway::sendToClient($client_id,'beat');
                }
            }
        }
    }

    public static function ImpOnMobileMessage($client_id, $message) {
        $mobile_socket_id = self::getSocketId($client_id);
        echo "msg from mobile ($mobile_socket_id): $message \n";
        if (isset($buzz)) {
            if ($buzz->onMobileMessage(self::getBoxIdOfMobile($client_id), $client_id, $message)) {
                echo "took by mobile buzz\n";
                return;
            }
        }
        self::forward2MyBox($client_id, $message);
    }


    public static function onMessage($client_id, $message) {
        echo "onMessage: $message\n";
        if($message == 'u') {
            //先取出session数据
            $session = Gateway::getSession($client_id);
            $session['stat_play']->click_num++;
            //之后得set一下，才能作用原数据上
            Gateway::setSession($client_id, $session);
        }
        if (strpos($message,"prof_") === 0) {
            self::ImpOnMobileConnected($client_id, $message);
        } else if (strpos($message,"box") === 0) {
            self::ImpOnBoxConnected($client_id, $message);
        } else {
            $buzz = self::getBoxBuzz($client_id);
            if (isset($buzz)) {
                self::ImpOnBoxMessage($client_id, $message);
            }
            else {
                self::ImpOnMobileMessage($client_id, $message);
            }
        }
    }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
        echo "onClose ()\n";
        self::redisUpdateClientNumber();

        $box_socket_id = null;

        if(isset($_SESSION) && array_key_exists('box_id', $_SESSION)) {
           $box_socket_id = $_SESSION['box_id'];
        }
        echo "getBoxIdOfMobile = $box_socket_id \n";

        if (empty($box_socket_id)) {
           echo "Box disconnected!\n";
           return;
        }

        if(array_key_exists('kick', $_SESSION)) { // 主机主动kick的不必发消息给主机
           echo "kick mobile no need to tell box";
            return;
        } else {
            $_SESSION['stat_play']->setEndTime(0);
        }

        $box_client_id = self::getClientIdByUid($box_socket_id);

        $mobile_quit_msg = $_SESSION['socket_id'] . "-" . q;
        Gateway::sendToClient($box_client_id, $mobile_quit_msg);
        echo "quit msg to box ($box_socket_id)\n";
   }

   public static function onConfigChanged($param) {
       echo 'onConfigChanged ' . $param;
   }
}
