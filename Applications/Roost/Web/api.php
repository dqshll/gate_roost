<?php

//require_once dirname(dirname(__FILE__)) . '/Events.php';

$RESULT = array('error'=>101, 'msg'=>'参数错误');

$DB_TAB_SHEET = 'sheets';
$DB_TAB_PROGRAM = 'programs';
$QR_FOLDER = '/alidata/www/ecmall/data/files/cha';
$BUZZ_URL = 'https://miniapp.edisonx.cn/h5/taihe2';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == "query" && !empty($_GET['pcid'])) {
        $RESULT['error'] = 0;
        $RESULT['sheet'] = onQueryHandler($_GET['pcid']);
    } else if ($action == "list") {
        $RESULT['error'] = 0;
        $RESULT['actions'] = onActionList();
    } else if ($action == "detail" && isset($_GET['aid'])) {
        $RESULT['error'] = 0;
        $RESULT['actions'] = onActionDetail($_GET['aid']);
    } else if ($action == "add_prog") {
        onProgramAdd($RESULT, $DB_TAB_PROGRAM);
    } else if ($action == "up_prog") {
        onProgramUpdate($RESULT, $DB_TAB_PROGRAM);
    } else if ($action == "del_prog") {
        onProgramDel($RESULT, $DB_TAB_PROGRAM);
    } else if ($action == "q_prog") {
        onProgramQuery($RESULT, $DB_TAB_PROGRAM);
    } else if ($action == "add_sht") {
        onSheetAdd($RESULT, $DB_TAB_SHEET);
    } else if ($action == "up_sht") {
        onSheetUpdate($RESULT, $DB_TAB_SHEET);
    } else if ($action == "del_sht") {
        onSheetDel($RESULT, $DB_TAB_SHEET);
    } else if ($action == "q_sht") {
        onSheetQuery($RESULT, $DB_TAB_SHEET);
    } else if ($action == 'stat' && !empty($_GET['user_id'])) {
        logStat();
    }
}

echo json_encode($RESULT);

/** Query */

function onProgramAdd (&$RESULT, $DB_TAB_PROGRAM) {

    $type = $_GET['type'];

    if(empty($type)) {
        $RESULT['error'] = 102;
        $RESULT['msg'] = '缺少参数 type';
        return;
    }

    $url = $_GET['url'];
    if (empty($url)) {
        $RESULT['error'] = 104;
        $RESULT['msg'] = '缺少参数 url';
        return;
    }

    $cinema_id = $_GET['cnmid'];
    if (empty($cinema_id)) {
        $RESULT['error'] = 105;
        $RESULT['msg'] = '缺少参数 cnmid';
        return;
    }

    $name = $_GET['name'];
    $desc = $_GET['desc'];

    $duration = 0;

    if (isset($_GET['dur'])) {
        $duration = $_GET['dur'];
    }

    connectDb();

    $RESULT['error'] = 0;
    $RESULT['msg'] = '操作成功';

    $sql = "INSERT INTO $DB_TAB_PROGRAM (name, type, duration, url, description, cinema_id) VALUES ('$name',$type,$duration,'$url','$desc','$cinema_id')";

    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
    }

    closeDb();
}

function onProgramUpdate (&$RESULT, $DB_TAB_PROGRAM) {

    $pid = $_GET['pid'];
    if(!isset($pid)) {
        $RESULT['error'] = 106;
        $RESULT['msg'] = '缺少参数 pid';
        return;
    }

    $type = $_GET['type'];
    if(empty($type)) {
        $RESULT['error'] = 102;
        $RESULT['msg'] = '缺少参数 type';
        return;
    }

    $duration = $_GET['dur'];
    if (empty($duration)) {
        $RESULT['error'] = 103;
        $RESULT['msg'] = '缺少参数 dur';
        return;
    }

    $url = $_GET['url'];
    if (empty($url)) {
        $RESULT['error'] = 104;
        $RESULT['msg'] = '缺少参数 url';
        return;
    }

    $cinema_id = $_GET['cnmid'];
    if (empty($cinema_id)) {
        $RESULT['error'] = 105;
        $RESULT['msg'] = '缺少参数 cnmid';
        return;
    }

    $name = $_GET['name'];
    $desc = $_GET['desc'];

    connectDb();

    $RESULT['error'] = 0;
    $RESULT['msg'] = '操作成功';

    $sql = "UPDATE $DB_TAB_PROGRAM SET name='$name', type=$type, duration='$duration', description='$desc', cinema_id='$cinema_id' WHERE pid='$pid'";

//    echo $sql;

    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
    }

    closeDb();
}

