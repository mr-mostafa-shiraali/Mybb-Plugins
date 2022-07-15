<?php 

		if(!defined("IN_MYBB"))
		{
			die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
		}


		$plugins->add_hook("member_register_start", "ploggedsrlp_reg");
		$plugins->add_hook("member_register_agreement", "ploggedsrlp_reg");
		$plugins->add_hook("member_login", "ploggedsrlp_log");


		function ploggedsrlp_info()
		{
			return array(
				"name"          => "Prevent Logged See Register/Login Page"."&nbsp;(<a href=\"https://mypgr.ir/programmer/\" target=\"_blank\">Order custom Plugin</a>)",
				"description"   => "Prevent Logged See Register/Login Page",
				"website"       => "https://mypgr.ir",
				"author"        => "Mostafa Shiraali",
				"authorsite"    => "https://mypgr.ir",
				"guid"=> "ploggedsrlp",
				"version"       => "1.0",
				"compatibility" => "*"
				);
		}
		function ploggedsrlp_activate()
		{
			global $mybb,$db;
		}
		function ploggedsrlp_deactivate()
		{
			global $db;
		}
		function ploggedsrlp_reg()
		{
			global $db,$mybb;
			$uid=$mybb->user['uid'];
			if($uid)
			{
				redirect($mybb->settings['bburl'],"You have already registered on the site");
			}
		}
		
		function ploggedsrlp_log()
		{
			global $db,$mybb;
			$uid=$mybb->user['uid'];
			if($uid)
			{
				redirect($mybb->settings['bburl'],"You have already logged on the site");
			}
		}
		
