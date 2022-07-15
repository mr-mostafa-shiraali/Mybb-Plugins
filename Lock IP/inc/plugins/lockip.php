<?php

/*
*
* lockip Plugin
* Copyright 2011 mostafa shirali
* http://ctboard.com
* No one is authorized to redistribute or remove copyright without my expressed permission.
*
*/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("admin_user_menu", "lockip_admin_nav");
$plugins->add_hook("admin_user_action_handler", "lockip_action_handler");
$plugins->add_hook('global_start', 'run_lockip');
if(!defined('LOCKIP_BASE')) define('LOCKIP_BASE',MYBB_ROOT.'inc/plugins/');
require_once LOCKIP_BASE.'lockip_class.php';
// The information that shows up on the plugin manager
function lockip_info()
{
global $lang;
$lang->load("lockip");
return array(
"name" => $lang->plugin_name,
"description" => $lang->plugin_dec,
"website" => "http://ctboard.com",
"author" => "Mostafa shirali",
"authorsite" => "http://ctboard.com",
"version" => "2.0",
'codename' => 'LockIp'
);
}

// This function runs when the plugin is activated.
function lockip_activate()
{
global $mybb, $db,$lang;
$lang->load("lockip");
		$db->query("CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."lockip (
			id smallint(4) NOT NULL auto_increment,
		    uip varchar(50) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  	
	$settings_group = array("name" => "lockip","title" => $lang->plugin_name,"description" => $lang->plugin_options,"disporder" => "88","isdefault" => "0");
    $db->insert_query("settinggroups", $settings_group);
    $gid = $db->insert_id();
	$setting[] = array("sid" => "","name" => "lockipenable","title" => $lang->op1_name,"description" => $lang->op1_dec,"optionscode" => "yesno","value" => "1","disporder" => 1,"gid" => intval($gid));
	$setting[] = array("sid" => "","name" => "lockiploction","title" => $lang->op2_name,"description" => $lang->op2_dec,"optionscode" => "text","value" => "http://ctboard.com","disporder" => 2,"gid" => intval($gid));
	
 	foreach ($setting as $i)
	{
		$db->insert_query("settings", $i);
	}
	rebuild_settings();


}



function run_lockip()
{
global $mybb,$db;

if($mybb->settings['lockipenable'] == 1)
{
$ip=lockip_checkip::get_client_ip();
$iparray=array();
$query=$db->query("SELECT uip from ".TABLE_PREFIX."lockip");
for($i=0;$i<$db->num_rows($query);$i++)
{
$fetch=$db->fetch_array($query);
$iparray[$i]=$fetch['uip'];
}
if(lockip_checkip::check($ip,$iparray))
{
header( 'Location: '.$mybb->settings['lockiploction']) ;
}
	
}	
}

//action
function lockip_action_handler(&$action)
{
	$action['lockip'] = array('active' => 'lockip', 'file' => 'lockip.php');
}




//menu
function lockip_admin_nav(&$sub_menu)
{
	global $mybb,$lang;
	
		$lang->load("lockip");
		end($sub_menu);
		$key = (key($sub_menu))+10;
		
		if(!$key)
		{
			$key = '60';
		}
		
		$sub_menu[$key] = array('id' => 'lockip', 'title' =>"{$lang->plugin_name}", 'link' => "index.php?module=user/lockip");

}






// This function runs when the plugin is deactivated.
function lockip_deactivate()
{
require '../inc/adminfunctions_templates.php';
global $mybb, $db, $templates;
			$db->delete_query("settinggroups","name='lockip'");
			$db->delete_query("settings","name IN ('lockipenable','lockiploction')");
		if($db->table_exists('lockip'))
		{
		$db->drop_table('lockip');
		}
		rebuild_settings();
}
?>