function onProgramDel (&$RESULT, $DB_TAB_PROGRAM) {
    $pid = $_GET['pid'];
    if(!isset($pid)) {
        $RESULT['error'] = 106;
        $RESULT['msg'] = '缺少参数 pid';
        return;
    }

    connectDb();

    $RESULT['error'] = 0;
    $RESULT['msg'] = '操作成功';

    $sql = "delete from $DB_TAB_PROGRAM where pid=$pid";
//    echo $sql;

    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
    }

    closeDb();
}

function onProgramQuery (&$RESULT, $DB_TAB_PROGRAM) {

    $cinema_id = $_GET['cnmid'];
    if (empty($cinema_id)) {
        $RESULT['error'] = 105;
        $RESULT['msg'] = '缺少参数 cnmid';
        return;
    }

    connectDb();

    $sql = "SELECT * FROM $DB_TAB_PROGRAM WHERE cinema_id='$cinema_id'";
//    echo $sql;

    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
    } else {
        $RESULT['error'] = 0;
        $RESULT['msg'] = '操作成功';

        $RESULT['progs'] = array();

        while ($item = mysql_fetch_array($action_result)) {
            $program = array();

            $program ['pid'] = $item['pid'];
            $program ['name'] = $item['name'];
            $program ['type'] = $item['type'];
            $program ['cnmid'] = $item['cinema_id'];
            $program ['dur'] = $item['duration'];
            $program ['url'] = $item['url'];

            array_push($RESULT['progs'], $program);
        }
    }

    closeDb();
}

function onSheetAdd (&$RESULT, $DB_TAB_SHEET) {

    $start_time = $_GET['st'];

    if(!isset($start_time)) {
        $RESULT['error'] = 102;
        $RESULT['msg'] = '缺少参数 st';
        return;
    }

    $start_time = toDTS($start_time); // TBD

    $pc_ids = $_GET['pcids'];
//    if (empty($pc_ids)) {
//        $RESULT['error'] = 105;
//        $RESULT['msg'] = '缺少参数 pcids';
//        return;
//    }

    $cinema_ids = $_GET['cnmids'];
    if (!isset($cinema_ids)) {
        $RESULT['error'] = 105;
        $RESULT['msg'] = '缺少参数 cnmids';
        return;
    }

    $programs = $_GET['progs'];
    if (!isset($cinema_ids)) {
        $RESULT['error'] = 107;
        $RESULT['msg'] = '缺少参数 progs';
        return;
    }

    $name = $_GET['name'];
    $desc = $_GET['desc'];

    connectDb();

    $RESULT['error'] = 0;
    $RESULT['msg'] = '操作成功';

    $sql = "INSERT INTO $DB_TAB_SHEET (name, start_time, pc_ids, programs, description, cinema_ids) VALUES ('$name','$start_time','$pc_ids','$programs','$desc','$cinema_ids')";

    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
        closeDb();
        return;
    }

    closeDb();

    Events::onConfigChanged($cinema_ids, $pc_ids);
}

function onSheetUpdate (&$RESULT, $DB_TAB_SHEET) {

    $sid = $_GET['sid'];

    if(!isset($sid)) {
        $RESULT['error'] = 108;
        $RESULT['msg'] = '缺少参数 sid';
        return;
    }

    $start_time = $_GET['st'];

    if(!isset($start_time)) {
        $RESULT['error'] = 102;
        $RESULT['msg'] = '缺少参数 st';
        return;
    }

    $start_time = toDTS($start_time); // TBD

    $pc_ids = $_GET['pcids'];
//    if (empty($pc_ids)) {
//        $RESULT['error'] = 105;
//        $RESULT['msg'] = '缺少参数 pcids';
//        return;
//    }

    $cinema_ids = $_GET['cnmids'];
    if (!isset($cinema_ids)) {
        $RESULT['error'] = 105;
        $RESULT['msg'] = '缺少参数 cnmids';
        return;
    }

    $programs = $_GET['progs'];
    if (!isset($cinema_ids)) {
        $RESULT['error'] = 107;
        $RESULT['msg'] = '缺少参数 progs';
        return;
    }

    $name = $_GET['name'];
    $desc = $_GET['desc'];

    connectDb();

    $RESULT['error'] = 0;
    $RESULT['msg'] = '操作成功';

    $sql = "UPDATE $DB_TAB_SHEET SET name='$name', start_time='$start_time', pc_ids='$pc_ids', programs='$programs', description='$desc', cinema_ids='$cinema_ids' WHERE sid='$sid'";

    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
        closeDb();
        return;
    }

    closeDb();
    Events::onConfigChanged($cinema_ids, $pc_ids);
}

