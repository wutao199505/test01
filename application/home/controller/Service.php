<?php
namespace app\home\controller;
use think\Controller;
use think\Db;


require_once EXTEND_PATH.'libs/Core_Page.class.php';

use think\Paginator;
// require "E:\php\WWW\hx1903\Ym-master\application\home\libs/Core_Page.class.php";

class Service extends Controller
{
    
    public function service()
    {   $navList =  Db::name('kind')->select();
        // 模板变量赋值
        $this->assign('navList', $navList);
        // 渲染模板输出
        return $this->fetch();
    	return view('service');
    }
    public function getService(){  
            $_POST=json_decode(file_get_contents("php://input"),1);
            $id=!empty($_POST)?$_POST['id']:'';      
        if($id){
             $result= Db::name('service')->order('id asc')->where('kid',$id)->select();
         }else{
            $result= Db::name('service')->order('id asc')->select();
         }
         if($result){
             return json_encode(array("code"=>10000,"msg"=>'获取数据成功',"allData"=>$result));
         }else{
             return json_encode(array("code"=>10001,"msg"=>'暂无数据'));
         }
           
    }

}
?>