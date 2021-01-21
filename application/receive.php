<?php
/**
 * Created by PhpStorm.
 * User: jmsite.cn
 * Date: 2019/1/15
 * Time: 13:16
 */
//声明连接参数
$config = array(
    'host' => 'localhost',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'luomk',
    'password' => '123'
);
//连接broker
$cnn = new AMQPConnection($config);
if (!$cnn->connect()) {
    echo "Cannot connect to the broker";
    exit();
}
//在连接内创建一个通道
$ch = new AMQPChannel($cnn);
//创建一个交换机
$ex = new AMQPExchange($ch);
//声明路由键
$routingKey = 'key_1';
//声明交换机名称
$exchangeName = 'exchange_1';
//设置交换机名称
$ex->setName($exchangeName);
//设置交换机类型
//AMQP_EX_TYPE_DIRECT:直连交换机
//AMQP_EX_TYPE_FANOUT:扇形交换机
//AMQP_EX_TYPE_HEADERS:头交换机
//AMQP_EX_TYPE_TOPIC:主题交换机
$ex->setType(AMQP_EX_TYPE_DIRECT);
//设置交换机持久
$ex->setFlags(AMQP_DURABLE);
//声明交换机
$ex->declareExchange();
//创建一个消息队列
$q = new AMQPQueue($ch);
//设置队列名称
$q->setName('queue_1');
//设置队列持久
$q->setFlags(AMQP_DURABLE);
//声明消息队列
$q->declareQueue();
//交换机和队列通过$routingKey进行绑定
$q->bind($ex->getName(), $routingKey);

//接收消息并进行处理的回调方法
function receive($envelope, $queue) {
    //休眠两秒，
    // sleep(2);
    //echo消息内容
    $msg = $envelope->getBody();
    // echo "消息是：".$msg;
    //下面处理该消息
    $msgArray = json_decode($msg,true);
    // var_dump($msgArray);
    $id = $msgArray["id"];
    $username = $msgArray["username"];
    $count = (int)$msgArray["count"];

    // echo "username:".$username;
    // echo "count:".$count;

    $con=mysqli_connect("localhost","product","123456","product");
    if (!$con) { 
        die('数据库连接失败'.$mysqli_error()); 
    } 


    // $result=mysqli_query($con,"select * from user where id ='{$id}' ;");
    // $row=mysqli_fetch_array($result);

    
    $pd=mysqli_query($con,"select * from product where id=1 ;");
    $pdrow = mysqli_fetch_array($pd);
    $amount = $pdrow["amount"];
    $new_amount = $amount - $count;
    if($new_amount >= 0){
        mysqli_query($con,"update product set amount = '{$new_amount}' where id=1");
        mysqli_query($con,"update user set amount = '{$count}' where id='{$id}'");
        echo "处理成功";
    }

    //显式确认，队列收到消费者显式确认后，会删除该消息
    $queue->ack($envelope->getDeliveryTag());
}
//设置消息队列消费者回调方法，并进行阻塞
$q->consume("receive");
//$q->consume("receive", AMQP_AUTOACK);//隐式确认,不推荐