<?php
     namespace app\alipay\controller;
     use think\Controller;
     use think\Loader;
     require_once dirname(dirname(dirname(dirname(__FILE__)))).'/extend/alipay/pagepay/service/AlipayTradeService.php';
     require_once dirname(dirname(dirname(dirname(__FILE__)))).'/extend/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
     /**
      * 支付类
      */
      class Pay extends Controller
      {
        public $config = array (	
            //应用ID,您的APPID。
            'app_id' => "2016101300677255",
    
            //商户私钥
            'merchant_private_key' => "MIIEpAIBAAKCAQEA1ybp4z3cIFLSS94LUuGN3WbMX0cn6dG0ejIzkAg2jwAIow21B47Avr01iAvAUT7nJSnpmJn/pCZKKqDKL5/Z5MXH2ODbXk1LUs7yzoUDadjXyiBF9PgQSvlpo671W6zOWEIRb4b0iigzkMdeGO7f/jivjDw8pzzpBt677fI2JoIuzblZFgMvzWpJGAHdZlhuuagEAqpzo5cnmkynzTjKyvu7pU4UfWK3L5O3AQkCzRB+gQSa7WfXcfn9G83AyC9f7YnELVD5XwDiMFyF06sPEmguVULxu8rzeYTwX82MP1zOyE8URWTkVn8byu4K+VJowN7ia0k/lUhS4vL/a56vYwIDAQABAoIBAGVX6RenDccT/AbMgpz5ra86Os4uYDidFNvyRaN0mu5k36yeSkVTEqtFyK9aia47aPlycpv0/hu+BXVq45HnQ+Wk2+vmjq4uzmolHO32bZMwFwUYiptHNbV7Ii19vgj+rvuXs0rLUOi64v8xSbuI2W2QGfB+dzt3T3Ry+iaszqD4F/hPLui98Ul2GazVla32NDAt4TZBACjfXuFsTrEXGJTulPaTh25jAKkze1r3y37P8RJSIkgjFT7zn42DNVm5JGTxjygddJmsWC/4BTxraCR5FR6r9KdFIVdfm41a+BPunh8inmcXqGpFx4HT6QRFKejKKOpgUseEB0iheLLHK0ECgYEA9HU6Em9raB5fsiigg6YD5rgl7Mg1ORuWiDqPuhS5HimG5E2y3uzhU1yplrESkSEK90BhDmHh3gATHEhloNLP/gyGW6YbmZSdjaZbVUT8DVB+A29HIU4Z6ZFKC2W1NqsjSLZfDim+xAsIQyUEQ62zKagR/8bm+X+2F0MqgOjYT5MCgYEA4U93ChSZQYKyQuu80P1RFIh/m6F4Rue9J2+zPdnjMO83jtYq5BRoYvnwgdDbZ9ClvzocVmg3um+F3Uich0eRMCvD7x+otIA5wEbuMGeJPJJA/zr1sxyF5O74+ar6HrCDnIu0GocB7paLx3w0fXAXrHJYigl2nWSDKp5NALTU4vECgYBhkkChhxNJtYXgqFKY+TRpbGrGvYpwxtAog6dGUGsq3MxYZ6LTU50EFuIhoX+Vqaik029pvCvY3hLCoSrfLAWUkw81mXE+tZDFLvcZa7Vm4w/I8yti4bd6AGGbdvcDYP9uUExUbNkViom7sxSmprfyEoFMo+khAJZ/ZnGajlV4PQKBgQCrpQUz6cNjU/UH5F1g/UqykNZeMuHVaqAAlij/2qko02UI7QGZ5i4sEOr3iqxLZ3mNt/B0p0qlPmbF0JZmvM/P2deXqQ+2CuV0JNU6jPXQ7j6T8k/R1s3uPVvxoB6SGLj7Hrbjaqy/HXN5UpcOZMG0PxxSdKkneLYwVo6lFh4EkQKBgQDsaj8r23WYWERP7g8IYYKtVXLdqXHSYVMwIFsUunB15uRtVzaQFlxxSVCaO1nyEDLDcRuIuSd4v+RDt2ahoRy7d8bqHDp+asB0f4Qe0qL/D4QFnLvzLq+4UuxsOxEMK6CXKNd0tQl2cL6wNbKnD2sWRwJDgdMxV2Tswi/PHGIxlA==",
            
            //异步通知地址
            'notify_url' => "http://www.baidu.com/alipay/notify_url.php",
            
            //同步跳转
            'return_url' => "http://127.0.0.1/alipay/return_url.php",
    
            //编码格式
            'charset' => "UTF-8",
    
            //签名方式
            'sign_type'=>"RSA2",
    
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",
    
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxC/Je4xLCmhdNT/ZghK3zBr0iyFY+wQHQkDgGxLO0TwjvhrgkTIklh7DrRzppAG/F3PsIoZT7wt6CE1WojUps0y64P/T4AgR4xks21qr6gc4B7v3S5avTToZIob3HNwF7rI95JO+EMIT9wWXn3rJyFtpS6xxQdmzPKg/jMP8JsYpHfmNhBNze7rD5+UG75EF0OUEtPMlJ0mZKdNC3QPZZ8sleKWTch9+Aga9VYhqZc5xUSfsiGIuXmdNIDdin4hedIAfStSxXvrXcBm51rwNvDUzFgxNdsACQ6/TluW/s3nuLNeqpBqoapkw8Q3v7ZG7JKNeQwvBKBFEiT25s2BH6QIDAQAB",
    );
    
       public function payPage(){
    
                //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = trim($_POST['WIDout_trade_no']);

            //订单名称，必填
            $subject = trim($_POST['WIDsubject']);

            //付款金额，必填
            $total_amount = trim($_POST['WIDtotal_amount']);

            //商品描述，可空
            $body = trim($_POST['WIDbody']);
    
           //构造参数
           $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
           $payRequestBuilder->setBody($body);
           $payRequestBuilder->setSubject($subject);
           $payRequestBuilder->setTotalAmount($total_amount);
           $payRequestBuilder->setOutTradeNo($out_trade_no);
    
           $aop = new \AlipayTradeService($this->config);
           var_dump($aop);
           var_dump($payRequestBuilder);exit;
    
           /**
            * pagePay 电脑网站支付请求
            * @param $builder 业务参数，使用buildmodel中的对象生成。
            * @param $return_url 同步跳转地址，公网可以访问
            * @param $notify_url 异步通知地址，公网可以访问
            * @return $response 支付宝返回的信息
            */
           $response = $aop->pagePay($payRequestBuilder,$this->config['return_url'],$this->config['notify_url']);
    
       }
    
    
       public function notify_url(){
           $arr=$_POST;
           $alipaySevice = new \AlipayTradeService($this->config);
           $alipaySevice->writeLog(var_export($_POST,true));
           $result = $alipaySevice->check($arr);
    
           /* 实际验证过程建议商户添加以下校验。
           1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
           2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
           3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
           4、验证app_id是否为该商户本身。
           */
           if($result) {//验证成功
               /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
               //请在这里加上商户的业务逻辑程序代
    
    
               //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    
               //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
    
               //商户订单号
    
               $out_trade_no = $_POST['out_trade_no'];
    
               //支付宝交易号
    
               $trade_no = $_POST['trade_no'];
    
               //交易状态
               $trade_status = $_POST['trade_status'];
    
    
               if($_POST['trade_status'] == 'TRADE_FINISHED') {
    
                   //判断该笔订单是否在商户网站中已经做过处理
                   //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                   //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                   //如果有做过处理，不执行商户的业务程序
    
                   //注意：
                   //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
               }
               else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                   //判断该笔订单是否在商户网站中已经做过处理
                   //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                   //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                   //如果有做过处理，不执行商户的业务程序
                   //注意：
                   //付款完成后，支付宝系统发送该交易状态通知
               }
               //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
               echo "success";    //请不要修改或删除
           }else {
               //验证失败
               echo "fail";
    
           }
       }
    
       public function return_url(){
           $arr=$_GET;
           $alipaySevice = new \AlipayTradeService($this->config);
           $result = $alipaySevice->check($arr);
    
           /* 实际验证过程建议商户添加以下校验。
           1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
           2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
           3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
           4、验证app_id是否为该商户本身。
           */
           if($result) {//验证成功
               /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
               //请在这里加上商户的业务逻辑程序代码
    
               //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
               //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    
               //商户订单号
               $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
    
               //支付宝交易号
               $trade_no = htmlspecialchars($_GET['trade_no']);
    
               //将订单表中的支付状态更改为已支付，并将支付宝交易号写入数据表中
               //Db::table('sp_order')->where('sn',$out_trade_no)->update(['pay_status'=>1,'alipay'=>$trade_no]);
    
               //$this->success('支付成功，跳转中...','index/index');
            echo "ok";
               //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
    
               /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
           }
           else {
               //验证失败
               echo "验证失败";
           }
       }
    
          //测试
          public function test(){
              return view('pay'); 
          }
      }
?>