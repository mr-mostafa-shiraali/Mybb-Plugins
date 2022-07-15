<?php
/*
*
* Plugin Uploader Plugin
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

	//$plugins->add_hook('usercp_profile_start', 'pluginuploader_usercp_profile_start');
	$plugins->add_hook("admin_config_menu", "pluginuploader_user_nav");
	$plugins->add_hook("admin_config_action_handler", "pluginuploader_action_handler");


	function pluginuploader_info()
	{
		global $mybb, $db;

	return [
		"name" => "Plugin Uploader",
		"description" => "add Plugin Uploader to website",
		"website" => "https://t.me/MostafaShiraali",
		"author" => "Mostafa Shiraali",
		"authorsite" => "https://t.me/MostafaShiraali",
		"version" => "1.0",
		"guid"=> "##7##pluginuploader##7##",
		"compatibility"	=> "*"
		];
	}

	function pluginuploader_activate()
	{
		global $mybb, $db;
	}
	function pluginuploader_deactivate()
	{
		global $mybb, $db;
	}
	
	function pluginuploader_user_nav(&$sub_menu)
	{
		global $mybb, $lang;
				
			end($sub_menu);
			$key = (key($sub_menu))+10;
			
			if(!$key)
			{
				$key = '60';
			}
			
			$sub_menu[$key] = ['id' => 'pluginuploader', 'title' => "Plugin Uploader", 'link' => "index.php?module=config-pluginuploader"];

	}
	//action
	function pluginuploader_action_handler(&$action)
	{
		$action['pluginuploader'] = ['active' => 'pluginuploader', 'file' => 'pluginuploader.php'];
	}

	




