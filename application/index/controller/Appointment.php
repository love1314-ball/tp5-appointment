<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;
use think\Request;


class Appointment extends IndexBase
{   
   public function add(){
	   $data['order'] = input("order");//订单号
	   $order = input('order');
	   $data['userid'] = input("userid");//用户名
	   $name = input("name/a");//姓名
	   $identity = input("identity/a");//身份证
	   $phone = input('phone/a');//获取景区id
	   $data['ticket'] = 1;//下单数量
	   $ticket = input('ticket');//这个是数量下单的数量，用来我们去和票数减少
	   $data['timeid'] = input('timeid');//时间表id
	   $timeid = $data['timeid'];
	   $data['status'] = 0;//插入订单票的状态0为正常/1为过期 
	   $data['temperature'] = 0;//体温默认为0/不发烧
	   $data['addtime'] = time();//获取增加时间的时间戳
	   $data['addtimeymd'] = input('addtimeymd');//购买的是哪一天的票
	   $data['scenicid'] = input('scenicid');//获取景区id

		//二维码插入
		$code = $this->qrcode($order);
	   //插入数据
	   for ($i=0; $i < count($name) ; $i++) { 
		   $data['user_name'] = $name[$i];
		   $data['identity'] = $identity[$i];
		   $data['phone'] = $phone[$i];
		   $data['code'] = $code;
		   $add = Db::name('order')->insert($data);
	   }
	   if ($add) {
		  $status = 0;
	   }else {
		   $status = 1;
	   }
	   return json($status);
   }


//下载生成的二维码-引用方法1

public function qrcode($order) {
        
	$request = Request::instance();
	header( 'Content-Type: text/html;charset=utf-8' );

	//引入二维码生成插件
	vendor( 'phpqrcode.phpqrcode' );

	// 生成的二维码所在目录+文件名
	$path = 'static/QRcode/';
	//生成的二维码所在目录
	if ( !file_exists( $path ) ) {

		mkdir( $path, 0700, true );
	}
	$time = time().'.png';
	//生成的二维码文件名
	$fileName = $path.$time;
	//1.拼装生成的二维码文件路径

	$data = 'http://www.tp5-appointment.com/index/mi/destroy?orderid='.$order;
	//2.生成二维码的数据( 扫码显示该数据 )

	$level = 'L';
	//3.纠错级别：L、M、Q、H

	$size = 10;
	//4.点的大小：1到10, 用于手机端4就可以了

	ob_end_clean();
	//清空缓冲区
	\QRcode::png( $data, $fileName, $level, $size );
	//生成二维码
	//文件名转码
	$file_name = iconv( 'utf-8', 'gb2312', $time );
	$file_path = $request->domain().'/'.$fileName;//插入数据库的字段
	return $file_path;
	// dump($file_path);
	// exit;
	//获取下载文件的大小
	// $file_size = filesize( $file_path );
	//
	// $file_temp = fopen ( $file_path, 'r' );
	//返回的文件
	// header( 'Content-type:application/octet-stream' );
	// //按照字节大小返回
	// header( 'Accept-Ranges:bytes' );
	// //返回文件大小
	// header( 'Accept-Length:'.$file_size );
	// //这里客户端的弹出对话框
	// header( 'Content-Disposition:attachment;filename='.$time );
	// echo fread ( $file_temp, filesize ( $file_path ) );
	// fclose ( $file_temp );
	// exit ();
}




}
