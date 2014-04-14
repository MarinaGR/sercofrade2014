//Global Variables

var current_view="ov_view_container_01";
var current_url="./index.html";

var previous_view="ov_view_container_01";
var previous_url="./index.html";

var current_view_background_color="#6B135B";
var current_view_background_image="url(./resources/general_images/loading_background.jpg)";

var prev_view_background_color="#6B135B";
var prev_view_background_image="url(./resources/general_images/loading_background.jpg)";


var referrer_from_herman="";
var referrer_from_proce="";
var selected_day_in_proce_list="none";

var destination="";
	
var route_map_url="";
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

var publi_url='http://sercofradeavila.com/server/publicidad/loader.php?day='+current_day_of_month+'&month='+current_month;

function show_geoloc(dest)
{
	destination=dest;	
	$("#geoloc_map_container").show();
	$('body,html').scrollTop($("#geoloc_map_container").offset().top);
	
	if (navigator.geolocation)
	{
		navigator.geolocation.getCurrentPosition(draw_geoloc,error_geoloc,{enableHighAccuracy:true, maximumAge:30000, timeout:30000});
	}
	else
	{
		$("#geoloc_map_text").html("Tu dispositivo no permite la geolocalización dinámica.");			
	}
}

function draw_geoloc(position)
{
	var latitude = position.coords.latitude;
  	var longitude = position.coords.longitude;
  	var latlong = latitude+","+longitude;
  	var url="https://www.google.com/maps/embed/v1/directions?key=AIzaSyAD0H1_lbHwk3jMUzjVeORmISbIP34XtzU&origin="+latlong+"&destination="+destination+"&avoid=tolls|highways&mode=walking&language=es";
  	$("#geoloc_map").attr("src",url);
  	$("#geoloc_map_text").html("Ruta desde tu posición actual hasta "+destination);	
}

function error_geoloc(error)
{
	$("#geoloc_map_text").html("La geolocalización ha fallado.");	
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

function onBodyLoad()
{
    document.addEventListener("deviceready", onDeviceReady, false);
    
    $('#ov_curtain').hide();
    
    $("#ads_iframe_1").attr('src', publi_url);    
    
    $('#ov_view_container_01').css("min-height",(viewport_height-60)+"px");
		
	$('#ov_view_container_02').css("min-height",(viewport_height-60)+"px");
	
	load_view({view_to_load: 'ov_view_container_01', url_to_load: './views/general/ov_view_loading.html', view_background_color: '#6B135B', view_background_image: 'url(./resources/general_images/loading_background.jpg)', view_to_hide: 'ov_view_container_02' });	
			
}

function load_view(options)
{
		/*if(options.url_to_load=="./index.html")
		{
			window.location.href=options.url_to_load;
			return false;
		}*/
		if(current_url=="./views/general/ov_view_main_menu.html")
		{
			navigator.app.exitApp();
			return false;
		}
			
		$('#'+options.view_to_load).load(options.url_to_load+"?"+Math.random(),function(){
			
				previous_view=options.view_to_hide;
				previous_url=current_url;
				prev_view_background_color=current_view_background_color;
				prev_view_background_image=current_view_background_image;
				current_view=options.view_to_load;
				current_url=options.url_to_load;			
				current_view_background_color=options.view_background_color;
				current_view_background_image=options.view_background_image;

				
				$('#'+options.view_to_load).css("background-color",options.view_background_color);
				
				$('#'+options.view_to_load).css("background-image",options.view_background_image);
				
				$('.ov_back_button_container').hide();
				
				$('#'+options.view_to_load).show('slide',{direction: "right", easing: "linear"},500,function(){
					
					$('body,html').scrollTop("0px");
					
					$('.ov_back_button_container').show();
					
					$('#'+options.view_to_hide).html('');
					
				});
				
				$('#'+options.view_to_hide).hide('slide',{direction: "left", easing: "linear"},500,function(){
					
					
					
				});								
		});
	
}

function onDeviceReady()
{
	document.addEventListener("backbutton", onBackKeyDown, false);
	document.addEventListener("menubutton", onMenuKeyDown, false);

}
    
function onBackKeyDown()
{
	
	load_view({view_to_load: previous_view, url_to_load: previous_url, view_background_color: prev_view_background_color, view_background_image: prev_view_background_image, view_to_hide: current_view });	
	
}

function onMenuKeyDown()
{
	load_view({view_to_load: 'ov_view_container_02', url_to_load: './views/general/ov_view_main_menu.html', view_background_color: view_background_image, view_background_image: prev_view_background_image, view_to_hide: previous_view });
}
