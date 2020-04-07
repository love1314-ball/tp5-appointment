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
        if ( $up ) {
            //更新页面
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

    //我是插入数据库操作

    public function ins()
 {
        $id = input( 'id' );

        if ( $_POST ) {
            if ( $id ) {

                $delid = Db::name('scenic')->where('id',$id)->value('timeid');
                $delid = explode(",", $delid);
                
                for ($i=0; $i <count($delid) ; $i++) { 
                  Db::name('times')->delete($delid[$i]);//将之前插入时间的内容删除，因为更新了内容
                }
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

                $data['id'] = $id;
                $data['img'] = $picture;
                $data['name'] = input( 'name' );
                $data['introduce'] = input( 'introduce' );
                $data['poll'] = input( 'poll' );
                $data['day'] = input( 'day' );
                $data['addtime'] = time();
                //time表插入
                $time['ticket'] = input( 'ticket' );
                $brgin = input( 'brgin/a' );
                //开始时间
                $finish = input( 'finish/a' );
                //结束时间
                $time['status'] = 0;
                //用不到

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

                $data['img'] = $picture;
                $data['name'] = input( 'name' );
                $data['introduce'] = input( 'introduce' );
                $data['poll'] = input( 'poll' );
                $data['day'] = input( 'day' );
                $data['addtime'] = time();
                //time表插入
                $time['ticket'] = input( 'ticket' );
                $brgin = input( 'brgin/a' );
                //开始时间
                $finish = input( 'finish/a' );
                //结束时间
                $time['status'] = 0;
                //用不到

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