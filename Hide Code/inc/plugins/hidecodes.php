<?php
/*
Hide Codes Plugin
Mostafa Shiraali
http://ctboard.com
*/
$plugins->add_hook("parse_message_start", "hidecodes_run");
$plugins->add_hook('parse_quoted_message', 'quoted_post');
function hidecodes_info()
{
	global $lang;
	$lang->load("hidecodes");
    return array(
        'name'        => $lang->hidecodes_name,
        'description' => $lang->hidecodes_dec,
        'website'     => 'http://ctboard.com',
        'version'     => '1.0.0',
        'author'      => 'Mostafa Shiraali',
        'authorsite'  => 'http://ctboard.com',
		"compatibility" => "*",
        'codename'        => 'hidecoeds'
        
    );
}
function hidecodes_activate()
{
	global $mybb, $db,$lang;
	$lang->load("hidecodes");
		$settings_group = array(
        "gid" => "",
        "name" => "hidecodes",
        "title" => $lang->hidecodes_name,
        "description" => $lang->hidecodes_dec,
        "disporder" => "88",
        "isdefault" => "0",
        );
    $db->insert_query("settinggroups", $settings_group);
	$hidemessage=$lang->hidemessage;
	$gid = $db->insert_id();
	$setting[] = array("sid" => "","name" => "hidecs_enable","title" => $lang->active_title,"description" => $lang->active_dec,"optionscode" => "yesno","value" => "0","disporder" => 1,"gid" => intval($gid),);
    $setting[] = array('sid'=> "",'name'=> 'hidecs_message','title'=> $lang->hidemsg_title,'description'	=> $lang->hidemsg_dec,'optionscode'=> 'textarea','value'=> $hidemessage,'disporder'=> 2,'gid'=> intval($gid),);
 	foreach ($setting as $i)
	{
		$db->insert_query("settings", $i);
	}
	rebuild_settings();
	
}
function hidecodes_deactivate()
{
		global $mybb, $db;
		$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='hidecodes'");
		$db->delete_query("settings","name IN ('hidecs_enable','hidecs_message')");
		rebuild_settings();
}
function hidecodes_run(&$message)
{
global $mybb;
if($mybb->settings['hidecs_enable'] == 1 && !$mybb->user['uid'])
{
	$pattern = "/\[(php|code)\]([\s\S]+?)\[\/(php|code)\]/s";
    $replace = "[align=center][color=#ff3333][b]{$mybb->settings['hidecs_message']}[/b][/color][/align]";
    $message = preg_replace($pattern, $replace, $message);
}

}
function quoted_post(&$quoted_post)
{
global $mybb;
if($mybb->settings['hidecs_enable'] == 1 && !$mybb->user['uid'])
{
	$pattern = "/\[(php|code)\]([\s\S]+?)\[\/(php|code)\]/s";
    $replace = "[align=center][color=#ff3333][b]{$mybb->settings['hidecs_message']}[/b][/color][/align]";
    $quoted_post = preg_replace($pattern, $replace, $quoted_post);
}
}
?>