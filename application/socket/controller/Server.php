<?php
    namespace app\socket\controller;
    use Workerman\Worker;

    /**
     * 服务端
     */
    class Server 
    { 
        public function server(){
            $ws=new Worker("websocket://127.0.0.1:4444");
            //启动4个进程
            $ws->count=4;
            //连接客户端
            $ws->onConnect=function($connection)
            {
                $id=$connection->getRemoteIp();
                $connection->send($id);
                
            };
            //收到消息
            $ws->onMessage=function($connection,$data)
            {
                $connection->send('hello'.$data);
            };
            Worker::runAll();
            
        }
        //protected $socket='websocket://127.0.0.1:9999';
        /**
         * 收到消息
         */
        /*public function onMessage($connection,$data)
        {
            $connection->send('我收到你的消息了');
        }
        /**
         * 建立连接时触发的回调函数
         */
        /*public function onConnect($connection)
        {

        }
        /**
         * 断开连接触发回调函数
         */
        /*public  function onClose($connection)
        {

        }
        /**
         * 客户端连接发生错误
         */
        /*public function onError($connection,$code,$msg)
        {
            echo "error $code $msg\n";
        }
        /**
         * 每个进程启动
         */
        //public function onWorkerStart($worker)
/*      {

        }*/
    }
?>