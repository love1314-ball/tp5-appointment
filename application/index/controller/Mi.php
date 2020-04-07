<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;
use think\Request;
// 我的个人中心

class Mi extends IndexBase
 {

    //前端 / 个人中心

    public function index()
 {

        $userid = 4;
        $all = Db::name( 'order' )->order( 'id desc' )->where( 'userid', $userid )->select();
        $are = 0;
        //已销毁票数
        $deny = 0;
        //没有销毁票数
        foreach ( $all as $key => $value ) {
            if ( $value['status'] == 0 ) {
                $deny++;
            } else {
                $are++;
            }
        }
        $this->assign( 'deny', $deny );
        $this->assign( 'are', $are );
        return $this->fetch( 'index' );
    }

    //前端 / 我的 / 状态页面

    public function status()
 {
        $where['userid'] = 4;
        $where['status'] = input( 'status' );
        $all = Db::name( 'order' )->order( 'id' )->group( 'order' )->where( $where )->select();
        foreach ( $all as $key => $value ) {
            $timeid = $value['timeid'];
            $all[$key]['time'] = Db::name( 'times' )->where( 'id', $timeid )->find();
        }
        foreach ( $all as $k => $v ) {
            $all[$k]['brgin'] = $v['addtimeymd'].' '.$v['time']['brgin'];
            $all[$k]['finish'] = $v['time']['finish'];
            $all[$k]['identity'] = substr_replace( $v['identity'], '****', 5, 9 );
            $all[$k]['name'] = Db::name( 'scenic' )->where( 'id', $all[$k]['scenicid'] )->value( 'name' );
        }
        $this->assign( 'all', $all );
        return $this->fetch( 'status' );
    }

    //前端 / 我的 / 订单详情页

    public function detail()
 {
        $order = input( 'order' );
        $all = Db::name( 'order' )->where( 'order', $order )->select();
        $scenic = Db::name( 'scenic' )->where( 'id', $all[0]['scenicid'] )->find();
        $time = Db::name( 'times' )->where( 'id', $all[0]['timeid'] )->find();
        foreach ( $all as $key => $value ) {
            $all[$key]['name'] = $scenic['name'];
            $all[$key]['img'] = $scenic['img'];
            $all[$key]['brgin'] = $value['addtimeymd'].' '.$time['brgin'];
            $all[$key]['finish'] = $time['finish'];
            $all[$key]['addtime'] = date( 'Y-m-d H:i:s', $value['addtime'] );
        }
        $this->assign( 'all', $all );
        return $this->fetch( 'detail' );

    }

    //管理员销毁订单

    public function destroy()
 {
        $order = input( 'order' );
        $user_id = 3;
        $all = Db::name( 'order' )->where( 'order', $order )->select();
        if ( $all ) {
            if ( $user_id == 3 ) {

                foreach ( $all as $key => $value ) {
                    if ( $all[$key]['status'] == 1 ) {
                        echo"<script>alert('票已销毁不可以进入');	</script>";
                        exit;
                    }
                }

                foreach ( $all as $key => $value ) {
                    $data['status'] = 1;
                    $up = Db::name( 'order' )->where( 'id', $all[$key]['id'] )->update( $data );
                }

                if ( $up ) {
                    echo"<script>alert('可进入');	</script>";

                } else {
                    echo"<script>alert('网络错误');	</script>";
                }
            } else {
                echo"<script>alert('非管理员操作');	</script>";
            }
        } else {
            echo"<script>alert('数据错误');	</script>";
        }
    }

}
