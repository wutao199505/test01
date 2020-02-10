<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
   class Worker extends Controller {
     /**
      * 分页方法
      */
       public function  getUser(){
         $page=!empty($_GET['page'])? $_GET['page']:1;//获取分页
         $limit=!empty($_GET['limit'])? $_GET['limit']:5;//默认查询条数
         $start=($page-1)*$limit;//获取开始位置
         $account=!empty($_GET['worekrName'])? $_GET['worekrName']:'';//判断搜索内容是否为空
       
         //搜索内容不为空
         if ($account){
          $result=Db::name('worker')
          ->alias('w') //别名
          ->field('w.id as id,w.name ,w.phone,w.address,w.status,s.name as sname,s.id as sid')
          ->join('service s','s.id=w.sid')
          ->limit($start,$limit)
          ->order('w.id esc')
          ->where('w.phone','like',"%".$account."%")
          ->select();
          $count=Db::name('worker')->where('phone','like',"%".$account."%")->count();//统计外派员工数量
        }else{
          $result=Db::name('worker')
          ->alias('w')
          ->field('w.id as id,w.name ,w.phone,w.address,w.status,s.name as sname,s.id as sid')
          ->join('service s','s.id=w.sid')
          ->limit($start,$limit)
          ->order('w.id esc')
          ->select();
          $count=Db::name('worker')->count();//统计外派员工数量
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
            $result = Db::name('worker')->where('id',$user)->find();
            if($result['status']==2){
              echo json_encode(array("code"=>10001,"msg"=>'该员工在工作中，不能删除',));
            }else{
              Db::name('worker')->where('id','=',$user)->delete();
              echo json_encode(array("code"=>10000));
            }
          }
          
        }

        /**
         * 添加外派员工
         */
        public function addUser(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $name=$info['name'];
            $phone=$info['phone'];
            $sid=$info['sid'];
            $address=$info['address'];
            $result = Db::name('worker')->where('phone',$phone)->find();
            if($result){
              echo json_encode(array('code'=>10001,'msg'=>'电话号码已存在'));
            }else{
              $data=[
                'name'=>$name,
                'phone'=>$phone,
                'sid'=>$sid,
                'address'=>$address,
              ];
              Db::name('worker')->insert($data);
              echo json_encode(array("code"=>10000));
            }



          }
          
        }
        /**
         * 修改员工
         */
        public function editUser(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          if ($method=='POST'){
            $name=$info['name'];
            $phone=$info['phone'];
            $address=$info['address'];
            $status=$info['status'];
            $id=$info['id'];
            $sid=$info['sid'];
             //判断电话号码是否存在
             $result = Db::name('worker')->where('id','<>',$id)->where('phone',$phone)->find();
             if($result){
              echo json_encode(array('code'=>10001,'msg'=>'电话号码已存在'));
             }else{
              $data=[
                'name'=>$name,
                'phone'=>$phone,
                'address'=>$address,
                'status'=>$status,
                'sid'=>$sid,
              ];
                Db::name('worker')->where('id','=',$id)->update($data); 
                echo json_encode(array("code"=>10000));           
            }          
          }
          
        }
        /**
         * 获取所有的服务项目
         */
        public function getService(){
           $result=Db::name('service')->select();
           echo json_encode(array("code"=>10000,"data"=>$result));
        }
   }
?>