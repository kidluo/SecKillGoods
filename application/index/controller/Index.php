<?php
namespace app\index\controller;
use think\Controller;
use AMQPConnection;
use AMQPChannel;
use AMQPExchange;


class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function res()
    {
        $res = db('user')->where('id',session('id'))->select();
        $count = $res[0]["amount"];
        $this->assign('count',$count);
        return $this->fetch('result');
    }

    public function handle()
    { 
        $count = input('count');
        // echo $count;
        //检查输入是否合法
        if($count<1 || $count>3){
            $this->error('输入数量不合法');
        }
        
        //先检查用户是否已经抢过单了,如果是，则不能重复抢
        $res = db('user')->where('id',session('id'))->select();
        if($res[0]["amount"]>0){
            $this->error('已经抢过，不能重复抢','index');
        }
        
       

        //以下为通过php发布消息
        $config = array(
            'host' => 'localhost',
            'vhost' => '/',
            'port' => 5672,
            'login' => 'luomk',
            'password' => '123'
        );
        $cnn = new AMQPConnection($config);
        if (!$cnn->connect()) {
            echo "Cannot connect to the broker";
            exit();
        }
        $ch = new AMQPChannel($cnn);
        $ex = new AMQPExchange($ch);
        //消息的路由键，一定要和消费者端一致
        $routingKey = 'key_1';
        //交换机名称，一定要和消费者端一致，
        $exchangeName = 'exchange_1';
        $ex->setName($exchangeName);
        $ex->setType(AMQP_EX_TYPE_DIRECT);
        $ex->setFlags(AMQP_DURABLE);
        $ex->declareExchange();
       
        $msg = array(
            'id' => session('id'),
            'username'  => session('username'),
            'count' => $count,
        );
        //发送消息到交换机，并返回发送结果
        //delivery_mode:2声明消息持久，持久的队列+持久的消息在RabbitMQ重启后才不会丢失
         "Send Message:".$ex->publish(json_encode($msg), $routingKey, AMQP_NOPARAM, array('delivery_mode' => 2))."\n";
        //代码执行完毕后进程会自动退出
            

        $this->success('成功提交');

        
    }
}
