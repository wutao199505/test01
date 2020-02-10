<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
   class Staff extends Controller {
     /**
      * 分页方法
      */
       public function  getUser(){
         $page=!empty($_GET['page'])? $_GET['page']:1;//获取分页
         $limit=!empty($_GET['limit'])? $_GET['limit']:5;//默认查询条数
         $start=($page-1)*$limit;//获取开始位置
         $account=!empty($_GET['id'])? $_GET['id']:'';//判断搜索内容是否为空
         $result=Db::name('admin')->alias('a')->field('a.id,a.account,a.name,a.status,a.roleid,b.rolename')->join('role b','a.roleid=b.id')->limit($start,$limit)->order('id esc')->select();//查询用户
         $count=Db::name('admin')->count();//统计用户数量
         //搜索内容不为空
         if ($account){
          $result=Db::name('admin')->alias('a')->field('a.id,a.account,a.name,a.status,a.roleid,b.rolename')->join('role b','a.roleid=b.id')->where('account','like',"%".$account."%")->limit($start,$limit)->order('id esc')->select();//查询用户
          $count=Db::name('admin')->where('account','like',"%".$account."%")->count();//统计用户数量
        }
       
         echo  json_encode(array("code"=>0,"msg"=>"数据获取成功","data"=>$result,"count"=>$count));//输出查询结果
         
       }
       /**
        * 删除方法
        */
        public function delUser(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $user=$info['id'];
            Db::name('admin')->where('id','=',$user)->delete();
            $code=10000;
          }
          echo json_encode(array("code"=>$code));
        }
        /**
         * 锁定用户
         */
        public function lockUser(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $user=$info['id'];
            Db::name('admin')->where('id','=',$user)->update(['status'=>2]);
            $code=10000;
          }
          echo json_encode(array("code"=>$code));
        }
        /**
         * 员工激活
         */
        public function useUser(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $user=$info['id'];
            Db::name('admin')->where('id','=',$user)->update(['status'=>1]);
            $code=10000;
          }
          echo json_encode(array("code"=>$code));
        }
        /**
         * 添加员工
         */
        public function addUser(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $user=$info['account'];
            $nickName=$info['nickName'];
            $role=$info['role'];
            $pwd=md5('admin123456');
            $data=[
              'account'=>$user,
              'name'=>$nickName,
              'roleid'=>$role,
              'pwd'=>$pwd,
            ];
            //查重
            $result=Db::name('admin')->where('account','=',$user)->find();
            if (!$result){
              Db::name('admin')->insert($data);
              $code=10000;
            }else{
              $code=100001;
            }
            
          }
          echo json_encode(array("code"=>$code));
        }
        /**
         * 修改员工
         */
        public function editUser(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $user=$info['account'];
            $name=$info['name'];
            $role=$info['role'];
            $id=$info['id'];
            $data=[
              'account'=>$user,
              'name'=>$name,
              'roleid'=>$role
            ];
            //查重
            //判断角色是否存在
            $result=Db::name('admin')->where('account','=',$user)->where('id','<>',$id)->find();
            if(!$result){
              Db::name('admin')->where('id','=',$id)->update($data);
              $code=10000;
            }else {
              $code=10001;
            }
          }
          echo json_encode(array("code"=>$code));
        }
        /**
         * 获取角色
         */
        public function getRole(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $result=Db::name('role')->select();
            if ($result){
              $code=10000;
              $data=$result;
            }else{
              $code=10001;
            }
          }
          echo json_encode(array("code"=>$code,"data"=>$data));
        }
   }
?>