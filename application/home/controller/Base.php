<?php 
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Session;
class Base extends Controller{

	public function __construct(){
        $account = Session::get('UserName');
		if(empty($account)){
			//没有登录
			if(!empty($_POST)){
				//ajax请求来的 返回json
				echo json_encode(array("code"=>11000,"msg"=>'账号未登录'));exit;
			}
			else{
				header("refresh:1,url=/hx1903/Ym-master/public");exit;
			}
		}
	}
}

?>