<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
    class Kind extends Controller{
        /**
         * 获取项目种类
         */
        public function getKind(){
            $page=!empty($_GET['page'])? $_GET['page']:1;//获取分页
            $limit=!empty($_GET['limit'])? $_GET['limit']:5;//默认查询条数
            $start=($page-1)*$limit;//获取开始位置
            $account=!empty($_GET['id'])? $_GET['id']:'';//判断搜索内容是否为空
            $result=Db::name('kind')->limit($start,$limit)->order('id esc')->select();//查询用户
            $count=Db::name('kind')->count();//统计用户数量
                //搜索内容不为空
                if ($account){
                 $result=Db::name('kind')
                 ->where('name','like',"%".$account."%")
                 ->limit($start,$limit)
                 ->order('id esc')
                 ->select();//查询用户
                 $count=Db::name('kind')->where('name','like',"%".$account."%")->count();//统计用户数量
               }
              
                echo  json_encode(array("code"=>0,"msg"=>"数据获取成功","data"=>$result,"count"=>$count));//输出查询结果
        }
        /**
         * 添加项目种类
         */
        public function addKind(){
            $request = request();//引入request函数
            $info = $request->param();//获取http数据集合  
            $method =$request->method();//获取提交方法POST或GET
            if ($method=='POST'){
              $name=$info['account'];
              $percent=$info['nickName'];
              $data=[
                  'name'=>$name,
                  'percent'=>$percent
              ];
              $result=Db::name('kind')->where('name','=',$name)->find();
              if ($result){
                $code=10001;
              }else{
                Db::name('kind')->insert($data);
                $code=10000;
              }
              
            }
            echo json_encode(array("code"=>$code));
        }
        /**
         * 删除种类
         */
        public function delKind(){
            $request = request();//引入request函数
            $info = $request->param();//获取http数据集合  
            $method =$request->method();//获取提交方法POST或GET
            if ($method=='POST'){
              $id=$info['id'];
              $result=Db::name('service')->where('kid','=',$id)->find();
              if ($result){
                $code=10001;
              }else{
                Db::name('kind')->where('id','=',$id)->delete();
                $code=10000;
              }
              
            }
            echo json_encode(array("code"=>$code));
        }
        /**
         * 修改种类
         */
        public function editKind(){
            $request = request();//引入request函数
            $info = $request->param();//获取http数据集合  
            $method =$request->method();//获取提交方法POST或GET
            if ($method=='POST'){
              $name=$info['account'];
              $percent=$info['name'];
              $id=$info['id'];
              $data=[
                  'name'=>$name,
                  'percent'=>$percent
              ];
              $result=Db::name('kind')->where('id','<>',$id)->where('name','=',$name)->find();
              if (!$result){
                Db::name('kind')->where('id','=',$id)->update($data);
                $code=10000;
              }else{
                $code=10001;
              }
              
            }
            echo json_encode(array("code"=>$code));
        }
    }
?>