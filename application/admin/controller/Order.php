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
    //景点首页

    public function index()
 {

        $date = input( 'date' );
        if ( $date ) {
            $time = $date;
        } else {
            $time = date( 'Y-m-d', time() );
        }
        /*
        搜索单个景区，一天的售票
        */
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
        $period = input('period');
        if ($period) {
            $period = $period;
        }else {
            $period = date('Y-m-d',time());
        }
        
        $scenic = Db::name('scenic')->select();

        $where['addtimeymd'] = $period;
        $order = Db::name('order')->where($where)->group('order')->select();

        dump($order);exit;   

        $this->assign('scenic',$scenic);
        $this->assign('period',$period);
        return $this->fetch( 'destroy' );
    }

    //订单销毁执行
    public function destroyup()
    {
        
        
    }




}