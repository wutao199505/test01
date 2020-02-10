<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Chart extends Controller 
{   
        public function getUSer(){
        $year=$_POST['year'];
        $sql="select month(regtime) as month , count(*)   as sum from ym_admin where year(regtime)='$year' group by month(regtime) ";

        $result=Db::query($sql);
        $monthArray=array();
        $sumArray=array();
        $info=array();
        foreach ($result as $key => $value) {
            $info[$value['month']]=$value['sum'];
        }
        for ($i=0; $i <13 ; $i++) { 
            $monthArray[]=$i.'月';
            $sumArray[]=!empty($info[$i])?$info[$i]:0;
        }
        echo json_encode(array("code"=>10000,"msg"=>"ok","month"=>$monthArray,"sum"=>$sumArray));
        }
        public function getMoney(){
        $year=$_POST['year'];
        $sql="select month(ordertime) as month , sum(price)  as sum from ym_order where year(ordertime)='$year' group by month(ordertime) ";
        $result=Db::query($sql);
        $monthArray=array();
        $sumArray=array();
        $info=array();
        foreach ($result as $key => $value) {
            $info[$value['month']]=$value['sum'];
        }
        for ($i=0; $i <13 ; $i++) { 
            $monthArray[]=$i.'月';
            $sumArray[]=!empty($info[$i])?$info[$i]:0;
        }
        echo json_encode(array("code"=>10000,"msg"=>"ok","month"=>$monthArray,"sum"=>$sumArray));
        

    }

    /*-----------------------------------------------------*/
        public function excel(){
            $excel=new PHPExcel();//创建PHPExcel对象
            /*
                文档的初始化设置....(省略)
                ...
            */
            $Sheet1 = $excel->createSheet();    //创建工作簿       
            /*设置sheet的name*/
            $Sheet1 ->setTitle('测试title');   
            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('Sample image');
            $objDrawing->setDescription('Sample image');
            //解码图片
            //var_dump(base64_decode(strstr($group_vol_img,'base64,')));
            $imgcode = "data:image/png;base64,iVBORw0KGgoAAAANSUhE...";       
            $im = imagecreatefromstring(base64_decode(str_replace('data:image/png;base64,', '', $imgcode)));
            $objDrawing->setImageResource($im);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);        ////渲染方法
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
            $objDrawing->setHeight(300);                //设置图片的高度
            $objDrawing->setWidth(400);                //设置图片的宽度
            $objDrawing->setCoordinates('B2');    //设置图片所在表格位置
            $objDrawing->setWorksheet($Sheet1);
        }


    /*---------------------------------------------------------*/

}