function onSheetDel (&$RESULT, $DB_TAB_SHEET) {
    $sid = $_GET['sid'];
    if(!isset($sid)) {
        $RESULT['error'] = 106;
        $RESULT['msg'] = '缺少参数 sid';
        return;
    }

    connectDb();
    $cinema_ids = null;
    $pc_ids = null;

    $sql = "select * from $DB_TAB_SHEET where sid=$sid";
    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
    } else {

        while ($item = mysql_fetch_array($action_result)) {
            $cinema_ids = $item['cinema_ids'];
            $pc_ids = $item['pc_ids'];
            break;
        }

        if (empty($cinema_ids)) {
            $RESULT['error'] = 111;
            $RESULT['msg'] = '找不到该节目单 sid=' . $sid;
            closeDb();
            return;
        }
    }

    $RESULT['error'] = 0;
    $RESULT['msg'] = '操作成功';

    $sql = "delete from $DB_TAB_SHEET where sid=$sid";
//    echo $sql;

    $action_result = mysql_query($sql);

    if (!$action_result) { // 空
        $RESULT['error'] = 110;
        $RESULT['msg'] = '数据库失败操作失败!';
        closeDb();
        return;
    }

    closeDb();
    if (isset($cinema_ids) && isset($pc_ids)) {
        Events::onConfigChanged($cinema_ids, $pc_ids);
    }
}

function onSheetQuery (&$RESULT, $DB_TAB_SHEET) {
    $pc_id = $_GET['pcid'];
    if (empty($pc_id)) {
        $cinema_id = $_GET['cnmid'];
        if (empty($cinema_id)) {
            $RESULT['error'] = 105;
            $RESULT['msg'] = '缺少参数 cnmid 或 pcid';
            return;
        } else {
            onSheetQueryByCinema($RESULT, $DB_TAB_SHEET);
        }
    } else {
        onSheetQueryByPC($RESULT, $DB_TAB_SHEET);
    }
}

function logStat () {
    global $RESULT;

    $user_id = $_GET['user_id'];
    if(empty($user_id)) {
        $RESULT['error'] = 121;
        return;
    }

    $start_time = $_GET['st'];
    if(empty($start_time)) {
        $RESULT['error'] = 122;
        return;
    }

    $stvalue = toDTS($start_time);

    global $DB_HOST, $DB_NAME;

    $db_connection = mysql_connect($DB_HOST,"root","e5cda60c7e");

    mysql_query("set names 'utf8'"); //数据库输出编码

    mysql_select_db($DB_NAME); //打开数据库

    $end_time = $_GET["ed"];
    $edvalue = toDTS($end_time);

    $dur = 0;
    if(!empty($end_time)) {
        $dur = ($end_time - $start_time) * 0.001;
    }

    $user_id = $_GET['user_id'];
    $nick = $_GET['nick'];
    $gender = $_GET['gender'];
    $aid = $_GET['aid'];
    $sid = $_GET['sid'];
    $uid = $_GET['uid'];
    $join_at = $_GET['join_at'];
    $lat = $_GET['lat'];
    $lng = $_GET['lng'];
    $repay_dur = $_GET['rpd'];

    $sql = "select * from find_stat where user_id = '$user_id' and start_time = '$stvalue'";

    $db_result = mysql_query($sql);

    $item = mysql_fetch_array($db_result);

    if ($item == false) {
        $sql = "INSERT INTO find_stat (user_id, name, gender, action_id, stage_id, union_id, duration, join_at, start_time, end_time, lat, lng, repay_dur) 
                              VALUES ('$user_id','$nick','$gender','$aid','$sid','$uid','$dur','$join_at','$stvalue','$edvalue','$lat','$lng','$repay_dur')";
    } else {
        $sql = "UPDATE find_stat SET duration='$dur', end_time='$edvalue', repay_dur='$repay_dur' WHERE user_id='$user_id' AND start_time='$stvalue'";
    }

    echo $sql;

    $db_result = mysql_query($sql);
    if (!$db_result) {
        $RESULT['error'] = 123;
        return;
    }
    $RESULT['error'] = 0;
    mysql_close();
}

