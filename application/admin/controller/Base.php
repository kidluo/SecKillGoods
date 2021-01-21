<?php
namespace app\admin\controller;
use think\Request;
use think\Controller;
use think\auth\Auth;
use think\Session;

class Base extends Controller
{
    public function _initialize()
    {
      $username=Session::get('username');
      if(!$username) $this->error('还没登录，赶紧的','login/index');
      $this->log();
    }

    public function log($msg= array('status' => 'true', 'remark' => ''))
    {
      //获取当前模块
      $m = request()->module();
      //获取当前控制器名称
      $c=request()->controller();
      //获取当前方法名
      $a=request()->action();

      $username=Session::get('username');
      $user_id=Session::get('id');
      $ip=$_SERVER['REMOTE_ADDR'];
      $machine=$_SERVER['HTTP_USER_AGENT'];
      $action = $m."/".$c."/".$a;
      $status = $msg["status"];
      $remark = $msg["remark"];
      db('admin_log')->insert(['username'  => $username,
                         'user_id' => $user_id,
                          'ip'=>$ip,
                          'machine'=>$machine,
                          'action' => $action,
                          'status' => $status,
                          'remark' => $remark
                          ]);

            
            
    }
 }