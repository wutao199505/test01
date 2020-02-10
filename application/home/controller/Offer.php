<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
// require_once EXTEND_PATH.'libs/Core_Page.class.php';

// use think\Paginator;

class Offer extends Controller
{	
	public $post;

	public function __construct(){
		$this->post = json_decode(file_get_contents("php://input"),1);
      
    }


    //跳转应聘页
    public function offer(){

    	return view('offer');
    }
 
    //获取服务项目
    public function getService(){
    	
        $result = Db::name('service')->field('name')->select();

        if ($result) {
        	echo json_encode(array("code"=>10000,"data"=>$result));
        }
       
        
    }

    //判断手机号码是否有被已录用的外派人员使用
    public function searchphone(){
        
        $phone = !empty($this->post['phone']) ? $this->post['phone'] : '';

        $result = Db::name('worker')->field('id')->where('phone', $phone)->find();

        if ($result) {
            echo json_encode(array("code"=>10000,"msg"=>'已被使用'));
        }else{
            echo json_encode(array("code"=>10001,"msg"=>'可以使用'));
        }
       
        
    }

    //存储应聘信息到数据库
    public function OfferInfo(){
    	$name = !empty($this->post['name']) ? $this->post['name'] : '';
    	$phone = !empty($this->post['phone']) ? $this->post['phone'] : '';
    	$work = !empty($this->post['work']) ? $this->post['work'] : '';
    	$address = !empty($this->post['address']) ? $this->post['address'] : '';
    	$content = !empty($this->post['content']) ? $this->post['content'] : '';
    	if ($name && $phone && $work && $address && $content) {

    		$data = ['name' => $name, 'phone' => $phone, 'work' => $work, 'address' => $address, 'content' => $content];

            $result = Db::name('offer')->insert($data);
                

	        if ($result) {
	        	echo json_encode(array("msg"=>'提交成功'));
	        }
    	}
                
    }


}
?>