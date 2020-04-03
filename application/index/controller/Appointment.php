<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;


class Appointment extends IndexBase
{   
   public function add(){
	   $data['order'] = input("order");//订单号
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

	   for ($i=0; $i < count($name) ; $i++) { 
		   $data['user_name'] = $name[$i];
		   $data['identity'] = $identity[$i];
		   $data['phone'] = $phone[$i];
		   $add = Db::name('order')->insert($data);
	   }
	   if ($add) {
		  $status = 0;
	   }else {
		   $status = 1;
	   }
	   return json($status);
   }


   public function test(){
   	$a = "410727200107022634";
   	$data['order'] = (int)$a;
   	dump($data);
   	// exit;
   	$add = Db::name('order')->insert($data);
   	dump($data);
   }




}
