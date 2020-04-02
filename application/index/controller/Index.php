<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;
use think\Session;

class Index extends IndexBase {

    //我是首页

    public function index() {

        $all = Db::name( 'scenic' )->order( 'id desc' )->select();

        $this->assign( 'all', $all );

        return $this->fetch( 'index' );
    }

    //我是时间那一页

    public function time() {

        $scenicid = input( 'id' );
        //获取景点的id
        if ( $scenicid ) {
            session( 'scenicid', $scenicid );
            $scenicid = session( 'scenicid' );
        } else {
            $scenicid = session( 'scenicid' );
        }
        // dump( $scenicid );

        /*
        获取时间信息的
        */
        $all = Db::name( 'set' )->where( 'id', 1 )->find();
        $day = $all['day'];
        $date = date( 'Y-m-d', time() );
        //获取当天日期
        for ( $i = 0; $i < $day; $i++ ) {
            $list[] = date( 'm-d', strtotime( "$date + $i day" ) );
        }
        /*
        获取时间信息结束
        */
        /*
        默认输出时间段开始
        */
        $data = Db::name( 'scenic' )->where( 'id', $scenicid )->find();
        $timeid = $data['timeid'];
        $timeid = explode( ',', $timeid );
        $data = Db::name( 'time' )->order( 'begin' )->select();
        foreach ( $data as $key => $value ) {
            $data[$key]['output'] = 0;
            //当output为0的时候不可选择，我们后台没有选择他然后就无法选择了
            for ( $i = 0; $i <count( $timeid ) ;
            $i++ ) {
                if ( $timeid[$i] == $value['id'] ) {
                    $data[$key]['output'] = 1;
                    //当output为1的时候输出
                }
            }
        }
        $time = new Time;
        $data = $time->transforms( $data );
        /*
        默认输出时间段结束
        */

        /**
        * 判断当前时间段，然后将票给销毁
        *  销毁票（时间段到了就销毁）
        */
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
        foreach ( $data as $k => $v ) { 
            $where['timeid'] = $v['id'];
            $timeid = $v['id'];
            $where['addtimeymd'] = $sun;
            $where['scenicid'] = $scenicid;
            $data[$k]['subtract'] = Db::name( 'order' )->where( $where )->count();
            //这是所购买的票数，让我们来相减
            $data[$k]['newticket'] = $v['ticket'] - $data[$k]['subtract'];
            //计算出的新的票数
        }
        // dump( $where );
        // dump( $data );
        // exit;
        $this->assign( 'same', $same );
        $this->assign( 'present', $present );
        $this->assign( 'data', $data );
        $this->assign( 'sun', $sun );
        $this->assign( 'list', $list );
        //获取的是几天信息
        $this->assign( 'all', $all );
        //获取时间信息的
        return $this->fetch( 'time' );
    }
    //我是购买那一页

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
        //获取景区id
        $data = Db::name( 'scenic' )->where( 'id', $scenicid )->find();
        $this->assign( 'data', $data );
        $this->assign( 'sun', $sun );
        $this->assign( 'scenicid', $scenicid );
        $this->assign( 'all', $all );
        $this->assign( 'timeid', $timeid );
        return $this->fetch( 'appointment' );
    }
}