/** Upload */
function onUploadHandler() {
    global $RESULT;
    $RESULT = array('error'=>$_FILES['upImgA']['error']);
    if ($RESULT['error'] === 0) {
        $pkgName = $_POST['name'];
        $duration = $_POST['dur'];
        $version = $_POST['ver'];
        $PosList = $_POST['p'];

        if (isset($pkgName) && isset($duration) && isset($version) && isset($PosList)) {
            $target_path_A = dirname(dirname(__FILE__)) . "/pkg/A.jpg";
            $target_path_B = dirname(dirname(__FILE__)) . "/pkg/B.jpg";
            $target_path_P = dirname(dirname(__FILE__)) . "/pkg/p.txt";
            posList2File($target_path_P, $PosList);

            if (!move_uploaded_file($_FILES['upImgA']['tmp_name'], $target_path_A)) {
                $RESULT['error'] = 104;
                echo json_encode($RESULT);
                return $RESULT;
            }
            if (!move_uploaded_file($_FILES['upImgB']['tmp_name'], $target_path_B)) {
                $RESULT['error'] = 104;
                echo json_encode($RESULT);
                return $RESULT;
            }

            $zip_path = dirname($target_path_A) . "/$pkgName-$version.zip";
            zipFile($target_path_A, $target_path_B, $target_path_P, $zip_path);
            $RESULT['error'] = save2Db($pkgName, $version, $zip_path, $PosList);
        } else {
            $RESULT['error'] = 103;
        }
    }
    return $RESULT;
}

function zipFile($file_path_A, $file_path_B, $file_path_P, $zip_file_path) {
    $zip = new ZipArchive();
    $zip->open($zip_file_path,ZipArchive::CREATE);   //打开压缩包
    $zip->addFile($file_path_A,basename($file_path_A));   //向压缩包中添加文件
    $zip->addFile($file_path_B,basename($file_path_B));
    $zip->addFile($file_path_P,basename($file_path_P));
    $zip->close();  //关闭压缩包
}

function zipQRPics($qr_folder, $zip_file_path) {
    exec("rm -f $zip_file_path");

    $zip = new ZipArchive();
    $zip->open($zip_file_path,ZipArchive::CREATE);   //打开压缩包

    if(@$handle = opendir($qr_folder)) { //注意这里要加一个@，不然会有warning错误提示：）
        while(($file = readdir($handle)) !== false) {
            if($file != ".." && $file != ".") { //排除根目录；
                $tmp = $qr_folder."/".$file;
                if(!is_dir($tmp)) { //忽略子文件夹
                    // echo "$tmp";
                    $zip->addFile($tmp, basename($tmp));
                }
            }
        }
        closedir($handle);
    }
    $zip->close();  //关闭压缩包
}

function curSystime() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}

function toDTS($value) {
    if ($value === 0) {
        return '0';
    } else {
        return date("Y-m-d@H:i:s" , substr($value,0,10));
    }
}

function connectDb () {

    $DB_HOST = 'api.edisonx.cn';
    $DB_NAME = 'roost';
    $DB_USER = 'root';
    $DB_PSW = 'e5cda60c7e';

    $db_connection = mysql_connect($DB_HOST, $DB_USER, $DB_PSW);

    mysql_query("set names 'utf8'"); //数据库输出编码

    mysql_select_db($DB_NAME); //打开数据库

    return $db_connection;
}

function closeDb () {
    mysql_close();
}

function redisConnect() {
    self::$redis = new Redis();
    $result = self::$redis->connect(self::$REDIS_HOST, self::$REDIS_HOST_PORT);
//        echo "connect redis result = $result";
}

function redisDisconnect () {
    self::$redis->close();
}

function redisSetPendingSheetFlag ($Uid) {
    return self::$redis->set(self::$REDIS_KEY_PENDING_PROGRAM . $Uid, 'u');
}

// 下述方法在 web 的进程里执行
function onConfigChangedForPC ($cinema_id, $pc_id) {
    $Uid = $cinema_id . '_' . $pc_id;
    self::redisSetPendingSheetFlag($Uid);

    $ws_py_path =  __DIR__ . '/Web/ws.py';
    $output = array();
    $result = false;
    exec ( "python $ws_py_path " . self::$TRIGER_PREFIX . $Uid , $output , $result);
//        var_dump($output);
//        echo "exec result = $result";
}

function onConfigChanged($cinema_id, $pcids_str) {

    $pcids = explode(',', $pcids_str);

    if(count($pcids) == 0) {
        return;
    }

    self::redisConnect();

    foreach ($pcids as $pcid) {
        self::onConfigChangedForPC($cinema_id, $pcid);
    }

    self::redisDisconnect();

}
?>