<?php
ini_set("display_errors",0);
//теоретически, надо проверять разрешения на доступ, но это чуть позже
if(isset($_POST["curdir"])) {
$files = Array();
$files_ = Array();
//echo(getcwd());

$is_win = false;
$os_string = php_uname('s');

if (strpos(strtoupper($os_string), 'WIN')!==false) {
$is_win = true;
} 
/*
else {
echo 'Linux';
}
*/

if($is_win) $curdir = mb_convert_encoding($_POST["curdir"], 'cp1251', 'UTF-8');
else $curdir = $_POST["curdir"];

if(substr($_POST["curdir"],0,2) != '..') {
	//if(!chdir(mb_convert_encoding($_POST["curdir"], 'cp1251', 'UTF-8'))) {
	if(!chdir($curdir)) {
		echo json_encode(Array("error"=>"Can't change directory1"));
		exit;
	}
}
else {
	$current_path = substr($_POST["curdir"],2);
	if($is_win) $current_path = mb_convert_encoding($current_path, 'cp1251', 'UTF-8');
	//echo($_POST["curdir"]);
//	echo($current_path);
	//if(!chdir(mb_convert_encoding($current_path, 'cp1251', 'UTF-8'))) 
	//if(!chdir(mb_convert_encoding($current_path, 'cp1251', 'UTF-8'))) {
	if(!chdir($current_path)) {
		echo json_encode(Array("error"=>"Can't change directory2"));
		exit;
	}
	if(!chdir("..")) {
		echo json_encode(Array("error"=>"Can't change parent directory"));
		exit;
	}
}
//chdir($_POST["curdir"]);
$path = getcwd();
	//$dir = opendir(".");
	//$files_ = scandir(mb_convert_encoding($path, 'UTF-8', 'cp1251'));
	$files_ = scandir($path);
	if($is_win) $path_ = mb_convert_encoding($path, 'UTF-8', 'cp1251');
	else $path_ = $path;
	
//	echo($path_);
	
	   $i = 0;
    foreach ($files_ as $file) {
        // смотрим, что пришло - "точки", директория или файл
 
		  if($file == "." || $file == "..") $files["files"][$i] = Array("parent" => 1, "name" => $file, "dir" => 2, "file" => 0, "path" => $path_); //если родительский и текущий
		  elseif(is_file($path. DIRECTORY_SEPARATOR .$file)) {
				$stat = stat($path. DIRECTORY_SEPARATOR .$file);
				$files["files"][$i]['file'] = 1;
				$files["files"][$i]['dir'] = 0;
				$files["files"][$i]['parent'] = 0;
				if($is_win) $files["files"][$i]['name'] = mb_convert_encoding($file, 'UTF-8', 'cp1251');
				else $files["files"][$i]['name'] = $file;
				//$files["files"][$i]['name_id'] = mb_convert_encoding(str_replace('.', '\\\.', $file), 'UTF-8', 'cp1251');
				//$files["files"][$i]['size'] = ROUND(((filesize($path .'\\' . $file)) / 1024),2);
				//$files["files"][$i]['last_update'] = date ("d.m.Y H:i:s", filemtime($path .'\\' . $file));
				$files["files"][$i]['size'] = ROUND((($stat["size"]) / 1024),2);
				$files["files"][$i]['last_update'] = date('d.m.Y H:i:s', $stat["mtime"]);
				if($is_win)
				$files["files"][$i]['path'] = mb_convert_encoding(($path.DIRECTORY_SEPARATOR . $file), 'UTF-8', 'cp1251');
				else
				$files["files"][$i]['path'] = $path.DIRECTORY_SEPARATOR . $file;
 			}
			elseif(is_dir($path. DIRECTORY_SEPARATOR .$file)) { // если директория
				$stat = stat($path. DIRECTORY_SEPARATOR .$file);
				$files["files"][$i]['file'] = 0;
				$files["files"][$i]['dir'] = 1;
				$files["files"][$i]['parent'] = 0;
				if($is_win) $files["files"][$i]['name'] = mb_convert_encoding($file, 'UTF-8', 'cp1251');
				else $files["files"][$i]['name'] = $file;
				//$files["files"][$i]['name'] = mb_convert_encoding($file, 'UTF-8', 'cp1251');
				$files["files"][$i]['size'] = "";
				if($is_win)
				$files["files"][$i]['path'] = mb_convert_encoding(($path. DIRECTORY_SEPARATOR . $file), 'UTF-8', 'cp1251');
				else
				$files["files"][$i]['path'] = $path. DIRECTORY_SEPARATOR . $file;
				$files["files"][$i]['last_update'] = date('d.m.Y H:i:s', $stat["mtime"]);
				$files["files"][$i]['count_files'] = count(scandir($path. DIRECTORY_SEPARATOR .$file)) - 2; //не считаем . и ..
				}
	$i++;
	}
	
	//print_r($files);
	
	$dir_ = Array(); $name_ = Array(); $f_ = Array();
	foreach($files as $value) {
		foreach($value as $key=>$arr){
		$dir_[$key]=$arr['dir'];
		$name_[$key]=$arr['name'];
		$f_[$key] = $arr;
		}
	}
	
	array_multisort($dir_, SORT_NUMERIC, SORT_DESC, $name_, SORT_ASC, $f_);
	//print_r($files2);
	 echo json_encode(Array("current_path" => mb_convert_encoding($path, 'UTF-8', 'cp1251'),"files" => $f_));
}
else 
 echo json_encode(Array("error"=>"нет данных"));
 
 /*
function getSimpleFilesListWithAddInfo($dirpath) {
    $result = array();
     
    $cdir = scandir($dirpath); 
    $i = 0;
    foreach ($cdir as $value) {
        // если это "не точки" и не директория
        if (!in_array($value,array(".", "..")) 
            && !is_dir($dirpath . DIRECTORY_SEPARATOR . $value)) {
             
            $result[$i]['name'] = $value;
            $result[$i]['size'] = filesize($dirpath . DIRECTORY_SEPARATOR . $value);
            $i++;
         }
    } 
     
    return $result;
} 
 */
 
 function json_encode_cyr($str) {
$arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
'\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
'\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
'\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
'\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
'\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
'\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
'\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
$arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');
$str1 = json_encode($str);
$str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
return $str2;
}
?>