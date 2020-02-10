<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Loader;
class Login 
{

    //登录方法
    public function UserLogin(){
        $request = request();//引入request函数
        $info = $request->param();//获取http数据集合  
        $method =$request->method();//获取提交方法POST或GET
        if ($method=='POST'){
            $code=$this->RemoveXSS($info['code']);//获取验证码
            $username=$this->RemoveXSS($info['username']);//获取前台登录名
            $password=$this->RemoveXSS(MD5($info['password']));//获取前台登录密码
            $name = 1;
            //判断验证码是否正确
            if(!captcha_check($code)){
                $flag=10004;//验证码错误
                $msg='验证码错误';
            }else{
                $result=Db::name('user')->where('account','=',$username)->where('pwd','=',$password)->select();//根据账号密码查找                
                if ($result){
                    //循环遍历账号有没有锁定
                    foreach($result as $key=>$value){
                        //账号存在未锁定                      
                        if ($value['status']==1){
                            $flag=10000;
                            Session::set('UserName',$value['account']);
                            $name = $value['name'];
                            $msg='登录成功';
                        }else{
                            $flag=10001;
                            $msg='账号被锁定';
                        }
                    }
                }else {
                    $flag=10002;//账号或者密码错误
                    $msg='账号或者密码错误';
                }
            }           
        }
        echo json_encode(array("code"=>$flag,"name"=>$name,"msg"=>$msg));       
    }
    /**
	 * 获取用户信息
	 */
	public function getUserInfo(){
		$UserName =Session::get('UserName');
			if($UserName){
                $result=Db::name('user')->where('account','=',$UserName)->select();//根据账号查找 
                foreach($result as $key=>$value){                   
                        $name = $value['name'];
                }  
				echo json_encode(array("code"=>10000,"msg"=>'获取成功',"name"=>$name));
			}else{
                echo json_encode(array("code"=>10001,"msg"=>'暂未登录'));
            }
	}
    
    /**
	 * 用户注销
	 */
	public function exitUser(){
        Session::delete('UserName');
        echo json_encode(array("code"=>10000,"msg"=>'退出成功'));
    }
    /**
	 * 获取短信验证码
	 */
	public function getCode(){           
            $phoneNum=$_POST['phoneNum'];//获取验证码手机号码            
            Loader::import('dx.api_demo.SmsDemo',EXTEND_PATH,'.php');
            set_time_limit(0);//设置执行时间
            $smsObj = new \SmsDemo();
            $code = rand(100000,999999);            
            $res =  $smsObj::sendSms($phoneNum,$code) ;  //发送手机号码和验证码
            if($res){
                Session::set($phoneNum.'code',$code+$phoneNum); //存电话号码？Session::set($phoneNum,$code+$phoneNum);
                echo json_encode(array("code"=>10000,"msg"=>'成功获取验证码,验证码3分钟后将失效，请及时填写'));              
            }else{
                echo json_encode(array("code"=>10001,"msg"=>'获取验证码失败，请稍后再试'));
            }
    }   
    /**
	 * 用户注册
	 */
	public function regUser(){           
        $phoneNum=$_POST['phoneNum'];//获取验证码手机号码            
        $name=$_POST['name'];//获取验证码手机号码            
        $myCode=$_POST['myCode'];//获取验证码手机号码            
        $code =Session::get($phoneNum.'code');
        // 查询用户表
        $result=Db::name('user')->select();
        if ($result){
            //循环遍历账号有没有被注册
            foreach($result as $key=>$value){
                //账号存在未锁定                      
                if ($value['account']==$phoneNum){
                    return array("code"=>10003,"msg"=>'该手机号码已经被注册','data'=>$result);
                }
            }
        }
        if($code==false){
            return array("code"=>10004,"msg"=>'验证码错误');
        }
        if(($myCode+$phoneNum)==$code){   
            $data=['account'=>$phoneNum,'name'=>$name] ;  
            $result=Db::name('user')->insert($data);     
            if($result){
                Session::delete($phoneNum.'code');
                echo json_encode(array("code"=>10000,"msg"=>'注册成功,默认密码a12345,请在个人中心进行修改'));
            }else{
                echo json_encode(array("code"=>10001,"msg"=>'注册失败'));
            }
        }else{
            echo json_encode(array("code"=>10002,"msg"=>'注册失败，请检查验证码是否输入正确'));
        }
} 
    
	//防止sql注入
	public function RemoveXSS($val) {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
           // ;? matches the ;, which is optional
           // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
           // @ @ search for the hex values
           $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
           // @ @ 0{0,7} matches '0' zero to seven times
           $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }
        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);
        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
           $val_before = $val;
           for ($i = 0; $i < sizeof($ra); $i++) {
              $pattern = '/';
              for ($j = 0; $j < strlen($ra[$i]); $j++) {
                 if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                 }
                 $pattern .= $ra[$i][$j];
              }
              $pattern .= '/i';
              $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
              $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
              if ($val_before == $val) {
                 // no replacements were made, so exit the loop
                 $found = false;
              }
           }
        }
        return $val;
    }
}
?>