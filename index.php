<?php
header("Content-Type: text/html; charset=utf-8");
//меняем текущий каталог на uploads
chdir("uploads");
$dir = getcwd();
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title>Файловый менеджер</title>
<script type="text/javascript" src="js/jquery-1.12.0.min.js"></script>
<script type="text/javascript" src="js/ajaxupload.js"></script>
<link rel="StyleSheet" type="text/css" href="css/manager_style.css">
</head>
<body>
<script>
document.oncontextmenu = function() {return false;}; 
</script>
<div class="content" style="width:50%;">
<!--путь-->
<div id="current_path"></div>
<div class="top" style="clear:both; width:100%;">
<span id="file_span"></span>
<input type="hidden" name="curdir" id="curdir" value="<?php echo $dir ?>"/>
<input type="button" name="upload_show" id="upload_show" value="Добавить файл" />
<input type="file" id="inputfile" name="inputfile" multiple="multiple" style="display:none" />
<input type="button" name="upload" id="upload" value="Загрузить файл"  style="display:none" />
<input type="button" name="cdir_" id="cdir_" value="Создать папку" />
<input type="text" name="dirname" id="dirname" value="" style="display:none" />
<input type="button" name="mkdir_" id="mkdir_" value="Создать" style="display:none" />
<span id="status"></span>
<ul id="files"></ul>
</div>
<div class="left" style="float:left; width:100%;">
<div id="list_error"></div>
<div id="status_bar"></div>
<!--сюда выводим список каталогов и файлов -->
<div id="listdir">
<!-- таки в таблицу-->
<table class = "list_items" id="list_items">
<tr id="first_tr">
<th width="40%">Название</th>
<th width="20%">Дата изменения</th>
<th width="20%">Тип</th>
<th width="20%">Размер</th>
</tr>
</table>
</div>
</div>
</div>
<script>
var target;
//при загрузке строим дерево текущего места
$( document ).ready(function() {
 	build_list();
});

//обработчик для скрытия меню и т.д. при клике на любой области экрана
$(document).on("click", function() {
//скрываем input и показываем ссылку
//скрываем поле выбора файла и папки, показываем кнопки
//var id_ = $(".selected-html-element").html();
//alert(target);
if(typeof(target) === 'undefined') return;
id_ = $(target).html();
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
//id_ = id_.slice(1);
$("#" + id_).css("display","block");
$("#dir_rename_" + id_).css("display","none");	
$("#file_rename_" + id_).css("display","none");	
$('*').removeClass('selected-html-element'); 	
jQuery('.context-menu').remove(); 
//-----------------

if($("#dirname").is(":visible")) {
$("#dirname").hide();
$("#mkdir_").hide();
}

if($("#inputfile").is(":visible")) {
$("#upload").hide();
$("#inputfile").hide();
}

//-----------------

});

//контекстное меню---------------------------------------------------------------------------------------------------------------------------------
// Вешаем слушатель события нажатие кнопок мыши для директорий:     
$(document).on("mousedown", ".tree_div", function(event) {    
	event.stopPropagation();       
//скрываем все предыдущие ссылки
$(".dir_rename").css("display","none");
$(".file_rename").css("display","none");
$(".tree_div").css("display","block");
$(".file_div").css("display","block");	
	// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":         
	$('*').removeClass('selected-html-element');        
	// Удаляем предыдущие вызванное контекстное меню:         
	$('.context-menu').remove();                  
	// Проверяем нажата ли именно правая кнопка мыши:         
	if (event.which === 3)  {                          
	// Получаем элемент на котором был совершен клик:             
	target = $(event.target);                          
	// Добавляем класс selected-html-element чтобы наглядно показать на чем именно мы кликнули (исключительно для тестирования):             
	target.addClass('selected-html-element');             
	// Создаем меню:             
	$('<div/>', {                
	class: 'context-menu' 
	// Присваиваем блоку наш css класс контекстного меню:            
	}).css({left: event.pageX+'px', 
	// Задаем позицию меню на X                 
	top: event.pageY+'px' 
	// Задаем позицию меню по Y             
	}).appendTo('body') 
	// Присоединяем наше меню к body документа:             
	.append( 
	// Добавляем пункты меню:                  
	$('<ul/>').append('<li><a href="#" id="rename_">Переименовать</a></li>').append('<li><a href="#" id="remove_">Удалить</a></li>')).show('fast'); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню          
	}     
	}); 
