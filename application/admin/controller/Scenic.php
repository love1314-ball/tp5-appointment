<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Db;
use think\Paginator;
use think\Request;
// 景点设置

class Scenic extends AdminBase
 {
    protected function _initialize()
 {
        parent::_initialize();
    }
    //预约/ 景点配置 / 首页

    public function index()
 {
        $all = Db::name( 'scenic' )->order( 'id desc' )->select();
        foreach ( $all as $key => $value ) {
            $all[$key]['addtime'] = date( 'Y-m-d H:i:s', $value['addtime'] );
        }
        $this->assign( 'all', $all );
        return $this->fetch( 'index' );
    }

    //预约/ 景点配置 / 增加景点

    public function add()
 {

        $up = input( 'up' );
        if ( $up ) {
            $scenic = Db::name( 'scenic' )->where( 'id', $up )->find();
            $timeid = explode( ',', $scenic['timeid'] );
            for ( $i = 0; $i < count( $timeid ) ;
            $i++ ) {
                $time[] = Db::name( 'times' )->where( 'id', $timeid[$i] )->find();
            }
            $this->assign( 'time', $time );
            $this->assign( 'scenic', $scenic );
            $this->assign( 'id', $up );
        }
        return $this->fetch( 'add' );
    }

    //预约/ 景点配置 / 增加景点 / 插入数据库操作

    public function ins()
 {
        $id = input( 'id' );

        $picture = '';
        if ( $_FILES['picture']['name'] == '' ) {
            $picture = input( 'former' );
        } else {
            $file = request()->file( 'picture' );
            $info = $file->move( ROOT_PATH . '/public/static/scenicimg/' );
            if ( $info ) {
                $Nmae = $info->getSaveName();
                $site = str_replace( '\\', '/', $Nmae );
                $picture = '/static/scenicimg/' . $site;
            } else {
                echo $file->getError();
            }
        }
        $data['img'] = $picture;
        $data['name'] = input( 'name' );
        $data['introduce'] = input( 'introduce' );
        $data['poll'] = input( 'poll' );
        $data['day'] = input( 'day' );
        $data['addtime'] = time();
        $time['ticket'] = input( 'ticket' );
        $brgin = input( 'brgin/a' );
        $finish = input( 'finish/a' );
        $time['status'] = 0;

        if ( $_POST ) {
            if ( $id ) {
                $data['id'] = $id;

                $delid = Db::name( 'scenic' )->where( 'id', $id )->value( 'timeid' );
                $delid = explode( ',', $delid );
                for ( $i = 0; $i < count( $delid ) ;
                $i++ ) {
                    Db::name( 'times' )->delete( $delid[$i] );
                }
                for ( $i = 0; $i <count( $brgin ) ;
                $i++ ) {
                    $time['brgin'] = $brgin[$i];
                    $time['finish'] = $finish[$i];
                    $timeid[] = Db::name( 'times' )->insertGetId( $time );
                }
                $timeid = implode( ',', $timeid );
                $data['timeid'] = $timeid;
                $update = Db::name( 'scenic' )->update( $data );
                if ( $update ) {
                    $this->success( '更新成功', 'admin/Scenic/index' );
                } else {
                    $this->error( '更新失败' );
                }
                //更新结束
            } else {
                //我是插入/插入开始
                for ( $i = 0; $i <count( $brgin ) ;
                $i++ ) {
                    $time['brgin'] = $brgin[$i];
                    $time['finish'] = $finish[$i];
                    $timeid[] = Db::name( 'times' )->insertGetId( $time );
                }
                $timeid = implode( ',', $timeid );
                $data['timeid'] = $timeid;
                $add = Db::name( 'scenic' )->insert( $data );
                if ( $add ) {
                    $this->success( '增加成功', 'admin/Scenic/index' );
                } else {
                    $this->error( '增加失败' );
                }
            }
            //插入结束
        }
    }

    //预约/ 景点配置 / 删除

    public function del()
 {
        $id = input( 'id' );
        $del = Db::name( 'scenic' )->delete( $id );
        if ( $del ) {
            $this->success( '删除成功', 'admin/Scenic/index' );
        } else {
            $this->error( '删除失败' );
        }
    }

    //预约/ 景点配置 / 查看

    public function look()
 {
        $id = input( 'id' );

        $time = date( 'Y-m-d', time() );
        //当日的日期
        $all = Db::name( 'scenic' )->where( 'id', $id )->find();
        $timeid = explode( ',', $all['timeid'] );
        for ( $i = 0; $i <count( $timeid ) ;
        $i++ ) {

            $where['timeid'] = $timeid[$i];
            $where['addtimeymd'] = $time;
            $where['scenicid'] = $id;
            $quantum[] = Db::name( 'times' )->where( 'id', $timeid[$i] )->find();
            //计算每个时间点购买的数量
            $quantum[$i]['purchase'] = Db::name( 'order' )->where( $where )->count();
            //将每个时间点购买的数量，再返回给数组、
        }

        $this->assign( 'all', $all );
        $this->assign( 'quantum', $quantum );
        return $this->fetch( 'look' );
    }

}