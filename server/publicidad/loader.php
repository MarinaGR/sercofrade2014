<?php
{
	// Always interesting to define error_reporting level for current script
	error_reporting(E_ERROR | E_PARSE | E_WARNIG | E_NOTICE);
	// Always interesting to define the relative path to root of the current file (saves time in copy-paste chunks of code)
	$h_root_path="./../";
	// Always interesting to define a unique page id (saves problems on ajax behaviour)
	$h_page_id="ads_loader";
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
	//Here we recover POST parameters (day and month)
	$current_user_day=$_GET["day"];
	$current_user_month=$_GET["month"];
	if($current_user_month!="3")
	{
		$out_of_key_dates=true;
	}
	elseif($current_user_day<="11" || $current_user_day>"20")
	{
		$out_of_key_dates=true;
	}
	else
	{
		$out_of_key_dates=false;
		$current_key_day=$current_user_day;
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
	//load and ad in db
	$h_ad_loading_status=h_function_load_add(array(
		"connection"=>$h_connection,
		"overwrite_current"=>false,
		"create_if_not_exists"=>true,
		"id"=>"ovnyline_ad", //unique id (no spaces, no special chars, just numbers and regular letters please...) mandatory of course
		"status"=>"1", // 1 will mean active, 0 will mean suspended,	mandatory of course		
		"featured_day"=>"0", // 0 will mean no featured day for this ad
		"number_of_impressions"=>"0",
		"destination_url"=>"www.ovnyline.com", // mandatory of course
		"small_image_route"=>"publicidad/banners/small/ovnyline.jpg",
		"big_image_route"=>"no",
		"number_of_visits"=>"0",
		"phone"=>"920038266",
		"email"=>"info@ovnyline.com",
		"address"=>"Calle Teso del Hospital Viejo, 10, Ávila",
		"latlong"=>"40.653881,-4.70389",
		"allow_geolocation"=>"0", // 0 will mean yes, no 1 will mean no
		"show_map"=>"0", // 0 will mean yes, no 1 will mean no				
	));
	//load and ad in db
	$h_ad_loading_status=h_function_load_add(array(
		"connection"=>$h_connection,
		"overwrite_current"=>false,
		"create_if_not_exists"=>true,
		"id"=>"cadenaser_ad", //unique id (no spaces, no special chars, just numbers and regular letters please...) mandatory of course
		"status"=>"1", // 1 will mean active, 0 will mean suspended,	mandatory of course		
		"featured_day"=>"0", // 0 will mean no featured day for this ad
		"number_of_impressions"=>"0",
		"destination_url"=>"www.cadenaser.com/emisoras/avila/", // mandatory of course
		"small_image_route"=>"publicidad/banners/small/cadenaser.jpg",
		"big_image_route"=>"no",
		"number_of_visits"=>"0",
		"phone"=>"920352428",
		"email"=>"avila@cadenaser.com",
		"address"=>"Calle Duque de Alba, 6, Ávila",
		"latlong"=>"40.655248,-4.693869",
		"allow_geolocation"=>"0", // 0 will mean yes, no 1 will mean no
		"show_map"=>"0", // 0 will mean yes, no 1 will mean no				
	));
	//load and ad in db
	$h_ad_loading_status=h_function_load_add(array(
		"connection"=>$h_connection,
		"overwrite_current"=>false,
		"create_if_not_exists"=>true,
		"id"=>"junta_ad", //unique id (no spaces, no special chars, just numbers and regular letters please...) mandatory of course
		"status"=>"1", // 1 will mean active, 0 will mean suspended,	mandatory of course		
		"featured_day"=>"0", // 0 will mean no featured day for this ad
		"number_of_impressions"=>"0",
		"destination_url"=>"www.ideapublicidad.com", // mandatory of course
		"small_image_route"=>"publicidad/banners/small/idea.jpg",
		"big_image_route"=>"no",
		"number_of_visits"=>"0",
		"phone"=>"920228382",
		"email"=>" info@ideapublicidad.com",
		"address"=>"Calle Bilbao 13, Ávila",
		"latlong"=>"40.652041,-4.68932",
		"allow_geolocation"=>"0", // 0 will mean yes, no 1 will mean no
		"show_map"=>"0", // 0 will mean yes, no 1 will mean no				
	));
}

{
	//recover a random ad to show
	$h_random_ad_recover_data=h_function_recover_random_add(array(
		"connection"=>$h_connection,
		"current_day"=>$current_user_day,
		"out_of_key_dates"=>$out_of_key_dates,
		"log_view"=>"0", // 0 will mean yes, no 1 will mean no	
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
</head>
<body style="margin:0px">
<div onclick="window.open('http://<?php echo urldecode($h_random_ad_recover_data["c8"]);?>')" style="cursor:pointer;width:100%;height:60px;max-height:60px;overflow:hidden;background-image:url(<?php echo $h_root_path.urldecode($h_random_ad_recover_data["c9"]);?>);background-repeat:no-repeat;background-position:center center">
	
</div>
</body>
</html>
