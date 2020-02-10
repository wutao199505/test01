<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Paginator;
class Index extends Controller
{
    /**
     * 
     */
    public function homeIndex()
    {
    	return view('index');
    }
}
?>