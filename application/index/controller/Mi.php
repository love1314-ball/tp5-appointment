<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;
// 我的个人中心

class Mi extends IndexBase
 {

    public function index()
 {

        $userid = 4;
        $all = Db::name( 'order' )->order( 'id desc' )->where( 'userid', $userid )->select();
        $are = 0;//已销毁票数
        $deny = 0;//没有销毁票数
        foreach ($all as $key => $value) {
           if ($value['status'] == 0) {
             $deny++;
           }else{
            $are++;
           }
        }
        $this->assign( 'deny', $deny );
        $this->assign( 'are', $are );
        return $this->fetch( 'index' );
    }

    //订单状态那一页
    public function status()
    {
        $where['userid'] = 4;
        $where['status'] = input('status');
        $all = Db::name( 'order' )->order( 'id desc' )->group( 'order' )->where( $where )->select();

        foreach ( $all as $key => $value ) {
            $timeid = $value['timeid'];
            $all[$key]['time'] = Db::name( 'time' )->where( 'id', $timeid )->find();
        }

        foreach ( $all as $k => $v ) {
            $all[$k]['begin'] = date( 'Y-m-d H:i', $v['time']['begin'] );
            $all[$k]['finish'] = date( 'H:i', $v['time']['finish'] );
            $all[$k]['identity'] = substr_replace( $v['identity'], '****', 5, 9 );
            $all[$k]['name'] = Db::name('scenic')->where('id',$all[$k]['scenicid'])->value('name');
        }
        // dump($all);exit;
        $this->assign( 'all', $all );
        return $this->fetch( 'status' );
    }





    //详细页面

    public function detail()
 {
        $order = input( 'order' );
        $all = Db::name( 'order' )->where( 'order', $order )->select();
        $scenic = Db::name( 'scenic' )->where( 'id', $all[0]['scenicid'] )->find();
        $time = Db::name( 'time' )->where( 'id', $all[0]['timeid'] )->find();

        foreach ( $all as $key => $value ) {
            $all[$key]['name'] = $scenic['name'];
            //景点的名字
            $all[$key]['img'] = $scenic['img'];
            //景点的图片
            $all[$key]['begin'] = $value['addtimeymd'].date( ' H:i', $time['begin'] );
            //开始时间
            $all[$key]['finish'] = date( 'H:i', $time['finish'] );
            //结束时间
            $all[$key]['addtime'] = date( 'Y-m-d H:i:s', $value['addtime'] );
            //订单插入时间
        }

        $this->assign( 'all', $all );

        return $this->fetch( 'detail' );

    }

}
