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

if(!$mybb->input['action'])
{
	$page->add_breadcrumb_item("Plugin Uploader", "index.php?module=config-pluginuploader");
	$page->add_breadcrumb_item("Install New Plugin", "index.php?module=config-pluginuploader");
	$page->output_header("Plugin Uploader");
	$form = new Form("index.php?module=config-pluginuploader&action=installplugin", "post" , "", true);
	$form_container = new FormContainer("Install New Plugin");
	$form_container->output_row("Select plugin zip file", "", $form->generate_file_upload_box('plugin_file', ['id' => 'plugin_file']), 'plugin_file');
	$form_container->end();

	$buttons[] = $form->generate_submit_button("Install Plugin");
	$form->output_submit_wrapper($buttons);

	$form->end();


}

function cepi_folder($path)
{
	mkdir($path, 0775, true);
	$index = @fopen(rtrim($path, '/').'/index.html', 'w');
	@fwrite($index, '<html>\n<head>\n<title></title>\n</head>\n<body>\n&nbsp;\n</body>\n</html>');
	@fclose($index);
}

if($mybb->input['action'] == "installplugin")
{

	
	if(class_exists( 'ZipArchive', false ))
	{
	if(!verify_post_check($mybb->get_input('my_post_key')))
	{
		flash_message("This Request is not valid.", 'error');
		admin_redirect("index.php?module=config-pluginuploader");	
	}
	
		$zip = new ZipArchive();//zip class object
		$path = MYBB_ROOT ."pluginuploader";
		if (!file_exists($path)) cepi_folder($path);
		if ($mybb->user['usergroup'] != 4)
		{
		flash_message("You do not have permission to do this", 'error');
		admin_redirect("index.php?module=config-pluginuploader");
		}
		
		$tmp_name = $_FILES["plugin_file"]["tmp_name"];
		$filename = basename($_FILES["plugin_file"]["name"]);
		
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if($ext == "zip")
		{
		$moved = @move_uploaded_file($_FILES['plugin_file']['tmp_name'],$path."/".$filename);
		if($moved)
		{
			$zip->open($path."/".$filename);
			if($zip->locateName('inc/') !== false) 
			{
			    $zip->extractTo(MYBB_ROOT);
				$zip->close();
				@unlink($path."/".$filename);
				flash_message("The plugin has been successfully installed", 'success');
				admin_redirect("index.php?module=config-pluginuploader");

			}
			else
			{
			$zip->close();
			@unlink($path."/".$filename);
			flash_message("Plugin directory in zip file not found", 'error');
			admin_redirect("index.php?module=config-pluginuploader");
			
			}
			
				
		}
		else
		{
		$zip->close();
		flash_message("There was a problem uploading the file, please try again", 'error');
		admin_redirect("index.php?module=config-pluginuploader");
		}
		}
		else
		{
		$zip->close();
		flash_message("Please Select a zip file", 'error');
		admin_redirect("index.php?module=config-pluginuploader");
		
		}

	}
	else
	{
		flash_message("The ZipArchive class is not activated on your server.Please activate this class first.", 'error');
		admin_redirect("index.php?module=config-pluginuploader");
		
	}

}