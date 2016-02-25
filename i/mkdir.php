<?php
ini_set("display_errors",0);
$is_win = false;
$os_string = php_uname('s');

if (strpos(strtoupper($os_string), 'WIN')!==false) {
$is_win = true;
}
//теоретически, надо проверять разрешения на доступ, но это чуть позже
if(isset($_POST["curdir"])) {
	$dir = $_POST["curdir"];
	if($is_win) $dir = mb_convert_encoding($dir, "cp1251", "utf-8");
	chdir($dir);
	
	$str = preg_replace('/[^a-zA-Zа-яА-ЯёЁ0-9_\s\.]/siu', '', $_POST["dirname"]);
	
	if($is_win) $str = mb_convert_encoding($str, "cp1251", "utf-8");
	
	if(!(mkdir( $str, 0777 )))
		echo json_encode(Array("error"=>"папка не создана"));
	else
		echo json_encode(Array("status"=>"ok"));
}
elseif(isset($_POST["dir_rename"]) && isset($_POST["dir_rename_hidden"])) {
	//chdir($_POST["curdir"]);
	
	//$str = mb_convert_encoding($_POST["dir_rename"], "cp1251", "utf-8");
	//$str = preg_replace('/[^a-zA-Zа-яА-ЯёЁ\_\.]/si', '', $_POST["dir_rename"]);
	$str = $_POST["dir_rename"];
	$str_old = $_POST["dir_rename_hidden"];
	$str = preg_replace('/[^a-zA-Zа-яА-ЯёЁ0-9_\s\.]/siu', '', $str);
	if($is_win) {
		$str = mb_convert_encoding($str, "cp1251", "utf-8");
		$str_old = mb_convert_encoding($str_old, "cp1251", "utf-8");
	}
	
	$path_parts = pathinfo($str_old);
	if(in_array($path_parts['basename'], Array('css', 'i', 'js'))) {
		echo json_encode(Array("error"=>"Директория необходима для работы, переименовывать нельзя!"));
		exit;
	}
	//echo $path_parts['dirname'];
	if(!rename($str_old, $path_parts['dirname']."/".$str))
	//if(!(mkdir( $str, 0777 )))
		echo json_encode(Array("error"=>"невозможно переименовать директорию!"));
	else
		echo json_encode(Array("status"=>"ok"));
}
elseif(isset($_POST["file_rename"]) && isset($_POST["file_rename_hidden"])) {
	//chdir($_POST["curdir"]);

	//$str = mb_convert_encoding($_POST["file_rename"], "cp1251", "utf-8");
	$str = preg_replace('/[^a-zA-Zа-яА-ЯёЁ0-9_\.\s]/siu', '', $_POST["file_rename"]);
	$str_old = $_POST["file_rename_hidden"];
	
	if($is_win) {
	$str = mb_convert_encoding($str, "cp1251", "utf-8");
	$str_old = mb_convert_encoding($str_old, "cp1251", "utf-8");
	}
	
	
	$path_parts = pathinfo($str_old);
	
	if(in_array($path_parts['basename'], Array('index.php', 'getdirlist.php', 'mkdir.php', 'uploadfiles.php', 'getfile.php', 'manager_style.css'))) {
		echo json_encode(Array("error"=>"Файлы необходимы для работы, трогать нельзя!"));
		exit;
	}
	
	//echo $path_parts['dirname'];
	if(!rename($str_old, $path_parts['dirname']."/".$str))
	//if(!(mkdir( $str, 0777 )))
		echo json_encode(Array("error"=>"невозможно переименовать файл!"));
	else
		echo json_encode(Array("status"=>"ok"));
}
elseif(isset($_POST["dir_remove"]) && isset($_POST["dir_remove_hidden"])) {
	//chdir($_POST["curdir"]);
	
	$str = $_POST["dir_remove_hidden"];
	if($is_win) $str = mb_convert_encoding($str, "cp1251", "utf-8");
	
		$path_parts = pathinfo($str);
		if(in_array($path_parts['basename'], Array('css', 'i', 'js'))) {
		echo json_encode(Array("error"=>"Директория необходима для работы, удалять нельзя!"));
		exit;
	}
	
	if(!(removeDirectory( $str )))
		echo json_encode(Array("error"=>"Невозможно удалить директорию"));
	else
		echo json_encode(Array("status"=>"ok"));
}
elseif(isset($_POST["file_remove"]) && isset($_POST["file_remove_hidden"])) {
	//chdir($_POST["curdir"]);
	
	$str = $_POST["file_remove_hidden"];
	if($is_win) $str = mb_convert_encoding($str, "cp1251", "utf-8");
	
		$path_parts = pathinfo($str);
		if(in_array($path_parts['basename'], Array('index.php', 'getdirlist.php', 'mkdir.php', 'uploadfiles.php', 'getfile.php', 'manager_style.css'))) {
		echo json_encode(Array("error"=>"Файлы необходимы для работы, трогать нельзя!"));
		exit;
		}
	
	if(!(unlink( $str )))
		echo json_encode(Array("error"=>"Невозможно удалить файл"));
	else
		echo json_encode(Array("status"=>"ok"));
}
else 
	echo json_encode(Array("error"=>"нет данных"));
	
	
function removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
       foreach($objs as $obj) {
         is_dir($obj) ? removeDirectory($obj) : unlink($obj);
       }
    }
    if(rmdir($dir)) return true;
  }


?>