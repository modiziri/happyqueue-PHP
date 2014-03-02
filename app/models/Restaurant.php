<?php

class Restaurant{
	function __construct(){
	}

	static function getAddr($id){
		$r = DB::sql('SELECT addr FROM restaurant WHERE id = :id', array(':id' => $id));
		if(count($r) > 0)
			return $r[0]['addr'];
		return false;
	}

	static function getName($id){
		$r = DB::sql('SELECT name FROM restaurant WHERE id = :id', array(':id' => $id));
		if(count($r) > 0)
			return $r[0]['name'];
		return false;
	}

	static function signUp($i){
		//Code::dump($i);
		$sql = "INSERT INTO restaurant VALUES ('', '{$i['name']}', '{$i['phone']}',
				'{$i['addr']}', '{$i['describe']}', '{$i['time']}', '{$i['table']}')";
		DB::sql($sql);
		return DB::get_insert_id();
	}

	static function getDetail($id) {
		$r = DB::sql('SELECT * FROM restaurant WHERE id = :id', array(':id' => $id));
		if (count($r) > 0){
			$r = $r[0];
			$r['num'] = Queue::getNum($id);
			$r['time'] = $r['num'] * $r['time'];
			$r['time_t'] = $r['time'];

			if($r['time'] <= 60){
				$r['time'].= " 分钟";
			}else{
				$h = floor($r['time'] / 60);
				$m = $r['time'] % 60;
				$r['time'] = $h."小时".$m."分钟";
			}
			
			return $r;
		} else{
			return false;
		}
	}

	static function getTime($id) {
		$r = DB::sql('SELECT time FROM restaurant WHERE id = :id', array(':id' => $id));
		return $r[0]['time'];
	}

	static function getBasicInfo($id) {
		$r = array();
		$temp = getDetail($id);
		if($temp != false){
			$r['id'] = $temp['id'];
			$r['name'] = $temp['name'];
			$r['num'] = $temp['num'];
			return $r;
		}
		return false;
	}

	static function getAllBasicInfo(){
		$a = array();
		$r = DB::sql('SELECT id FROM restaurant WHERE 1');
		$num = count($r);
		foreach($r as $v)
			$a[] = self::getDetail($v['id']);
		return $a;
	}

	static function updateTime($t){
		$id = F3::get("COOKIE.se_user_admin");

		$sql = "UPDATE restaurant SET `time` = '{$t}' WHERE id = '$id';";
		DB::sql($sql);
	}

	static function update($a){
		$id = F3::get("COOKIE.se_user_admin");

		$sql = "UPDATE restaurant SET name = '{$a['name']}', phone = '{$a['phone']}',
					addr = '{$a['addr']}', `describe` = '{$a['describe']}',
					`table` =  '{$a['table']}' WHERE id = '$id';";
		DB::sql($sql);

		$sql = "DELETE FROM `table` WHERE rid = $id";
		DB::sql($sql);

		Table::setTable($id, $a['table']);

	}
}

