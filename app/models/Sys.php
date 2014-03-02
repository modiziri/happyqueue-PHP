<?php

class Sys{

	/**
	 * 处理SQL
	 */
	static function format_sql($sql){
		if(F3::get("DBT") == 'mysql'){
			if(is_array($sql)){
				$new = array();
				foreach($sql as $k => $s)
					$new[$k] = self::replace_sql($s);
				return $new;
			}else{
				return self::replace_sql($sql);
			}
		}else if(F3::get("DBT") == 'mssql'){
			return str_replace('\\\'', "\'\'", $sql);
		}
	}

	/**
	 * 替换SQL里的识别符
	 */
	static function replace_sql($sql){
		$sql = str_replace('[', '`', $sql);
		$sql = str_replace(']', '`', $sql);
		$sql = preg_replace('/SET IDENTITY_INSERT (\w+) ON;/',  '', $sql);
		$sql = preg_replace('/SET IDENTITY_INSERT (\w+) OFF;/', '', $sql);
		return $sql;
	}

	static function scope_parameter($category,$name) {
		if($category != -1) {
			$sql1 = "SELECT MIN([min]) as min_min FROM parameter_num WHERE name = {$name} AND [1st_category] = {$category}";
			$sql2 = "SELECT MAX([max]) as max_max  FROM parameter_num WHERE name = {$name} AND [1st_category] = {$category}";
		} else {
			$sql1 = "SELECT MIN([min]) as min_min FROM parameter_num WHERE name = {$name}";
			$sql2 = "SELECT MAX([max]) as max_max FROM parameter_num WHERE name = {$name}";
		}

		//echo $sql1;
		$r1 = DB::sql($sql1);
		$r2 = DB::sql($sql2);
		$r = array('min' => $r1[0]["min_min"], 'max' => $r2[0]["max_max"]);

		$r['min'] = ($r['min'] == F3::get('MIN')) ? '' : number_format($r['min'], 1);
		$r['max'] = ($r['max'] == F3::get('MAX')) ? '' : number_format($r['max'], 1);

		//Code::dump($r);
		return $r;
	}

	/**
	 * 处理文本中的换行和空格
	 */
	static function convert_br_space($text){
		$text = str_replace(' ', '&nbsp;', $text);
		$text = str_replace('\\r\\n', '<br />', $text);
		//$text = nl2br($text);
		return $text;
	}

	static function convert_num($d, $type = 'db'){
		//if(isset($d['convert'])) $convert = $d['convert'];
		$convert = null;
		//if($type == 'db'){
			$d['min'] = self::convert_unit_db($d['min'], $d['unit'], $convert);
			$d['max'] = self::convert_unit_db($d['max'], $d['unit'], $convert);
		//}else{
			//$d['min'] = self::convert_unit_show($d['min'], $d['unit'], $convert);
			//$d['max'] = self::convert_unit_show($d['max'], $d['unit'], $convert);
		//}
		$min  = self::convert_min($d['min'], $d['max'], $d['type']);
		//Code::dump($min);
		$d['max']  = self::convert_max($d['min'], $d['max'], $d['type']);
		$d['min'] = $min;
		//Code::dump($d);
		return $d;
	}

	/**
	 * 转换数字格式用于编辑参数信息页面显示
	 */
	static function convert_num_show($d){
		$min = $d['min'];
		$max = $d['max'];
		//Code::dump($d);
		if($min == $max) {
			$max = '';
		} else if($min == F3::get('MIN')) {
			$min = $max;
			$max = '';
		} else if($max == F3::get('MAX')) {
			$max = '';
		}
		//Code::dump($min);
		//Code::dump(F3::get('MIN'));
		//Code::dump(($min == F3::get('MIN')));

		$d['min'] = $min == '' ? '' : self::convert_unit_show($min, $d['unit']);
		$d['max'] = $max == '' ? '' : self::convert_unit_show($max, $d['unit']);
		//Code::dump($d);
		return $d;
	}


	/**
	 * 存入或查询数据库时单位转换
	 */
	static function convert_unit_db($before, $id, $convert = null){
		if($convert === null)
			$convert = self::get_unit_convert($id);
		if($convert == 0) return $before;
		$after = $before / $convert;
		return $after;
	}

