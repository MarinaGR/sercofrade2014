//Global Variables

var now = new Date().getTime();

//OJO CAMBIAR ESTO Y CONFIG
var extern_siteurl_notif="http://sercofradeavila.com/";
var extern_siteurl=extern_siteurl_notif+"index.html?app=mobile&app_ios=mobile&flag="+now; 
var extern_siteurl_op=extern_siteurl_notif+"server/functions/api.php";

var extern_web=extern_siteurl+'/server/publicidad/load_adpage.php?anuncio=';

//Get the screen and viewport size
var viewport_width=$(window).outerWidth();
var viewport_height=$(window).outerHeight();
var screen_width=screen.width;
var screen_height=screen.height; 
var start_session;


$(document).ready(function() {
	$("#contenido").height(parseInt($(window).height())-4+"px");
});

function onBodyLoad()
{	
    document.addEventListener("deviceready", onDeviceReady, false); 
	
	var fecha=getLocalStorage("fecha"); 
	if(typeof fecha == "undefined"  || fecha==null)	
	{	
		setLocalStorage("fecha", now); 
	}
	
	onOnline();
	
	var myIframe=document.getElementById('contenido');
	myIframe.addEventListener("load", function() { }, false);
	
}

function onDeviceReady()
{	
	document.addEventListener("offline", onOffline, false);
	document.addEventListener("online", onOnline, false);
	
	document.addEventListener("backbutton", onBackKeyDown, false);
	document.addEventListener("menubutton", onMenuKeyDown, false);
		
	var start_session=getSessionStorage("start_session"); 
	if(typeof start_session == "undefined" || start_session==null)	
	{	
		var nueva_fecha=parseInt(getLocalStorage("fecha"))+1000*60*60*24*2; //2 días
		
		if(now>nueva_fecha) //cada 2 días limpia cache
		{
			window.cache.clear(function(status) {}, function(status) {});
			setLocalStorage("fecha", now);
		}
		getSessionStorage("start_session", "inicio");
	}

	check_internet();			
	
}

function onBackKeyDown()
{
	var myIframe=document.getElementById('contenido');
	if((myIframe.contentWindow.document.location.href).indexOf("ov_view_main_menu.html")!=-1 || ($("#contenido").attr("src")).indexOf("offline.html")!=-1)
	{		
		navigator.app.exitApp();
		return false;
	}
	window.history.back();
}

function onMenuKeyDown()
{
	//window.location.href='index.html';
}

function onOnline()
{
	setTimeout(function(){
		$("#contenido").attr("src",extern_siteurl+"/index.html?now="+now);
	},250);
	
	/*var networkState = navigator.connection.type;

    var states = {};
    states[Connection.UNKNOWN]  = 'Unknown connection';
    states[Connection.ETHERNET] = 'Ethernet connection';
    states[Connection.WIFI]     = 'WiFi connection';
    states[Connection.CELL_2G]  = 'Cell 2G connection';
    states[Connection.CELL_3G]  = 'Cell 3G connection';
    states[Connection.CELL_4G]  = 'Cell 4G connection';
    states[Connection.CELL]     = 'Cell generic connection';
    states[Connection.NONE]     = 'No network connection';

    alert('Conexión: ' + states[networkState]);*/

}
function onOffline()
{
	setTimeout(function(){
		$("#contenido").attr("src","offline.html");
	},250);

}

function check_internet(){

	var isOffline = 'onLine' in navigator && !navigator.onLine;

	if(isOffline) 
	{		
		setTimeout(function(){
			$("#contenido").attr("src","offline.html");				
		},250);
	}
	else 
	{
		if(typeof $("#contenido").attr("src") == "undefined")
		{			
			setTimeout(function(){
				$("#contenido").attr("src",extern_siteurl+"/index.html?now="+now);
			},250);
			
			/*NOTIFICACIONES
			
			var values="date="+getLocalStorage("fecha");
			ajax_operation_cross(values,"ov_get_notifications");
			
			//CADA 6 HORAS
			setInterval(function(){
				var values2="date="+getLocalStorage("fecha");
				ajax_operation_cross(values2,"ov_get_notifications");
			},6*60*60*1000);  //cada minuto: 1min*60seg*1000; cada 24 horas: 24*60*60*1000
			
			*/
		}		
	}

}

function get_var_url(variable){

	var tipo=typeof variable;
	var direccion=location.href;
	var posicion=direccion.indexOf("?");
	
	posicion=direccion.indexOf(variable,posicion) + variable.length; 
	
	if (direccion.charAt(posicion)== "=")
	{ 
        var fin=direccion.indexOf("&",posicion); 
        if(fin==-1)
        	fin=direccion.length;
        	
        return direccion.substring(posicion+1, fin); 
    } 
	else
		return false;
	
}

function setLocalStorage(keyinput,valinput) 
{
	if(typeof(window.localStorage) != 'undefined') { 
		window.localStorage.setItem(keyinput,valinput); 
	} 
	else { 
		alert("localStorage no definido"); 
	}
}
function getLocalStorage(keyoutput)
{
	if(typeof(window.localStorage) != 'undefined') { 
		return window.localStorage.getItem(keyoutput); 
	} 
	else { 
		alert("localStorage no definido"); 
	}
}
function setSessionStorage(keyinput,valinput)
{
	if(typeof(window.sessionStorage) != 'undefined') { 
		window.sessionStorage.setItem(keyinput,valinput); 
	} 
	else { 
		alert("sessionStorage no definido"); 
	}
}
function getSessionStorage(keyoutput)
{
	if(typeof(window.sessionStorage) != 'undefined') { 
		return window.sessionStorage.getItem(keyoutput); 
	} 
	else { 
		alert("sessionStorage no definido"); 
	}
}