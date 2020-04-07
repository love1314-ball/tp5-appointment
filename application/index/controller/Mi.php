<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;
use think\Request;
// 我的个人中心

class Mi extends IndexBase
 {

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

    //订单状态那一页

    public function status()
 {
        $where['userid'] = 4;
        $where['status'] = input( 'status' );
        $all = Db::name( 'order' )->order( 'id' )->group('order')->where( $where )->select();
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
        // dump( $all );
        // exit;

        $this->assign( 'all', $all );
        return $this->fetch( 'status' );
    }

    //详细页面

    public function detail()
 {
        $order = input( 'order' );
        // $er = $this->qrcode( $order );
        // dump( $er );
        // exit;
        $all = Db::name( 'order' )->where( 'order', $order )->select();
        $scenic = Db::name( 'scenic' )->where( 'id', $all[0]['scenicid'] )->find();
        $time = Db::name( 'times' )->where( 'id', $all[0]['timeid'] )->find();

        foreach ( $all as $key => $value ) {
            $all[$key]['name'] = $scenic['name'];
            //景点的名字
            $all[$key]['img'] = $scenic['img'];
            //景点的图片
            $all[$key]['brgin'] = $value['addtimeymd'].' '.$time['brgin'];
            //开始时间
            $all[$key]['finish'] = $time['finish'];
            //结束时间
            $all[$key]['addtime'] = date( 'Y-m-d H:i:s', $value['addtime'] );
            //订单插入时间
        }

        $this->assign( 'all', $all );

        return $this->fetch( 'detail' );

    }

    //生成二维码

    //下载生成的二维码-引用方法1

    public function qrcode( $order ) {

        $request = Request::instance();
        header( 'Content-Type: text/html;charset=utf-8' );

        //引入二维码生成插件
        vendor( 'phpqrcode.phpqrcode' );

        // 生成的二维码所在目录+文件名
        $path = 'static/QRcode/';
        //生成的二维码所在目录
        if ( !file_exists( $path ) ) {

            mkdir( $path, 0700, true );
        }
        $time = time().'.png';
        //生成的二维码文件名
        $fileName = $path.$time;
        //1.拼装生成的二维码文件路径

        $data = 'http://www.tp5-appointment.com/index/mi/destroy?order="$order"';
        //2.生成二维码的数据( 扫码显示该数据 )

        $level = 'L';
        //3.纠错级别：L、M、Q、H

        $size = 10;
        //4.点的大小：1到10, 用于手机端4就可以了

        ob_end_clean();
        //清空缓冲区
        \QRcode::png( $data, $fileName, $level, $size );
        //生成二维码
        //文件名转码
        $file_name = iconv( 'utf-8', 'gb2312', $time );
        $file_path = $request->domain().'/'.$fileName;
        //插入数据库的字段
        return $file_path;
        // dump( $file_path );
        // exit;
        //获取下载文件的大小
        // $file_size = filesize( $file_path );
        //
        // $file_temp = fopen ( $file_path, 'r' );
        //返回的文件
        // header( 'Content-type:application/octet-stream' );
        // //按照字节大小返回
        // header( 'Accept-Ranges:bytes' );
        // //返回文件大小
        // header( 'Accept-Length:'.$file_size );
        // //这里客户端的弹出对话框
        // header( 'Content-Disposition:attachment;filename='.$time );
        // echo fread ( $file_temp, filesize ( $file_path ) );
        // fclose ( $file_temp );
        // exit ();
    }

    //销毁

    public function destroy()
 {
        $order = input( 'order' );
        $user_id = 3;
        $all = Db::name( 'order' )->where( 'order', $order )->select();
        if ( $all ) {
            if ( $user_id == 3 ) {

                foreach ($all as $key => $value) {
                    if ($all[$key]['status'] == 1) {
                        echo"<script>alert('票已销毁不可以进入');	</script>";exit;
                    }
                }

                foreach ($all as $key => $value) {
                    $data['status'] = 1;
                    $up = Db::name('order')->where('id',$all[$key]['id'])->update($data);
                }   
                if ($up) {
                    echo"<script>alert('可进入');	</script>";

                }else {
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