	/**
	 * 读出数据库时单位转换
	 */
	static function convert_unit_show($before, $id, $convert = null){
		if($convert === null)
			$convert = self::get_unit_convert($id);
		if($convert == 0) return $before;
		//Code::dump($convert);
		$after = $before * $convert;
		return $after;
	}

	static function get_unit_convert($id){
		$r = DB::sql("SELECT [convert] FROM unit WHERE id = {$id}");
		return $r[0]['convert'];
	}

	/**
	 * 根据数值类型转换最大值
	 */
	static function convert_max($min, $max, $type){
		switch($type){
			case '1':
				//Code::dump($max);
				return $max;
			case '2':
				//Code::dump($min);
				return $min;
			case '3':
				return F3::get('MAX');
			case '4':
				//Code::dump($min);
				return $min;
		}
	}

	/**
	 * 根据数值类型转换最小值
	 */
	static function convert_min($min, $max, $type){
		//return ($return == 'min')?self::convert_min($min, $type):self::convert_max($max, $type);
		switch($type){
			case '1':
			case '2':
			case '3':
				return $min;
			case '4':
				return F3::get('MIN');
		}
	}

	/**
	 * 生成查询的结果html
	 */
	static function generate_search_result(
		$dids, $keys = false, $current_page = 0, $each_page_show = 10){
		
		//Code::dump($dids);
		if(count($dids)==0){
			echo "根据输入的条件，没有找到结果";
			return;
		}
		sort($dids);
		$total = count($dids);
		//Code::dump($dids);
		$dids = array_slice( $dids, $current_page * $each_page_show, $each_page_show);
		//Code::dump($dids);
		$ids = '';
		foreach($dids as $v)
			$ids .= "{$v},";
		//Code::dump($ids);
		$ids = substr($ids, 0, -1);

		//Code::dump($ids);

		$basic = Device::get_basic_detail($ids, true, true);

		if($basic === false){//其他情况，如纪录删除不完整，参数表没删干净
			echo "根据输入的条件，没有找到结果";
			return;
		}

		foreach($basic as &$v)
			$v['dyear'] = Sys::genrate_year_show($v['min_year'], $v['max_year'], null, true);
		
		$detail_keys = '';
		if(is_array($keys)){
			$basic = self::result_highlight($basic, $keys);
			foreach($keys as $k)
				$detail_keys .= "{$k} ";
		}
		$detail_keys= substr($detail_keys, 0, -1);

		//Code::dump($detail_keys);

		$detail_keys = urlencode($detail_keys);
		//Code::dump($detail_keys);
		F3::set('keys', $detail_keys);

		$page_max = ($current_page  + 1) * $each_page_show;
		$page_max = $page_max > $total ? $total : $page_max;

		F3::set('all', $basic);
		F3::set('page_min', $current_page * $each_page_show + 1);
		F3::set('page_max', $page_max);

		//pagination
		$total_page = ceil($total / $each_page_show);
		$pagination = Sys::pagination(
			$current_page, $total_page, $each_page_show, $show_group = '', $onclick = 'showPage');
		F3::set('pagination', $pagination);
		F3::set('total', $total);

		echo Template::serve('user/result.html');
		
	}

	static function result_highlight($strs, $keys = array(), $ommit = ''){
		if(empty($keys) || !is_array($strs)) return $strs;
		foreach($strs as $r => &$str){
			if($r === $ommit) continue;
			foreach($str as $k => &$string){
				//Code::dump($string);
				foreach($keys as $key){
					//preg_replace("/($key)/",'<em color="red">\\1</em>',$string);
					//Code::dump($key);
					if($key == '') continue;
					$string = str_replace($key, "<em>{$key}</em>", $string);
				}
			}
		}
		return $strs;
	}


	static function remove_bad_str($str){
		$badString = array(
			'~',  '!',  '@',  '#',  '$',  '%',  '^',  '&',  '*',  '(',  ')',  '-',  '+',  '[',  ']',
			':',  ';',  '\'', '"',  '|',  '\\', ',',  '.',  '?',  '/',  '<',  '>');
		$str = str_replace($badString,' ',$str);
		$str = preg_replace('/\s+/',' ',$str);
		return $str;
	}

	/**
	 * 根据数据库中的研制年代数据生成可阅读格式良好的文本
	 */
	static function genrate_year_show($min, $max, $comment, $easy = false){
		if($min == $max)
			$r = "{$min}年";
		else
			$r = "{$min} ~ {$max}年";
		return $easy ? $r : ("{$r}<br/>{$comment}");
	}

