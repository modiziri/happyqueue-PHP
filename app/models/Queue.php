<?php
class Queue{

    static function cancel($phone){
        $sql = "delete from queue where phone = $phone and status = 1";
        DB::sql($sql);
    }

	static function arrive($phone){
		// send msg
		$sql = "UPDATE queue SET status = 'arrived' WHERE phone = $phone AND status = 'smsed'";
		$r = DB::sql($sql);
	}

	static function notify($phone){
		// send msg
		$rid = F3::get("COOKIE.se_user_admin");
		$name = Restaurant::getName($rid);
		$addr = Restaurant::getAddr($rid);
		$msg = new PHPFetion("15114588070", "731yichun623");
		$msg->send($phone, "感谢您参加<$name>排队，请您与30分钟内到达<$addr>用餐，祝您用餐愉快！");

		$sql = "UPDATE queue SET status = 'smsed' WHERE phone = $phone AND status = 'queuing'";
		$r = DB::sql($sql);
	}

	static function getAll($rid){
		$a = array();
		$sql = "SELECT * FROM `table` WHERE rid = $rid ORDER BY capacity DESC";
		//echo $sql;
		$r = DB::sql($sql);
		//Code::dump($r);

		foreach($r as $v){
			$c = $v['capacity'];
			$sql = "SELECT * FROM queue WHERE rid = $rid AND `table` = $c
				AND (status = 'queuing' OR status = 'smsed') ORDER BY time ASC";
			$rs = DB::sql($sql);
			//Code::dump($rs);
			$tmp = array();
			foreach($rs as $k => $v){
				$tmp[$k] = array();
				$tmp[$k]['num'] = $v['num'];
				$tmp[$k]['status'] = $v['status'];
				$tmp[$k]['phone'] = $v['phone'];
				$tmp[$k]['time'] = date("H:i:s", $v['time']);
			}
			$a[] = array(
				'capacity' => $c,
				'customer' => $tmp
			);
		}
		return $a;
	}

	static function addItem($rid, $phone, $num){
		if(User::exist("phone", $phone, "queue", "status = 'queuing'"))
			return -2;
		$time = time();
		$table = Table::getTable($rid, $num);
		if($table == -1)
			return -1;
		
		$sql = "INSERT INTO queue VALUES 
			('', $rid, '$table', '$phone', '$num', '$time', '', '', 'queuing')";
		//echo $sql;
		$r = DB::sql($sql);
		return 1;
	}

	static function getNextCustomer(){
		$rid = F3::get("COOKIE.se_user_admin");
		if($rid == 0)
			return false;
		$rs = DB::sql('SELECT phone FROM queue WHERE rid = :rid AND status = "queuing" ORDER BY time ASC', 
			array(':rid' => $rid)
		);
		//Code::dump($rs);
		$phone = $rs[0]['phone'];
	}

	static function getNum($rid){
		$rs = DB::sql('SELECT COUNT(*) FROM queue WHERE rid = :rid AND status = "queuing"', 
			array(':rid' => $rid)
		);
		return $rs[0]["COUNT(*)"];
	}
}
