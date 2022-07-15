<?php
/*
*
* Prefix Filter Plugin
* Copyright 2020 Mostafa Shiraali
* https://mypgr.ir
* No one is authorized to redistribute or remove copyright without my expressed permission.
* No one is authorized to share this plugin with other.
* Only those who purchased this plugin are allowed to use it.
* Ways to buy this plugin or order a new plugin
* Telegram : @MostafaShiraali
* Whatsapp : +989351567347
* Email : mr.mostafa.shiraali@gmail.com
*/
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

	$plugins->add_hook('forumdisplay_threadlist', 'PrefixFilter_forumdisplay_threadlist');

function PrefixFilter_info()
{
		global $mybb, $db,$lang;

	$lang->load("PrefixFilter");
return ["name" => $lang->pxfr,"description" => $lang->pxfr_inf_des,"website" => "https://mypgr.ir","author" => "Mostafa Shiraali","authorsite" => "https://mypgr.ir","version" => "1.1","guid"=> "##7##PrefixFilter##7##","compatibility"	=> "*"];
}



function PrefixFilter_activate()
{
	global $mybb, $db,$lang;
	$lang->load("PrefixFilter");
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets("forumdisplay_threadlist","#^#i", '{\$prefixfilter}');
	$settings_group = ["name" => "PrefixFilter","title" => $lang->pxfr,"description" => $lang->pxfr_des,"disporder" => "88","isdefault" => "0"];
	$db->insert_query("settinggroups", $settings_group);
    $gid = $db->insert_id();
	$setting[] = ["name" => "PrefixFilter_enable","title" => $lang->pxfr_act_opt,"description" => $lang->pxfr_act_opt_des,"optionscode" => "yesno","value" => "0","disporder" => 1,"gid" => intval($gid)];
	$setting[] = ["name" => "PrefixFilter_groups","title" => $lang->pxfr_grp_opt,"description" => $lang->pxfr_grp_opt_des,"optionscode" => "groupselect","value" => "4","disporder" => 2,"gid" => intval($gid)];
	$setting[] = ["name" => "PrefixFilter_forums","title" => $lang->pxfr_frm_opt,"description" => $lang->pxfr_frm_opt_des,"optionscode" => "forumselect","value" => "-1","disporder" => 3,"gid" => intval($gid)];
 	foreach ($setting as $i){$db->insert_query("settings", $i);}
	rebuild_settings();


	$db->insert_query("templates",  ["title"=> "PrefixFilter","template"=>  $db->escape_string('<div style="width: 88%;display: inline-block;margin: 10px 0px 11px 5px;">{$lang->pxfr_fbp} {$prefixes}<a style="margin: 0 3px 0 4px;text-decoration: aliceblue;" href="{$mybb->settings[\'bburl\']}/forumdisplay.php?fid={$fid}">{$lang->pxfr_all}</a></div>'),"sid"=> 1]);

}


	function PrefixFilter_deactivate()
	{
	global $mybb, $db;
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets("forumdisplay_threadlist", '#'.preg_quote('{$prefixfilter}').'#i', '',0);
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='PrefixFilter'");
	$db->delete_query("settings","name IN ('PrefixFilter_enable','PrefixFilter_groups','PrefixFilter_forums')");
	$db->delete_query("templates","title IN ('PrefixFilter')");
	rebuild_settings();

	}
	function PrefixFilter_forumdisplay_threadlist()
	{
	global $prefixfilter,$db,$fid,$mybb,$lang,$templates;
		$lang->load("PrefixFilter");

	//Check User Can Up Thread
	$allowed = false;
    $allowedGroups = explode(',', $mybb->settings['PrefixFilter_groups']);
    $additionalGroups = explode(',', $mybb->user['additionalgroups']);
	foreach ($additionalGroups as $gid) 
	{
        if (in_array($gid, $allowedGroups)) 
		{
		$allowed = true;
        }
    }

    if (in_array($mybb->user['usergroup'], $allowedGroups) || $mybb->settings['PrefixFilter_groups'] == "-1") {
        $allowed = true;
    }
	//Check User Can Up Thread

	//Check Forum
	$allowedForums = explode(',', $mybb->settings['PrefixFilter_forums']);
	//Check Forum

	if($mybb->settings['PrefixFilter_enable'] ==1 && $allowed && (in_array($fid, $allowedForums) || $mybb->settings['PrefixFilter_forums']=="-1"))
	{
		$prefix_cache = build_prefixes();

		if(!empty($prefix_cache))
		{
				foreach($prefix_cache as $prefix)
				{					
						$forums = explode(",", $prefix['forums']);
						if(!in_array($fid, $forums) || $prefix['forums'] == "-1")
						{
						$ql=$db->simple_select("threads","tid","fid='{$fid}' AND prefix='{$prefix['pid']}'");
						if($db->num_rows($ql)>0)
						{
						$prefixes .="<a style=\"margin: 0 3px 0 4px;text-decoration: aliceblue;\" href=\"{$mybb->settings['bburl']}/forumdisplay.php?fid={$fid}&prefix={$prefix['pid']}\">{$prefix['displaystyle']}</a>";
						}
						}
					
				}


		if($prefixes!="")
		{
        eval("\$prefixfilter = \"".$templates->get("PrefixFilter")."\";");
		}
		}
	
	}
	
	
	
	}



