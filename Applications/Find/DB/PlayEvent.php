<?php
class PlayEvent {

    var $DB_SERVER = 'api.edisonx.cn';
    var $DB_USER = 'root';
    var $DB_PSW = 'e5cda60c7e';
    var $DB_NAME = 'game';
    var $DB_TABLE = 'flydog';

    var $user_id;
    var $openid;
    var $nickname;
    var $thumb;
    var $country;
    var $city;
    var $sex="";
    var $ts_barcode=0;
    var $ts_wait=0;
    var $ts_start=0;
    var $ts_end=0;
    var $ts_retry=0;
    var $duration=0;
    var $score=0;
    var $quit=0;
    var $pcid=7;
    var $right_click=0;
    var $click_num=0;

    public function PlayEvent ($msg, $retry) {

        $strArray = explode(';', $msg);

        $this->setBarCodeTime();
        if ($retry) {
            $this->setRetryTime();
        }

        $this->nickname = $strArray[2];
        $this->thumb = $strArray[3];

        if(count($strArray) > 4) {
            $this->openid = $strArray[4];
            $this->user_id = $strArray[5];
            $this->country = $strArray[6];
            $this->city = $strArray[7];
            $this->sex=$strArray[8];
        }
    }

    private function setBarCodeTime(){
        $this->ts_barcode = $this->curSystime();
    }

    private function setWaitTime(){ //取早值
        if ($this->ts_wait == 0) {
            $this->ts_wait = $this->curSystime();
        }
    }

    public function setStartTime(){
        $this->ts_start = $this->curSystime();
    }

    public function setEndTime($quit){
        $this->ts_end = $this->curSystime();
        if ($this->ts_end > $this->ts_start) {
            $this->duration = floatval($this->ts_end - $this->ts_barcode) * 0.001;
        }
        $this->quit = $quit;
        $this->save2Db();
    }

    private function setRetryTime(){
        echo "setRetryTime";
        $this->ts_retry = $this->curSystime();
    }

    public function setScore($value){
        $this->score = $value;
    }

    public function pareMsgForLog($value){
        $first = substr( $value, 0, 1 );
        if ($first == 'p') {
            $this->setStartTime();
        } else if ($first == 'w') {
            $this->setWaitTime();
        } else if ($first == 'b') {
            $this->setStartTime();
        } else if ($first == 's') {
            $this->score = substr($value, 1, strlen($value) -1);
        }
    }

    private function toDTS($value) {
        if ($value === 0) {
            return '0';
        } else {
            return date("Y-m-d@H:i:s" , substr($value,0,10));
        }
    }

    private function save2Db () {
//        $t0 = getSysCurTime();
        echo "saving db";

        $db_connection = mysql_connect($this->DB_SERVER,$this->DB_USER,$this->DB_PSW);

        mysql_query("set names 'utf8'"); //数据库输出编码

        mysql_select_db($this->DB_NAME); //打开数据库


        $v_ts_barcode = $this->toDTS($this->ts_barcode);
        $v_ts_wait = $this->toDTS($this->ts_wait);
        $v_ts_start = $this->toDTS($this->ts_start);
        $v_ts_end = $this->toDTS($this->ts_end);
        $v_ts_retry = $this->toDTS($this->ts_retry);

        $sql = "insert into $this->DB_TABLE (user_id,openid,nickname,thumb,country,city,sex,ts_barcode,ts_wait,ts_start,ts_end,ts_retry,duration,score,quit,host_ip,click_num,right_click,pcid) 
        values ('$this->user_id','$this->openid','$this->nickname','$this->thumb','$this->country','$this->city','$this->sex',
        '$v_ts_barcode','$v_ts_wait','$v_ts_start','$v_ts_end','$v_ts_retry','$this->duration','$this->score','$this->quit','" . $GLOBALS['host_ip'] ."'," . $this->click_num . "," . $this->right_click . "," . $this->pcid . ")";
       //echo $sql;
        mysql_query($sql);

        mysql_close(); //关闭MySQL连接
//        $t1 = getSysCurTime();
//        echo "db ok time_db = " . ($t1 - $t0);

        // for test
//        $this->getRankList(0, $this->user_id);
    }

    private  function curSystime() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

    public function getRankList($screenId, $userId) {
        echo "query rank";
        $response_string = null;

        $db_connection = mysql_connect($this->DB_SERVER,$this->DB_USER,$this->DB_PSW);

        mysql_query("set names 'utf8'"); //数据库输出编码

        mysql_select_db($this->DB_NAME); //打开数据库

//        $sql = "select * from $this->DB_TABLE where screen_id = $screenId and user_id = $userId";
        $sql = "select * from $this->DB_TABLE where screen_id = $screenId ORDER BY score DESC LIMIT 20";
//        echo $sql;
        $result = mysql_query($sql);
        if ($result === false) {
            $response_string = "no record";
        } else {
            $theUserRecord = null;

            $rankList = array();

            while ($msg = mysql_fetch_array($result)) {
                $record = array();
                $record ['nk'] = $msg['nickname'];
                $record ['thb'] = $msg['thumb'];
                $record ['s'] = $msg['score'];
                $uid = $msg['user_id'];
                $record ['uid'] = $uid;
                if ($uid == $userId) {
                    $theUserRecord = $record;
                }
                array_push($rankList, $record);
            }

            if (empty($theUserRecord)) {
                $sql = "select * from $this->DB_TABLE where screen_id = $screenId and user_id = $userId ORDER BY ts_end DESC LIMIT 1";
//            echo $sql;
                $result = mysql_query($sql);
                if ($result !== false) {
                    $msg = mysql_fetch_array($result);
                    if (!empty($msg)) {
                        $record = array();
                        $record ['nk'] = $msg['nickname'];
                        $record ['thb'] = $msg['thumb'];
                        $record ['s'] = $msg['score'];
                        $record ['uid'] = $msg['user_id'];
                    } else {
                        die("can not find the user");
                    }
                }
            }
            $response_string = array("rank" => $rankList, "user" => $theUserRecord);
        }

        mysql_close(); //关闭MySQL连接

        var_dump($response_string);
        return json_encode($response_string);
    }
}
?>