	static function two_into_one($two)  //二维数组into一维数组
	{
		$one = array();

		foreach($two as $i)
			foreach($i as $j)
				array_push($one,$j);
		//Code::dump($one);
		return $one;
	}

	static function array_logic($logic,$a,$b) // 两个数组进行logic(AND OR NOT)运算
	{
		if($logic == 'AND')  //交集
			return array_intersect($a,$b);

		if($logic == 'OR')  //并集
		{
			$a = array_merge($a,$b);
			$a = array_flip($a);
			return array_keys($a);
		}
	
		if($logic == 'NOT')  //差集
			return array_diff($a,$b);
	}

	static function genrate_basic_category_show($a, $b, $c){
		$r = Device::get_id_name('basic_category');
		$str = "{$r[$a]}  {$r[$b]}  {$r[$c]} ";
		return $str;
	}

	static function set_basic_category(){
		//$sql1 = "SELECT * FROM basic_category";
		$r = array();
		$r['1st_category'] = Device::get_id_name('basic_category', 'id < 10 OR id = 100');
		$r['2nd_category'] = Device::get_id_name('basic_category', 'id > 10 AND id < 20 OR id = 100');
		$r['3rd_category'] = Device::get_id_name('basic_category', 'id > 20 AND id < 40 OR id = 100');
		//Code::dump($r);
		F3::set('c', $r);
		return;
	}

	/*
	*格式化高级搜索的post数组
	*/
	static function format_advance_post($data)
	{
		$item = Array();

		foreach(array_keys($data) as $keys) {
			if($keys == 'page') continue;
			$k = explode('-',$keys);
			$c = $k[0];
			if(!isset($k[1])){
				Code::dump($keys);
				Code::dump($k);
				return;
			}
			$s = $k[1];
			if(!isset($item[$c]))
				$item[$c] = Array();

			//Code::dump($s);
			$temp = array('item', 'search_type');
			if(in_array($s, $temp)){
				$t = explode('-', $data[$keys]);
				$item[$c][$s] = $t[0];
			}else{
			$item[$c][$s] = trim($data[$keys]);
			}
		}
		return $item;
	}

	/**
	 * 格式化结果数组
	 */
	static function format_show_parameter($q) {
		$r = array();
		foreach($q as $v){
			if(!isset($r[$v['id']])){
				$r[$v['id']] = array();
				$r[$v['id']]['name'] = $v['cn'];
			}
			//Code::dump($v);

			$content = ($v['num_str'] == "str")? $v['content'] : $v['comment'];

			//$comment = $v['comment'];
			//if($comment != ''){
				//$comment = " (".$comment.")";
			//}
			
			$comment = ($v['num_str'] == "str")?
				'':self::format_parameter_num($v['min'], $v['max'], $v['show'], $v['symbol'], $v['unit']);
			$comment = $comment == '' ? '' : "&nbsp;&nbsp;({$comment})";

			$r[$v['id']][] = array(
				'name' => $v['nn'],
				'content' => $content,
				'comment' => $comment
			);
		}
		return $r;
	}

	/**
	 * 格式化数字样式
	 *
	 */
	static function format_parameter_num($min, $max, $type, $symbol, $unit_id)
	{
		if($min == F3::get('MIN')) $min = $max;
		if($max == F3::get('MAX')) $max = '';

		$min = self::convert_unit_show($min, $unit_id);
		$max = self::convert_unit_show($max, $unit_id);

		$count = substr_count($type, "%s");
		if($count == 2){
			$content = sprintf($type, $min, $symbol);
		}else if($count == 3){
			$content = sprintf($type, $min, $max, $symbol);
		}
		//Code::dump($content);
		return $content;
	}

	static function basic_form_validation($data)
	{
		$dname = trim($data['dname']);

		if(empty($dname))
			$msg = "名称不能为空";
		else if(Device::is_name_exist($dname) == true) //验证名称是否存在
			$msg = "该名称已存在";
		else if(!preg_match("/^\d{4}$/", $data['min_year']) || !preg_match("/^\d{4}$/", $data['max_year']))
            $msg = "年代必须为4位数字"; 
		else if($data['min_year'] > $data['max_year'])
			$msg = "最小年代应<=最大年代";
		else if(!isset($data['1st_category']) || !isset($data['2nd_category']) || !isset($data['3rd_category']))
			$msg = "请选择分类";
		else
			return TRUE;

		return $msg;
	}

