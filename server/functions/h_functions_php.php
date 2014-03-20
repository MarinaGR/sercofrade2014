<?php
//Change this value for your own array to prevent security issues
$h_default_encrypt_seed=array(array("A","!*!"),array("1","-*-"),array("O","***"),array("3","*?*"),array("z","*+*"));

function h_function_redirect_to_canonical($params)
{
	switch ($params["behaviour"])
	{
		case 'www_to_nonwww':
			if (substr($_SERVER["HTTP_HOST"], 0, 4) === 'www.')
			{
				$corrected_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
				if ($_SERVER["SERVER_PORT"] != "80")
				{
				    $corrected_url .= substr($_SERVER['SERVER_NAME'], 4).":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				} 
				else 
				{
				    $corrected_url .= substr($_SERVER['SERVER_NAME'], 4).$_SERVER["REQUEST_URI"];
				}
				header($_SERVER["SERVER_PROTOCOL"].' 301 Moved Permanently');
				header('Location: '.$corrected_url);
				exit();
			}
		break;
		case 'nonwww_to_www':
			if (substr($_SERVER["HTTP_HOST"], 0, 4) != 'www.')
			{
				$corrected_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
				if ($_SERVER["SERVER_PORT"] != "80")
				{
				    $corrected_url .= "www.".$_SERVER['SERVER_NAME'].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				} 
				else 
				{
				    $corrected_url .= "www.".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
				}
				header($_SERVER["SERVER_PROTOCOL"].' 301 Moved Permanently');
				header('Location: '.$corrected_url);
				exit();
			}
		break;
		
		default:
			
		break;
	}	
	
}

function h_function_check_session($params)
{		
	if(!session_start())
	{
		$url_not_found=false;
				
		if(isset($params["relative_to_root_url_to_redirect_on_fail"]) || $params["relative_to_root_url_to_redirect_on_fail"]!="")
		{
			if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
			{
				$url_not_found=true;
			}
			header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
			exit();
		}		
		if(!isset($params["relative_to_root_url_to_redirect_on_fail"]) || $params["relative_to_root_url_to_redirect_on_fail"]=="" || $url_not_found)
		{
			return false;
		}
	}
	
	return true;
}

function h_function_set_session_vars($params)
{
	if($params["override_current_values"])
	{
		foreach($params["vars"] as $var)
		{
			$_SESSION[$var[0]]=$var[1];
		}
	}
	else
	{
		foreach($params["vars"] as $var)
		{
			if($_SESSION[$var[0]]=="" || !isset($_SESSION[$var[0]]))
			{
				$_SESSION[$var[0]]=$var[1];
			}
		}
	}
}

function h_function_set_languages($params)
{
	if($params["overwrite_current"] || !file_exists($params["languages_file_url"]))
	{
		$languages_file_content="";
		foreach($params["languages"] as $language)
		{
			$languages_file_content.=$language[0].";".$language[1].";".$language[2].";".$language[3]."**";
		}
		$languages_file_content=rtrim($languages_file_content,"**");
		
		$file_result=file_put_contents($params["languages_file_url"],$languages_file_content,LOCK_EX);
		if(!$file_result)
		{
			return false;			
		}
		return true;
	}
	
	return true;
	
}

function h_function_check_and_set_language($params)
{
	if(!file_exists($params["languages_file_url"]))
	{
		return false;
	}
	$languages_file_content=file_get_contents($params["languages_file_url"]);
	$languages_exploded=explode("**",$languages_file_content);
	foreach($languages_exploded as $lang)
	{
		$lang_values=explode(";",$lang);
		if($lang_values[0]==$params["language_to_check"])
		{
			if($lang_values[3]=="active")
			{
				return $lang_values[0];
			}
		}
		if($lang_values[2]=="default")
		{
			$default_lang=$lang_values[0];
		}
	}
	if(!isset($default_lang))
	{
		return false;
	}
	
	return $default_lang;
}

function h_function_get_db_connection($params)
{
	{	
		if(!isset($params["first_to_try"]))
		{
			$first="regular";
		}
		else
		{
			$first=$params["first_to_try"];
		}	
				
		if(isset($params["alternative_connection_values"]))
		{
				$host_a=$params["alternative_connection_values"]["host"];
				$user_a=$params["alternative_connection_values"]["user"];
				$password_a=$params["alternative_connection_values"]["password"];
				$dbname_a=$params["alternative_connection_values"]["dbname"];
				$port_a=$params["alternative_connection_values"]["port"];
				$socket_a=$params["alternative_connection_values"]["socket"];
				$alternative=true;
				$a_connection_string=$host_a.";".$user_a.";".$password_a.";".$dbname_a.";".$port_a.";".$socket_a;
		}
		else
		{
			$alternative=false;
			$a_connection_string="";
		}
		if(isset($params["connection_values"]))
		{
				$host=$params["connection_values"]["host"];
				$user=$params["connection_values"]["user"];
				$password=$params["connection_values"]["password"];
				$dbname=$params["connection_values"]["dbname"];
				$port=$params["connection_values"]["port"];
				$socket=$params["connection_values"]["socket"];
				$connection_string=$host.";".$user.";".$password.";".$dbname.";".$port.";".$socket;
				$regular=true;
		}
		else
		{
			$regular=false;
			$connection_string="";
		}		
	}

	{	
		if($params["overwrite_current_connection_file"] || !file_exists($params["connection_file_url"]))
		{
			if(!$regular)
			{
				if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
				{
					return false;
				}
				header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
				exit();	
				
			}
			if($first=="alternative" && !$alternative)
			{
				if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
				{
					return false;
				}
				header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
				exit();	
			}
			if($first=="alternative" && $alternative)
			{					
				if($port_a=="" && $socket_a=="")
				{
					$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a,$socket_a);
				}
				elseif($port_a!="" && $socket_a=="")
				{
					$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a);
				}
				else
				{
					$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a);
				}
				if ($connection->connect_errno)
				{
					$connection_stat=false;
				}
				else
				{
					$connection_stat=true;
				}
					
				if(!$connection_stat)
				{
					if($port=="" && $socket=="")
					{
						$connection=new mysqli($host,$user,$password,$dbname,(int)$port,$socket);
					}
					elseif($port!="" && $socket=="")
					{
						$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
					}
					else
					{
						$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
					}
					if ($connection->connect_errno)
					{
						$connection_stat=false;
					}
					else
					{
						$connection_stat=true;
					}
				}
				
				if(!$connection_stat)
				{
					if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
					{
						return false;
					}
					header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
					exit();	
				}			
			}		
			if($first=="regular" && $alternative)
			{
				if($port=="" && $socket="")
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port,$socket);
				}
				elseif($port!="" && $socket="")
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
				}
				else
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
				}
				if ($connection->connect_errno)
				{
					$connection_stat=false;
				}
				else
				{
					$connection_stat=true;
				}			
				
				if(!$connection_stat)
				{
					if($port_a=="" && $socket_a="")
					{
						$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a,$socket_a);
					}
					elseif($port_a!="" && $socket_a="")
					{
						$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a);
					}
					else
					{
						$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a);
					}
					if ($connection->connect_errno)
					{
						$connection_stat=false;
					}
					else
					{
						$connection_stat=true;
					}				
				}
				
				if(!$connection_stat)
				{
					if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
					{
						return false;
					}
					header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
					exit();
				}			
			}
			if($first=="regular" && !$alternative)
			{
				if($port=="" && $socket="")
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port,$socket);
				}
				elseif($port!="" && $socket="")
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
				}
				else
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
				}
				if ($connection->connect_errno)
				{
					$connection_stat=false;
				}
				else
				{
					$connection_stat=true;
				}			
				
				if(!$connection_stat)
				{
					if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
					{
						return false;
					}
					header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
					exit();	
				}			
			}
			
			$connection_file_contents=$a_connection_string."**".$connection_string."**".$first;
			if(!isset($params["encrypt_seed"]))
			{
				$params["encrypt_seed"]=$h_default_encrypt_seed;
			}
			$connection_file_contents_encoded=h_encrypt_decrypt_string(array(
				"mode"=>"encrypt",
				"string"=>$connection_file_contents,
				"seed"=>$params["encrypt_seed"]
			));
			
			$file_result=file_put_contents($params["connection_file_url"],$connection_file_contents_encoded,LOCK_EX);
			if(!$file_result)
			{
				if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
				{
					return false;
				}
				header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
				exit();	
			}
			
			return $connection;		
		}
	}

	{
		$connection_file_contents=file_get_contents($params["connection_file_url"]);
		if(!isset($params["encrypt_seed"]))
		{
			$params["encrypt_seed"]=$h_default_encrypt_seed;
		}
		$connection_file_contents_decoded=h_encrypt_decrypt_string(array(
			"mode"=>"decrypt",
			"string"=>$connection_file_contents,
			"seed"=>$params["encrypt_seed"]
		));
				
		$connection_exploded_values=explode("**",$connection_file_contents_decoded);
		$alternative_values=$connection_exploded_values[0];
		$regular_values=$connection_exploded_values[1];
		$first=$connection_exploded_values[2];
			
		$alternative=false;
		if($alternative_values!="")
		{
			$alternative_exploded_values=explode(";",$alternative_values);
			$host_a=$alternative_exploded_values[0];
			$user_a=$alternative_exploded_values[1];
			$password_a=$alternative_exploded_values[2];
			$dbname_a=$alternative_exploded_values[3];
			$port_a=$alternative_exploded_values[4];
			$socket_a=$alternative_exploded_values[5];
			$alternative=true;		
		}
		$regular=false;
		if($regular_values!="")
		{
			$regular_exploded_values=explode(";",$regular_values);
			$host=$regular_exploded_values[0];
			$user=$regular_exploded_values[1];
			$password=$regular_exploded_values[2];
			$dbname=$regular_exploded_values[3];
			$port=$regular_exploded_values[4];
			$socket=$regular_exploded_values[5];
			$regular=true;		
		}
			
		if(!$regular)
		{
			if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
			{
					return false;
			}
			header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
			exit();			
		}
		if($first=="alternative" && !$alternative)
		{
			if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
			{
				return false;
			}
			header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
			exit();		
		}
		if($first=="alternative" && $alternative)
		{					
			if($port_a=="" && $socket_a=="")
			{
				$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a,$socket_a);
			}
			elseif($port_a!="" && $socket_a=="")
			{
				$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a);
			}
			else
			{
				$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a);
			}
			if ($connection->connect_errno)
			{
				$connection_stat=false;
			}
			else
			{
				$connection_stat=true;
			}
						
			if(!$connection_stat)
			{
				if($port=="" && $socket=="")
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port,$socket);
				}
				elseif($port!="" && $socket=="")
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
				}
				else
				{
					$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
				}
				if ($connection->connect_errno)
				{
					$connection_stat=false;
				}
				else
				{
					$connection_stat=true;
				}
			}
				
			if(!$connection_stat)
			{
				if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
				{
					return false;
				}
				header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
				exit();
			}			
		}		
		if($first=="regular" && $alternative)
		{
			if($port=="" && $socket="")
			{
				$connection=new mysqli($host,$user,$password,$dbname,(int)$port,$socket);
			}
			elseif($port!="" && $socket="")
			{
				$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
			}
			else
			{
				$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
			}
			if ($connection->connect_errno)
			{
				$connection_stat=false;
			}
			else
			{
				$connection_stat=true;
			}			
			
			if(!$connection_stat)
			{
				if($port_a=="" && $socket_a="")
				{
					$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a,$socket_a);
				}
				elseif($port_a!="" && $socket_a="")
				{
					$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a);
				}
				else
				{
					$connection=new mysqli($host_a,$user_a,$password_a,$dbname_a,(int)$port_a);
				}
				if ($connection->connect_errno)
				{
					$connection_stat=false;
				}
				else
				{
					$connection_stat=true;
				}				
			}
				
			if(!$connection_stat)
			{
				if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
				{
					return false;
				}
				header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
				exit();
			}			
		}
		if($first=="regular" && !$alternative)
		{
			if($port=="" && $socket="")
			{
				$connection=new mysqli($host,$user,$password,$dbname,(int)$port,$socket);
			}
			elseif($port!="" && $socket="")
			{
				$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
			}
			else
			{
				$connection=new mysqli($host,$user,$password,$dbname,(int)$port);
			}
			if ($connection->connect_errno)
			{
				$connection_stat=false;
			}
			else
			{
				$connection_stat=true;
			}			
			
			if(!$connection_stat)
			{
				if(!file_exists($params["relative_to_root_url_to_redirect_on_fail"]))
				{
					return false;
				}
				header("Location: ".$params["relative_to_root_url_to_redirect_on_fail"]);
				exit();
			}			
		}		
		
		return $connection;
	}	
}

