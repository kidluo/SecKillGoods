<?php
namespace app\admin\controller;
use think\Controller;

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
            $this->error('验证码不正确','login/index');
          }
        
        if (!$username) {
            $this->error('用户名不能为空');
        }
        if (!$password) {
            $this->error('密码不能为空');
        }

        $res = db('admin')->where('username',$username)->select();
        
        if(!$res){
            $this->error('用户名或密码错误，请重新输入');
        }
        if (($username == $res[0]['username'] && md5($password) == $res[0]['password'])
            ) 
            {
            session('username', $username);
            session('password', $password);
            session('client', $_SERVER['HTTP_USER_AGENT']);
            session('ip',$_SERVER['REMOTE_ADDR']);

            
            $this->success('登入成功', 'main/index');
            
        }
        else {
            $this->error('登录失败，检查用户名和密码');
        }
     


        
        
    }   

    public function logout()
    {
        session('username', null);
        $this->success('退出成功', 'login');

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