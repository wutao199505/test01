<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Image;


class Myself extends Base
{
   public function __construct(){
      parent::__construct();//执行上一级构造函数
    
  }
    public function myself(){ 
        // $list = Db::name('order')->order("id asc")->paginate(4);
		// // 把分页数据赋值给模板变量list
		// $this->assign('nameArr', $list);  
    	return view('myself');
    }
    public function getInfo(){  
        $_POST=json_decode(file_get_contents("php://input"),1);
        $id=!empty($_POST)?$_POST['id']:'';   

        if($id){
            $UserName =Session::get('UserName');
             $result= Db::name('user')->where('account', $UserName)->select();
             if($result){
                echo json_encode(array("code"=>10000,"msg"=>'获取成功',"data"=> $result,));
             }else{
                echo json_encode(array("code"=>10001,"msg"=>'获取失败'));
             }
             
         }else{
            echo json_encode(array("code"=>10002,"msg"=>'无参数'));
         }
        
    }
    //文件上传方法
    public function upload(){      
      // 获取上传文件 例如上传了001.jpg
        $file = request()->file('filename');
        if($file){
         // 设置大小和类型      移动到框架应用根目录/public/uploads/ 目录下
         $info = $file->validate(['size'=>20*1024*1024,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
         if($info){
            // 成功上传后 获取上传信息
             $ImgName= $info->getSaveName();
             $imgPath='./uploads/'.$ImgName;
             $image = \think\Image::open($imgPath);
           
             // 获取宽度、高度
             $width = $image->width();
             $height = $image->height();
             $type = $image->type();
             $type = $type=='jpeg' ? 'jpg' : $type;
             $thumbImg=str_replace(".".$type,"_thumb.".$type,$imgPath);
             $imgPaths='uploads/'.$ImgName;
             $thumbImgs=str_replace(".".$type,"_thumb.".$type,$imgPaths);
             $url = '/hx1903/Ym-master/public/'.$thumbImgs;
             $data=[
               'head'=>$url,
               ];
               $UserName =Session::get('UserName');
           $result = Db::name('user')->where('account', $UserName)->update($data); 
             $image->save($thumbImg);
             echo json_encode(array("code"=>10000,"msg"=>'修改成功',"url"=>$url)) ;

         }else{
             // 上传失败获取错误信息
             echo $file->getError();
         }
     }
    }
    public function changePhone(){  
        $_POST=json_decode(file_get_contents("php://input"),1);
        $phone=!empty($_POST)?$_POST['phone']:'';   
        if($phone){
            $data=[
                'phone'=>$phone,
                ];
            $UserName =Session::get('UserName');
            $result = Db::name('user')->where('account', $UserName)->update($data); 
             if($result){
                echo json_encode(array("code"=>10000,"msg"=>'修改成功'));
             }else{
                echo json_encode(array("code"=>10001,"msg"=>'修改失败'));
             }            
         }else{
            echo json_encode(array("code"=>10002,"msg"=>'无参数'));
         }       
    }
    public function changAddress(){  
        $_POST=json_decode(file_get_contents("php://input"),1);
        $address=!empty($_POST)?$_POST['address']:'';   
        if($address){
            $data=[
                'address'=>$address,
                ];
            $UserName =Session::get('UserName');
            $result = Db::name('user')->where('account', $UserName)->update($data); 
             if($result){
                echo json_encode(array("code"=>10000,"msg"=>'修改成功'));
             }else{
                echo json_encode(array("code"=>10001,"msg"=>'修改失败'));
             }            
         }else{
            echo json_encode(array("code"=>10002,"msg"=>'无参数'));
         }       
    }
    public function changPwd(){  
        $UserName =Session::get('UserName');
        $_POST=json_decode(file_get_contents("php://input"),1);
        $pwd=!empty($_POST)?$_POST['pwd']:'';   
        $oldPwd=!empty($_POST)?$_POST['oldPwd']:'';   
        $result=Db::name('user')->where('account','=',$UserName)->select();//根据账号查找           
        foreach($result as $key=>$value){                   
                $myPwd = $value['pwd'];
        }
        if(md5($oldPwd)==$myPwd){
            $data=[
                'pwd'=>md5($pwd),
                ];
            $result = Db::name('user')->where('account', $UserName)->update($data); 
             if($result){
                echo json_encode(array("code"=>10000,"msg"=>'修改成功'));
             }else{
                echo json_encode(array("code"=>10001,"msg"=>'修改失败'));
             } 
        }else{
            echo json_encode(array("code"=>10002,"msg"=>'原密码输入错误'));
        }     
    }

    //获取订单
    public function getOrder(){
        $user =Session::get('UserName');
        //uid通过电话号码找到对饮user的id
        $_POST=json_decode(file_get_contents("php://input"),1);
        $status=!empty($_POST)?$_POST['status']:'1';  
        $uid= Db::name('user')->where('account',$user)->find();
        $uid=$uid['id'];
        if($status==1){
            $result = Db::name('order')->where('uid','=',$uid)->where('status','=',1)->select();
        }else{
            $result = Db::name('order')->where('uid','=',$uid)->where('status','<>',1)->select();
        }
         echo json_encode(array('code'=>0,'msg'=>'获取订单数据成功','data'=>$result));
       
    }   
}
