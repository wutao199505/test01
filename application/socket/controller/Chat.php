<?php
    namespace app\socket\controller;
    use think\Controller;
    use think\Session;
    use think\Request;
    use think\Db;
    class Chat extends Controller{
        /**
         * 获取后台客服id
         */
        public function getAdminChat()
        {
            $sendid=Session::get('adminName');
            $result=Db::name('admin')->field('id,name')->where('account','=',$sendid)->select();
            echo json_encode(array("sendid"=>$result));
            //echo $result;
        }
        /**
         * 获取前台用户id
         */
        public function getHomeChat(){
            $sendid=Session::get('UserName');
            $result=Db::name('user')->where('account',$sendid)->field('id')->find();
            echo json_encode(array("sendid"=>$result['id']));

        }
        /**
         * 聊天数据持久化
         */
        public function saveMsg()
        {
            if (Request::instance()->isAjax()){
                $data=input("post.");
                $datas['sendid']=$data['sendid'];
                $datas['fromid']=$data['fromid'];
                $datas['content']=$data['data'];
                $datas['isread']=$data['isread'];
                Db::name('chat')->insert($datas);
            }
        }
        /**
         * 聊天表情数据持久化
         */
        public function saveEmjon(){
            if (Request::instance()->isAjax()){
                $data=input("post.");
                $datas['sendid']=$data['sendid'];
                $datas['fromid']=$data['fromid'];
                $datas['content']=$data['src'];
                $datas['isread']=$data['isread'];
                $datas['type']=2;
                Db::name('chat')->insert($datas);
            }
        }
        /**
         * 获取聊天记录
         */
        public function getChatContent(){
            if (Request::instance()->isAjax()){
                $data=input("post.");
                $id=$data['sendid'];
                
                $result=Db::name('chat')
                ->where('sendid','=',$id)
                ->whereOr('fromid','=',$id)
                ->order('id esc')
                ->select();
                echo json_encode(array("msg"=>$result));
            }
        }
        /**
         * 图片上传
         */
        public function upload(){
            $files=$_FILES['file'];//获取文件
            $sendid=input('sendid');//送id、
            $fromid=input('fromid');//接受id
            $fileName=$files['name'];//文件名
            $name=explode('.',$fileName);//截取点位置开始
            $newPath=$name[1];//拼接文件名
            $suffix=strtolower(strrchr($fileName,'.'));
            $img=@date("YmdHis").mt_rand(1000,9999).".".$newPath;
            $type=['.jpg','.jpeg','gif','.png'];//默认图片类型
            if(!in_array($suffix,$type)){
                echo json_encode(array("code"=>10001));
            }else{
                if($files['size']/1024>5120){
                echo json_encode(array("code"=>10002));

                }else{
                    $uploadPath="../public/static/image/chat/".$img;//图片路径
                    $res=move_uploaded_file($files['tmp_name'],$uploadPath);
                    if($res){
                        $url="/hx1903/Ym-master/public/static/image/chat/".$img;
                        $data['sendid']=$sendid;
                        $data['fromid']=$fromid;
                        $data['content']=$url;
                        $data['type']=3;
                        $data['isread']=1;
                        Db::name('chat')->insert($data);//存入聊天列表
                        echo json_encode(array("code"=>10000,"url"=>$url));

                    }else {
                        
                        echo json_encode(array("code"=>10003));
                    }
                }
            }
        }
        /**
         * 获取头像和姓名
         */
        public function getHead($sendid){
            $result=Db::name('user')->where('id','=',$sendid)->field('head,name')->find();
            return $result;
        }
        /**
         * 获取最后一条消息
         * 
         */
        public function getLastMsg($sendid,$fromid){
            $result=Db::name('chat')->where('sendid',$sendid)->where('fromid',$fromid)->field('content,type')->order('id desc')->limit(1)->find();
            return $result;
        }
        /**
         * 获取聊天消息
         */
        public function getMsg(){
            $sendid=input('sendid');
            $result=Db::name('chat')->where('fromid','=',$sendid)->group('sendid')->select();
            $rows=array_map(function($res){
                return[
                    'head_url'=>$this->getHead($res['sendid'])['head'],//头像
                    'username'=>$this->getHead($res['sendid'])['name'],//昵称
                    'content'=>$this->getLastMsg($res['sendid'],$res['fromid'])['content'],//最后一条消息
                    'sendid'=>$res['sendid'],//发送id
                    'type'=>$this->getLastMsg($res['sendid'],$res['fromid'])['type']//消息类型
                ];
            },$result);
            //var_dump($result);
            echo json_encode(array("data"=>$rows));
        }
    }
?>