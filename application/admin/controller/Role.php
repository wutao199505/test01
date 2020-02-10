<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Role extends Controller 
{   


    //获取所有角色
    public function getAllRole(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 6;

        $page = !empty($_GET['page']) ? $_GET['page'] : 1;

        $start = ($page-1)*$limit;

        $result = Db::name('role')->limit($start,$limit)->select();

        $count = Db::name('role')->count();
        
        echo json_encode(array('code'=>0,'msg'=>'获取角色数据成功','count'=>$count,'data'=>$result));

    }

    //删除角色
    public function delRole(){
        $roleid = !empty($_POST['id']) ? $_POST['id'] : 0;
        if ($roleid) {

            $result = Db::name('role')->where('id', $roleid)->delete();
            
            echo json_encode(array('msg'=>'删除成功'));
        }

    }

    //添加角色
    public function addRole(){
        $rolename = !empty($_POST['rolename']) ? $_POST['rolename'] : '';
        $roledescribe = !empty($_POST['roledescribe']) ? $_POST['roledescribe'] : '';
        if ($rolename && $roledescribe) {
            
            //判断角色是否存在
            $result2 = Db::name('role')->where('rolename',$rolename)->find();
            if ($result2) {
                echo json_encode(array('code'=>10001,'msg'=>'角色已存在'));
            }else{

                $data = ['rolename' => $rolename, 'roledescribe' => $roledescribe];

                $result = Db::name('role')->insert($data);
                
                echo json_encode(array('code'=>10000,'msg'=>'添加成功'));
            }
           
        }

    }

    //修改角色
    public function editRole(){
        $rolename = !empty($_POST['rolename']) ? $_POST['rolename'] : '';
        $roledescribe = !empty($_POST['roledescribe']) ? $_POST['roledescribe'] : '';
        $roleid = !empty($_POST['roleid']) ? $_POST['roleid'] : 0;
        if ($rolename && $roledescribe && $roleid) {
            
            //判断角色是否存在
            $result2 = Db::name('role')->where('id','<>',$roleid)->where('rolename',$rolename)->find();
            if ($result2) {
                echo json_encode(array('code'=>10001,'msg'=>'角色已存在'));
            }else{

                $result = Db::name('role')->where('id', $roleid)->update(['rolename' => $rolename,'roledescribe' => $roledescribe]);
                
                echo json_encode(array('code'=>10000,'msg'=>'修改成功'));
            }
           
        }

    }

    //获取角色权限
    public function getPower(){
        $roleid = !empty($_POST['id']) ? $_POST['id'] : 0;
        if ($roleid) {
            //所有菜单
            $result2 = Db::name('menu')->select();
            //角色权限
            $result = Db::name('link')->field('menuid')->where('roleid','=',$roleid)->select();

            $ary = [];
            $arr = [];
            foreach ($result as $key => $value) {
                $arr[] = $value['menuid'];
            }
            foreach ($result2 as $key => $value) {
                // $arrs[$key]=$value;

                $ary[$key] =['id'=>$value['id'],'name'=>$value['menuname'],'pId'=>$value['parentid']];
                if(in_array($value['id'], $arr)){

                    $ary[$key]['checked'] = true;
                    $ary[$key]['open'] = true;
                }
            }
            

           
            echo json_encode(array('msg'=>'获取权限成功','data'=>$ary));

        }
    }

    //修改角色权限
    public function editPower(){
        $roleid = !empty($_POST['roleid']) ? $_POST['roleid'] : 0;
        $idList = !empty($_POST['idList']) ? $_POST['idList'] : [];

        
        //先删除角色所有权限
        $result = Db::name('link')->where('roleid',$roleid)->delete();

        $data = [];
        if ($idList) {
           //更新新的权限
            foreach ($idList as $key => $value) {
                $data[$key] = ['roleid'=>$roleid,'menuid'=>$value];
            }
        }
        
        if ($data) {
            $result2 = Db::name('link')->insertAll($data);
            
        }

        echo json_encode(array('msg'=>'修改成功','code'=>10000));           
        
    }



    
}