<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Db;
use think\Paginator;
use think\Request;
// 景点设置

class Order extends AdminBase
 {
    protected function _initialize()
 {
        parent::_initialize();
    }


    //预约/ 统计票数 /
    public function index()
 {

        $date = input( 'date' );
        if ( $date ) {
            $time = $date;//前端传过来的值
        } else {
            $time = date( 'Y-m-d', time() );//如果没有传过来值获取当前时间
        }
       
        $all = Db::name( 'scenic' )->select();
        //搜索所有的影片
        foreach ( $all as $key => $value ) {
            $where['scenicid'] = $value['id'];
            // 将所有的景区id作为条件，然后去查找订单中相同的，将相同的count一下，然后赋值给一个变量，拼接在数组中
            $where['addtimeymd'] = $time;
            //还要满足时间
            $all[$key]['statistics'] = Db::name( 'order' )->where( $where )->count();
            //满足两个条件，然后循环查找景区id与订单中的scenicid相同的进行统计
        }
        /**
        * 搜索所有影片一点售票
        */
        $number = 0;
        foreach ( $all as $k => $v ) {
            $number = $number + $v['statistics'];
        }
        $this->assign( 'number', $number );
        $this->assign( 'all', $all );
        $this->assign( 'time', $time );
        return $this->fetch( 'index' );
    }

    //核销销毁订单

    public function destroy()
 {

        $period = input( 'period' );
        if ( $period ) {
            $period = $period;
        } else {
            $period = date( 'Y-m-d', time() );
        }
        $where['addtimeymd'] = $period;

        $order = input( 'order' );
        if ( $order ) {
            $where['order'] = $order;
        }
        $scenic = input( 'scenic' );
        if ( $scenic ) {
            $where['scenicid'] = $scenic;
        }

        $scenic = Db::name( 'scenic' )->select();

        $order = Db::name( 'order' )->where( $where )->group( 'order' )->select();
        foreach ( $order as $key => $value ) {
            $time = Db::name( 'times' )->where( 'id', $order[$key]['timeid'] )->find();
            $order[$key]['name'] = Db::name( 'scenic' )->where( 'id', $order[$key]['scenicid'] )->value( 'name' );
            $order[$key]['begin'] = $value['addtimeymd']. ' '.$time['brgin'];
            $order[$key]['finish'] = $time['finish'];
        }
        $this->assign( 'scenic', $scenic );
        $this->assign( 'period', $period );
        $this->assign( 'order', $order );

        return $this->fetch( 'destroy' );
    }

    //订单状态更改

    public function status()
 {
        $status = input( 'status' );
        $order = input( 'order' );
        if ( $status == 1 ) {
            $data['status'] = 0;
        } else {
            $data['status'] = 1;
        }

        $all = Db::name('order')->where('order',$order)->select();
        foreach ($all as $key => $value) {
            Db::name( 'order' )->where( 'id', $value['id'] )->update( $data );
        }
        $this->success( '更改成功', 'admin/Order/destroy' );
    }

    //订单删除

    public function orderdel()
 {
        $id = input( 'id' );
        $del = Db::name( 'order' )->delete( $id );
        $this->success( '删除', 'admin/Order/destroy' );

    }

}