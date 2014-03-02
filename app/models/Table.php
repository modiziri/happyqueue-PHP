<?php

class Table{

	static function setTable($rid, $t){
		$a = explode(";", $t);
		//Code::dump($a);
		foreach($a as $v){
			if($v == "")
				continue;
			$b = explode(":", $v);
			//Code::dump($b);
			$c = $b[0];
			$n = $b[1];
			$sql = "INSERT INTO `table` VALUES ('$rid', '$c', 0, '$n')";
			DB::sql($sql);
		}
	}

	static function getTable($rid, $num){
		$sql = "SELECT * FROM `table` WHERE rid = $rid ORDER BY capacity DESC";
		//echo $sql;
		$r = DB::sql($sql);
		$tmp = -1;
		foreach($r as $v){
			if($v['capacity'] < $num)
				break;
			$tmp = $v['capacity'];
		}
		return $tmp;
	}
}