function h_encrypt_decrypt_string($params)
{
	if(!isset($params["seed"]))
	{
		$params["seed"]=$h_default_encrypt_seed;
	}	
		
	switch ($params["mode"])
	{
		case 'encrypt':
			
			$encoded_string=base64_encode($params["string"]);
			foreach($params["seed"] as $s_element)
			{
				$encoded_string=str_replace($s_element[0], $s_element[1], $encoded_string);
			}
			
			return $encoded_string;
			
		break;
		
		case 'decrypt':
			
			$decoded_string=$params["string"];
			
			foreach($params["seed"] as $s_element)
			{
				$decoded_string=str_replace($s_element[1], $s_element[0], $decoded_string);
			}
			$decoded_string=base64_decode($decoded_string);
			
			return $decoded_string;
			
		break;
		
		default:
			return $params["string"];
		break;
	}
}

function h_function_create_regular_table($params)
{
	switch ($params["type"])
	{
		case 'small':
			$query="
			  CREATE TABLE IF NOT EXISTS ".$params["name"]." (
			  id bigint(20) NOT NULL AUTO_INCREMENT ,
			  c1 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c2 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c3 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c4 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c5 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c6 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c7 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c8 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c9 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c10 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c11 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c12 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c13 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c14 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c15 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c16 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c17 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c18 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c19 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c20 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,			  		 		  
			  c41 datetime DEFAULT NULL ,
			  c42 datetime DEFAULT NULL ,
			  c43 datetime DEFAULT NULL ,
			  c44 datetime DEFAULT NULL ,
			  c45 datetime DEFAULT NULL ,		  		  
			  PRIMARY KEY (id)		  
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			
			if(!$params["connection"]->query($query))
			{
				return false;
			}
			else
			{
				return true;
			}
		
		break;
		
		case 'small_no_dates':
			$query="
			  CREATE TABLE IF NOT EXISTS ".$params["name"]." (
			  id bigint(20) NOT NULL AUTO_INCREMENT ,
			  c1 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c2 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c3 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c4 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c5 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c6 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c7 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c8 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c9 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c10 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c11 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c12 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c13 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c14 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c15 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c16 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c17 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c18 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c19 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c20 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,			  		 		  
			  PRIMARY KEY (id)		  
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			
			if(!$params["connection"]->query($query))
			{
				return false;
			}
			else
			{
				return true;
			}
		
		break;
		
		case 'medium':
			$query="
			  CREATE TABLE IF NOT EXISTS ".$params["name"]." (
			  id bigint(20) NOT NULL AUTO_INCREMENT ,
			  c1 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c2 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c3 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c4 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c5 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c6 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c7 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c8 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c9 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c10 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c11 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c12 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c13 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c14 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c15 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c16 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c17 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c18 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c19 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c20 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c21 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c22 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c23 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c24 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c25 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c26 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c27 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c28 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c29 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c30 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c41 datetime DEFAULT NULL ,
			  c42 datetime DEFAULT NULL ,
			  c43 datetime DEFAULT NULL ,
			  c44 datetime DEFAULT NULL ,
			  c45 datetime DEFAULT NULL ,		  		  
			  PRIMARY KEY (id)		  
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			
			if(!$params["connection"]->query($query))
			{
				return false;
			}
			else
			{
				return true;
			}
		
		break;
		
		case 'medium_no_dates':
			$query="
			  CREATE TABLE IF NOT EXISTS ".$params["name"]." (
			  id bigint(20) NOT NULL AUTO_INCREMENT ,
			  c1 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c2 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c3 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c4 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c5 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c6 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c7 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c8 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c9 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c10 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c11 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c12 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c13 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c14 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c15 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c16 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c17 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c18 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c19 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c20 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c21 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c22 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c23 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c24 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c25 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c26 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c27 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c28 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c29 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c30 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  PRIMARY KEY (id)		  
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			
			if(!$params["connection"]->query($query))
			{
				return false;
			}
			else
			{
				return true;
			}
		
		break;
		
		case 'large':
			$query="
			  CREATE TABLE IF NOT EXISTS ".$params["name"]." (
			  id bigint(20) NOT NULL AUTO_INCREMENT ,
			  c1 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c2 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c3 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c4 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c5 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c6 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c7 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c8 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c9 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c10 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c11 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c12 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c13 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c14 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c15 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c16 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c17 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c18 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c19 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c20 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c21 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c22 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c23 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c24 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c25 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c26 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c27 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c28 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c29 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c30 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c31 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c32 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c33 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c34 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c35 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c36 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c37 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c38 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c39 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c40 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,		 		  
			  c41 datetime DEFAULT NULL ,
			  c42 datetime DEFAULT NULL ,
			  c43 datetime DEFAULT NULL ,
			  c44 datetime DEFAULT NULL ,
			  c45 datetime DEFAULT NULL ,		  		  
			  PRIMARY KEY (id)		  
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			
			if(!$params["connection"]->query($query))
			{
				return false;
			}
			else
			{
				return true;
			}
		
		break;		
		
		case 'large_no_dates':
			$query="
			  CREATE TABLE IF NOT EXISTS ".$params["name"]." (
			  id bigint(20) NOT NULL AUTO_INCREMENT ,
			  c1 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c2 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c3 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c4 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c5 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c6 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c7 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c8 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c9 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c10 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c11 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c12 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c13 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c14 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c15 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c16 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c17 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c18 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c19 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c20 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c21 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c22 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c23 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c24 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c25 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c26 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c27 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c28 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c29 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c30 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c31 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c32 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c33 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c34 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c35 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c36 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c37 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c38 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c39 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,
			  c40 varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL ,		 		  
			  PRIMARY KEY (id)		  
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			
			if(!$params["connection"]->query($query))
			{
				return false;
			}
			else
			{
				return true;
			}
		
		break;
		
		default:
			return false;
		break;
	}	
}

function h_function_create_admin_user($params)
{
	{	
		$table_result=h_function_create_regular_table(array(
			"connection"=>$params["connection"],
			"type"=>"small_no_dates",
			"name"=>"h_admin_users",		
		));
		if(!$table_result)
		{
			return "[h_error_h_admin_users_table_creation_error]";
		}
	}
	
	{
		$query="SELECT * FROM h_admin_users WHERE c1='".urlencode($params["id"])."'";
		if(!$params["connection"]->query($query))
		{
			return "[h_error_sql_execution_error]";
		}
	}
	
	if($params["connection"]->affected_rows==0 && !$params["create_if_not_exists"])
	{
		return "OK";
	}
	
	if($params["connection"]->affected_rows==0 && $params["create_if_not_exists"])
	{
		{	
			$values["c1"]=array("c1",$params["id"]); //unique id
			$values["c2"]=array("c2",time()); // creation time_stamp
			$values["c3"]=array("c3",time()); // modification time_stamp
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}
			else
			{
				$values["c4"]=array("c4","1"); //By default active
			}
			
			$values["c5"]=array("c5","h_admin_users"); //Table of the item
		}
		
		{		
			if(!isset($params["password_encrypt_seed"]) && $params["password_encrypt_seed"]=="")
			{
				$params["password_encrypt_seed"]=$h_default_encrypt_seed;
			}
			$encrypted_password=h_encrypt_decrypt_string(array(
				"mode"=>"encrypt",
				"string"=>$params["password"],
				"seed"=>$params["encrypt_seed"]
			));	
			$values["c6"]=array("c6",$encrypted_password);					
			$values["c7"]=array("c7",$params["access_name"]);
			$values["c8"]=array("c8",$params["emails"]);
			$values["c9"]=array("c9",$params["phones"]);
			$values["c10"]=array("c10",$params["name"]);
			$values["c11"]=array("c11",$params["middlename"]);
			$values["c12"]=array("c12",$params["lastname"]);
			if(isset($params["admin_type"]) && $params["admin_type"]!="")
			{
				$values["c13"]=array("c13",$params["admin_type"]); // 0 will mean full access admin, 1 will mean limited access admin
			}
			else
			{
				$values["c13"]=array("c13","1"); //By default limited
			}			
		}
		
		{
			$insert_stat=h_function_manage_item_in_db(array(
				"mode"=>"insert",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_admin_users"			
			));
			if(!$insert_stat)
			{
				return "[h_error_sql_execution_insert_error]";
			}
		}
		
		return "OK";		
	}
	
	if($params["connection"]->affected_rows>=1 && $params["overwrite_current"])
	{
		{
			$values["c3"]=array("c3",time());
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}			
		}
		{
			if(isset($params["password"]) && $params["password"]!="")
			{	
				if(!isset($params["password_encrypt_seed"]) && $params["password_encrypt_seed"]=="")
				{
					$params["password_encrypt_seed"]=$h_default_encrypt_seed;
				}
				$encrypted_password=h_encrypt_decrypt_string(array(
					"mode"=>"encrypt",
					"string"=>$params["password"],
					"seed"=>$params["encrypt_seed"]
				));	
				$values["c6"]=array("c6",$encrypted_password);
			}
			if(isset($params["access_name"]) && $params["access_name"]!="")
			{					
				$values["c7"]=array("c7",$params["access_name"]);
			}
			if(isset($params["emails"]) && $params["emails"]!="")
			{
				$values["c8"]=array("c8",$params["emails"]);
			}
			if(isset($params["phones"]) && $params["phones"]!="")
			{
				$values["c9"]=array("c9",$params["phones"]);
			}
			if(isset($params["name"]) && $params["name"]!="")
			{
				$values["c10"]=array("c10",$params["name"]);
			}
			if(isset($params["middlename"]) && $params["middlename"]!="")
			{
				$values["c11"]=array("c11",$params["middlename"]);
			}
			if(isset($params["lastname"]) && $params["lastname"]!="")
			{
				$values["c12"]=array("c12",$params["lastname"]);
			}
			if(isset($params["admin_type"]) && $params["admin_type"]!="")
			{
				$values["c13"]=array("c13",$params["admin_type"]); // 0 will mean full access admin, 1 will mean limited access admin
			}			
		}

		{
			$update_stat=h_function_manage_item_in_db(array(
				"mode"=>"update",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_admin_users"			
			));
			if(!$update_stat)
			{
				return "[h_error_sql_execution_update_error]";
			}
		}
		
		return "OK";
	}
	if($params["connection"]->affected_rows>=1 && !$params["overwrite_current"])
	{
		return "OK";
	}
}

function h_function_manage_item_in_db($params)
{
	switch ($params["mode"])
	{
		case 'insert':
			$values=$params["values"];		
			$fields="(";
			$cols="(";
			foreach($values as $value)
			{
				if(substr($value[0],0,1)=="c")
				{
					$fields.=$value[0].",";
					$cols.="'".urlencode(trim($value[1]))."',";
				}
			}
			$fields=rtrim($fields, ",");
			$cols=rtrim($cols, ",");
			$fields.=")";
			$cols.=")";
			$query="INSERT INTO ".$params["table"]." ".$fields." VALUES ".$cols;
			$result=$params["connection"]->query($query);
			if(!$result)
			{
				return false;
			}
			return true;			
		break;
		
		case 'update':
			$values=$params["values"];	
			$fields="";
			foreach($values as $value)
			{
				if(substr($value[0],0,1)=="c")
				{
					$fields.=$value[0]."='".urlencode(trim($value[1]))."',";
				}
			}
			$fields=rtrim($fields, ",");
			$query="UPDATE ".$params["table"]." SET ".$fields." WHERE c1='".$values["c1"][1]."'";
			$result=$params["connection"]->query($query);
			if(!$result)
			{
				return false;
			}
			return true;
		break;
		
		default:
			return false;
		break;
	}
}

function h_function_manage_simple_text($params)
{
	$table_result=h_function_create_regular_table(array(
		"connection"=>$params["connection"],
		"type"=>"small_no_dates",
		"name"=>"h_simple_texts",		
	));
	if(!$table_result)
	{
		return "[h_error_h_simple_texts_table_creation_error]";
	}
	$query="SELECT * FROM h_simple_texts WHERE c1='".urlencode($params["id"])."'";
	$result=$params["connection"]->query($query);
	if(!$result)
	{
		return "[pfHE_06]";
	}
	if($params["connection"]->affected_rows==0 && $params["create_if_not_exists"])
	{
		$values["c1"]=array("c1",$params["id"]);
		$values["c2"]=array("c2",time());
		$values["c3"]=array("c3",time());
		if(isset($params["current_user_id"]) && $params["current_user_id"]!="")
		{
			$values["c4"]=array("c4",$params["current_user_id"]);
		}
		else
		{
			$values["c4"]=array("c4","jdoe");
		}
		
		if(isset($params["current_user_id"]) && $params["current_user_id"]!="")
		{
			$values["c5"]=array("c5",$params["current_user_id"]);
		}
		else
		{
			$values["c5"]=array("c5","jdoe");
		}
		
		if(isset($params["current_user_type"]) && $params["current_user_type"]!="")
		{
			$values["c6"]=array("c6",$params["current_user_type"]);
		}
		else
		{
			$values["c6"]=array("c6","unknown");
		}
		
		if(isset($params["current_user_type"]) && $params["current_user_type"]!="")
		{
			$values["c7"]=array("c7",$params["current_user_type"]);
		}
		else
		{
			$values["c7"]=array("c7","unknown");
		}
		
		if(isset($params["status"]) && $params["status"]!="")
		{
			$values["c8"]=array("c8",$params["status"]);
		}
		else
		{
			$values["c8"]=array("c8","active");
		}
		
		$values["c9"]=array("c9","h_simple_texts");
		
		foreach($params["values"] as $t_val)
		{
			$values["c10"]=array("c10",$t_val[0]);
			$values["c11"]=array("c11",$t_val[1]);
			$insert_stat=h_function_manage_item_in_db(array(
				"mode"=>"insert",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_simple_texts"			
			));
			if(!$insert_stat)
			{
				return "[pfHE_05]";
			}
			usleep(200000);
		}
		
		return $params["values"][$params["current_lang"]][1]; 
		
	}
	if($params["connection"]->affected_rows==0 && !$params["create_if_not_exists"])
	{
		return "[pfHE_07]";
	}
	while($row=$result->fetch_assoc())
	{
		if(urldecode($row["c10"])==$params["current_lang"])
		{
			$text_to_return=urldecode($row["c11"]);
			return $text_to_return;
		}
	}
		
	return "[pfHE_08]";
}

function h_function_create_html_head()
{
	
}

function h_function_load_add($params)
{
	{	
		$table_result=h_function_create_regular_table(array(
			"connection"=>$params["connection"],
			"type"=>"small",
			"name"=>"h_advertising_items",		
		));
		if(!$table_result)
		{
			return "[h_error_h_advertising_items_table_creation_error]";
		}
	}
	
	{
		$query="SELECT * FROM h_advertising_items WHERE c1='".urlencode($params["id"])."'";
		if(!$params["connection"]->query($query))
		{
			return "[h_error_sql_execution_error]";
		}
	}
	
	if($params["connection"]->affected_rows==0 && !$params["create_if_not_exists"])
	{
		return "OK";
	}
	
	if($params["connection"]->affected_rows==0 && $params["create_if_not_exists"])
	{
		{	
			$values["c1"]=array("c1",$params["id"]); //unique id
			$values["c2"]=array("c2",time()); // creation time_stamp
			$values["c3"]=array("c3",time()); // modification time_stamp
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}
			else
			{
				$values["c4"]=array("c4","1"); //By default active
			}
			
			$values["c5"]=array("c5","h_advertising_items"); //Table of the item
		}
		
		{				
			$values["c6"]=array("c6",$params["featured_day"]);					
			$values["c7"]=array("c7",$params["number_of_impressions"]);
			$values["c8"]=array("c8",$params["destination_url"]);
			$values["c9"]=array("c9",$params["small_image_route"]);
			$values["c10"]=array("c10",$params["big_image_route"]);
			$values["c11"]=array("c11",$params["number_of_visits"]);
			$values["c12"]=array("c12",$params["phone"]);
			$values["c13"]=array("c13",$params["web"]);
			$values["c14"]=array("c14",$params["email"]);
			$values["c15"]=array("c15",$params["address"]);
			$values["c16"]=array("c16",$params["latlong"]);
			$values["c17"]=array("c17",$params["allow_geolocation"]);
			$values["c18"]=array("c18",$params["show_map"]);			
		}
		
		{
			$insert_stat=h_function_manage_item_in_db(array(
				"mode"=>"insert",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_advertising_items"			
			));
			if(!$insert_stat)
			{
				return "[h_error_sql_execution_insert_error]";
			}
		}
		
		return "OK";		
	}
	
	if($params["connection"]->affected_rows>=1 && $params["overwrite_current"])
	{
		{
			$values["c3"]=array("c3",time());
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}			
		}
		
		{
			if(isset($params["featured_day"]) && $params["featured_day"]!="")
			{					
				$values["c6"]=array("c6",$params["featured_day"]);
			}
			if(isset($params["number_of_impressions"]) && $params["number_of_impressions"]!="")
			{					
				$values["c7"]=array("c7",$params["number_of_impressions"]);
			}
			if(isset($params["destination_url"]) && $params["destination_url"]!="")
			{
				$values["c8"]=array("c8",$params["destination_url"]);
			}
			if(isset($params["small_image_route"]) && $params["small_image_route"]!="")
			{
				$values["c9"]=array("c9",$params["small_image_route"]);
			}
			if(isset($params["big_image_route"]) && $params["big_image_route"]!="")
			{
				$values["c10"]=array("c10",$params["big_image_route"]);
			}
			if(isset($params["number_of_visits"]) && $params["number_of_visits"]!="")
			{
				$values["c11"]=array("c11",$params["number_of_visits"]);
			}
			if(isset($params["phone"]) && $params["phone"]!="")
			{
				$values["c12"]=array("c12",$params["phone"]);
			}
			if(isset($params["web"]) && $params["web"]!="")
			{
				$values["c13"]=array("c13",$params["web"]); 
			}
			if(isset($params["email"]) && $params["email"]!="")
			{
				$values["c14"]=array("c14",$params["email"]); 
			}
			if(isset($params["address"]) && $params["address"]!="")
			{
				$values["c15"]=array("c15",$params["address"]); 
			}
			if(isset($params["latlong"]) && $params["latlong"]!="")
			{
				$values["c16"]=array("c16",$params["latlong"]); 
			}
			if(isset($params["allow_geolocation"]) && $params["allow_geolocation"]!="")
			{
				$values["c17"]=array("c17",$params["allow_geolocation"]); 
			}
			if(isset($params["show_map"]) && $params["show_map"]!="")
			{
				$values["c18"]=array("c18",$params["show_map"]); 
			}					
		}

		{
			$update_stat=h_function_manage_item_in_db(array(
				"mode"=>"update",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_advertising_items"			
			));
			if(!$update_stat)
			{
				return "[h_error_sql_execution_update_error]";
			}
		}
		
		return "OK";
	}
	if($params["connection"]->affected_rows>=1 && !$params["overwrite_current"])
	{
		return "OK";
	}
}

function h_function_recover_random_add($params)
{
	{
		$values=array();
		if(!$params["out_of_key_dates"])
		{
			$query="SELECT * FROM h_advertising_items WHERE c6='".urlencode($params["current_day"])."'";
			$result=$params["connection"]->query($query);
			if(!$result)
			{
				return false;
			}
			if($params["connection"]->affected_rows==0)
			{
				$query="SELECT * FROM h_advertising_items ORDER BY c7 ASC";
				$result=$params["connection"]->query($query);
				if(!$result)
				{
					return false;
				}
				$row=$result->fetch_assoc();
				$values["c1"]=array("c1",urldecode($row["c1"]));
				$values["c7"]=array("c7",intval(urldecode($row["c7"]))+1);
				$update_stat=h_function_manage_item_in_db(array(
					"mode"=>"update",
					"connection"=>$params["connection"],
					"values"=>$values,
					"table"=>"h_advertising_items"			
				));						
				return $row;				
			}
			$row=$result->fetch_assoc();
			$values["c1"]=array("c1",urldecode($row["c1"]));
			$values["c7"]=array("c7",intval(urldecode($row["c7"]))+1);
			$update_stat=h_function_manage_item_in_db(array(
				"mode"=>"update",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_advertising_items"			
			));				
			return $row;							
		}
		$query="SELECT * FROM h_advertising_items ORDER BY c7 ASC";
		$result=$params["connection"]->query($query);
		if(!$result)
		{
			return false;
		}
		$row=$result->fetch_assoc();		
		$values["c1"]=array("c1",urldecode($row["c1"]));
		$values["c7"]=array("c7",intval(urldecode($row["c7"]))+1);
		$update_stat=h_function_manage_item_in_db(array(
			"mode"=>"update",
			"connection"=>$params["connection"],
			"values"=>$values,
			"table"=>"h_advertising_items"			
		));				
		return $row;		
	}
	
}

function h_function_load_new($params)
{
	{	
		$table_result=h_function_create_regular_table(array(
			"connection"=>$params["connection"],
			"type"=>"small",
			"name"=>"h_news_items",		
		));
		if(!$table_result)
		{
			return "[h_error_h_news_items_table_creation_error]";
		}
	}
	
	{
		$query="SELECT * FROM h_news_items WHERE c1='".urlencode($params["id"])."'";
		if(!$params["connection"]->query($query))
		{
			return "[h_error_sql_execution_error]";
		}
	}
	
	if($params["connection"]->affected_rows==0 && !$params["create_if_not_exists"])
	{
		return "OK";
	}
	
	if($params["connection"]->affected_rows==0 && $params["create_if_not_exists"])
	{
		{	
			$values["c1"]=array("c1",$params["id"]); //unique id
			$values["c2"]=array("c2",time()); // creation time_stamp
			$values["c3"]=array("c3",time()); // modification time_stamp
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}
			else
			{
				$values["c4"]=array("c4","1"); //By default active
			}
			
			$values["c5"]=array("c5","h_news_items"); //Table of the item
		}
		
		{				
			$values["c6"]=array("c6",$params["day"]);					
			$values["c7"]=array("c7",$params["month"]);
			$values["c8"]=array("c8",$params["hour"]);
			$values["c9"]=array("c9",$params["title"]);
			$values["c10"]=array("c10",$params["text"]);
					
		}
		
		{
			$insert_stat=h_function_manage_item_in_db(array(
				"mode"=>"insert",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_news_items"			
			));
			if(!$insert_stat)
			{
				return "[h_error_sql_execution_insert_error]";
			}
		}
		
		return "OK";		
	}
	
	if($params["connection"]->affected_rows>=1 && $params["overwrite_current"])
	{
		{
			$values["c3"]=array("c3",time());
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}			
		}
		
		{
			if(isset($params["day"]) && $params["day"]!="")
			{					
				$values["c6"]=array("c6",$params["day"]);
			}
			if(isset($params["month"]) && $params["month"]!="")
			{					
				$values["c7"]=array("c7",$params["month"]);
			}
			if(isset($params["hour"]) && $params["hour"]!="")
			{
				$values["c8"]=array("c8",$params["hour"]);
			}
			if(isset($params["title"]) && $params["title"]!="")
			{
				$values["c9"]=array("c9",$params["title"]);
			}
			if(isset($params["text"]) && $params["text"]!="")
			{
				$values["c10"]=array("c10",$params["text"]);
			}
								
		}

		{
			$update_stat=h_function_manage_item_in_db(array(
				"mode"=>"update",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_news_items"			
			));
			if(!$update_stat)
			{
				return "[h_error_sql_execution_update_error]";
			}
		}
		
		return "OK";
	}
	if($params["connection"]->affected_rows>=1 && !$params["overwrite_current"])
	{
		return "OK";
	}
}

