<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;

class Aftersale extends Controller 
{   


    //获取所有售后信息
    public function getAllAftersale(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 6;

        $page = !empty($_GET['page']) ? $_GET['page'] : 1;

        $start = ($page-1)*$limit;

        $result = Db::name('aftersale')->alias('a')->field('a.*,afi.url')->join('afimage afi','afi.afid = a.id')->select();

        $count = Db::name('aftersale')->alias('a')->field('a.*,afi.url')->join('afimage afi','afi.afid = a.id')->count();
        //图片数组
        $ary = [];
        //重构图片数组
        foreach ($result as $key => $value) {
            $ary[$value['id']][] = [$value['url']];
           
        }
        
        $result2 = Db::name('aftersale')->alias('a')->field('a.*,afi.url')->join('afimage afi','afi.afid = a.id')->limit($start,$limit)->group('a.id')->select();

        $count2 = Db::name('aftersale')->alias('a')->field('a.*,afi.url')->join('afimage afi','afi.afid = a.id')->group('a.id')->count();
        //
        foreach ($result2 as $key1 => $value1) {
            foreach ($ary as $key2 => $value2) {
                if ($value1['id'] == $key2) {
                    $result2[$key1]['url'] = $value2;
                }
            }
        }

        echo json_encode(array('code'=>0,'msg'=>'获取售后数据成功','count'=>$count2,'data'=>$result2));


        

    }


    //同意售后并且录入售后描述和退款金额信息
    public function agreeAftersale(){
        $reason = !empty($_POST['reason']) ? $_POST['reason'] : '';
        $money = !empty($_POST['money']) ? $_POST['money'] : '';
        $id = !empty($_POST['id']) ? $_POST['id'] : 0;
        $adminaccount = Session::get('adminName');
        if ($reason && $money && $id && $adminaccount) {

            //录入信息
            $data = ['reason' => $reason, 'money' => $money, 'status' => 2,'adminaccount' => $adminaccount];

            $result = Db::name('aftersale')->where('id', $id)->update($data);

            if ($result) {
                echo json_encode(array('code'=>10000,'msg'=>'同意售后'));
            }
            
           
        }

    }

    //拒绝售后并且录入售后描述信息
    public function refuseAftersale(){
        $reason = !empty($_POST['reason']) ? $_POST['reason'] : '';
        $id = !empty($_POST['id']) ? $_POST['id'] : 0;
        $adminaccount = Session::get('adminName');
        if ($reason && $id && $adminaccount) {

            //录入信息
            $data = ['reason' => $reason, 'status' => 3,'adminaccount' => $adminaccount];

            $result = Db::name('aftersale')->where('id', $id)->update($data);

            if ($result) {
                echo json_encode(array('code'=>10000,'msg'=>'拒绝售后'));
            }
            
           
        }

    }



    
}