<?php
class StatLog {

    var $DB_SERVER = 'api.edisonx.cn';
    var $DB_USER = 'root';
    var $DB_PSW = 'e5cda60c7e';
    var $DB_NAME = 'roost';
    var $DB_TABLE = 'stat';

    var $ts_start=0;
    var $ts_end=0;
    var $duration=0;
    var $pcid='';
    var $cnmid='';

    public function StatLog ($cnmid, $pcid) {

        $this->setStartTime();

        $this->cnmid = $cnmid;
        $this->pcid = $pcid;
    }



    public function setStartTime(){
        $this->ts_start = $this->curSystime();
    }

    public function setEndTime($quit){
        $this->ts_end = $this->curSystime();
        if ($this->ts_end > $this->ts_start) {
            $this->duration = intval(floatval($this->ts_end - $this->ts_start) * 0.001);
        }
        $this->quit = $quit;
        $this->save2Db();
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

        $v_ts_start = $this->toDTS($this->ts_start);
        $v_ts_end = $this->toDTS($this->ts_end);

        $sql = "insert into $this->DB_TABLE (ts_start,ts_end, duration, pc_id, cinema_id) 
        values ('$v_ts_start','$v_ts_end','$this->duration','$this->pcid','$this->cnmid')";
       //echo $sql;
        mysql_query($sql);

        mysql_close(); //关闭MySQL连接
    }

    private  function curSystime() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

//    public function getRankList($screenId, $userId) {
//        echo "query rank";
//        $response_string = null;
//
//        $db_connection = mysql_connect($this->DB_SERVER,$this->DB_USER,$this->DB_PSW);
//
//        mysql_query("set names 'utf8'"); //数据库输出编码
//
//        mysql_select_db($this->DB_NAME); //打开数据库
//
////        $sql = "select * from $this->DB_TABLE where screen_id = $screenId and user_id = $userId";
//        $sql = "select * from $this->DB_TABLE where screen_id = $screenId ORDER BY score DESC LIMIT 20";
////        echo $sql;
//        $result = mysql_query($sql);
//        if ($result === false) {
//            $response_string = "no record";
//        } else {
//            $theUserRecord = null;
//
//            $rankList = array();
//
//            while ($msg = mysql_fetch_array($result)) {
//                $record = array();
//                $record ['nk'] = $msg['nickname'];
//                $record ['thb'] = $msg['thumb'];
//                $record ['s'] = $msg['score'];
//                $uid = $msg['user_id'];
//                $record ['uid'] = $uid;
//                if ($uid == $userId) {
//                    $theUserRecord = $record;
//                }
//                array_push($rankList, $record);
//            }
//
//            if (empty($theUserRecord)) {
//                $sql = "select * from $this->DB_TABLE where screen_id = $screenId and user_id = $userId ORDER BY ts_end DESC LIMIT 1";
////            echo $sql;
//                $result = mysql_query($sql);
//                if ($result !== false) {
//                    $msg = mysql_fetch_array($result);
//                    if (!empty($msg)) {
//                        $record = array();
//                        $record ['nk'] = $msg['nickname'];
//                        $record ['thb'] = $msg['thumb'];
//                        $record ['s'] = $msg['score'];
//                        $record ['uid'] = $msg['user_id'];
//                    } else {
//                        die("can not find the user");
//                    }
//                }
//            }
//            $response_string = array("rank" => $rankList, "user" => $theUserRecord);
//        }
//
//        mysql_close(); //关闭MySQL连接
//
//        var_dump($response_string);
//        return json_encode($response_string);
//    }
}
?>