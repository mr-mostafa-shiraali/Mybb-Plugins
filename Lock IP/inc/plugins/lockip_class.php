<?php

/*
*
* lockip Plugin
* Copyright 2011 mostafa shirali
* http://ctboard.com
* No one is authorized to redistribute or remove copyright without my expressed permission.
*
*/
class lockip_checkip
{
		public static function check($ip,$iparray)
		{
		$check=false;
		$first_ip=explode(".",$ip);
		for($i=0;$i<count($iparray);$i++)
		{
		$second_ip=explode(".",$iparray[$i]);
		if($first_ip[0]==$second_ip[0] OR $second_ip[0]=="*" OR lockip_checkip::IP_IS_Between($first_ip[0],$second_ip[0]))
		{
			if($first_ip[1]==$second_ip[1] OR $second_ip[1]=="*" OR lockip_checkip::IP_IS_Between($first_ip[1],$second_ip[1]))
			{
				if($first_ip[2]==$second_ip[2] OR $second_ip[2]=="*" OR lockip_checkip::IP_IS_Between($first_ip[2],$second_ip[2]))
				{
					if($first_ip[3]==$second_ip[3] OR $second_ip[3]=="*" OR lockip_checkip::IP_IS_Between($first_ip[3],$second_ip[3]))
					{
					$check=true;
					}
			
				}
		
			}
		}
		}
		return $check;
		}
		
		public static function IP_IS_Between($ippart,$range)
		{
		if(preg_match("#^\d{1,3}-\d{1,3}$#i",$range))
		{
		$between=explode("-",$range);
		if($ippart>=$between[0] AND $ippart<=$between[1])
		{
		return true;
		}
		else
		{
		return false;
		}
		}
		else
		{
		return false;
		}
		}
		
	public static function get_client_ip() 
		{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
		return $ipaddress;
		}
}
?>
