<?php
/**
 * Created by PhpStorm.
 * User: dingqun
 * Date: 2017/12/14
 * Time: 下午3:25
 */

//http://www.91qzb.com/thinkphp/public/index.php/api/index/weixin?type=run
//http://980.so/2K3nkh

require_once __DIR__ . '/Buzz.php';
//require_once dirname(dirname(__FILE__)) . '/Utils.php';

class BuzzGame implements Buzz{
//    var $mState = 'wait';
//    var $mGroupId;
//    var $mScoreArray;
//    var $mBox;
    function __construct() {
        echo "creating Game Buzz \n";
//        $this->changeState('wait');
//        $this->mScoreArray = array();
//        $this->mScoreArray[0] = array(0,0,0,0,0);
//        $this->mScoreArray[1] = array(0,0,0,0,0);
//        $this->mScoreArray[2] = array(0,0,0,0,0);
    }

//    private function changeState ($v) {
//        echo "changing state from $this->mState to $v \n";
//        $this->mState = $v;
//    }

//    private function strEndWith($haystack, $needle) {
//
//        $length = strlen($needle);
//        if($length == 0)
//        {
//            return true;
//        }
//        return (substr($haystack, -$length) === $needle);
//    }
//
//    private function broadcast ($connections, $msg) {
//        foreach ($connections as $conn) {
//            if ($conn != $this->mBox) {
//                $conn->send($msg);
//                echo "boradcasting + 1\n";
//            }
//        }
//    }

// --------------------------------   control loop function --------------------------------

//    private function forwardM2BWithId ($box, $mobile, $msg) {
//        $data = "{$mobile->uid}-".$msg;
//        if (!empty($box)) {
//            $box->send($data);
//        }
//    }

    public function onBoxMessage ($box_client_id, $message) {
//        echo "box msg $msg\n";
//        $this->mBox = $box;

        return false;
    }

    public function onMobileMessage ($box_client_id, $mobile_client_id, $message) {
//        echo "mail msg $msg\n";
        $handled = false;
//
//        if (strpos($msg, 'bst:') === 0) {
//            $this->forwardM2BWithId($box, $mobile, $msg);
//            $handled = true;
//        }

        return $handled;
    }

    public static function pareMsgForLog($mobile_client_id, $value, &$session){
        $first = substr( $value, 0, 1 );
        if ($first == 'p') {
            $session['stat_play']->setStartTime();
        } else if ($first == 'w') {
            $session['stat_play']->setWaitTime();
        } else if ($first == 'b') {
            $session['stat_play']->setStartTime();
        } else if ($first == 's') {
            $session['stat_play']->right_click++;
            $session['stat_play']->score = substr($value, 1, strlen($value) -1);
        }
    }
// --------------------------------   business function below --------------------------------
//    private function  onUserProfileReceived ($box, $mobile, $msg) {
//        $this->forwardM2BWithId ($box, $mobile, $msg);
//    }

}