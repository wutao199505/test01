<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Offer extends Controller 
{   


    //获取所有应聘信息
    public function getAllOffer(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 6;

        $page = !empty($_GET['page']) ? $_GET['page'] : 1;

        $start = ($page-1)*$limit;

        $result = Db::name('offer')->alias('o')->field('o.*,s.id as sid')->join('service s','s.name = o.work')->limit($start,$limit)->order('o.addtime desc')->select();

        $count = Db::name('offer')->alias('o')->field('o.*,s.id as sid')->join('service s','s.name = o.work')->count();
        
        echo json_encode(array('code'=>0,'msg'=>'获取应聘数据成功','count'=>$count,'data'=>$result));

    }

    //拒绝应聘
    public function refuseOffer(){
        $offerid = !empty($_POST['id']) ? $_POST['id'] : 0;
        if ($offerid) {

            $result = Db::name('offer')->where('id', $offerid)->update(['status' => 3]);
            
            echo json_encode(array('msg'=>'已拒绝'));
        }

    }

    //同意应聘并且录入应聘者信息到外派人员表
    public function agreeOffer(){
        $name = !empty($_POST['name']) ? $_POST['name'] : '';
        $phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
        $address = !empty($_POST['address']) ? $_POST['address'] : '';
        $sid = !empty($_POST['sid']) ? $_POST['sid'] : 0;
        $offerid = !empty($_POST['offerid']) ? $_POST['offerid'] : 0;
        if ($name && $phone && $address && $sid && $offerid) {
            //修改应聘信息为已录用
            $result2 = Db::name('offer')->where('id', $offerid)->update(['status' => 2]);

            //录入信息到外派人员表
            $data = ['name' => $name, 'phone' => $phone, 'address' => $address, 'sid' => $sid];

            $result = Db::name('worker')->insert($data);

            if ($result && $result2) {
                echo json_encode(array('code'=>10000,'msg'=>'录入成功'));
            }
            
           
        }

    }

    



    
}