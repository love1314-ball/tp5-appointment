<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Db;
use think\Paginator;
// 预约管理

class Appointment extends AdminBase
 {
    protected function _initialize()
 {
        parent::_initialize();
    }

    public function index()
 {

        $all = Db::name( 'order' )->order( 'addtimeymd desc' )->group( 'order' )->select();
        foreach ( $all as $k => $v ) {
            $status = Db::name( 'time' )->where( 'id', $v['timeid'] )->find();
            $all[$k]['status'] = $status['status'];
            $all[$k]['identity'] =  substr_replace( $v['identity'], '****', 5, 9 );
        }
        $time = new Time;
        $all = $time->order( $all );
        $this->assign( 'all', $all ); 
        // dump($all);
        // exit;
        return $this->fetch( 'index' );
    }
    //删除订单

    public function orderdel()
 {
        $id = input( 'id' );
        $del = Db::name( 'order' )->delete( $id );
        if ( $del ) {
            $this->success( '删除成功' );
        } else {
            $this->errorr( '删除失败' );
        }
    }

    //设置页面

    public function set() 
 {

        $id = 1;
        $all = Db::name( 'set' )->where( 'id', $id )->find();
        $this->assign( 'all', $all );
        return $this->fetch( 'set' );
    }

    //设置更改信息

    public function setup()
 {
        $data['poll'] = input( 'poll' );
        $data['day'] = input( 'day' );
        $id = 1;
        $update = Db::name( 'set' )->where( 'id', $id )->update( $data );
        if ( $update ) {
            $this->success( '更新成功', 'admin/Appointment/set' );
        } else {
            $this->errorr( '更新失败' );
        }
    }

    //时间设置

    public function time()
 {
        $up = input( 'up' );
        //判断是更新还是增加页面
        $id = input( 'id' );
        if ( $up == 1 ) {
            $all = Db::name( 'time' )->where( 'id', $id )->find();
            $time = new Time;
            $all = $time->transition( $all );
            // dump( $all );
            // exit;
            $this->assign( 'all', $all );
            return $this->fetch( 'time' );
        } else {
            return $this->fetch( 'time' );
        }
    }
    //时间插入

    public function timeadd()
 {

        $id = input( 'id' );
        if ( $id ) {
            $begin = input( 'begin' );
            $time = date( 'Y-m-d', time() );
            //这里我要进行变成活的（我可以手动更改的）
            $new = $time.$begin;
            $data['begin'] = strtotime( $new );
            $finish = input( 'finish' );
            $new = $time.$finish;
            $data['finish'] = strtotime( $new );
            $data['ticket'] = input( 'ticket' );
            $data['status'] = 0;
            $add = Db::name( 'time' )->where( 'id', $id )->update( $data );
            if ( $add ) {
                $this->success( '编辑成功', 'admin/Appointment/look' );
            } else {
                $this->errorr( '编辑失败' );
            }
        } else {
            $begin = input( 'begin' );
            //获取开始时间
            $time = date( 'Y-m-d', time() );
            //这里我要进行变成活的（我可以手动更改的）
            $new = $time.$begin;
            //年月日拼接开始时间
            $data['begin'] = strtotime( $new );
            //时间转为时间戳
            // $nowtimes = date( 'Y-m-d H:i:s', $begin );
            //时间戳转为时间

            $finish = input( 'finish' );
            $new = $time.$finish;
            //年月日拼接开始时间
            $data['finish'] = strtotime( $new );
            //时间转为时间戳

            $data['ticket'] = input( 'ticket' );
            //票数
            $data['status'] = 0;
            //状态默认为正常

            $add = Db::name( 'time' )->insert( $data );
            if ( $add ) {
                $this->success( '添加成功', 'admin/Appointment/look' );
            } else {
                $this->errorr( '增加失败' );
            }
        }

    }

    //时间查看

    public function look()
 {
        $date = input( 'time' );
        //条件时间
        $all = Db::name( 'time' )->order( 'begin' )->select();
        $alls = $all;
        //下面要用到
        $time = new Time;
        $all = $time->switch( $all ); 
        $this->assign( 'list', $all );
        return $this->fetch( 'look' );
    }

    //时间更改

    public function timeup()
 {
        $status = input( 'status' );
        $id = input( 'id' );
        if ( $status == '1' ) {
            $data['status'] = 0;
            $update = Db::name( 'time' )->where( 'id', $id )->update( $data );
            if ( $update ) {
                $this->success( '更改成功' );
            } else {
                $this->errorr( '更改失败' );
            }
        }
        if ( $status == '0' ) {
            $data['status'] = 1;
            $update = Db::name( 'time' )->where( 'id', $id )->update( $data );
            if ( $update ) {
                $this->success( '更改成功' );
            } else {
                $this->errorr( '更改失败' );
            }
        }
    }
    //删除

    function timedel()
 {
        $id = input();
        $del = Db::name( 'time' )->delete( $id );
        if ( $del ) {
            $this->success( '删除成功' );
        } else {
            $this->errorr( '删除失败' );
        }
    }
    //订单查看

    public function order() {
        $order = input( 'order' );
        $all = Db::name( 'order' )->where( 'order', $order )->select();
        $timeid = $all[0]['timeid'];
        $time = Db::name( 'time' )->where( 'id', $timeid )->find();
        $scenic = Db::name('scenic')->where('id',$all[0]['scenicid'])->find();
        foreach ( $all as $key => $value ) {
            $all[$key]['identity'] = substr_replace( $value['identity'], '****', 5, 9 );
            $all[$key]['addtime'] = date( 'Y-m-d H:i:s', $value['addtime'] );
            $all[$key]['begin'] = $all[$key]['addtimeymd'].date( ' H:i', $time['begin'] );
            $all[$key]['finish'] = $all[$key]['addtimeymd'].date( ' H:i', $time['finish'] );
            $all[$key]['name'] = $scenic['name'];
            $all[$key]['img'] = $scenic['img'];
        }
       
        $this->assign( 'all', $all );
        return $this->fetch( 'order' );
    }

    //导出内容

    public function export()
 {
        //    读取数据+搜索
        $fileName = '订单表' . date( '_YmdHis' );
        //文档的名字我们也可以跟一个变量这里我写的是一个死值拼接当前时间
        $all = Db::name( 'order' )->alias( 'a' )->join( 'scenic w', 'a.scenicid = w.id', 'left' )->order( 'a.id desc' )->select();

        foreach ( $all as $key => $value ) {
            if ( $value['status'] == 0 ) {
                $all[$key]['status'] = '正常';
            }
            if ( $value['status'] == 1 ) {
                $all[$key]['status'] = '已销毁';
            }
            if ( $value['temperature'] == 0 ) {
                $all[$key]['temperature'] = '正常';
            }
            if ( $value['temperature'] == 1 ) {
                $all[$key]['temperature'] = '发烧';
            }
        }

        //查找内容
        $data = '';
        $data = $data .'<table class="bioage" border="2">
        <tr>
        <td>用户id</td>
        <td>用户名</td>
        <td>电影id</td>
        <td>电影名</td>
        <td>订单号</td>
        <td>身份证号</td>
        <td>是否发烧</td>
        <td>订单状态</td>
        <td>时间id</td>
        <td>购买票数</td>
        <td>添加的时间</td>
        </tr>';
        //声名变量然后拼接一个表头
        foreach ( $all as $v ) {
            //遍历输出
            $data = $data ."<tr>
            <td>{$v['userid']}</td> " .
            "<td>{$v['user_name']} </td> " .
            "<td>{$v['scenicid']} </td> " .
            "<td>{$v['name']} </td> " .
            "<td>{$v['order']} </td> " .
            "<td>{$v['identity']} </td> " .
            "<td>{$v['temperature']} </td> " .
            "<td>{$v['status']} </td> " .
            "<td>{$v['timeid']} </td> " .
            "<td>{$v['ticket']} </td> " .
            "<td>{$v['addtimeymd']} </td> " .
            '</tr>';
            //声名变量拼接输出 所有值
        }
        $data = $data . '</table>';
        //结束标签
        //打印看看是否有值
        header( 'pragma:public' );
        //缓存问题，开启之后为了防止有缓存
        header( 'Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $fileName . '.xls"' );
        //类型为xls表格的形式是。
        header( "Content-Disposition:attachment;filename=$fileName.xls" );
        //弹出下载窗口，下载文件的名字就为我们前面传过来的名字格式为表格
        echo '<html>'."<meta charset='UTF-8'>".$data.'</html>';
        //输出我们的内容在xls中我们防止的乱码
    }

}
