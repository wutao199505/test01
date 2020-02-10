<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use app\admin\controller\Base;
use think\View;


class Index extends Base 
{   
    
    public function __construct(){
        parent::__construct();//执行上一级构造函数
      
    }
 

    //引入后台主页面
    public function index()
    {
    	return view("index");
    }

    //引入用户管理页面
    public function user()
    {
        return view("user");
    }
    //引入员工管理页面
    public function staff()
    {
        return view("staff");
    }
    //引入外派人员管理页面
    public function worker()
    {
        return view("worker");
    }
    //引入角色管理页面
    public function role()
    {
        return view("role");
    }
    //引入项目管理页面
    public function projectMen()
    {
        return view("projectMen");
    }
    //引入项目分类管理
    public function kind()
    {
        return view("kind");
    }
    //引入未付款订单管理页面
    public function unPay()
    {
        return view("unPay");
    }
    //引入已付款订单管理页面
    public function pay(){
        return view("pay");
    }

    //引入应聘管理页面
    public function offer()
    {
        return view("offer");
    }

    //引入活动管理页面
    public function activity()
    {
        return view("activity");
    }
    //引入报表
    public function chart()
    {
        return view("chart");
    }
    // //引入聊客服
    // public function chat()
    // {
    //     return view("chat");
    // }

    //引入项目发布页面
    public function projectRelease(){
        return view("projectRelease");
    }
    //售后
    public function aftersale(){
        return view('aftersale');
    }
    //引入聊天界面
    public function chat()
    {
        $view = new View();
        $sendid=Session::get('adminName');
        $result=Db::name('admin')->field('id')->where('account','=',$sendid)->find();
        $data['sendid'] =$result['id']; 
        $view->assign('data',$data);
        return $view->fetch('index/chat');
    }

    //获取后台登陆者信息和菜单
    public function getAdmin(){
        $account = Session::get('adminName');

        if ($account) {
            $sql = "select ym_admin.name,ym_role.rolename,ym_menu.menuname,ym_menu.id,ym_menu.url,ym_menu.parentid from ym_admin,ym_role,ym_menu,ym_link where ym_admin.account = '$account' and ym_admin.roleid = ym_role.id and ym_role.id = ym_link.roleid and ym_menu.id = ym_link.menuid";

            $adminInfo = Db::query($sql);

            //重构数据
            foreach ($adminInfo as $key => $value) {
                if($value['parentid']==0){
                    $ary[$value['id']]['mainmenu']=$value['menuname'];
                    $ary[$value['id']]['url']=$value['url'];
                }
                else{
                    $ary[$value['parentid']]['sonMenu'][]=['sonMenu'=>$value['menuname'],'sonurl'=>$value['url']]; 
                }
            }

            if(!empty($adminInfo)){
                echo json_encode(array("code"=>10000,"msg"=>'获取后台在线用户信息成功',"data"=>$ary,"name"=>$adminInfo[0]['name'],"rolename"=>$adminInfo[0]['rolename']));
            }else{
                echo json_encode(array("code"=>10001,"msg"=>'获取后台在线用户信息失败'));   
            }           

        }
    }

    //用户注销
    public function exitUser(){
        Session::delete('adminName');
        echo json_encode(array("code"=>10000,"msg"=>'已退出登录'));
    }

}
?>