//--------------контекстное меню--------------------------------------------------------------------------------------------------------

	
	// Вешаем слушатель события нажатие кнопок мыши для ссылок-файлов:     
$(document).on("mousedown", ".file_div", function(event) {    
	event.stopPropagation(); 
	//скрываем все предыдущие ссылки
	$(".dir_rename").css("display","none");
	$(".file_rename").css("display","none");
	$(".tree_div").css("display","block");
	$(".file_div").css("display","block");	
	// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":         
	$('*').removeClass('selected-html-element');        
	// Удаляем предыдущие вызванное контекстное меню:         
	$('.context-menu').remove();                  
	// Проверяем нажата ли именно правая кнопка мыши:         
	if (event.which === 3)  {                          
	// Получаем элемент на котором был совершен клик:             
	target = $(event.target);                          
	// Добавляем класс selected-html-element что бы наглядно показать на чем именно мы кликнули (исключительно для тестирования):             
	target.addClass('selected-html-element');             
	// Создаем меню:             
	$('<div/>', {                
	class: 'context-menu' 
	// Присваиваем блоку наш css класс контекстного меню:            
	}).css({left: event.pageX+'px', 
	// Задаем позицию меню на X                 
	top: event.pageY+'px' 
	// Задаем позицию меню по Y             
	}).appendTo('body') 
	// Присоединяем наше меню к body документа:             
	.append( 
	// Добавляем пункты меню:                  
	$('<ul/>').append('<li><a href="#" id="file_rename_">Переименовать</a></li>').append('<li><a href="#" id="file_remove_">Удалить</a></li>').append('<li><a href="#" id="save_as_">Сохранить файл как</a></li>')).show('fast'); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню          
	}     
	}); 
	//--------------
//--------------------------------------------------------------------------------------------------------------------------------------------------

//скрываем/отображаем поле для имени директории при создании
$('#cdir_').click(function( event ){
event.stopPropagation();
if($("#dirname").is(":visible")) {
$("#dirname").hide();
$("#mkdir_").hide();
}
else {
$("#dirname").show();
$("#mkdir_").show();
$("#upload").hide();
$("#inputfile").hide();
}
});

//скрываем/отображаем поля для загрузки файлов
$('#upload_show').click(function( event ){
event.stopPropagation();
if($("#upload").is(":visible")) {
$("#upload").hide();
$("#inputfile").hide();
}
else {
$("#upload").show();
$("#inputfile").show();
$("#mkdir_").hide();
$("#dirname").hide();
}
});

//переходим по двойному клику 
$(document).on("dblclick", ".tree_div", function(event){
var destdir = $(this).attr('href');
$("#curdir").val(destdir);
build_list(); //строим дерево
});

$(document).on("dblclick", ".parent_div", function(event){
var destdir = $(this).attr('href');
$("#curdir").val(destdir);
build_list(); //строим дерево
});

//переходим по контекстному меню 
$(document).on("click", "#rename_", function(event){
event.stopPropagation();
//alert("dsffdgfdgd");
//alert($(".selected-html-element").parent(".tree_div").attr("href"));
//var curdir = $("#curdir").val();
//var id_ = $(".selected-html-element").html();
var id_ = $(target).html();
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
//id_ = id_.slice(1);
//alert(id_);
//показываем input для изменения названия и скрываем ссылку
$("#" + id_).css("display","none");
$("#dir_rename_" + id_).css("display","block");
// Удаляем предыдущие вызванное контекстное меню:        
jQuery('.context-menu').remove(); 
});


//обработчикb поля изменения имени директории 
//первая функция = для ie, иначе у него не генерируется событие change по enter
$(document).on("keypress", ".dir_rename", function(e){
   if (e.which == 13 && !e.shiftKey) {
        $(this).blur();
        return false;
    }
});

$(document).on("blur", ".dir_rename", function() {
//скрываем input и показываем ссылку
//var id_ = $(".selected-html-element").html();
//alert(target);
var id_ = $(target).html();
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
//id_ = id_.slice(1);
$("#" + id_).css("display","block");
$("#dir_rename_" + id_).css("display","none");	
$('*').removeClass('selected-html-element'); 	
});


$(document).on("click", ".dir_rename", function(event){
	event.stopPropagation();
});

