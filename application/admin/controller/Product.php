<?php
namespace app\admin\controller;
use think\Controller;

class Product extends Base
{

   
    public function info()
    {
        $res = db('product')->where('id',1)->select();
        // var_dump($res);
        $cnt = $res[0]['amount'];
        $this->assign('cnt',$cnt);
        return $this->fetch();
    }
  
    public function amount()
    {
        $res = db('product')->where('id',1)->select();
        // var_dump($res);
        $cnt = $res[0]['amount'];
        $this->assign('cnt',$cnt);
        return $this->fetch();
    }

    public function modify()
    {
        $new_amount = input('number');
        if($new_amount<1) $this->success('数量不合法，输入正确的数量');
        // echo $new_amount;
        db('product')->where('id',1)->update(['amount' => $new_amount]);
        return $this->success('修改成功','product/info');
    }



   
}
