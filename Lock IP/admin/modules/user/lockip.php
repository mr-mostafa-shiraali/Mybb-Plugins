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

// Load language packs for this section
$lang->load("lockip");

$page->add_breadcrumb_item("{$lang->mp}", "index.php?module=user/lockip");

	switch ($mybb->input['action'])
	{


	case "lockip_add":
	$nav = "lockip_add";
    break;
	default:
    $nav = "lockip_home";

	}

log_admin_action();

	$page->output_header("{$lang->mp}");

	$sub_tabs['lockip_home'] = array(
		'title' => "{$lang->list}",
		'link' => "index.php?module=user/lockip",
		'description' => "{$lang->list_dec}"
	);

	$sub_tabs['lockip_add'] = array(
		'title' => "{$lang->add}",
		'link' => "index.php?module=user/lockip&amp;action=lockip_add",
		'description' => "{$lang->add_dec}");

	$page->output_nav_tabs($sub_tabs, $nav);


if($page->active_action != "lockip")
	{
		return;
	}



if($mybb->input['action'] == "lockip_add")
{

		$form = new Form("index.php?module=user/lockip&amp;action=lockip_save", "post", "lockip_save",1);

		$form_container = new FormContainer($lang->add);

	$form_container->output_row("{$lang->IP} <em>*</em>", "", $form->generate_text_box('userip', $mybb->input['userip'], array('id' => 'userip')), 'userip');


		$form_container->end();
		$form_container->construct_row();
	
		$buttons[] = $form->generate_submit_button("SUBMIT");
		$form->output_submit_wrapper($buttons);
		$form->end();
	

}


//Delete users
if($mybb->input['action'] == "lockip_delete")
{
$userip=($_GET['userip']);
$db->query("DELETE FROM ".TABLE_PREFIX."lockip WHERE uip='$userip'");
					flash_message("{$lang->del_alert}", 'success');
					admin_redirect("index.php?module=user/lockip");
}





if($mybb->input['action'] == "lockip_save")
{
$userip=($mybb->input['userip']);
if(!preg_match("#^(\d{1,3}-\d{1,3}|\d{1,3}|\*)\.(\d{1,3}-\d{1,3}|\d{1,3}|\*)\.(\d{1,3}-\d{1,3}|\d{1,3}|\*)\.(\d{1,3}-\d{1,3}|\d{1,3}|\*)$#i",$userip))
{
flash_message("{$lang->nvalid_alert}", 'error');
admin_redirect("index.php?module=user/lockip");
}
{
$query=$db->query("SELECT * FROM ".TABLE_PREFIX."lockip WHERE uip='$userip'");
$user=$db->fetch_array($query);
$row=$db->num_rows($query);
if($row==0)
{
					$insert = array( "uip" => $userip); 
					$db->insert_query("lockip", $insert);
					flash_message("{$lang->added_alert}", 'success');
					admin_redirect("index.php?module=user/lockip");
					

}
else
{
flash_message("{$lang->exist_alert}", 'error');
admin_redirect("index.php?module=user/lockip");
}
}

}

if(!$mybb->input['action'])
{


		$form = new Form("index.php?module=user/lockip", "post");

		$form_container = new FormContainer($lang->ipmt);
		$form_container->output_row_header("ID", array('class' => 'align_left', width => '75%'));
          $form_container->output_row_header("USER IP", array('class' => 'align_left', width => '75%'));
		$query = $db->simple_select("lockip", "*", "1=1");

		while($lockip = $db->fetch_array($query))
		{

			$form_container->output_cell("<div style=\"padding-left: ".(40*($depth-1))."px;\"><strong>{$lockip['id']}</strong></a><br /><small>{$lockip['uip']}</small></div>");

		$popup = new PopupMenu("award_{$lockip['id']}", "Options");

		$popup->add_item("{$lang->del_ip}", "index.php?module=user/lockip&amp;action=lockip_delete&amp;userip={$lockip['uip']}");

		$form_container->output_cell($popup->fetch(), array("class" => "align_center"));

		$form_container->construct_row();

		}

		$form_container->end();
		$form->end();

}
   $page->output_footer();

?>