function h_function_show_news($params)
{
		$query="SELECT * FROM h_news_items ORDER BY id DESC LIMIT 0,10";
		$result=$params["connection"]->query($query);
		if(!$result)
		{
			echo "<div style='padding:10px;font-family:'3';font-size:12px;text-align:center'>No se han podido recuperar las noticias, disculpa las molestias.</div>";
		}
		if($params["connection"]->affected_rows==0)
		{
			echo "<div style='padding:10px;font-family:'3';font-size:12px;text-align:center'>Ahora mismo no hay ninguna noticia de última hora.</div>";				
		}
		while($row=$result->fetch_assoc())
		{
			echo '<div style="padding:10px;border-bottom:1px solid black">';
			echo '<div style="float:left;width:20%;text-align:right;font-size:2em;font-family:Times New Roman">';
			echo urldecode($row["c6"]);
			echo '<br>';
			echo '<span style="font-size:0.6em">'.urldecode($row["c7"]).'</span>';
			echo '</div>';
			echo '<div style="float:right;width:70%;text-align:left;font-size:0.8em;font-family:Times New Roman">';
			echo '<span style="font-size:1.2em;font-weight:bold">'.urldecode($row["c9"]).'</span>';
			echo '<br><br>';
			echo urldecode($row["c10"]);
			echo '</div>';
			echo '<div style="clear:both"></div>';
			echo '</div>';
		}
}

