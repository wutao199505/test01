<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class User extends Controller 
{   


    //获取前台所有用户
    public function getAllUser(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 6;

        $page = !empty($_GET['page']) ? $_GET['page'] : 1;

        $account = !empty($_GET['account']) ? $_GET['account'] : '';

        $start = ($page-1)*$limit;

        if ($account) {
            $result = Db::name('user')->where('account','like','%'.$account.'%')->limit($start,$limit)->select();

            $count = Db::name('user')->where('account','like','%'.$account.'%')->count();
        }else{
            $result = Db::name('user')->limit($start,$limit)->select();

            $count = Db::name('user')->count();
        }
   
        echo json_encode(array('code'=>0,'msg'=>'获取用户数据成功','count'=>$count,'data'=>$result));

    }

    //重置前台用户密码
    public function resetUser(){
        $userid = !empty($_POST['id']) ? $_POST['id'] : 0;
        if ($userid) {
            $result = Db::name('user')->where('id', $userid)->update(['pwd' => md5('123456')]);
            
            echo json_encode(array('msg'=>'重置密码成功'));
        }

    }

    //解锁前台用户
    public function useUser(){
        $userid = !empty($_POST['id']) ? $_POST['id'] : 0;
        $status = !empty($_POST['status']) ? $_POST['status'] : '';
        if ($userid && $status) {
            $result = Db::name('user')->where('id', $userid)->update(['status' => $status]);
            
            echo json_encode(array('msg'=>'解锁成功'));
        }

    }

    //锁定前台用户
    public function lockUser(){
        $userid = !empty($_POST['id']) ? $_POST['id'] : 0;
        $status = !empty($_POST['status']) ? $_POST['status'] : '';
        if ($userid && $status) {
            $result = Db::name('user')->where('id', $userid)->update(['status' => $status]);
            
            echo json_encode(array('msg'=>'锁定成功'));
        }

    }

    

}