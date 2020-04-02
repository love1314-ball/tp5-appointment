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
    //景点首页

    public function index()
 {

        $all = Db::name( 'scenic' )->order( 'id desc' )->select();
        foreach ( $all as $key => $value ) {
            $all[$key]['addtime'] = date( 'Y-m-d H:i:s', $value['addtime'] );
        }
        $this->assign( 'all', $all );
        return $this->fetch( 'index' );
    }

    //增加景点/更改

    public function add()
 {

        $up = input( 'up' );
        //增加页面
        $all = Db::name( 'time' )->order( 'begin' )->select();
        if ( $up ) {
            //更新页面
            $scenic = Db::name( 'scenic' )->where( 'id', $up )->find();
            $timeid = explode( ',', $scenic['timeid'] );
            //-----
            foreach ( $all as $key => $value ) {
                $all[$key]['pitch'] = 0;
                //当pitch为0的时候我们就不默认选中
                for ( $i = 0; $i <count( $timeid ) ;
                $i++ ) {
                    if ( $timeid[$i] == $value['id'] ) {
                        $all[$key]['pitch'] = 1;
                        //当pitch为1的时候我就默认选上
                    }
                }
            }
            $this->assign( 'scenic', $scenic );
            $this->assign( 'id', $up );
        }
        $time = new Time;
        $all = $time->transform( $all );
        $this->assign( 'all', $all );
        return $this->fetch( 'add' );
    }

    //我是插入数据库操作

    public function ins()
 {
        $id = input( 'id' );

        if ( $_POST ) {
            if ( $id ) {
                //我是更新
                $picture = '';
                if ( $_FILES['picture']['name'] == '' ) {
                    $picture = input( 'former' );
                } else {
                    $file = request()->file( 'picture' );
                    $info = $file->move( ROOT_PATH . '/public/static/scenicimg/' );
                    if ( $info ) {
                        $Nmae = $info->getSaveName();
                        $site = str_replace( '\\', '/', $Nmae );
                        $newaddress = '/static/scenicimg/' . $site;
                        $picture = $newaddress;
                    } else {
                        echo $file->getError();
                    }
                }
                $data['img'] = $picture;
                $data['name'] = input( 'name' );
                $timeid = input( 'timeid/a' );
                $data['timeid'] = implode( ',', $timeid );
                $data['addtime'] = time();
                $data['id'] = $id;
                $update = Db::name( 'scenic' )->update( $data );
                if ( $update ) {
                    $this->success( '更新成功', 'admin/Scenic/index' );
                } else {
                    $this->error( '更新失败' );
                }

            } else {
                //我是插入

                $picture = '';
                $file = request()->file( 'picture' );
                $info = $file->move( ROOT_PATH . '/public/static/scenicimg/' );
                if ( $info ) {
                    $Nmae = $info->getSaveName();
                    $site = str_replace( '\\', '/', $Nmae );
                    $newaddress = '/static/scenicimg/' . $site;
                    $picture = $newaddress;
                } else {
                    echo $file->getError();
                }
                $data['name'] = input( 'name' );
                $timeid = input( 'timeid/a' );
                $data['timeid'] = implode( ',', $timeid );
                $data['img'] = $picture;
                $data['addtime'] = time();
                $add = Db::name( 'scenic' )->insert( $data );
                if ( $add ) {
                    $this->success( '增加成功', 'admin/Scenic/index' );
                } else {
                    $this->error( '增加失败' );
                }

            }

        }
    }

    //删除

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

    //查看景点

    public function look()
 {
        $id = input( 'id' );
        $time = date('Y-m-d',time());//当日的日期
        $all = Db::name('scenic')->where('id',$id)->find();
        $timeid = explode(",", $all['timeid']);
        for ($i=0; $i <count($timeid) ; $i++) { 
            $where['timeid'] = $timeid[$i];
            $where['addtimeymd'] = $time;
            $where['scenicid'] = $id;
            $quantum[] = Db::name('time')->where('id',$timeid[$i])->find();//计算每个时间点购买的数量
            $quantum[$i]['purchase'] = Db::name('order')->where($where)->count();//将每个时间点购买的数量，再返回给数组、
        }
        foreach ($quantum as $key => $value) {
            $quantum[$key]['begin'] = date('H:i',$value['begin']);//开始时间
            $quantum[$key]['finish'] = date('H:i',$value['finish']);//结束时间

        }
       $this->assign('all',$all);
       $this->assign('quantum',$quantum);
       return $this->fetch('look');
    }

}