function h_function_recover_offers($params)
{
	$query="SELECT * FROM h_advertising_items WHERE c10<>'no' ORDER BY id DESC LIMIT 0,10";
		$result=$params["connection"]->query($query);
		if(!$result)
		{
			echo "<div style='padding:10px;font-family:Arial;font-size:12px;text-align:center'>No se han podido recuperar las ofertas, disculpa las molestias.</div>";
		}
		if($params["connection"]->affected_rows==0)
		{
			echo "<div style='padding:10px;font-family:Arial;font-size:12px;text-align:center'>Ahora mismo no hay ninguna oferta disponible.</div>&nbsp;&nbsp;";				
		}
		while($row=$result->fetch_assoc())
		{
			echo '<div style="padding-bottom:20px;">';
			echo '<img src="'.$params["root_path"].urldecode($row["c10"]).'" style="width:100%;max-width:500px;display:block;margin:auto" alt="imagen no encontrada"/>';
			echo '</div>';
		}
}

function h_function_load_event_to_track($params)
{
	{	
		$table_result=h_function_create_regular_table(array(
			"connection"=>$params["connection"],
			"type"=>"small",
			"name"=>"h_tracking_events",		
		));
		if(!$table_result)
		{
			return "[h_error_h_news_items_table_creation_error]";
		}
	}
	
	{
		$query="SELECT * FROM h_tracking_events WHERE c1='".urlencode($params["id"])."'";
		if(!$params["connection"]->query($query))
		{
			return "[h_error_sql_execution_error]";
		}
	}
	
	if($params["connection"]->affected_rows==0 && !$params["create_if_not_exists"])
	{
		return "OK";
	}
	
	if($params["connection"]->affected_rows==0 && $params["create_if_not_exists"])
	{
		{	
			$values["c1"]=array("c1",$params["id"]); //unique id
			$values["c2"]=array("c2",time()); // creation time_stamp
			$values["c3"]=array("c3",time()); // modification time_stamp
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}
			else
			{
				$values["c4"]=array("c4","1"); //By default active
			}
			
			$values["c5"]=array("c5","h_tracking_events"); //Table of the item
		}
		
		{				
			$values["c6"]=array("c6",$params["name"]);					
			$values["c7"]=array("c7",$params["current_latlong"]);
			$values["c8"]=array("c8",$params["current_timestamp"]);
			$values["c9"]=array("c9",$params["previous_latlong"]);
			$values["c10"]=array("c10",$params["previous_timestamp"]);
					
		}
		
		{
			$insert_stat=h_function_manage_item_in_db(array(
				"mode"=>"insert",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_tracking_events"			
			));
			if(!$insert_stat)
			{
				return "[h_error_sql_execution_insert_error]";
			}
		}
		
		return "OK";		
	}
	
	if($params["connection"]->affected_rows>=1 && $params["overwrite_current"])
	{
		{
			$values["c3"]=array("c3",time());
			if(isset($params["status"]) && $params["status"]!="")
			{
				$values["c4"]=array("c4",$params["status"]); //status (1-Active,0-Suspended)
			}			
		}
		
		{
			if(isset($params["name"]) && $params["name"]!="")
			{					
				$values["c6"]=array("c6",$params["name"]);
			}
			if(isset($params["current_latlong"]) && $params["current_latlong"]!="")
			{					
				$values["c7"]=array("c7",$params["current_latlong"]);
			}
			if(isset($params["current_timestamp"]) && $params["current_timestamp"]!="")
			{
				$values["c8"]=array("c8",$params["current_timestamp"]);
			}
			if(isset($params["previous_latlong"]) && $params["previous_latlong"]!="")
			{
				$values["c9"]=array("c9",$params["previous_latlong"]);
			}
			if(isset($params["previous_timestamp"]) && $params["previous_timestamp"]!="")
			{
				$values["c10"]=array("c10",$params["previous_timestamp"]);
			}
								
		}

		{
			$update_stat=h_function_manage_item_in_db(array(
				"mode"=>"update",
				"connection"=>$params["connection"],
				"values"=>$values,
				"table"=>"h_tracking_events"			
			));
			if(!$update_stat)
			{
				return "[h_error_sql_execution_update_error]";
			}
		}
		
		return "OK";
	}
	if($params["connection"]->affected_rows>=1 && !$params["overwrite_current"])
	{
		return "OK";
	}
}

function h_function_recover_tracking_event($params)
{
	$query="SELECT * FROM h_tracking_events WHERE c1='".urlencode($params["id"])."'";
	$result=$params["connection"]->query($query);
	if(!$result)
	{
		return false;
	}
	if($params["connection"]->affected_rows==0)
	{
		return false;			
	}
	$row=$result->fetch_assoc();
	return $row;
}
?>