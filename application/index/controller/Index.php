<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;
use think\Session;

class Index extends IndexBase {

    //前端 / 首页

    public function index() {
        $all = Db::name( 'scenic' )->order( 'id desc' )->select();
        $this->assign( 'all', $all );
        return $this->fetch( 'index' );
    }

    //前端 / 预定时间页

    public function time() {

        $scenicid = input( 'id' );
        //获取景点的id
        if ( $scenicid ) {
            session( 'scenicid', $scenicid );
            $scenicid = session( 'scenicid' );
        } else {
            $scenicid = session( 'scenicid' );
        }
        $all = Db::name( 'scenic' )->where( 'id', $scenicid )->find();
        $day = $all['day'];
        $date = date( 'Y-m-d', time() );
        for ( $i = 0; $i < $day; $i++ ) {
            $list[] = date( 'm-d', strtotime( "$date + $i day" ) );
        }
        $data = Db::name( 'scenic' )->where( 'id', $scenicid )->find();
        $timeid = $data['timeid'];
        $timeid = explode( ',', $timeid );
        for ( $i = 0; $i < count( $timeid ) ;
        $i++ ) {
            $time[] = Db::name( 'times' )->where( 'id', $timeid[$i] )->find();
        }
        $present = date( 'H:i', time() );
        /*
        获取当前时间的日期，然后去订单里面查找对应的信息，是插入的年月日+开始时间，计算出票数。
        再用time表中的总票数减去订单表中购买的数量，就计算出所剩下的数量了
        */
        $year = date( 'Y-', time() );
        $sun = input( 'sun' );
        if ( $sun ) {
            $sun = $year.$sun;
        } else {
            $sun = $year.date( 'm-d', time() );
        }
        /*
        判断当天的还是下n天的
        */
        if ( $sun == date( 'Y-m-d', time() ) ) {
            $same = 0;
            //这说明，我选择的日期为当天的，那么我来判断是否到点，到点票提示已销毁
        } else {
            $same = 1;
            //这个是不是当天，那么过来某个时间点我照样可以选择时间。
        }
        //查找订单时间为当日，或者选择的日期
        //所有的票数，和时间点（少一个条件，商品的id。否则不知道哪个商品了）
        foreach ( $time as $k => $v ) {
            $where['timeid'] = $v['id'];
            $timeid = $v['id'];
            $where['addtimeymd'] = $sun;
            $where['scenicid'] = $scenicid;
            $time[$k]['subtract'] = Db::name( 'order' )->where( $where )->count();
            //这是所购买的票数，让我们来相减
            $time[$k]['newticket'] = $v['ticket'] - $time[$k]['subtract'];
            //计算出的新的票数
        }
        $this->assign( 'same', $same );
        $this->assign( 'present', $present );
        $this->assign( 'sun', $sun );
        $this->assign( 'list', $list );
        $this->assign( 'all', $all );
        $this->assign( 'time', $time );
        return $this->fetch( 'time' );
    }

    //前端 / 下单页面

    public function appointment() {
        $timeid = input( 'timeid' );
        $sun = input( 'sun' );
        if ( $sun ) {
            $sun = $sun;
        } else {
            $sun = date( 'Y-m-d', time() );
        }
        $all = Db::name( 'set' )->where( 'id', 1 )->find();
        $scenicid = session( 'scenicid' );
        $data = Db::name( 'scenic' )->where( 'id', $scenicid )->find();
        $this->assign( 'data', $data );
        $this->assign( 'sun', $sun );
        $this->assign( 'scenicid', $scenicid );
        $this->assign( 'all', $all );
        $this->assign( 'timeid', $timeid );
        return $this->fetch( 'appointment' );
    }
}
