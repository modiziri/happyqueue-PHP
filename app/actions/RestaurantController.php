<?php

class RestaurantController{

	function __construct(){
		if(User::is_login()){
			F3::set("login", "true");
			F3::set("title", "餐厅管理");
		}else{
			F3::reroute('/login');
		}

		if(User::is_admin() === false){
			F3::reroute('/');
		}
		F3::set('route', 'admin');
		F3::set('admin', 'true');
	}

	function method(){
		F3::reroute('/admin');
	}

	function listQueue(){
		$rida = F3::get("COOKIE");
		//Code::dump($rida);
		$rid = $rida['se_user_admin'];
		if($rid == 0)
			F3::reroute("/admin/signup");

		$all = Queue::getAll($rid);
		$info = Restaurant::getDetail($rid);
	
		$a = F3::get("GET.m");

		if($a == "man")
			F3::set("auto", "false");
		else
			F3::set("auto", "true");

		F3::set("all", $all);
		F3::set("i", $info);
		echo Template::serve('admin/listqueue.html');
	}


	function notifyUser(){
		// phone
		$user = F3::get("GET.id");
		Queue::notify($user);
		F3::reroute("/admin");
	}

	function customerArrive(){
		$user = F3::get("GET.id");
		Queue::arrive($user);
		F3::reroute("/admin");
	}


	function showSignUpRestaurant(){
		echo Template::serve('admin/signup.html');
	}

	function signUp(){
		//$rid = F3::get("GET.rid");
		$a = $this->getInfo();

		$id = Restaurant::signUp($a);

		User::updateAdmin($id);

		$table = F3::get("POST.table");

		Table::setTable($id, $table);

		F3::reroute("/admin");

	}

	function showEditBasicInfo(){
		$id = F3::get("COOKIE.se_user_admin");
		$r = Restaurant::getDetail($id);
		F3::set("r", $r);
		echo Template::serve('admin/editbasic.html');
	}

	function showEditWaitTime(){
		$id = F3::get("COOKIE.se_user_admin");
		$t = Restaurant::getTime($id);
		F3::set("time", $t);
		echo Template::serve('admin/edittime.html');
	}

	function editWaitTime(){
		$t = F3::get("POST.time");
		Restaurant::updateTime($t);
		F3::reroute("/admin");
	}


	function editBasicInfo(){
		$a = $this->getInfo();
		Restaurant::update($a);
		F3::reroute("/admin");
	}


	function getInfo(){
		$a = array();
		$a['phone'] = F3::get("POST.phone");
		$a['name'] = F3::get("POST.name");
		$a['time'] = F3::get("POST.time");
		$a['table'] = F3::get("POST.table");
		$a['addr'] = F3::get("POST.addr");
		$a['describe'] = F3::get("POST.describe");
		return $a;
	}

}
