<?php
//if(isset($_GET["curdir"]) && isset($_GET["file_name"])) {
if(isset($_GET["file_name"])) {
$file_name = urldecode($_GET["file_name"]);
$file_name = mb_convert_encoding($file_name, "cp1251", "utf-8");
if(file_exists($file_name)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' .basename($file_name));
    //header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($_GET["file_name"]));
    readfile($_GET["file_name"]);
	exit;
	//return json_encode(Array("status" => "ok"));
	}
}
elseif(isset($_POST["file_exists"]) && isset($_POST["curdir"])){
	
}
?>