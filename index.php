<?php

date_default_timezone_set('Asia/Shanghai');

require __DIR__.'/app/lib/base.php';

F3::config('app/cfg/setup.cfg');
F3::config('app/cfg/routes.cfg');

$services_json = json_decode(getenv("VCAP_SERVICES"),true);
$mysql_config = $services_json["mysql-5.1"][0]["credentials"];

$username = $mysql_config["username"];
$password = $mysql_config["password"];
$hostname = $mysql_config["hostname"];
$port = $mysql_config["port"];
$db = $mysql_config["name"];

echo $username;
echo $password;
echo $hostname;
echo $port;
echo $db;

try{
	$dsn = 'mysql:host='.$hostname.';port='.$port.';dbname='.$db;
	//F3::set('DB', new DB($dsn, $username, $password));
    F3::set('DB',new DB('mysql:host=localhost;port=3306;dbname=happy','root',''));

}catch(PDOException $e){
	echo $e.message;
	echo "db error";
	exit;
}

F3::run();

?>