	static function category_form_validation($data)
	{
		$name = trim($data['name']);

		if(empty($name))
			$msg = "名称不能为空";
		else if(Category::is_name_exist($name,'category') !== FALSE) //验证名称是否存在
			$msg = "该名称已存在";
		else
			return TRUE;

		return $msg;
	}

	static function format_parameter($data)
	{
		$fc = $data['1st_category'];  //1st_category
		$did = $data['did'];  
		$num = array();
		$str = array();

		foreach(array_keys($data) as $keys)
		{
			if($keys != '1st_category' && $keys!='did')
			{
				$k = explode('-',$keys);
				if($k[0] == 'n')
				{
					$sub = "{$k[1]}"."{$k[2]}";
					if(!isset($num[ $sub ]))
						$num[$sub] = array();
					$num[ $sub ]['did'] = $did;
					$num[ $sub ]['1st_category'] = $fc;
					$num[ $sub ]['2nd_category'] = $k[1];
					$num[ $sub ]['name'] = $k[2];
					$num[ $sub ][ $k[3] ] = $data[$keys];
				}
				else if($k[0] == 's')
				{
					$sub = "{$k[1]}"."{$k[2]}";
					if(!isset($str[ $sub ]))
						$str[$sub] = array();
					$str[ $sub ]['did'] = $did;
					$str[ $sub ]['1st_category'] = $fc;
					$str[ $sub ]['2nd_category'] = $k[1];
					$str[ $sub ]['name'] = $k[2];
					$str[ $sub ][ $k[3] ] = $data[$keys];
				}
			}
		}

		return array('num' => $num, 'str' => $str);
	}

	static function time_quaters(){
		static $times = null;

		if($times == null){
			for($i = 0; $i < 24; $i++){
				for($j = 0; $j < 60; $j += 15){
					$times[] = sprintf('%02d:%02d', $i, $j);
				}
			}
		}

		return $times;
	}

	static function the(){
		$args = func_get_args();
		$arr = $args[0];

		if($args < 2 || !is_array($arr)){
			return '';
		}


		for($i = 1; $i < count($args); $i++){
			$k = $args[$i];

			if(is_array($arr) && array_key_exists($k, $arr)){
				$arr = $arr[$k];
			}else{
				break;
			}
		}

		return $arr;
	}

	static function set_msg($msg, $success)
	{
		F3::set('GET.msg', $msg);
		F3::set('GET.success', $success);
		F3::set('GET.has_submit', 1);

		F3::set('msg', $msg);
		F3::set('success', $success);
		F3::set('has_submit', 1);
	}

	static function pagination($current_page, $total_pages, $each_page_show, $show_group = '', $onclick = false){
		$html = "";
		$html .= "<div class='pagination pagination-right'> <ul>";

		if($current_page == 0):
			$html .= "<li class='disabled'><a href='#'>上一页</a></li>";
		else:
			$prev_page = $current_page - 1;
			$html .= "<li><a href='";
			$html .= $onclick? "#' onclick='{$onclick}(${prev_page}, this)'":"list?ugroup={$show_group}&&page={$prev_page}";
			$html .= "'>上一页</a></li>";

		endif;


		for($i = 0; $i < $total_pages; $i++):
			$j = $i + 1;
			if($i == $current_page):
				$html .= "<li class='active'><a href='#'>{$j}</a></li>";
			else: 
				$html .= "<li><a href='";
				$html .= $onclick? "#' onclick='{$onclick}(${i}, this)'":"list?ugroup={$show_group}&&page={$i}";
				$html .= "'>{$j}</a></li>";
			endif;
		endfor;


		if($current_page == $total_pages - 1):
			$html .= "<li class='disabled'><a href='#'>下一页</a></li>";
		else:
			$next_page = $current_page + 1;
			$html .= "<li><a href='";
			$html .= $onclick? "#' onclick='{$onclick}(${next_page}, this)'":"list?ugroup={$show_group}&&page={$next_page}";
			$html .= "'>下一页</a></li>";
		endif;

		$html .= "</ul></div>";

		return $html;

	}
};

?>
