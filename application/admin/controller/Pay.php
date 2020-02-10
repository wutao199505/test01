<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Pay extends Controller 
{   
 

    //获取订单
    public function getOrder(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 6;
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $account = !empty($_GET['account']) ? $_GET['account'] : '';
        $start = ($page-1)*$limit;
        if ($account) {
            $result = Db::name('order')->limit($start,$limit)
            ->where('status','<>',1)->where('ordernum','like','%'.$account.'%')->select();
 
            $count = Db::name('order')->where('status','<>',1)
            ->where('ordernum','like','%'.$account.'%')->count();
        }else{
            $result = Db::name('order')->limit($start,$limit)->where('status','<>',1)->select();
            $count = Db::name('order')->where('status','<>',1)->count();
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
    //获取能被安排的员工
    public function getWorder(){
        $sid = !empty($_POST['sid']) ? $_POST['sid'] : '';
        $result = Db::name('worker')->where('sid','=',$sid)->where('status','=',1)->select();
        if($result){
            echo json_encode(array('code'=>10000,'msg'=>'获取服务表信息成功','data'=>$result));
        }else{
            echo json_encode(array('code'=>10001,'msg'=>'获取服务表信息失败'));
        }

    }

        //确定外派员工
        public function sureWorker(){
            $id = !empty($_POST['id']) ? $_POST['id'] : '';
            $workerTel = !empty($_POST['workerTel']) ? $_POST['workerTel'] : '';
            $wname = !empty($_POST['wname']) ? $_POST['wname'] : '';
            $data=[
                'wname'=>$wname,
                'workerTel'=>$workerTel,
                'status'=>3,
              ];
            $result = Db::name('order')->where('id',$id)->update($data);
            if($result){
                echo json_encode(array('code'=>10000,'msg'=>'派单成功'));
            }else{
                echo json_encode(array('code'=>10001,'msg'=>'派单失败'));
            }
    
    
        }
        //确定完成订单
        public function sure(){
            $status = !empty($_POST['status']) ? $_POST['status'] : '';
            $id = !empty($_POST['id']) ? $_POST['id'] : '';
            $data=[
                'status'=>$status,
                ];
            $result =  Db::name('order')->where('id',$id)->update($data); 
            if($result){
                echo json_encode(array('code'=>10000,'msg'=>'订单已完成'));
            }else{
                echo json_encode(array('code'=>10001,'msg'=>'确认完成订单失败'));
            } 
        }
        //确定取消订单
        public function cancel(){
            $status = !empty($_POST['status']) ? $_POST['status'] : '';
            $content = !empty($_POST['content']) ? $_POST['content'] : '';
            $id = !empty($_POST['id']) ? $_POST['id'] : '';
            $data=[
                'status'=>$status,
                'content'=>$content,
                ];
            $result = Db::name('order')->where('id',$id)->update($data);  
            if($result){
                echo json_encode(array('code'=>10000,'msg'=>'取消订单成功'));
            }else{
                echo json_encode(array('code'=>10001,'msg'=>'取消订单失败'));
            } 
        }        


    

}