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

    // 预约/ 订单查看
    public function index()
 {
        $order = input( 'order' );
        $phone = input( 'phone' );
        $user_name = input( 'user_name' );
        $status = 0;
        if ( $order ) {
            $where['order'] = $order;
            $status = 1;
        }
        if ( $phone ) {
            $where['phone'] = $phone;
            $status = 1;
        }
        if ( $user_name ) {
            $where['user_name'] = $user_name;
            $status = 1;
        }
        if ( $status == 1 ) {
            $all = Db::name( 'order' )->where( $where )->order( 'addtimeymd desc' )->group( 'order' )->select();
        } else {
            $all = Db::name( 'order' )->order( 'addtimeymd desc' )->group( 'order' )->select();
        }
        foreach ( $all as $k => $v ) {
            $status = Db::name( 'times' )->where( 'id', $v['timeid'] )->find();
            $all[$k]['status'] = $status['status'];
            $all[$k]['identity'] =  substr_replace( $v['identity'], '****', 5, 9 );
        }
        $this->assign( 'all', $all );
        return $this->fetch( 'index' );
    }

    //预约/ 订单查看 / 删除
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

    //预约/ 订单查看 / 查看

    public function order() {
        $order = input( 'order' );
        $all = Db::name( 'order' )->where( 'order', $order )->select();
        $timeid = $all[0]['timeid'];
        $time = Db::name( 'times' )->where( 'id', $timeid )->find();
        $scenic = Db::name( 'scenic' )->where( 'id', $all[0]['scenicid'] )->find();
        foreach ( $all as $key => $value ) {
            $all[$key]['identity'] = substr_replace( $value['identity'], '****', 5, 9 );
            $all[$key]['addtime'] = date( 'Y-m-d H:i:s', $value['addtime'] );
            $all[$key]['brgin'] = Db::name('times')->where('id' , $all[$key]['timeid'])->value('brgin');
            $all[$key]['finish'] = Db::name('times')->where('id' , $all[$key]['timeid'])->value('finish');
            $all[$key]['name'] = $scenic['name'];
            $all[$key]['img'] = $scenic['img'];
        }
        $this->assign( 'all', $all );
        return $this->fetch( 'order' );
    }

    //预约/ 订单查看 / 导出内容

    public function export()
 {
        $fileName = '订单表' . date( '_YmdHis' );
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
