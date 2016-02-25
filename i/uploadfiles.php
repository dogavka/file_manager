<?php
 ini_set("display_errors",0);
 
 $is_win = false;
$os_string = php_uname('s');

if (strpos(strtoupper($os_string), 'WIN')!==false) {
$is_win = true;
}
 
// Здесь нужно сделать все проверки передаваемых файлов и вывести ошибки если нужно
 
// Переменная ответа
 if(isset($_POST["curdir"])) {
	$dir = $_POST["curdir"];
	if($is_win) $dir = mb_convert_encoding($dir, "cp1251", "utf-8");
	chdir($dir);

 
$data = array();
 
//if( isset( $_GET['uploadfiles'] ) ){
    $error = false;
    $files = array();
 
    //$uploaddir = '../uploads/'; 
 
    // Создадим папку если её нет
 
    //if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );
 
    // переместим файлы из временной директории в указанную
    foreach( $_FILES as $file ){
		if($is_win) $file["name"] = mb_convert_encoding($file["name"], "cp1251", "utf-8");
        if( move_uploaded_file( $file['tmp_name'], basename($file['name']) ) ){
            $files[] = realpath( $_POST["curdir"] . $file['name'] );
        }
        else{
            $error = true;
        }
    }
 
    $data = $error ? array('error' => 'Ошибка загрузки файлов.') : array('files' => $files );
 
    echo json_encode( $data );
}
else 
	echo json_encode(Array("error"=>"нет данных"));
//}
?>