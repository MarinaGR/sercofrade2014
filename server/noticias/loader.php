<?php
{
	// Always interesting to define error_reporting level for current script
	error_reporting(E_ERROR | E_PARSE | E_WARNIG | E_NOTICE);
	// Always interesting to define the relative path to root of the current file (saves time in copy-paste chunks of code)
	$h_root_path="./../";
	// Always interesting to define a unique page id (saves problems on ajax behaviour)
	$h_page_id="news_loader";
	// Always interesting to catch browser accepted language
	$h_browser_language=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}

{
	// Load hoopale php functions file (if you do not include this file, hoopale will not work properly)
	// Always interesting to check if file exists before including it.
	if(file_exists($h_root_path."functions/h_functions_php.php"))
	{
		include($h_root_path."functions/h_functions_php.php");
	}
	else
	{
		// If the functions file cannot be found, do whatever you want here, this is only a default error exit call
		// (For public web sites this not very pretty)
		exit("[h_error_php_functions_file_not_found]");
	}
}

{
	//If we need a db_connection, we can invoke h_function_get_db_connection
	//If we provide connection values parameters, the connection file will be created, so again write permissions...
	$h_connection=h_function_get_db_connection(array(
		"relative_to_root_url_to_redirect_on_fail"=>$h_root_path."publicidad/default.php",
		"connection_values"=>array("host"=>"127.0.0.1","user"=>"cofrade","password"=>"12345_ser","dbname"=>"cofrade","port"=>"","socket"=>""),
		"overwrite_current_connection_file"=>false,
		"connection_file_url"=>$h_root_path."/configuration/h_db_connection.conf",
		"encrypt_seed"=>array(array("A","!*!"),array("1","-*-"),array("O","***"),array("3","*?*"),array("z","*+*"))
	));
	if(!$h_connection)
	{
		//do whatever you want here, this is only a default error exit call
		
		exit("[h_error_connecting_to_db]");
	}
}

{
	//load a new in db
	$h_new_loading_status=h_function_load_new(array(
		"connection"=>$h_connection,
		"overwrite_current"=>true,
		"create_if_not_exists"=>true,
		"id"=>"news_1", //unique id (no spaces, no special chars, just numbers and regular letters please...) mandatory of course
		"status"=>"1", // 1 will mean active, 0 will mean suspended,	mandatory of course		
		"day"=>"17", 
		"month"=>"MAR",
		"hour"=>"19:00", // mandatory of course
		"title"=>"Quedan 25 días ",
		"text"=>"Ya sólo restan 25 días para el comienzo de los actos de la Semana Santa 2014"						
	));
	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>SER COFRADE 2014 - Guía Semana Santa - Cadena SER Ávila</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, maximum-scale=3.0, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
<meta name="robots" content="NOINDEX,NOFOLLOW,NOARCHIVE,NOODP,NOSNIPPET">
<meta name="description" content="SER COFRADE 2014, La guía de información para la Semana Santa Ávila 2014. Procesiones, Cofradías, Horarios, Recorridos, Información...">
<META name="CACHE-CONTROL" content="NO-CACHE">
<META name="EXPIRES" content="-1">
<link id="ov_style_link_01" href="./../../styles/styles_01_complete.css" rel="stylesheet" type="text/css">	
</head>
<body style="margin:0px;background-color:#FFF">
<div>
<?php
{
	//show the last 10 news
	h_function_show_news(array(
		"connection"=>$h_connection,
		"max_number"=>"10",
		"paginate_type"=>"none",
		"classes"=>array()
	));
}
?>	
</div>
</body>
</html>
