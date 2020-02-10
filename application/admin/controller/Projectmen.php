<?php
    namespace app\admin\controller;
    use think\Controller;
    use think\Db;
    class ProjectMen extends Controller{
        /**
         * 获取项目信息
         */
        public function getProject(){
                $page=!empty($_GET['page'])? $_GET['page']:1;//获取分页
                $limit=!empty($_GET['limit'])? $_GET['limit']:5;//默认查询条数
                $start=($page-1)*$limit;//获取开始位置
                $account=!empty($_GET['id'])? $_GET['id']:'';//判断搜索内容是否为空
                $result=Db::name('service')
                ->alias('a')
                ->field('a.id,a.image,a.name as nameMen,a.describe,a.price,a.status,a.issuetime,a.addtime,a.outtime,a.company,b.name')
                ->join('kind b','a.kid=b.id')
                ->limit($start,$limit)
                ->order('id desc')->select();//查询用户
                $count=Db::name('service')->count();//统计用户数量
                //搜索内容不为空
                if ($account){
                 $result=Db::name('service')
                 ->alias('a')
                 ->field('a.id,a.image,a.name as nameMen,a.describe,a.price,a.status,a.issuetime,a.addtime,a.outtime,a.company,b.name')
                 ->join('kind b','a.kid=b.id')
                 ->where('a.name','like',"%".$account."%")
                 ->limit($start,$limit)
                 ->order('id desc')
                 ->select();//查询用户
                 $count=Db::name('service')->where('name','like',"%".$account."%")->count();//统计用户数量
               }
              
                echo  json_encode(array("code"=>0,"msg"=>"数据获取成功","data"=>$result,"count"=>$count));//输出查询结果
                
        }
        /**
         * 上架服务
         */
        public function upService(){
          $request = request();//引入request函数
          $info = $request->param();//获取http数据集合  
          $method =$request->method();//获取提交方法POST或GET
          $date=@date('Y-m-d H:i:s');
          if ($method=='POST'){
            $user=$info['id'];
            Db::name('service')->where('id','=',$user)->update(['status'=>1,'addtime'=>$date]);
            $code=10000;
          }
          echo json_encode(array("code"=>$code));
        }
        /**
         * 下架
         */
        public function outService(){
            $request = request();//引入request函数
            $info = $request->param();//获取http数据集合  
            $method =$request->method();//获取提交方法POST或GET
            $date=@date('Y-m-d H:i:s');
            if ($method=='POST'){
              $user=$info['id'];
              $result=Db::name('worker')->where('sid','=',$user)->where('status','=',2)->find();
              if ($result){
                  $code=10001;
              }else {
                  Db::name('service')->where('id','=',$user)->update(['status'=>2,'outtime'=>$date]);
                  $code=10000;
              }
              
            }
            echo json_encode(array("code"=>$code));
          }
          /**
           * 删除项目
           */
          public function delService(){
            $request = request();//引入request函数
            $info = $request->param();//获取http数据集合  
            $method =$request->method();//获取提交方法POST或GET
            if ($method=='POST'){
              $user=$info['id'];
              $result=Db::name('worker')->where('sid','=',$user)->find();
              if ($result){
                  $code=10001;
              }else {
                  Db::name('service')->where('id','=',$user)->delete();
                  $code=10000;
              }
              
            }
            echo json_encode(array("code"=>$code));
          }
          /**
           * 项目图片多图获取
           */
          public function getImgs()
          {
            $sid=input('sid');
            $result=Db::name('image')->where('sid','=',$sid)->select();
            if ($result){
              echo json_encode(array("code"=>10000,"data"=>$result));
            }else {
            
              echo json_encode(array("code"=>10001));
              
            }
          }
          /**
           * 筛选
           */
          public function getStatus()
          {
            $status=input('status');
            $page=!empty($_GET['page'])? $_GET['page']:1;//获取分页
            $limit=!empty($_GET['limit'])? $_GET['limit']:5;//默认查询条数
            $start=($page-1)*$limit;//获取开始位置
            $account=!empty($_GET['id'])? $_GET['id']:'';//判断搜索内容是否为空
            $result=Db::name('service')
                ->alias('a')
                ->where('a.status','=',$status)
                ->field('a.id,a.image,a.name as nameMen,a.describe,a.price,a.status,a.issuetime,a.addtime,a.outtime,a.company,b.name')
                ->join('kind b','a.kid=b.id')
                ->limit($start,$limit)
                ->order('id desc')->select();//查询用户
            $count=Db::name('service')->where('status','=',$status)->count();//统计用户数量
            echo  json_encode(array("code"=>0,"msg"=>"数据获取成功","data"=>$result,"count"=>$count));//输出查询结果

          }
    }
?>