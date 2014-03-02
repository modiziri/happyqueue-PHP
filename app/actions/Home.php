<?php

class Home{

	function __construct(){
		if(User::is_login()){
			F3::set("login", "true");
		}
	}

	function run(){
		F3::set('title',"HappyQueue");
		echo Template::serve('index.html');
	}

	function showLoginPage(){
		F3::set('title',"登录");
		F3::set('login_status','');
		F3::set('error_display','none');
		echo Template::serve('login.html');
	}

	function login(){
		F3::set('title',"登录");
		$user = User::valid(F3::get('POST.uname'), F3::get('POST.upass'));
		if($user !== false){ //login success
			User::login($user);
			F3::reroute('/');
		} else {
			F3::set('login_status','error');
			F3::set('error_display','inline-block');
			echo Template::serve('login.html');
		}
	}

	function logout(){
		User::logout();
		F3::reroute('/');
	}

	function showSignUp(){
		echo Template::serve('user/usersignup.html');
	}

	function signUp(){
		$info = array();
		$info['name'] = F3::get('POST.uphone');
		$info['pass'] = F3::get('POST.upass');
		//$info['pass'] = F3::get('POST.upass'); //TODO check pass
		$info['phone'] = F3::get('POST.uphone');
		$info['type'] = F3::get('POST.ugroup');

		$uid = User::signUp($info);

		//if(F3::get('GET.mobile') != false){
			// WEB客户端
		if($uid != -2){
			//TODO 注册成功提示
			F3::reroute("/");
			//echo Template::serve('index.html');
		}else{
			F3::set("has_submit", "true");
			F3::set("success", "false");
			F3::set("msg", "该手机号码已注册");
			F3::set("p", F3::get('POST'));
			echo Template::serve('user/usersignup.html');
		}
	}

	function noaccess()
	{
		F3::reroute('/');
	}

	function about(){
		F3::set('route', 'about');
		echo Template::serve('about.html');
	}

};

?>
