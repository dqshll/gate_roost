<?php
/**
 * Created by PhpStorm.
 * User: dingqun
 * Date: 2017/12/14
 * Time: 下午3:37
 */

interface Buzz {
    public function onBoxMessage ($box_client_id, $message);
    public function onMobileMessage ($box_client_id, $mobile_client_id, $message);
}