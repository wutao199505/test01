<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
   class projectRelease extends Controller {
   		public function getkind(){
   			$result=Db::name('kind')->select();
   			if($result){
   				echo json_encode(array("code"=>10000,"msg"=>"获取成功","data"=>$result));
   			}
   		}



   		public function release(){
   			// var_dump($_POST);
   			// var_dump($_FILES);
   			$kind=$_POST['kind'];
     		$serviceName=$_POST['serviceName'];
     		$serviceDes=$_POST['serviceDes'];
     		$servicePrice=$_POST['servicePrice'];
     		$company=$_POST['company'];
     		$serviceStatus=$_POST['serviceStatus'];
     		$serviceCont=$_POST['serviceCont'];
     		
     		
     		$res=Db::name('service')->where('name',$serviceName)->find();
     		if($res){
     			echo json_encode(array("code"=>10002,"msg"=>"服务已存在"));
     			exit;
     		}
     		if($serviceStatus==1){
     			//立即上架
     			$time=date("Y-m-s h:i:s");
     		}else{
     			$time='';
     		}
     		$data = ['kid' => $kind, 'name' => $serviceName, 'describe' => $serviceDes,'price'=>$servicePrice, 'status' =>$serviceStatus,'company'=>$company,'addtime' =>$time,'content'=>$serviceCont];
			$result=Db::name('service')->insert($data);
			$userId = Db::name('service')->getLastInsID();
			if($result){
				//传图片数据
				 $this->upload($_FILES['file0'],$userId,1);
			for ($i=1; $i<count($_FILES); $i++)
				{
				    $this->upload($_FILES['file'.$i],$userId,2);
				}
				//不曾在图片上传的函数里被打断，成功
				echo json_encode(array("code"=>10000,"msg"=>"接收到了"));
			}
			//Db::name('user')->insertGetId($data);
   			
   		}
  
    
   		public function upload($value,$serviceid,$type=''){


//找后缀名（2）
$name=$value["name"];
$type=$type;

$ary=explode('.', $name);
$count2=count($ary);
//文件后缀名
$saveType=$ary[$count2-1];


$fileName=ROOT_PATH."/public/static/image/".@date("YmdHis").mt_rand(1000,9999).".".$saveType;
	if($value["error"]==0){
		if($saveType=='jpg'||$saveType=='png'||$saveType=='bmp'){
			if($value["size"]<(2*1024*1024)){
				
				move_uploaded_file($value['tmp_name'],$fileName);
				if($type==1){
				$data = ['image' =>  "../../".$fileName];
				$result=Db::name('service')->where('id',$serviceid)->update($data);
				}
				else if($type==2){
					$fileName=str_replace("public/", "",$fileName);
					$data = ['sid'=>$serviceid,'url' =>  "../../".$fileName];
					$result=Db::name('image')->insert($data);
				}
				
				
			}else{
				//echo "<script>alert('图片过大，不可超过2M');</script>";
				echo json_encode(array("code"=>10004,"msg"=>'图片过大，不可超过2M'));exit;
			}
		}else{
			//echo "<script>alert('不是图片');</script>";
			echo json_encode(array("code"=>10005,"msg"=>'不是图片'));exit;
		}
	}else{
		//echo "<script>alert('异常');</script>";
		echo json_encode(array("code"=>10006,"msg"=>'异常'));exit;

	}



	}
  }
?>