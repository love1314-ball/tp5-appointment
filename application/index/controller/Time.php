<?php

namespace app\index\controller;

use app\common\controller\AdminBase;
use think\Db;
// 声明一个公共函数，用来转换我们的时间戳

class Time extends AdminBase 
 {
    //  我是scenic控制器的时间转换

    public function transforms( $all )
 {
        foreach ( $all as $k => $v ) {
            $all[$k]['begin'] = date( 'H:i', $v['begin'] );
            $all[$k]['finish'] = date( 'H:i', $v['finish'] );
        }
        return $all;
    }

    

}
