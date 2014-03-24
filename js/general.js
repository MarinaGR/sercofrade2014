	//Global Variables
	var referrer_from_herman="";
	var referrer_from_proce="";
	var selected_day_in_proce_list="none";
	var deployed=true;
	var destination="";
	var destination_decoded="";
	var destination_zoom="";
	var destination_center="";
	var destination_container="";
	var destination_big_container="";	
	var current_url="";
	//Get current_date
	var current_date=new Date();
	var current_day_of_month=current_date.getDate();
	var current_month=current_date.getMonth();
	var current_year=current_date.getFullYear();
	//Get the screen and viewport size
	var viewport_width=$(window).outerWidth();
	var viewport_height=$(window).outerHeight();
	var screen_width=screen.width;
	var screen_height=screen.height;
	
	
	/*   
	$(window).resize(function(){		
	 	
	 	viewport_width=$(window).outerWidth();
		viewport_height=$(window).outerHeight();
		screen_width=screen.width;
		screen_height=screen.height;
		
		$('#ov_view_container_01').css("min-height",viewport_height+"px");
		$('#ov_view_container_02').css("min-height",viewport_height+"px");
		
		$("#ov_zone_09_1").css("height",(viewport_height-60)+"px");
		$("#ov_box_06_1").css("height",(viewport_height-60)+"px");
		$("#ov_box_07_1").css("height",(viewport_height-60)+"px");
		$("#ov_box_07_1").css("overflow","auto");
		
		$("#ov_zone_15_1").css("height",(viewport_height-60)+"px");
		$("#ov_zone_15_2").css("height",(viewport_height-60)+"px");
		
		$("#ov_zone_12_1").css("height",(viewport_height-250)+"px");
		$("#ov_zone_12_1").css("overflow","auto");
		
		$(".ov_box_02_b").css("width","100%");
		$(".ov_box_02_b").css("overflow","auto");		
		
	});
	*/
	
	function onBodyLoad() {
        document.addEventListener("deviceready", onDeviceReady, false);
    }
    
    function onDeviceReady() {
        document.addEventListener("backbutton", onBackKeyDown, false);
		document.addEventListener("menubutton", onMenuKeyDown, false);

    }
    
	function onBackKeyDown() {
    }
    function onMenuKeyDown() {
    }


	function load_ads(container)
	{
		var url="http://sercofradeavila.com/server/publicidad/loader.php?day="+current_day_of_month+"&month="+current_month;
		$("#"+container).html('<iframe style="margin:0px;width:100%;height:60px;border:none;overflow:hidden;" seamless="seamless" src="'+url+'"></iframe>');		    
	}
	
	function load_offers(container)
	{
		var url="http://sercofradeavila.com/server/publicidad/loader_offer.php?day="+current_day_of_month+"&month="+current_month;
		$("#"+container).html('<iframe style="margin:0px;width:100%;height:2500px;border:none;overflow:hidden;" seamless="seamless" src="'+url+'"></iframe>');
	}
	
	function load_geoloc(container)
	{
		var url="http://sercofradeavila.com/server/seguimiento/loader.php?day="+current_day_of_month+"&month="+current_month;
	  	$("#"+container).html('<iframe style="margin:0px;width:100%;height:2500px;border:none;overflow:hidden;" seamless="seamless" src="'+url+'"></iframe>');
	}
	
	function load_news(container)
	{
		var url="http://sercofradeavila.com/server/noticias/loader.php";
		$("#"+container).html('<iframe style="margin:0px;width:100%;height:2500px;border:none;overflow:hidden;" seamless="seamless" src="'+url+'"></iframe>');
	}
	
	function calculate_day()
	{
		if(selected_day_in_proce_list=="none")
		{
			if(current_month!=3)
			{
				$(".ov_box_10_b").attr("class","ov_box_10");
				$("#ov_box_10_11_abr").attr("class","ov_box_10_b");
				$(".ov_vertical_space_03_b").attr("class","ov_vertical_space_03");
				$("#ov_vertical_space_03_11_abr").attr("class","ov_vertical_space_03_b");
				$(".ov_zone_13_b").attr("class","ov_zone_13");
				$("#ov_zone_13_11_abr").attr("class","ov_zone_13_b");
				selected_day_in_proce_list=11;
			}
			else if(current_day_of_month<=11 || current_day_of_month>20)
			{
				$(".ov_box_10_b").attr("class","ov_box_10");
				$("#ov_box_10_11_abr").attr("class","ov_box_10_b");
				$(".ov_vertical_space_03_b").attr("class","ov_vertical_space_03");
				$("#ov_vertical_space_03_11_abr").attr("class","ov_vertical_space_03_b");
				$(".ov_zone_13_b").attr("class","ov_zone_13");
				$("#ov_zone_13_11_abr").attr("class","ov_zone_13_b");
				selected_day_in_proce_list=11;
			}
			else
			{
				$(".ov_box_10_b").attr("class","ov_box_10");
				$("#ov_box_10_"+current_day_of_month+"_abr").attr("class","ov_box_10_b");
				$(".ov_vertical_space_03_b").attr("class","ov_vertical_space_03");
				$("#ov_vertical_space_03_"+current_day_of_month+"_abr").attr("class","ov_vertical_space_03_b");
				$(".ov_zone_13_b").attr("class","ov_zone_13");
				$("#ov_zone_13_"+current_day_of_month+"_abr").attr("class","ov_zone_13_b");
				selected_day_in_proce_list=11;
			}				
		}
		else
		{
			$(".ov_box_10_b").attr("class","ov_box_10");
			$("#ov_box_10_"+selected_day_in_proce_list+"_abr").attr("class","ov_box_10_b");
			$(".ov_vertical_space_03_b").attr("class","ov_vertical_space_03");
			$("#ov_vertical_space_03_"+selected_day_in_proce_list+"_abr").attr("class","ov_vertical_space_03_b");
			$(".ov_zone_13_b").attr("class","ov_zone_13");
			$("#ov_zone_13_"+selected_day_in_proce_list+"_abr").attr("class","ov_zone_13_b");
		}
	}
	
	function draw_map(url,container)
	{
		 $("#"+container).attr('src', url);
	}
	
	function show_route_2(dest,zoom,center,container,big_container)
	{
		$("#"+big_container).show();
		destination=dest;		
		destination_zoom=zoom;
		destination_center=center;
		destination_container=container;
		destination_big_container=big_container;
							
		if (navigator.geolocation)
		{
			alert(destination);
			
			navigator.geolocation.getCurrentPosition(show_position_2,error_position_2,{enableHighAccuracy:true, maximumAge:30000, timeout:27000});
		}
		else
		{
			alert("No geolocalizacion");
			
			$("#"+container).html('<div class="ov_text_18"><br>Lo sentimos, pero tu dispositivo no permite geolocalización.</div>');			
		}
	}
	
	function show_position_2(position)
	{
		alert("GEOLOCALIZACION");
		alert(position);
		
		var latitude = position.coords.latitude;
  		var longitude = position.coords.longitude;
  		var latlong = latitude+","+longitude;
  		var url="https://www.google.com/maps/embed/v1/directions?key=AIzaSyAD0H1_lbHwk3jMUzjVeORmISbIP34XtzU&origin="+latlong+"&destination="+destination+"&avoid=tolls|highways&mode=walking&language=es";
  		
  		alert(latlong);
  		alert(destination);
  		alert(destination_container);
  		
  		$("#"+destination_container).attr('src', url);
				
		$("#"+destination_big_container+"_text").html("Ruta desde tu posición actual hasta "+destination);
	}
	
	function error_position_2(error)
	{
		alert("Fallo en la geolocalización");
		
		$("#"+destination_container).html('<div class="ov_text_18"><br>La geolocalización de tu posición ha fallado.</div>');		
	}
