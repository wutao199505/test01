<?php
namespace app\home\controller;
use think\Controller;
use think\Db;




use think\Paginator;


class Activity extends Controller
{
    
    public function activity()
    {   $navList =  Db::name('kind')->select();
        // 模板变量赋值
        $this->assign('navList', $navList);
        // 渲染模板输出
        return $this->fetch();
    	return view('activity');
    }
    public function getService(){  
            $_POST=json_decode(file_get_contents("php://input"),1);
            $id=!empty($_POST)?$_POST['id']:''; 
            $time=Date("Y-m-d h:i:s");     
        if($id){
             $result= Db::name('activity')->alias('a')->join('__SERVICE__ s','a.sid = s.id')->order('a.id asc')->where('kid',$id)->where('endtime','>',$time)->select();
         }else{
            $result= Db::name('activity')->alias('a')->join('__SERVICE__ s','a.sid = s.id')->order('a.id asc')->where('endtime','>',$time)->select();
         }
         if($result){
             return json_encode(array("code"=>10000,"msg"=>'获取数据成功',"allData"=>$result));
         }else{
             return json_encode(array("code"=>10001,"msg"=>'暂无数据'));
         }
           
    }

}
?>