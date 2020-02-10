<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Session;

class Details extends Controller
{
    public function details(){
        return view('details');
    }
    //获取服务信息
    public function getInfo(){
    	// var_dump($_POST["page"]);
    	 $_POST=json_decode(file_get_contents("php://input"),1);
    	//服务id
    	$id=$_POST['id'];
       if(!is_numeric( $id )) {
       $newId= str_replace("ac",'',$id);
        // 所有数据
        $result=Db::name('activity')->alias('a')->join('__SERVICE__ s','a.sid = s.id')->where('a.id',$newId)->select();
        $result['type']="2";
       }else{
        // 所有数据
        $result=Db::name('service')->where('id',$id)->select();
       }
    	
    	if($result){
    		echo json_encode(array('code'=>10000,'msg'=>'获取数据成功','data'=>$result)); 
    	}else{
    		echo json_encode(array('code'=>10001,'msg'=>'获取数据失败'));
    	}
	}
	//获取最新八条订单信息
	  public function getNewOrder(){
    	// var_dump($_POST["page"]);
    	 $_POST=json_decode(file_get_contents("php://input"),1);
    	//服务id
    	$id=$_POST['id'];
    	// 所有数据
    	$result=Db::name('order')->where('sid',$id)->limit(0,8)->select();

    	if($result){
    		echo json_encode(array('code'=>10000,'msg'=>'获取数据成功','data'=>$result)); 
    	}else{
    		echo json_encode(array('code'=>10001,'msg'=>'获取数据失败'));
    	}
	}
	//获取详情图片
	  public function getDetails(){
    	// var_dump($_POST["page"]);
    	 $_POST=json_decode(file_get_contents("php://input"),1);
    	//服务id
    	$id=$_POST['id'];
    	// 所有数据
    	$result=Db::name('image')->where('sid',$id)->select();

    	if($result){
    		echo json_encode(array('code'=>10000,'msg'=>'获取数据成功','data'=>$result)); 
    	}else{
    		echo json_encode(array('code'=>10001,'msg'=>'获取数据失败'));
    	}
	}
    public function getRate(){
    	// var_dump($_POST["page"]);
    	//服务id
    	$id=$_POST['id'];

    	// 所有数据
    	$result=Db::name('rate')->where('sid',$id)->select();
    	//一次显示几条
    	$length=3;
    	//总条数
    	$count=ceil(count($result)/$length);
    	//当前页
    	$page=$_POST["page"];
    	//
    	
    	$res=Db::name('rate')->alias('r')->join('__USER__ u','r.sid = u.id')->where('sid',$id)->limit(($page-1)*$length,$length)->select();
    	if($res){
    		return array('code'=>10000,'msg'=>'获取数据成功','count'=>$count,'data'=>$res);
    	}else{
    		return array('code'=>10001,'msg'=>'获取数据失败');
		}
	}
    public function order(){
        //session存的电话号码
        $user=Session::get('UserName');
        //status默认1
    
        //type=1为普通类型商品直接生成订单
        //type=2为活动商品，查找结束时间，当前时间不超过endtime的时候才可以生成订单，否则返回消息
        $type=$_POST['type'];

        $number=$_POST['number'];
        $numberP=$_POST['numberP'];
        //查数据库找商品
        $sid=$_POST['sid'];
         $newId= str_replace("ac",'',$sid);
        if($type==1){
             $good =  Db::name('service')->where('id',$sid)->find();
             //单价
             $unitPrice=$good['price'];
        }else{
             $good =  Db::name('activity')->where('id',$newId)->find();
             $unitPrice=$good['money'];
             //找到活动结束时间，判断endtime个当前时间。
             //time()是时间戳，要比较将datatime转时间戳
             //用strtotime()
             if(time()>strtotime($good['endtime'])){ 
                echo json_encode(array("code"=>10004,"msg"=>"不在活动时间内"));
               exit;
             }
        }
         //$sid找到商品的对应的价格$unitPrice*数量*人数。
        $price=$unitPrice*$number*$numberP;
        //通过sid找到对应服务，找到服务名称；再找服务种类的抽成百分比
        $service =  Db::name('service')->where('id',$newId)->find();
         //服务名称
        $servicename=$service['name'];
         //种类id
        $kid = $service['kid'];
        //找到该条种类信息
        $percent=Db::name('kind')->where('id',$kid)->find();
        //抽成
        $percent =$percent ['percent'];
        $money=$price*$percent/100;

        $orderman=$_POST['orderman'];
        $phone=$_POST['phone'];
        $address=$_POST['address'];
        $remarks=$_POST['remarks'];
        $worktime=$_POST['worktime'];
        
       
        //orderNum后台根据时间生成拼接5位随机数
        $orderNum=date("YmdHis");
  
        //随机数
        $code=mt_rand(11111,99999);
        $orderNum=$orderNum.$code;
  
        //uid通过电话号码找到对饮user的id
        $uid= Db::name('user')->where('account',$user)->find();
        $uid=$uid['id']; 

       $data=["sid"=>$newId,"type"=>$type,"price"=>$price,"orderman"=>$orderman,"phone"=>$phone,"address"=>$address,"remarks"=>$remarks,"worktime"=>$worktime,"ordernum"=>$orderNum,"workmannum"=>$numberP,"money"=>$money,'servicename'=>$servicename,'uid'=>$uid];

        $result=Db::name('order')->insert($data);
        if($result){
            echo json_encode(array("code"=>10000,"msg"=>"预约服务成功"));
        }else{
            echo json_encode(array("code"=>10001,"msg"=>"偶欧,发生了未知错误,请重试。"));
        }
    }


}
?>