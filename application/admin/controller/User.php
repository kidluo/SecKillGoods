<?php
namespace app\admin\controller;
use think\Controller;

class User extends Base
{

   
    public function admin(){
        $info=db('admin')->select();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function user(){
        $info=db('user')->select();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function del($id)
    {
       
        db('user')->where('id',$id)->delete();
        return $this->success('删除成功');
    }
  


   
}
