<?php

/*
*
*activeafterxpost Plugin
* Copyright 2013 mostafa shirali
* http://www.kingofpersia.ir
* http://www.pctricks.ir
*
*/
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
$plugins->add_hook("forumdisplay_start", "activeafterxpost_newthread");
$plugins->add_hook("showthread_start", "activeafterxpost_showthread");

function activeafterxpost_info()
{
global $lang;
$lang->load("activeafterxpost");
return array(
	"name" => $lang->activeafterxpost_plugin_name,
	"description" => $lang->activeafterxpost_plugin_dec,
	"website" => "http://www.pctricks.ir",
	"author" => "Mostafa shirali",
	"authorsite" => "www.pctricks.ir",
	"version" => "1.0",
	"guid"        => "activeafterxpost",
	"compatibility"	=> "18*"
);
}
function activeafterxpost_activate()
{
global $mybb, $db, $templates,$lang;
$lang->load("activeafterxpost");

   $settings_group = array(
        "gid" => "",
        "name" => "activeafterxpost",
        "title" => $lang->activeafterxpost_setting_name,
        "description" => $lang->activeafterxpost_setting_dec,
        "disporder" => "1",
        "isdefault" => "0",
        );

	$db->insert_query("settinggroups", $settings_group);
    $gid = $db->insert_id();
	
	$setting[] = array("sid" => "","name" => "activeafterxpost_enable","title" => $lang->activeafterxpost_enable,"description" => $lang->activeafterxpost_enable_dec,"optionscode" => "yesno","value" => "0","disporder" => 1,"gid" => intval($gid),);	
	$setting[] = array("sid" => "","name" => "activeafterxpost_grouppermission","title" => $lang->activeafterxpost_grouppermission,"description" => $lang->activeafterxpost_grouppermission_dec,"optionscode" => "text","value" => 10,"disporder" => 2,"gid" => intval($gid),);	
 	foreach ($setting as $i)
	{
		$db->insert_query("settings", $i);
	}
rebuild_settings();

	
}
/********************************** ACtive Post After X POST*******************/
function activeafterxpost_newthread()
{
global $mybb,$lang,$db;

if($mybb->settings['activeafterxpost_enable'] == 1)
{
	$lang->load("activeafterxpost");
	$uid=$mybb->user['uid'];
	$query_nvis=$db->query("SELECT * FROM ".TABLE_PREFIX."posts WHERE uid='$uid' AND visible='0'");
	if($db->num_rows($query_nvis)>0)
		{
	$query=$db->query("SELECT * FROM ".TABLE_PREFIX."posts WHERE uid='$uid' AND visible='1'");
	$num=$db->num_rows($query);
	if($num>=intval($mybb->settings['activeafterxpost_grouppermission']))
	{
	$query_new_post=$db->query("SELECT * FROM ".TABLE_PREFIX."posts WHERE uid='$uid' AND visible='0' ORDER BY pid DESC");
	for($i=0;$i<$db->num_rows($query_new_post);$i++)
	{
	$fetch=$db->fetch_array($query_new_post);
	$postid=$fetch['pid'];
	$query_update_post=$db->query("UPDATE ".TABLE_PREFIX."posts SET visible='1' WHERE uid='$uid' AND pid='$postid'");
	}
	$query_new_thread=$db->query("SELECT * FROM ".TABLE_PREFIX."threads WHERE uid='$uid' AND visible='0' ORDER BY tid DESC");
	for($i=0;$i<$db->num_rows($query_new_thread);$i++)
	{
	$fetch=$db->fetch_array($query_new_thread);
	$threadid=$fetch['tid'];
	$query_update_post=$db->query("UPDATE ".TABLE_PREFIX."threads SET visible='1' WHERE uid='$uid' AND tid='$threadid'");
	}
	}	
		}
	

}
}

function activeafterxpost_showthread()
{
global $mybb,$lang,$db;

if($mybb->settings['activeafterxpost_enable'] == 1)
{
	$lang->load("activeafterxpost");
	$uid=$mybb->user['uid'];
	$query_nvis=$db->query("SELECT * FROM ".TABLE_PREFIX."posts WHERE uid='$uid' AND visible='0'");
	if($db->num_rows($query_nvis)>0)
		{
	$query=$db->query("SELECT * FROM ".TABLE_PREFIX."posts WHERE uid='$uid' AND visible='1'");
	$num=$db->num_rows($query);
	if($num>=intval($mybb->settings['activeafterxpost_grouppermission']))
	{
	$query_new_post=$db->query("SELECT * FROM ".TABLE_PREFIX."posts WHERE uid='$uid' AND visible='0' ORDER BY pid DESC");
	for($i=0;$i<$db->num_rows($query_new_post);$i++)
	{
	$fetch=$db->fetch_array($query_new_post);
	$postid=$fetch['pid'];
	$query_update_post=$db->query("UPDATE ".TABLE_PREFIX."posts SET visible='1' WHERE uid='$uid' AND pid='$postid'");
	}
	}	
		}
	

}
}
/********************************** ACtive Post After X POST*******************/



function activeafterxpost_deactivate()
{
global  $db;
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='activeafterxpost'");
	$db->delete_query("settings","name IN ('activeafterxpost_enable','activeafterxpost_grouppermission')");
rebuild_settings();

}




?>
