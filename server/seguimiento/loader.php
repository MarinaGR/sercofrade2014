<?php
{
	// Always interesting to define error_reporting level for current script
	error_reporting(E_ERROR | E_PARSE | E_WARNIG | E_NOTICE);
	// Always interesting to define the relative path to root of the current file (saves time in copy-paste chunks of code)
	$h_root_path="./../";
	// Always interesting to define a unique page id (saves problems on ajax behaviour)
	$h_page_id="geoloc_loader";
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
	$h_event_loading_status=h_function_load_event_to_track(array(
		"connection"=>$h_connection,
		"overwrite_current"=>false,
		"create_if_not_exists"=>true,
		"id"=>"via_matris_event", //unique id (no spaces, no special chars, just numbers and regular letters please...) mandatory of course
		"status"=>"1", // 1 will mean active, 0 will mean suspended,	mandatory of course		
		
		"name"=>"Via Matris", // 0 will mean no featured day for this ad
		"current_latlong"=>"40.655224,-4.702798",
		"current_timestamp"=>time(),
		"previous_latlong"=>"40.655224,-4.702798", 
		"previous_timestamp"=>time()				
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
	if ($current_user_day=='11' && $current_user_month=='3')
	{
		$h_tracking_event=h_function_recover_tracking_event(array(
			"connection"=>$h_connection,
			"id"=>"via_matris_event"						
		));
		?>
		<div style="font-family:'4';font-size:1.5em;color:#FFF;padding:10px;text-align:center;background-color:#5E3656">
			VIA MATRIS<br>
			<span style="font-size:0.5em">Salida a las 20:45h desde Convento Santa Teresa (A)</span>
		</div>
		<div style="position:relative;width:90%;margin:auto">
			<div id="ov_mapa_ruta_via_matris_1" style="position:absolute;opacity:1;width:100%;">
				<iframe style="width:100%;height:300px;border:none" seamless="seamless" src="https://maps.google.es/maps?saddr=Plaza+la+Santa,+05001+%C3%81vila&daddr=Paseo+Rastro+to:Calle+San+Segundo,+20,+%C3%81vila+to:Plaza+Catedral+to:Calle+Tom%C3%A1s+Luis+de+Victoria,+%C3%81vila+to:Plaza+Zurraqu%C3%ADn+to:Plaza+Mercado+Chico,+%C3%81vila+to:Calle+Vallesp%C3%ADn,+5,+%C3%81vila+to:Calle+Jimena+Bl%C3%A1zquez,+%C3%81vila+to:Calle+las+Dama,+%C3%81vila+to:Calle+Intendente+Aizpuru,+%C3%81vila&hl=es&ie=UTF8&sll=40.655316,-4.699778&sspn=0.005543,0.011362&geocode=FcRYbAId7Dy4_ylzbLVFGfNADTGE2zkEzEHQwg%3BFcJVbAIdvEa4_w%3BFclZbAIdy1W4_ynVzeeyBPNADTGAReJQX7nU_A%3BFUBdbAIdAVG4_w%3BFb1ebAId70y4_ymVeIyFHPNADTGZi2ueSXZnRA%3BFWpfbAIdIEm4_w%3BFS1ebAIdCUe4_ymHt5ZxHPNADTEQ8oqoJdkUlA%3BFbJebAIdAEW4_ynnE_cRHPNADTGMvOir1OFHVg%3BFUJdbAIdZkG4_ylpPdypHvNADTFkDNgqALeowg%3BFdNbbAIdtT64_ymt96qyHvNADTEJa896oAEB_Q%3BFflabAIdNj24_ymd05FLGfNADTHawBNoLqKb3A&dirflg=w&mra=ls&t=m&z=15&output=embed"></iframe>
			</div>
			<div id="ov_mapa_ruta_via_matris_1_b" style="position:absolute;opacity:0.6;width:100%;z-index:10">
				<iframe style="width:100%;height:300px;border:none" seamless="seamless" src="https://maps.google.es/maps?q=<?php echo urldecode($h_tracking_event["c7"]);?>&hl=es&ie=UTF8&ll=40.655316,-4.699778&sspn=0.005543,0.011362&dirflg=w&mra=ls&t=m&z=15&output=embed"></iframe>
			</div>
			<div style="position:absolute;opacity:0.6;width:100%;z-index:12;height:300px;opacity:0.0">
				
			</div>
		</div>
		<div style="width:90%;margin:auto;padding-top:310px;font-family:'3';font-size:0.9em">			
			- El marcador rojo (A) señala la posición de la cabecera de la procesión desde la última actualización.
			<br>
			- La última actualización se realizó a las<br> <b><?php echo date("H:i:s",$h_tracking_event["c8"]);?></b>
			<br>		
			- Puntos Clave:			
			<br>
			
			<span class="ov_span_03" id="ov_place_via_matris_1">Plaza de la Santa (A)</span>
		
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_2">Paseo del Rastro (B)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_3">Calle San Segundo (C)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_4">Plaza de la Catedral (D)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_5">Calle Tomás Luis de Victoria (E)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_6">Plaza de Zurraquín (F)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_7">Plaza del Mercado Chico (G)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_8">Calle Vallespín (H)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_9">Calle Jimena Blázquez (I)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_10">Calle Las Damas (J)</span>
			
			<br>
			<span class="ov_span_03" id="ov_place_via_matris_11">Calle del Intendente Aizpuru (K)</span>	
			
		</div>
		<?php
	}
}
?>	
</div>
</body>
</html>
