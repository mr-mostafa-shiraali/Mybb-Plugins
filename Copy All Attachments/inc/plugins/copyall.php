<?php
/*
*
* Copy All Plugin
* Copyright 2022 Mostafa Shiraali
* No one is authorized to redistribute or remove copyright without my expressed permission.
* No one is authorized to share this plugin with other.
* Only those who purchased this plugin are allowed to use it.
* Ways to buy this plugin or order a new plugin
* Telegram : @MostafaShiraali
* Discord : MostafaShiraali#7754
* Email : mr.mostafa.shiraali@gmail.com
*/
	if(!defined("IN_MYBB"))
	{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
	}


	$plugins->add_hook('postbit', 'copyall_postbit');
	$plugins->add_hook('pre_output_page', 'copyall_scripts');
	$plugins->add_hook('portal_announcement', 'copyall_announcement');

	function copyall_info()
	{
		global $mybb, $db;

	return [
		"name" => "Copy All",
		"description" => "add copy button for attachments",
		"website" => "https://t.me/MostafaShiraali",
		"author" => "Mostafa Shiraali",
		"authorsite" => "https://t.me/MostafaShiraali",
		"version" => "1.0",
		"guid"=> "##7##copyall##7##",
		"compatibility"	=> "*"
		];
	}

	function copyall_activate()
	{
		global $mybb, $db;
		$gid = $db->insert_query("settinggroups", [
		"name" => "copyall",
		"title" => "Copy All",
		"description" => "Setting For Copy All",
		"disporder" => "88",
		"isdefault" => "0"
		]);
		$settings = [
		[
		"name" => "copyall_enable",
		"title" => "Active",
		"description" => "Do You Want Active Plugin?",
		"optionscode" => "yesno",
		"value" => "0",
		"disporder" => 1,
		"gid" => intval($gid)
		],
		[
		"name" => "copyall_groups",
		"title" => "User Groups",
		"description" => "Select User Groups Which Can Use Copy Button",
		"optionscode" => "groupselect",
		"value" => "4",
		"disporder" => 2,
		"gid" => intval($gid)
		],
		[
		"name" => "copyall_forums",
		"title" => "Forums",
		"description" => "Select Forums Which Can Use Copy Button ",
		"optionscode" => "forumselect",
		"value" => "-1",
		"disporder" => 3,
		"gid" => intval($gid)
		]
		];
	$db->insert_query_multiple("settings", $settings);
	rebuild_settings();
	
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets("postbit_attachments","/(<fieldset>)/", "$1{\$post['copyall']}");


	}
	function copyall_deactivate()
	{
		global $mybb, $db;
		$db->delete_query("settinggroups", "name = 'copyall'");
		$db->delete_query('settings', "name LIKE 'copyall_%'");
		rebuild_settings();
		require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
		find_replace_templatesets("postbit_attachments","/\{\$post\[\'copyall\'\]\}/", "",0);

	}

	function copyall_isallowed(&$fid)
	{
		global $mybb,$db;
		$allowed = false;
		$allowedGroups = explode(',', $mybb->settings['copyall_groups']);
		$additionalGroups = explode(',', $mybb->user['additionalgroups']);

		foreach ($additionalGroups as $gid) 
		{
			if (in_array($gid, $allowedGroups) && $gid != "") 
			{
			$allowed = true;
			}
		}

		if (in_array($mybb->user['usergroup'], $allowedGroups) || $mybb->settings['copyall_groups'] == "-1") {
			$allowed = true;
		}

		if($allowed)
		{
			$allowedForums = explode(',', $mybb->settings['copyall_forums']);
			if((in_array($fid, $allowedForums) || $mybb->settings['copyall_forums']=="-1"))
			{
				$allowed = true;
			}
			else
			{
			    $allowed = false;
			}
		}

		
		return $mybb->settings['copyall_enable'] && $allowed;
	}
	function copyall_postbit(&$post)
	{
		global $mybb,$db;
		if($post['attachments'] != "" && copyall_isallowed($post['fid']))
		{
		$downloadllicon = '<img src="'.$mybb->settings['bburl'].'/images/copydls.png" id="copyallatach">';
		$post['attachments'] = str_replace("</legend>","</legend>{$downloadllicon}",$post['attachments']);
		}
	}
	
	function copyall_announcement()
	{
		global $mybb,$db,$announcement,$post;
		
		if(copyall_isallowed($announcement['fid']))
		$post['copyall'] = '<img src="'.$mybb->settings['bburl'].'/images/copydls.png" id="copyallatach">';

	}

	function copyall_scripts(&$page)
	{
		global $mybb, $db;
		$post = $db->fetch_array($db->simple_select("posts", "fid", "pid = ".$mybb->get_input('pid', MyBB::INPUT_INT)));
		if(in_array(THIS_SCRIPT,["showthread.php","portal.php"]) && copyall_isallowed($post['fid']))
		{
			$script = '<style>
			#copyallatach{width: 24px;cursor: pointer;float: right;}
			</style>
			<script type="text/javascript" >
					$(document).ready(function() {
					$("img#copyallatach").on("click",function(){
						let files = [];
						$(this).parent("fieldset").find(\'a[href^="attachment.php?aid"]\').each(function( index ) {
							files.push("'.$mybb->settings['bburl'].'/" + $(this).attr("href"));
					 
					});
					 
							    var $attachments = $("<textarea>");
								$("body").append($attachments);
								$attachments.val(files.join("\r\n")).select();
								document.execCommand("copy");
								$attachments.remove();
								alert("The attached URLs have been copied successfully");
					}); 
					});
						</script>';
				$page = str_replace('</head>', "{$script}</head>", $page);
		
		}

	}





