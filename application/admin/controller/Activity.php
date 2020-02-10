<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Activity extends Controller 
{   


    //获取所有活动
    public function getAllActivity(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 6;

        $page = !empty($_GET['page']) ? $_GET['page'] : 1;

        $start = ($page-1)*$limit;

        $result = Db::name('activity')->alias('a')->field('a.*,s.name')->join('service s','a.sid = s.id')->limit($start,$limit)->select();

        $count = Db::name('activity')->alias('a')->field('a.*,s.name')->join('service s','a.sid = s.id')->count();
        
        echo json_encode(array('code'=>0,'msg'=>'获取活动数据成功','count'=>$count,'data'=>$result));

    }

    //获取服务项目名和id
    public function getServiceName(){
        $result = Db::name('service')->field('id,name')->select();

        echo json_encode(array('code'=>10000,'msg'=>'获取服务id和名称成功','data'=>$result));
    }


    //添加活动
    public function addActivity(){
        $sid = !empty($_POST['sid']) ? $_POST['sid'] : 0;
        $starttime = !empty($_POST['starttime']) ? $_POST['starttime'] : '';
        $endtime = !empty($_POST['endtime']) ? $_POST['endtime'] : '';
        $money = !empty($_POST['money']) ? $_POST['money'] : '';
        $status = !empty($_POST['status']) ? $_POST['status'] : 0;

        if ($sid && $starttime && $endtime && $money && $status) {
            
            //判断活动是否存在
            $result2 = Db::name('activity')->where('sid',$sid)->find();
            if ($result2) {
                echo json_encode(array('code'=>10001,'msg'=>'该活动已存在'));
            }else{

                $data = ['sid' => $sid, 'starttime' => $starttime, 'endtime' => $endtime, 'money' => $money, 'status' => $status];

                $result = Db::name('activity')->insert($data);
                
                echo json_encode(array('code'=>10000,'msg'=>'添加成功'));
            }
           
        }

    }

    //删除活动
    public function delActivity(){
        $activityid = !empty($_POST['id']) ? $_POST['id'] : 0;
        if ($activityid) {

            $result = Db::name('activity')->where('id', $activityid)->delete();
            
            echo json_encode(array('msg'=>'删除成功'));
        }

    }

    //修改活动
    public function editActivity(){
        $id = !empty($_POST['id']) ? $_POST['id'] : 0;
        $starttime = !empty($_POST['starttime']) ? $_POST['starttime'] : '';
        $endtime = !empty($_POST['endtime']) ? $_POST['endtime'] : '';
        $money = !empty($_POST['money']) ? $_POST['money'] : '';
        if ($id && $starttime && $endtime && $money) {


            $result = Db::name('activity')->where('id', $id)->update(['starttime' => $starttime,'endtime' => $endtime,'money' => $money]);
            
            echo json_encode(array('code'=>10000,'msg'=>'修改成功'));

           
        }

    }

    //活动上 下架
    public function changeStatus(){
        $id = !empty($_POST['id']) ? $_POST['id'] : 0;
        $status = !empty($_POST['status']) ? $_POST['status'] : 0;
        if ($id && $status) {

            $result = Db::name('activity')->where('id', $id)->update(['status' => $status]);
            
            if ($status == 1) {
                echo json_encode(array('code'=>10000,'msg'=>'上架成功'));
            }else{
                echo json_encode(array('code'=>10000,'msg'=>'下架成功'));
            }
           
        }

    }





    
}