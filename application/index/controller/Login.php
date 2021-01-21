<?php
namespace app\index\controller;
use think\Controller;
use Think\Verify;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function login()
    {
        return $this->fetch('index');
    }
    

    public function dologin()
    {

        $username=input('username');
        $password=input('password');

        if(!$this->testcaptcharesult()){
          db('user_log')->insert(['username'  => $username,
           'ip'=>$_SERVER['REMOTE_ADDR'],
           'machine'=>$_SERVER['HTTP_USER_AGENT'],
           'action' => "login",
           'status' => "false",
           'remark' => "failed"
           ]);
          $this->error('验证码不正确','login/index');
        }
        

        $res = db('user')->where('username',$username)->select();
        
        if(!$res){
          db('user_log')->insert(['username'  => $username,
           'ip'=>$_SERVER['REMOTE_ADDR'],
           'machine'=>$_SERVER['HTTP_USER_AGENT'],
           'action' => "login",
           'status' => "false",
           'remark' => "failed"
           ]);
            $this->error('用户名或密码错误，请重新输入','login/index');
        }
        if (md5($password) == $res[0]['password'])
            {
            session('id', $res[0]["id"]);
            session('username', $username);
            session('client', $_SERVER['HTTP_USER_AGENT']);
            session('ip',$_SERVER['REMOTE_ADDR']);

            //登录成功的情况写入日志
            db('user_log')->insert(['username'  => $username,
                         'user_id' => session('id'),
                          'ip'=>session('ip'),
                          'machine'=>session('client'),
                          'action' => "login",
                          'status' => "true",
                          'remark' => "success"
                          ]);

            $this->success('登录成功', 'index/index');
            
            
            
        }
        else {
          //登录失败的情况写入日志
           db('user_log')->insert(['username'  => $username,
           'ip'=>$_SERVER['REMOTE_ADDR'],
           'machine'=>$_SERVER['HTTP_USER_AGENT'],
           'action' => "login",
           'status' => "false",
           'remark' => "failed"
           ]);
            $this->error('登录失败，检查用户名和密码','login/index');
        }
     


        
        
    }   

    public function logout()
    {
       db('user_log')->insert(['username'  => session('username'),
        'user_id' => session('id'),
           'ip'=>$_SERVER['REMOTE_ADDR'],
           'machine'=>$_SERVER['HTTP_USER_AGENT'],
           'action' => "logout",
           'status' => "true",
           'remark' => "success"
           ]);
        session('username', null);
        $this->success('退出成功', 'login/index');

    }

    public function testcaptcharesult()
    {
        $code = input('captcha');
        if (!captcha_check($code)){
            return false;
        }else{
            return true;
        }

    }

   
    
   
}