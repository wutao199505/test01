<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
// require_once EXTEND_PATH.'libs/Core_Page.class.php';

// use think\Paginator;

class Aftersale extends Controller
{	
	public $post;

	public function __construct(){
		$this->post = json_decode(file_get_contents("php://input"),1);
      
    }


    //跳转售后页
    public function aftersale(){

    	return view('aftersale');
    }
 
    //上传图片和图片信息
    public function upload(){
        $ordernum = !empty($_POST['ordernum']) ? $_POST['ordernum'] : '';
        $reason = !empty($_POST['reason']) ? $_POST['reason'] : '';
        if ($ordernum && $reason) {
            //先存储售后信息 返回id
            $data = ['ordernum' => $ordernum, 'content' => $reason];

            $result=Db::name('aftersale')->insert($data);
            //获取存储的id
            $Id = Db::name('aftersale')->getLastInsID();
            //循环存储图片   和   加入数据库
            foreach ($_FILES as $key => $value) {
                $oldName = $value['name'];  //取出文件原来的名字
                $pos = strrpos($oldName,'.');  //找到最后一个点的位置，
                $ext = substr($oldName, $pos);  //截取pos点之后的东西

                $fileName= @date("ymdHis").mt_rand(1000,9999).$ext;  //图片新名称
                move_uploaded_file($value['tmp_name'],'../public/static/image/aftersaleImg/'.$fileName); //临时位置移动到指定位置

                $data2 = ['afid'=>$Id,'url' =>  "../../../static/image/aftersaleImg/".$fileName];

                $result=Db::name('afimage')->insert($data2);

            }

            echo json_encode(array("code"=>10000,"msg"=>'提交成功'));

        }

    }

   

}
?>