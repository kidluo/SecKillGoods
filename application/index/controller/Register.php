<?php
namespace app\index\controller;
use think\Controller;

class Register extends Controller
{
    public function index()
    {
        return $this->fetch();
    }


    public function doregister()
    {

        //密码用md5
        $username=input('username');
        $password=input('password');
        $repass = input('repassword');
        
        if (!$this->checkUsername($username)) {
            $this->error('用户名格式不合法，重新输入');
        }
        if($this->checkExistUsername($username)){
            $this->error('用户名已存在');
        }
        if ($password != $repass) {
            $this->error('两次密码不一致，请检查');
        }
        if (!$this->checkPassword($password)) {
            $this->error('密码格式不合法，重新输入');
        }
        
        db('user')->insert(['username'=>$username, 'password'=>md5($password)]);
        $this->success('注册成功,前往登录页面', 'login/index');
        
     


        
        
    }   


    //字母开头，至少4位
    public function checkUsername($username)
    {
        if ((preg_match('/^[a-z]{1,20}[\d_]{0,20}$/i', $username))&& strlen($username)>3 && strlen($username)<12) {
            return true;
        } else {
            return false;
        }
    }

    //数字，字母大小写，特殊符号，至少8位
    public function checkPassword($password)
    {
        if ((preg_match('/^[a-z]{1,20}[\d_]{1,20}$/i', $password))&& strlen($password)>=8) {
            return true;
        } else {
            return false;
        }
       
    }
    public function checkExistUsername($username)
    {
        $res = db('user')->where('username',$username)->select();
        if($res) return true;
        else return false;
    }

    
   
}