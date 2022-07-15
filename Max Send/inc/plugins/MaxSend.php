<?php

/*
*
* MaxSend Plugin
* Copyright 2015 Mostafa shirali
* http://ctboard.com
* No one is authorized to redistribute or remove copyright without my expressed permission.
*
*/

if(!defined("IN_MYBB"))
{
die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}


$plugins->add_hook('admin_user_groups_edit_graph_tabs', 'maxsend_usergroup_permissions_tab');
$plugins->add_hook('admin_user_groups_edit_graph', 'maxsend_usergroup_permissions');
$plugins->add_hook('admin_user_groups_edit_commit', 'maxsend_usergroup_permissions_save');
$plugins->add_hook('newthread_start', 'maxsend_check_threads');
$plugins->add_hook('newreply_start', 'maxsend_check_reply');
$plugins->add_hook('newreply_do_newreply_start', 'maxsend_check_reply');
function MaxSend_info()
{
global $lang;
$lang->load("MaxSend");
return array(
"name" => $lang->maxsend_plugin_name,
"description" => $lang->maxsend_plugin_dec,
"website" => "http://ctboard.com",
"author" => "Mostafa shirali",
"authorsite" => "http://ctboard.com",
"version" => "1.0",
'codename' => 'MaxSend'
);
}
function MaxSend_activate()
{
global $mybb, $db,$lang,$cache;
$lang->load("MaxSend");
	if(!$db->field_exists('maxthread', "usergroups")){$db->query("ALTER TABLE ".TABLE_PREFIX."usergroups ADD maxthread INT(5) NOT NULL DEFAULT '0'");}
	if(!$db->field_exists('maxreply', "usergroups")){$db->query("ALTER TABLE ".TABLE_PREFIX."usergroups ADD maxreply INT(5) NOT NULL DEFAULT '0'");}
	$cache->update_usergroups();
	
}

function MaxSend_deactivate()
{
global $db;
		if($db->field_exists('maxthread', "usergroups")){$db->query("ALTER TABLE ".TABLE_PREFIX."usergroups Drop maxthread");}
		if($db->field_exists('maxreply', "usergroups")){$db->query("ALTER TABLE ".TABLE_PREFIX."usergroups Drop maxreply");}

}

//maxsend permissions
function maxsend_usergroup_permissions_tab(&$tabs)
{
	global $lang;
	$lang->load('MaxSend');
	$tabs['maxsend_perms'] = $lang->maxsend_plugin_name;
}
function maxsend_usergroup_permissions()
{
	global $mybb, $lang, $form;
	$lang->load('MaxSend');

	
	echo "<div id=\"tab_maxsend_perms\">";	
	$form_container = new FormContainer($lang->maxsend_plugin_name);
	$form_container->output_row($lang->maxsend_maxthread." <em>*</em>", "", $form->generate_text_box('maxthread', $mybb->input['maxthread'], array('id' => 'maxthread')), 'maxthread');
	$form_container->output_row($lang->maxsend_maxreply." <em>*</em>", "", $form->generate_text_box('maxreply', $mybb->input['maxreply'], array('id' => 'maxreply')), 'maxreply');
	$form_container->end();
	echo "</div>";	
}
function maxsend_usergroup_permissions_save()
{
	global $mybb, $updated_group,$db;
		$updated_group['maxthread'] =intval($db->escape_string($mybb->input['maxthread']));
		$updated_group['maxreply'] =intval($db->escape_string($mybb->input['maxreply']));

}

function maxsend_check_threads()
{
global $mybb, $lang, $db;
$lang->load('MaxSend');
$msuid=intval($mybb->user['uid']);
$maxthread=$mybb->usergroup['maxthread'];
$mstquery = $db->simple_select('threads','uid',"uid = '$msuid'");
if($maxthread!=0 AND $maxthread<=$db->num_rows($mstquery))
{
 error("{$lang->maxsend_limitthread}", "{$lang->maxsend_error_title}");
}
}

function maxsend_check_reply()
{
global $mybb, $lang, $db;
$lang->load('MaxSend');
$msuid=intval($mybb->user['uid']);
$maxreply=$mybb->usergroup['maxreply'];
$msrquery = $db->simple_select('posts','uid',"uid = '$msuid' AND `replyto`>'0'");
if($maxreply!=0 AND $maxreply<=$db->num_rows($msrquery))
{
error("{$lang->maxsend_limitreply}", "{$lang->maxsend_error_title}");
}
}
?>