$(document).on("change", ".dir_rename", function(){
//alert("dsffdgfdgd");
//alert($(".selected-html-element").parent(".tree_div").attr("href"));
//var curdir = $("#curdir").val();
//alert($(this).val());
//var id_ = $(".selected-html-element").html();
var id_ = $(target).html();
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
//id_ = id_.slice(1);
//alert(id_);

var dir_rename = $(this).val();
dir_rename = $.trim(dir_rename);
if(dir_rename.length == 0){
	alert("Название директории не может быть пустым!");
	return;
}
var dir_id = $(this).attr("id");
dir_id = dir_id.replace(/\./g, "\\.");
dir_id = dir_id.replace(/\s/g, "\\ ");
var dir_rename_hidden_id = dir_id.slice(11);
//alert(dir_rename_hidden_id);
var dir_rename_hidden = $("#dir_rename_hidden_" + dir_rename_hidden_id).val();
//alert($(target).html());
//alert(dir_id);
        $.ajax({
        url: 'i/mkdir.php',
        type: 'POST',
        data: {dir_rename:dir_rename, dir_rename_hidden:dir_rename_hidden},
        cache: false,
        dataType: 'json',
        //processData: false, // Не обрабатываем файлы (Don't process the files)
        //contentType: false, // Так jQuery скажет серверу, что это строковой запрос
        success: function(respond, textStatus, jqXHR ){
            // Если все ОК
             if( typeof respond.error === 'undefined' ){
				build_list();
            }
            else{
				$("#list_error").html("ОШИБКА: " + respond.error);
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
			$("#list_error").html('ОШИБКИ AJAX запроса: ' + textStatus );
            console.log('ОШИБКИ AJAX запроса: ' + textStatus );
        }
    });


/*
$("#" + id_).css("display","block");
$("#dir_rename_" + id_).css("display","none");
// Удаляем предыдущие вызванное контекстное меню:        
jQuery('.context-menu').remove(); 
*/
});

//удаление директории
$(document).on("click", "#remove_", function(event){
event.stopPropagation();
var id_ = $(target).html();
//alert(id_);
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
// Удаляем предыдущие вызванное контекстное меню:        
jQuery('.context-menu').remove(); 

//var dir_rename_hidden_id = id_.slice(11);
//alert(dir_rename_hidden_id);
var dir_hidden = $("#dir_rename_hidden_" + id_).val();
       $.ajax({
        url: 'i/mkdir.php',
        type: 'POST',
        data: {dir_remove:id_, dir_remove_hidden:dir_hidden},
        cache: false,
        dataType: 'json',
        //processData: false, // Не обрабатываем файлы (Don't process the files)
        //contentType: false, // Так jQuery скажет серверу, что это строковой запрос
        success: function(respond, textStatus, jqXHR ){
            // Если все ОК
             if( typeof respond.error === 'undefined' ){
				build_list();
            }
            else{
				$("#list_error").html("ОШИБКА: " + respond.error);
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
			$("#list_error").html('ОШИБКИ AJAX запроса: ' + textStatus );
            console.log('ОШИБКИ AJAX запроса: ' + textStatus );
        }
    });

});

//-----------------------обработчик изменения имени файла-------------------------------------------------------------------
//переходим по контекстному меню 
$(document).on("click", "#file_rename_", function(event){
event.stopPropagation();
//alert("dsffdgfdgd");
//alert($(".selected-html-element").parent(".tree_div").attr("href"));
//var curdir = $("#curdir").val();
//var id_ = $(".selected-html-element").html();
var id_ = $(target).html();
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
//показываем input для изменения названия и скрываем ссылку
$("#" + id_).css("display","none");
$("#file_rename_" + id_).css("display","block");
// Удаляем предыдущие вызванное контекстное меню:        
jQuery('.context-menu').remove(); 
});

$(document).on("keypress", ".file_rename", function(e){
   if (e.which == 13 && !e.shiftKey) {
        $(this).blur();
        return false;
    }
});

$(document).on("blur", ".file_rename", function() {
var id_ = $(target).html();
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
$("#" + id_).css("display","block");
$("#file_rename_" + id_).css("display","none");	
$('*').removeClass('selected-html-element'); 	
});


$(document).on("click", ".file_rename", function(event){
	event.stopPropagation();
});

$(document).on("change", ".file_rename", function(){
var id_ = $(target).html();
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");

var file_rename = $(this).val();
file_rename = $.trim(file_rename);
if(file_rename.length == 0){
	alert("Имя файла не может быть пустым!");
	return;
}
var file_id = $(this).attr("id");
file_id = file_id.replace(/\./g, "\\.");
file_id = file_id.replace(/\s/g, "\\ ");
var file_rename_hidden_id = file_id.slice(12);
//alert(dir_rename_hidden_id);
var file_rename_hidden = $("#file_rename_hidden_" + file_rename_hidden_id).val();
        $.ajax({
        url: 'i/mkdir.php',
        type: 'POST',
        data: {file_rename:file_rename, file_rename_hidden:file_rename_hidden},
        cache: false,
        dataType: 'json',
        //processData: false, // Не обрабатываем файлы (Don't process the files)
        //contentType: false, // Так jQuery скажет серверу, что это строковой запрос
        success: function(respond, textStatus, jqXHR ){
            // Если все ОК
             if( typeof respond.error === 'undefined' ){
				build_list();
            }
            else{
				$("#list_error").html("ОШИБКА: " + respond.error);
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
			$("#list_error").html('ОШИБКИ AJAX запроса: ' + textStatus );
            console.log('ОШИБКИ AJAX запроса: ' + textStatus );
        }
    });
});

//--------------------------------------------------------------------------------------------------------------------------

//контекстное меню файлов

//сохранить как
$(document).on("click", "#save_as_", function(){
var curdir = $("#curdir").val();
var id_ = $(target).html();
//alert(id_); return;
//var file_rename = $(this).val();
//var file_id = $(this).attr("id");
//var file_rename_hidden_id = file_id.slice(11);
var file_id = id_;
file_id = file_id.replace(/\./g, "\\.");
file_id = file_id.replace(/\s/g, "\\ ");
//alert(file_id);
var file_rename_hidden = $("#file_rename_hidden_" + file_id).val();
//'doImg.php?img=' + xhr.responseText;
document.location.href = './i/getfile.php?curdir=' + curdir + '&file_name=' + file_rename_hidden;
return;
    });

// Переменная куда будут располагаться данные файлов
 var files;
 
// Вешаем функцию на событие
// Получим данные файлов и добавим их в переменную
$('#inputfile').change(function(){
    files = this.files;
});

// Вешаем функцию на событие click и отправляем AJAX запрос с данными файлов
 
$('#upload').click(function( event ){
    event.stopPropagation(); // Остановка происходящего
    event.preventDefault();  // Полная остановка происходящего
 
 $("#inputfile").show();
 var curdir = $("#curdir").val();
    // Создадим данные формы и добавим в них данные файлов из files
 
    var data = new FormData();
	var l = 0;
    $.each( files, function( key, value ){
        data.append( key, value );
		l++;
    });
 
 if(l == 0) {
	alert("Файл не выбран!");
	return;
 }
 
 data.append("curdir", curdir);
    // Отправляем запрос
	
	$("#status_bar").html("Выполняется загрузка файла...");
 
    $.ajax({
        url: 'i/uploadfiles.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Не обрабатываем файлы (Don't process the files)
        contentType: false, // Так jQuery скажет серверу, что это строковой запрос
        success: function(respond, textStatus, jqXHR ){
			
           // Если все ОК
 
            if( typeof respond.error === 'undefined' ){
                // Файлы успешно загружены, делаем что нибудь здесь
 
                // выведем пути к загруженным файлам в блок '.ajax-respond'
 
                var files_path = respond.files;
                var html = '';
               // $.each( files_path, function( key, val ){
					//html += val +'<br>'; 
					//$('<li></li>').appendTo('#files').html(val).addClass('success');
					
				//} 
				
				//)
                //$('.ajax-respond').html( html );
				//$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
				
				
				build_list();
				
            }
            else{
				//$('<li></li>').appendTo('#files').text(respond.error).addClass('error');
				$("#status_bar").html(" ");
				$("#list_error").html('ОШИБКА: ' + respond.error );
                console.log('ОШИБКА: ' + respond.error );
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
			$("#status_bar").html(" ");
			$("#list_error").html('ОШИБКИ AJAX запроса: ' + textStatus );
            console.log('ОШИБКИ AJAX запроса: ' + textStatus );
        },
		complete: function() {
			$("#status_bar").html(" ");
			$("#inputfile").remove();
			//$("#file_span").append('<input type="file" id="inputfile" name="inputfile" multiple="multiple"/>'); // создаём новый чистый input
			$('<input type="file" id="inputfile" name="inputfile" multiple="multiple"/>').insertAfter($("#upload_show")); // создаём новый чистый input
			$("#inputfile").hide();
			$("#upload").hide();
		}
    });
 
});

//удаление файла
$(document).on("click", "#file_remove_", function(event){
event.stopPropagation();
var id_ = $(target).html();
//alert(id_);
id_ = id_.replace(/\./g, "\\.");
id_ = id_.replace(/\s/g, "\\ ");
// Удаляем предыдущие вызванное контекстное меню:        
jQuery('.context-menu').remove(); 

//var dir_rename_hidden_id = id_.slice(11);
//alert(dir_rename_hidden_id);
var file_hidden = $("#file_rename_hidden_" + id_).val();
       $.ajax({
        url: 'i/mkdir.php',
        type: 'POST',
        data: {file_remove:id_, file_remove_hidden:file_hidden},
        cache: false,
        dataType: 'json',
        //processData: false, // Не обрабатываем файлы (Don't process the files)
        //contentType: false, // Так jQuery скажет серверу, что это строковой запрос
        success: function(respond, textStatus, jqXHR ){
            // Если все ОК
             if( typeof respond.error === 'undefined' ){
				build_list();
            }
            else{
				$("#list_error").html("ОШИБКА: " + respond.error);
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
			$("#list_error").html('ОШИБКИ AJAX запроса: ' + textStatus );
            console.log('ОШИБКИ AJAX запроса: ' + textStatus );
        }
    });

});
//------------------------------------------------------------------------------------------------------------------------------------------------

// Вешаем функцию на событие click для папки и отправляем AJAX запрос с данными

$(document).on("click", "#dirname", function(event){
	event.stopPropagation();
});


$(document).on("click", "#inputfile", function(event){
	event.stopPropagation();
});
 
$('#mkdir_').click(function( event ){
    event.stopPropagation(); // Остановка происходящего
    event.preventDefault();  // Полная остановка происходящего
 
    // Создадим данные формы и добавим в них данные файлов из files
 
    var dirname = $("#dirname").val();
	var curdir = $("#curdir").val();
 
	dirname = $.trim(dirname);
	if(dirname.length == 0){
		alert("Название директории не может быть пустым!");
		return;
	}
    // Отправляем запрос
 
    $.ajax({
        url: 'i/mkdir.php',
        type: 'POST',
        data: {dirname: dirname,curdir: curdir},
        cache: false,
        dataType: 'json',
 //       processData: false, // Не обрабатываем файлы (Don't process the files)
  //      contentType: false, // Так jQuery скажет серверу, что это строковой запрос
        success: function(respond, textStatus, jqXHR ){
 
            // Если все ОК
 
            if( typeof respond.error === 'undefined' ){
				$("#dirname").val("");
				$("#dirname").hide();
				$("#mkdir_").hide();
                //директория создана, перерисовываем список
 				build_list();
            }
            else{
				$("#list_error").html('ОШИБКА: ' + respond.error );
                console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error );
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
            console.log('ОШИБКИ AJAX запроса: ' + textStatus );
        }
    });
 
});

function build_list() {
// Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":         
$('*').removeClass('selected-html-element');  

// Удаляем предыдущие вызванное контекстное меню:        
jQuery('.context-menu').remove();  
  
//Удаляем сообщения об ошибках
$("#list_error").html(" ");

var curdir = $("#curdir").val();
//alert(curdir);
//$("#listdir").html("");
 $("#list_items").find("tr:gt(0)").remove();
        $.ajax({
        url: 'i/getdirlist.php',
        type: 'POST',
        data: {curdir:curdir},
        cache: false,
        dataType: 'json',
        //processData: false, // Не обрабатываем файлы (Don't process the files)
        //contentType: false, // Так jQuery скажет серверу, что это строковой запрос
        success: function(respond, textStatus, jqXHR ){
            // Если все ОК
             if( typeof respond.error === 'undefined' ){
				//строим дерево
				//var res = JSON.parse(respond.files);
				//alert(res);
				//alert(respond.files["name"]);
				//alert(respond);
				$("#current_path").html("Текущий путь: " + respond.current_path);
				jQuery.each(respond.files, function(key, val){
				//alert(respond.files[0]["parent"]);
				//alert(val["name"]);
				//$("<tr><td>more content</td><td>more content</td></tr>").insertAfter($("tr:last"));

				var parent_str = '<tr class="color_tr"><td><img src="i/_up.png"/><a class="parent_div" onclick="javascript:void(0);return false;" href="..'+ val["path"] +'" id="' + val["name"] +'">' + val["name"] + '</a>';
				parent_str += '<td></td>';
				parent_str += '<td>Родительская<br>директория</td>';
				parent_str += '<td></td></tr>';				


				var dir_str = '<tr class="color_tr"><td><img src="i/_folder_new.png"/><a class="tree_div" onclick="javascript:void(0);return false;" href="'+ val["path"] +'" id="' + val["name"] +'">' + val["name"] + '</a>';
				dir_str += '<input class="dir_rename" type="text" name="dir_rename_' + val["name"] + '" id= "dir_rename_' + val["name"] + '" value="' + val["name"] + '" style="display:none;" size="50"/>';
				dir_str += '<input type="hidden" name="dir_rename_hidden_' + val["name"] + '" id= "dir_rename_hidden_' + val["name"] + '" value="' + val["path"] + '"/></td>';
				dir_str += '<td>' + val["last_update"] + '</td>';
				dir_str += '<td>Директория,<br>объектов ' + val["count_files"] + '</td>';
				dir_str += '<td></td></tr>';
				
				var file_str = '<tr class="color_tr"><td><a class="file_div" onclick="javascript:void(0);return false;"  href="'+ val["path"] +'" id="' + val["name"] +'">' + val["name"] + '</a>';
				file_str += '<input class="file_rename" type="text" name="file_rename_' + val["name"] + '" id= "file_rename_' + val["name"] + '" value="' + val["name"] + '" style="display:none;" size="50"/>';
				file_str += '<input type="hidden" name="file_rename_hidden_' + val["name"] + '" id= "file_rename_hidden_' + val["name"] + '" value="' + val["path"] + '"/></td>';
				file_str += '<td>' + val["last_update"] + '</td>';
				file_str += '<td>файл</td>';
				file_str += '<td>' + val["size"] + ' Кб</td></tr>';
				//$('<a class="file_div"  href="'+ val["path"] +'"><p>' + val["name"] + ', size ' + val["size"] +'</p></a>').appendTo("#listdir");

				if(val["parent"] == 1) {
					if(val["name"] == "..")
						//$('<a class="tree_div" onclick="javascript:void(0);return false;" href="..'+ val["path"] +'"><p>' + val["name"] + '</p></a>').appendTo("#listdir");
					$(parent_str).insertAfter($("tr:last"));
				}
				else if(val["dir"] == 1)
					//$('<a class="tree_div" onclick="javascript:void(0);return false;" href="'+ val["path"] +'"><p>+' + val["name"] + '</p></a><input class="dir_rename" type="text" name="dir_rename_' + val["name"] + '" id= "dir_rename_' + val["name"] + '" value="' + val["name"] + '" style="display:none;"/><input type="hidden" name="dir_rename_hidden_' + val["name"] + '" id= "dir_rename_hidden_' + val["name"] + '" value="' + val["path"] + '"/>').appendTo("#listdir");
					//$(dir_str).appendTo("#listdir");
					$(dir_str).insertAfter($("tr:last"));
				else
					//$('<a class="file_div"  href="'+ val["path"] +'"><p>' + val["name"] + ', size ' + val["size"] +'</p></a>').appendTo("#listdir");
					//$(file_str).appendTo("#listdir");
					$(file_str).insertAfter($("tr:last"));
				});

				$(".color_tr:last").css("background-color", "#90ee90");
				var tr_clr=$(".color_tr:last").css("background-color");
				$(".color_tr:last").css("background-color", "white");

				$(".color_tr").hover(function(){
				if ($(this).css("background-color")!=tr_clr)
				$(this).css("background-color", "#EEE5DE")},function(){
				if ($(this).css("background-color")!=tr_clr)
				$(this).css("background-color", "white")
				});				
 
            }
            else{
				$("#list_error").html('ОШИБКА: ' + respond.error );
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
			$("#list_error").html("невозможно получить список файлов и каталогов");
            console.log('ОШИБКИ AJAX запроса: ' + textStatus );
        }
    });
}
</script>
</body>
</html>