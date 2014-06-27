<?php
require_once("./tianyiSDK.php");
$aid = '123';		//AppID
$sid = '123';		//SecretID
$tianyi = new tianyiSDK($aid,$sid);
$to = '13355556666';
$smsdata = array('param1' => 'java编程语言','param2' => '2014-06-30');
echo $tianyi->sendTemplateSMS($to,$smsdata,'91000491');
echo $tianyi->sendCaptchaSMS($to);
