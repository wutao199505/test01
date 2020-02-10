<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Unpay extends Controller 
{   


    //获取订单
    public function getOrder(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 6;

        $page = !empty($_GET['page']) ? $_GET['page'] : 1;

        $account = !empty($_GET['account']) ? $_GET['account'] : '';

        $start = ($page-1)*$limit;

        if ($account) {
            $result = Db::name('order')->limit($start,$limit)
            ->where('status','=',1)->where('ordernum','like','%'.$account.'%')->select();
 
            $count = Db::name('order')->where('status','=',1)
            ->where('ordernum','like','%'.$account.'%')->count();
        }else{
            $result = Db::name('order')->limit($start,$limit)->where('status','=',1)->select();
            $count = Db::name('order')->where('status','=',1)->count();
        }
   
        echo json_encode(array('code'=>0,'msg'=>'获取订单数据成功','count'=>$count,'data'=>$result));

    }

    //查看订单详情
    public function lookOrder(){
        $id = !empty($_POST['id']) ? $_POST['id'] : '';
        $result = Db::name('service')->where('id','=',$id)->select();
        if($result){
            echo json_encode(array('code'=>10000,'msg'=>'获取服务表信息成功','data'=>$result));
        }else{
            echo json_encode(array('code'=>10001,'msg'=>'获取服务表信息失败'));
        }

    }



    

}