<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Db;
// 声明一个公共函数，用来转换我们的时间戳

class Time extends AdminBase
 {

    //多个数组选择这个

    public function switch( $all ) {
        foreach ( $all as $k => $v ) {
            $all[$k]['begin'] = date( 'H:i', $v['begin'] );
            $all[$k]['finish'] = date( 'H:i', $v['finish'] );
        }
        return $all;
    }

    //单个数组选这个

    public function transition( $all ) {

        $time = $all['begin'];
        $all['begin'] = date( 'H:i', $all['begin'] );
        $all['finish'] = date( 'H:i', $all['finish'] );
        $all['setting'] = date( 'Y-m-d', $time );
        //选择设置日期
        return $all;
    }

    //我是order控制器的时间转换

    public function order( $all ) {
        foreach ( $all as $k => $v ) {
            $all[$k]['addtime'] = date( 'Y-m-d H:i:s', $v['addtime'] );
        }
        return $all;
    }

    //  我是scenic控制器的时间转换

    public function transform( $all )
 {
        foreach ( $all as $k => $v ) {
            $all[$k]['begin'] = date( 'H:i', $v['begin'] );
            $all[$k]['finish'] = date( 'H:i', $v['finish'] );
        }
        return $all;
    }

    //  order控制器来计算我们的票数的

    public function orders( $all, $time, $scenic )
 {

        //计算总票数
        $number = 0;
        foreach ( $all as $k => $v ) {
            $addtime = date( 'Y-m-d', $v['addtime'] );
            $all[$k]['addtime'] = $addtime;
            if ( $time == $addtime ) {
                $number++;
            }
        }
        //计算每个电影的票数

        for ( $i = 0; $i < count( $scenic ) ; $i++ ) {
            // dump( $scenic[$i] );
            $where['scenicid'] = $scenic[$i];
            $where['addtimeymd'] = $time;
            $apiece[] = Db::name( 'order' )->where( $where )->count();
        }
        // apiece[]这个是每个商品的售票数量

        /**
        * 错误想法版本
        *
        $a = 0;
        foreach ( $test as $key => $value ) {
            foreach ( $value as $ke => $val ) {
                $addtime = date( 'Y-m-d', $val['addtime'] );
                if ( $time == $addtime ) {
                    for ( $i = 0; $i < count( $scenic ) ;
                    $i++ ) {

                        if ( $val['scenicid'] == $scenic[$i] ) {
                            $abc = count( $val['scenicid'] );
                        }
                        dump( $val );
                    }
                }
            }
        }
        *
        */
        $big = [$number,$apiece];
        return $big;
    }

}
