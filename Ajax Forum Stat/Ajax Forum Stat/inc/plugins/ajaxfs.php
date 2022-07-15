<?php
/*
*
* ajaxfs Plugin
* Copyright 2011 mostafa shirali
* http://mypgr.ir
* No one is authorized to redistribute or remove copyright without my expressed permission.
*
*/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
$plugins->add_hook('index_start', 'ajaxfs');
$plugins->add_hook("xmlhttp", "Ajax_fs_do_action");
// The information that shows up on the plugin manager
function ajaxfs_info()
{
global $lang;
$lang->load("ajaxfs");
return array(
		"name" => $lang->ajaxfs_name,
		"description" =>$lang->ajaxfs_dec ,
		"website" => "http://mypgr.ir",
		"author" => "Mostafa shirali",
		"authorsite" => "http://mypgr.ir",
		"version" => "6.3.0",
        "guid"=> "saqswdxcdfefgttvg",
		"compatibility"	=> "18*"
);
}
// This function runs when the plugin is activated.
function ajaxfs_activate()
{
global $mybb, $db, $templates,$cache,$lang;
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets("index","#".preg_quote('{$forums}')."#i", '{\$ajaxfs_top_panel}{\$forums}{\$ajaxfs_down_panel}');
		$lang->load("ajaxfs");
		    $settings_group = array(
        "name" => "ajaxfs",
        "title" => $lang->ajaxfs_settinggroup,
        "description" => $lang->ajaxfs_settinggroup_dec,
        "disporder" => "88",
        "isdefault" => "0",
        );
    $db->insert_query("settinggroups", $settings_group);
    $gid = $db->insert_id();
	$positions = "select\ntop={$lang->ajaxfs_top}\ndown={$lang->ajaxfs_down}";
	$setting[] = array("name" => "ajaxfs_enable","title" => $lang->ajaxfs_active,"description" => $lang->ajaxfs_active_dec,"optionscode" => "yesno","value" => "0","disporder" => 1,"gid" => intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_lastpost','title'=> $lang->ajaxfs_last_post,'description'	=> $lang->ajaxfs_last_post_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 2,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_mostview','title'=> $lang->ajaxfs_view_post,'description'	=> $lang->ajaxfs_view_post_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 3,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_hottopics','title'=> $lang->ajaxfs_most_reply,'description'	=> $lang->ajaxfs_most_reply_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 4,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_lastuser','title'=> $lang->ajaxfs_last_user,'description'	=> $lang->ajaxfs_last_user_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 5,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_mostposter','title'=> $lang->ajaxfs_most_sender,'description'	=> $lang->ajaxfs_most_sender_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 6,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_mostpoint','title'=> $lang->ajaxfs_top_point,'description'	=> $lang->ajaxfs_top_point_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 7,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_mostthanked','title'=> $lang->ajaxfs_top_thanks,'description'	=> $lang->ajaxfs_top_thanks_dec,'optionscode'=> 'yesno','value'=> '0','disporder'=> 8,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_mostthanker','title'=> $lang->ajaxfs_top_thankers,'description'	=> $lang->ajaxfs_top_thankers_dec,'optionscode'=> 'yesno','value'=> '0','disporder'=> 9,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_popularfile','title'=> $lang->ajaxfs_most_download,'description'	=> $lang->ajaxfs_most_download_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 10,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_Topreferrers','title'=> $lang->ajaxfs_most_reffer,'description'	=> $lang->ajaxfs_most_reffer_dec,'optionscode'=> 'yesno','value'=> '1','disporder'=> 11,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_customforum','title'=> $lang->ajaxfs_customforum,'description'	=> $lang->ajaxfs_customforum_dec,'optionscode'=> 'yesno','value'=> '0','disporder'=> 12,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_customforum_in','title'=> $lang->ajaxfs_customforum_in,'description'	=> $lang->ajaxfs_customforum_in_dec,'optionscode'=> 'textarea','value'=> $lang->ajaxfs_customforum_in_value,'disporder'=> 13,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_number_post','title'=> $lang->ajaxfs_number_post,'description'	=> $lang->ajaxfs_number_post_dec,'optionscode'=> 'text','value'=> 15,'disporder'=> 14,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_number_tops','title'=> $lang->ajaxfs_number_tops,'description'	=> $lang->ajaxfs_number_tops_dec,'optionscode'=> 'text','value'=> 15,'disporder'=> 15,'gid'=> intval($gid),);
    $setting[] = array( 'name'=> 'ajaxfs_position','title'=> $lang->ajaxfs_position,'description'	=> $lang->ajaxfs_position_dec,'optionscode'=> $positions,'value'=> 'down','disporder'=> 16,'gid'=> intval($gid),);
 	foreach ($setting as $i)
	{
		$db->insert_query("settings", $i);
	}
rebuild_settings();

}






function ajaxfs()
{
	global $mybb,$lang,$db,$ajaxfs_top_panel,$ajaxfs_down_panel,$theme,$parser,$templates;
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	$bburl=$mybb->settings['bburl'];

		if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
if($mybb->settings['ajaxfs_enable'] ==1)
{
	$lang->load("ajaxfs");
	if($mybb->settings['ajaxfs_lastpost'] ==1)
	{
	$post_info_option .='<input id="lastpost" type="radio" name="tabs" checked><label for="lastpost">'.$lang->ajaxfs_last_post.'</label>';
	}
	if($mybb->settings['ajaxfs_mostview'] ==1)
	{
	$post_info_option .='<input id="mosthit" type="radio" name="tabs" ><label for="mosthit">'.$lang->ajaxfs_view_post.'</label>';
	}
	if($mybb->settings['ajaxfs_hottopics'] ==1)
	{
	$post_info_option .='<input id="hotpost" type="radio" name="tabs" ><label for="hotpost">'.$lang->ajaxfs_most_reply.'</label>';
	}

/*********************************************** LAST POST *************************************************/
$userid=intval($mybb->user['uid']);
$gid=($userid)?$mybb->user['usergroup']:1;
$postnums=$mybb->settings['ajaxfs_number_post'];



$query = $db->query("SELECT fid,uid,username,tid,pid
FROM ".TABLE_PREFIX."posts WHERE visible='1'
AND pid IN(SELECT MAX(pid) FROM ".TABLE_PREFIX."posts GROUP BY tid ) ORDER BY dateline DESC LIMIT 0,{$postnums}");

$lastpost="<section id=\"lastpost\"><table cellspacing='0' id='Ajxfstable'>
<tr>
<th>{$lang->ajaxfs_lastpost_title}</th>
<th>{$lang->ajaxfs_lastpost_starter}</th>
<th>{$lang->ajaxfs_lastpost_writer}</th>
</tr>";



	$post_counter=0;
	$rows=array();
	while($result = $db->fetch_array($query))
	{
		$rows[]=$result;
	}
	foreach($rows as $row)
	{
		$fid=$row['fid'];
		$query_fp = $db->simple_select('forumpermissions', 'canviewthreads,canview', "fid='$fid' AND gid='$gid'");
		$query_fpn=$db->num_rows($query_fp);

		$canread=1;
		if($query_fpn!=0)
		{
		$fetch_fp=$db->fetch_array($query_fp);
		if($fetch_fp['canviewthreads']==1)
		{
		$canread=1;
		}
		else
		{
		$canread=0;
		}
		}
		if(($query_fpn==0 AND $canread==1) || $fetch_fp['canview']==1)
		{
		$tid=$row['tid'];
		
		$query = $db->simple_select("threads", "firstpost,lastposteruid", "tid = '{$tid}'");
		$thread =$db->fetch_array($query);

		////Get Last poster

		$lpuid=$thread['lastposteruid'];
		$query_users = $db->query("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='{$lpuid}'");
		$fetch_users =$db->fetch_array($query_users);
		$lastposter=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$profilelink_lastposter=get_profile_link($lpuid);
		////Get Last poster

		////Get Starter
		
		$query = $db->simple_select("posts", "uid", "pid = '{$thread['firstpost']}'");
		$post = $db->fetch_array($query);
		$strateruid=$post['uid'];
		
		$query_users = $db->query("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$strateruid'");
		$fetch_users =$db->fetch_array($query_users);
		$strater=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$profilelink_starter=get_profile_link($strateruid);

		////Get Starter

		$query_view = $db->query("SELECT uid FROM ".TABLE_PREFIX."threadsread WHERE tid='$tid' AND uid='$userid'");
		$postlink = get_post_link($row['pid']);
		$post_pid=$row['pid'];
		if($db->num_rows($query_view)==0)
		{
		$read_image='<img src="'.$bburl.'/images/fs_unread.gif">';
		}
		else
		{
		$read_image='<img src="'.$bburl.'/images/fs_read.gif">';
		}

		$query_thread_sub = $db->query ("SELECT subject FROM ".TABLE_PREFIX."threads WHERE tid='$tid'");
		$fetch_thread_sub=$db->fetch_array($query_thread_sub);
		$thread_sub=htmlspecialchars_uni(substr($fetch_thread_sub['subject'],0,90));
		$post_counter++;
		$linenumber=$post_counter;
		$lastpost .='<tr>
		<td><span id="pagenumber">'.sprintf('%02d', $linenumber).'</span>&nbsp;&nbsp;&nbsp;'.$read_image.'&nbsp;&nbsp;&nbsp;<a href="'.$postlink.'" target="_blank" id="'.$post_pid.'" class="postitems" >  '.$thread_sub.'</a></td>
		<td><a href="'.$profilelink_starter.'" target="_blank" id="'.$strateruid.'" class="usersitems" >'.$strater.'</a></td>
		<td><a href="'.$profilelink_lastposter.'" target="_blank" id="'.$lastposteruid.'" class="usersitems" >'.$lastposter.'</a></td>
		</tr>';
		}
	}
	$lastpost .='</table></section>';	

/*********************************************** LAST POST *************************************************/

/************************************************ OTHER POSTS **********************************************/
	if($mybb->settings['ajaxfs_customforum'] ==1)
	{
	$other_cat=$mybb->settings['ajaxfs_customforum_in'];
	$category_number=explode('>>',$other_cat);
	if($category_number==1)
	{
	$category_name_part=explode('|',$other_cat);
	$category_name=$category_name_part[0];
	$forum_ids=preg_replace("#[^0-9,]#i",'', $category_name_part[1]);
	$post_info_option .='<input id="other_cat" type="radio" name="tabs" ><label for="other_cat">'.$category_name.'</label>';
		$query = $db->query("SELECT t1.*
		FROM ".TABLE_PREFIX."posts t1
		JOIN (
		SELECT pid
		FROM ".TABLE_PREFIX."posts
		WHERE fid
		IN (".$forum_ids.")
		AND visible='1' ORDER BY pid DESC
		)t2 ON t1.pid = t2.pid ORDER BY t1.dateline DESC");
$otherposts="<section id=\"other_cat\"><table cellspacing='0' id='Ajxfstable'><tr><th>{$lang->ajaxfs_lastpost_title}</th><th>{$lang->ajaxfs_lastpost_writer}</th></tr>";
		if($db->num_rows($query)==0)
		{
		$post_number=0;
		}
		else
		{
		if($db->num_rows($query)<=$mybb->settings['ajaxfs_number_post'])
		{
		$post_number=$db->num_rows($query);
		}
		else
		{
		$post_number=$mybb->settings['ajaxfs_number_post'];
		}

		}
	$post_counter=0;
	while($post_counter<$post_number)
	{
		$fetch=$db->fetch_array($query);
		$fid=$fetch['fid'];
		$query_forum_permissins=$db->query ("SELECT canviewthreads,canview FROM  ".TABLE_PREFIX."forumpermissions WHERE fid='$fid' AND gid='$gid'");
		$canread=1;
		if($db->num_rows($query_forum_permissins)!=0)
		{
		$fetch_forum_permissins=$db->fetch_array($query_forum_permissins);
		if($fetch_forum_permissins['canviewthreads']==1)
		{
		$canread=1;
		}
		else
		{
		$canread=0;
		}
		}
		if($db->num_rows($query_forum_permissins)==0 AND $canread==1 AND ($db->num_rows($query_forum_permissins)==0 || $fetch_forum_permissins['canview']==1))
		{
		$profilelink=$bburl.'/member.php?action=profile&uid='.$fetch['uid'];
		$lastposter=$fetch['username'];
		$lastposteruid=$fetch['uid'];
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$lastposteruid'");
		$fetch_users =$db->fetch_array($query_users);
		$lastposter=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$profilelink_lastposter=$bburl.'/member.php?action=profile&uid='.$lastposteruid;
		$tid=$fetch['tid'];
		$uid=$mybb->user['uid'];
		$query_view = $db->query("SELECT uid FROM ".TABLE_PREFIX."threadsread WHERE tid='$tid' AND uid='$uid'");
		$threadlink = get_post_link($fetch['pid']);
		$post_pid=$fetch['pid'];
		if($db->num_rows($query_view)==0)
		{
		$read_image='<img src="'.$bburl.'/images/fs_unread.gif">';
		}
		else
		{
		$read_image='<img src="'.$bburl.'/images/fs_read.gif">';
		}
		$thread_id=$fetch['tid'];
		$query_thread_sub = $db->query ("SELECT subject FROM ".TABLE_PREFIX."threads WHERE tid='$thread_id'");
		$fetch_thread_sub=$db->fetch_array($query_thread_sub);
		$thread_sub=htmlspecialchars_uni(substr($fetch_thread_sub['subject'],0,90));
		$linenumber=$post_counter+1;
		$otherposts .='<tr><td><span id="pagenumber">'.sprintf('%02d', $linenumber).'</span>&nbsp;&nbsp;&nbsp;'.$read_image.'&nbsp;&nbsp;&nbsp;<a href="'.$threadlink.'" target="_blank" id="'.$post_pid.'" class="postitems" >  '.$thread_sub.'</a></td><td><a href="'.$profilelink_lastposter.'" target="_blank" id="'.$lastposteruid.'" class="usersitems" >'.$lastposter.'</a></td></tr>';
	$post_counter++;
	}
	}
	$otherposts .='</table></section>';
	$other_cat_info .=$otherposts;
	}
	else //If Other Table More One
	{
	for($i=0;$i<count($category_number);$i++)
	{
	$category_name_part=explode('|',$category_number[$i]);
	$category_name=$category_name_part[0];
	$forum_ids=preg_replace("#[^0-9,]#i",'', $category_name_part[1]);
	$post_info_option .='<input id="other_cat_'.$i.'" type="radio" name="tabs" ><label for="other_cat_'.$i.'">'.$category_name.'</label>';
		$query = $db->query ("SELECT t1.*
FROM ".TABLE_PREFIX."posts t1
JOIN (
SELECT pid
FROM ".TABLE_PREFIX."posts
WHERE fid
IN (".$forum_ids.")
AND visible='1' ORDER BY pid DESC
)t2 ON t1.pid = t2.pid ORDER BY t1.dateline DESC");
$otherposts="<section id=\"other_cat_'.$i.'\"><table cellspacing='0' id='Ajxfstable'><tr><th>{$lang->ajaxfs_lastpost_title}</th><th>{$lang->ajaxfs_lastpost_writer}</th></tr>";
if($db->num_rows($query)==0)
{
$post_number=0;
}
else
{
if($db->num_rows($query)<=$mybb->settings['ajaxfs_number_post'])
{
$post_number=$db->num_rows($query);
}
else
{
$post_number=$mybb->settings['ajaxfs_number_post'];
}

}
$post_counter=0;
	while($post_counter<$post_number)
	{
		$fetch=$db->fetch_array($query);
		$fid=$fetch['fid'];
		$query_forum_permissins=$db->query ("SELECT canviewthreads,canview FROM  ".TABLE_PREFIX."forumpermissions WHERE fid='$fid' AND gid='$gid'");
		$canread=1;
		if($db->num_rows($query_forum_permissins)!=0)
		{
		$fetch_forum_permissins=$db->fetch_array($query_forum_permissins);
		if($fetch_forum_permissins['canviewthreads']==1)
		{
		$canread=1;
		}
		else
		{
		$canread=0;
		}
		}
		if($db->num_rows($query_forum_permissins)==0 AND $canread==1 AND ($db->num_rows($query_forum_permissins)==0 || $fetch_forum_permissins['canview']==1))
		{
		$profilelink=$bburl.'/member.php?action=profile&uid='.$fetch['uid'];
		$lastposter=$fetch['username'];
		$lastposteruid=$fetch['uid'];
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$lastposteruid'");
		$fetch_users =$db->fetch_array($query_users);
		$lastposter=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$profilelink_lastposter=$bburl.'/member.php?action=profile&uid='.$lastposteruid;
		$tid=$fetch['tid'];
		$uid=$mybb->user['uid'];
		$query_view = $db->query("SELECT uid FROM ".TABLE_PREFIX."threadsread WHERE tid='$tid' AND uid='$uid'");
		$threadlink = get_post_link($fetch['pid']);
		$post_pid=$fetch['pid'];
		if($db->num_rows($query_view)==0)
		{
		$read_image='<img src="'.$bburl.'/images/fs_unread.gif">';
		}
		else
		{
		$read_image='<img src="'.$bburl.'/images/fs_read.gif">';
		}
		$thread_id=$fetch['tid'];
		$query_thread_sub = $db->query ("SELECT subject FROM ".TABLE_PREFIX."threads WHERE tid='$thread_id'");
		$fetch_thread_sub=$db->fetch_array($query_thread_sub);
		$thread_sub=htmlspecialchars_uni(substr($fetch_thread_sub['subject'],0,90));
		$linenumber=$post_counter+1;
		$otherposts .='<tr><td><span id="pagenumber">'.sprintf('%02d', $linenumber).'</span>&nbsp;&nbsp;&nbsp;'.$read_image.'&nbsp;&nbsp;&nbsp;<a href="'.$threadlink.'" target="_blank" id="'.$post_pid.'" class="postitems" >  '.$thread_sub.'</a></td><td><a href="'.$profilelink_lastposter.'" target="_blank" id="'.$lastposteruid.'" class="usersitems" >'.$lastposter.'</a></td></tr>';
	$post_counter++;
	}
	}
	$otherposts .='</table></section>';
	$other_cat_info .=$otherposts;
	}
	
	}


	}
/************************************************ OTHER POSTS **********************************************/
/************************************************ Last user **********************************************/

	$query_user = $db->query ("SELECT uid FROM ".TABLE_PREFIX."users ORDER BY uid DESC LIMIT 0,".(int)$mybb->settings['ajaxfs_number_tops']);
	while($fetch_user =$db->fetch_array($query_user))
	{
		$uid=$fetch_user['uid'];
		$profile_user_link=$bburl.'/member.php?action=profile&uid='.$uid;
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
		$fetch_users =$db->fetch_array($query_users);
		$username=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);

	$lastuser .='<font style="hieght:18.75px;align:right;text-align:center;float:right;vertical-align:top;"><a href="'.$profile_user_link.'" target="_blank"  id="'.$uid.'" class="usersitems" >'.$username.'</a></font><br/>';

	}
/************************************************ Last user **********************************************/
/************************************************ Top poster **********************************************/
	$query_top_poster = $db->query ("SELECT uid,postnum FROM ".TABLE_PREFIX."users ORDER BY postnum DESC LIMIT 0,".(int)$mybb->settings['ajaxfs_number_tops']);
	while($fetch_top_poster =$db->fetch_array($query_top_poster))
	{
		$uid=$fetch_top_poster['uid'];
		$profile_top_poster_link=get_profile_link($uid);
		$top_poster_post_link=$bburl.'/search.php?action=finduser&uid='.$uid;
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
		$fetch_users =$db->fetch_array($query_users);
		$username=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
	$top_poster .='<font style="hieght:18.75px;align:right;text-align:right;float:right;vertical-align:top;"><a href="'.$profile_top_poster_link.'" target="_blank"  id="'.$uid.'" class="usersitems" >'.$username.'</a></font><span style="hieght:20px;align:left;text-align:left;float:left;"><a href="'.$top_poster_post_link.'" target="_blank">'.$fetch_top_poster['postnum'].'</a></span><br>';
}
/************************************************ Top poster **********************************************/
/************************************************ Top ponit **********************************************/
	$query_top_reputation = $db->query ("SELECT uid,reputation FROM ".TABLE_PREFIX."users ORDER BY reputation DESC LIMIT 0,".(int)$mybb->settings['ajaxfs_number_tops']);
	while($fetch_top_reputation =$db->fetch_array($query_top_reputation))
	{
		$uid=$fetch_top_reputation['uid'];
		$profile_top_reputation_link=get_profile_link($uid);
		$top_reputation_reputation_link=$bburl.'/reputation.php?uid='.$uid;
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
		$fetch_users =$db->fetch_array($query_users);
		$username=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$top_ponit .='<font style="hieght:18.75px;align:right;text-align:right;float:right;vertical-align:top;"><a href="'.$profile_top_reputation_link.'" target="_blank" id="'.$uid.'" class="usersitems" >'.$username.'</a></font><span style="hieght:20px;align:left;text-align:left;float:left;"><a href="'.$top_reputation_reputation_link.'" target="_blank">'.$fetch_top_reputation['reputation'].'</a></span><br>';
}
/************************************************ Top ponit **********************************************/
/************************************************ Top thanks **********************************************/
	if($mybb->settings['ajaxfs_mostthanked'] ==1)
	{
	$query_top_thank = $db->query ("SELECT uid,thxcount FROM ".TABLE_PREFIX."users ORDER BY thxcount DESC LIMIT 0,".(int)$mybb->settings['ajaxfs_number_tops']);
	$num_rows=$db->num_rows($query_top_thank);
	while($fetch_top_thank =$db->fetch_array($query_top_thank))
	{
		$uid=$fetch_top_thank['uid'];
		$profile_top_thank_link=get_profile_link($uid);
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
		$fetch_users =$db->fetch_array($query_users);
		$username=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$top_thanks .='<font style="hieght:18.75px;align:right;text-align:right;float:right;vertical-align:top;color: black;"><a href="'.$profile_top_thank_link.'" target="_blank" id="'.$uid.'" class="usersitems" >'.$username.'</a></font><span style="hieght:20px;align:left;text-align:left;float:left;">'.$fetch_top_thank['thxcount'].'</span><br>';

	}
	}
/************************************************ Top Do Thamk **********************************************/

	if($mybb->settings['ajaxfs_mostthanker'] ==1)
	{
	$query_top_thank = $db->query ("SELECT uid,thx FROM ".TABLE_PREFIX."users ORDER BY thx DESC LIMIT 0,".(int)$mybb->settings['ajaxfs_number_tops']);
	while($fetch_top_thank =$db->fetch_array($query_top_thank))
	{
		$uid=$fetch_top_thank['uid'];
		$profile_top_thank_link=get_profile_link($uid);
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
		$fetch_users =$db->fetch_array($query_users);
		$username=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$top_thank_do .='<font style="hieght:18.75px;align:right;text-align:right;float:right;vertical-align:top;color: black;"><a href="'.$profile_top_thank_link.'" target="_blank" id="'.$uid.'" class="usersitems" >'.$username.'</a></font><span style="hieght:20px;align:left;text-align:left;float:left;">'.$fetch_top_thank['thx'].'</span><br>';

	}
	}
/************************************************ Top Do Thamk **********************************************/
/************************************************ Top File **********************************************/
	function SubjectLength($subject, $length="", $half=false)
	{
	global $mybb;
	$length = $length ? intval($length) : intval('25');
	$half ? $length = ceil($length/2) : NULL;
	if ($length != 0)
	{
		if (my_strlen($subject) > $length) 
		{
			$subject = my_substr($subject,0,$length) . '...';
		}
	}
	return $subject;
	}
		

	$query_top_file = $db->query ("SELECT pid,downloads FROM ".TABLE_PREFIX."attachments ORDER BY downloads DESC LIMIT 0,".(int)$mybb->settings['ajaxfs_number_tops']);

	while($fetch_top_file =$db->fetch_array($query_top_file))
	{
		
		$pid=$fetch_top_file['pid'];
		$query_post = $db->query("SELECT subject FROM ".TABLE_PREFIX."posts WHERE pid='$pid'");
		$query_post_fetch=$db->fetch_array($query_post);

		$subject = htmlspecialchars_uni(SubjectLength($parser->parse_badwords($query_post_fetch['subject']), NULL, true));
	$postlink = get_post_link($pid)."#pid".$pid;
	$top_file .='<font style="hieght:18.75px;align:right;text-align:right;float:right;vertical-align:top;color: black;"><a href="'.$postlink.'" target="_blank">'.$subject.'</a></font><span style="hieght:20px;align:left;text-align:left;float:left;">'.$fetch_top_file['downloads'].'</span><br>';

	}
/************************************************ Top File **********************************************/		
/************************************************ Top Reffer **********************************************/		
	$query = $db->query("
	SELECT u.uid,u.username,u.usergroup,u.displaygroup,count(*) as refcount 
	FROM ".TABLE_PREFIX."users u 
	LEFT JOIN ".TABLE_PREFIX."users r ON (r.referrer = u.uid) 
	WHERE r.referrer = u.uid 
	GROUP BY r.referrer DESC 
	ORDER BY refcount DESC 
	LIMIT 0 ,15");

	while($topreferrer=$db->fetch_array($query ))
	{
		$uid = $topreferrer['uid'];
		$refnum = $topreferrer['refcount'];
		$profilelink = get_profile_link($uid);
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
		$fetch_users =$db->fetch_array($query_users);
		$username=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$top_reffer .='<font style="hieght:18.75px;align:right;text-align:right;float:right;vertical-align:top;color: black;"><a href="'.$profilelink.'" target="_blank" id="'.$uid.'"  class="usersitems" >'.$username.'</a></font><span style="hieght:20px;align:left;text-align:left;float:left;">'.$refnum.'</span><br>';

	}
/************************************************ Top Reffer **********************************************/		

	if($mybb->settings['ajaxfs_lastuser'] ==1)
	{
	$user_info_option .='<div><input id="last_user" name="accordion-1" type="checkbox" />
	<label for="last_user">'.$lang->ajaxfs_last_user.'</label><div class="article ac-small">'.$lastuser.'</div></div>';
	}
	if($mybb->settings['ajaxfs_mostposter'] ==1)
	{
	$user_info_option .='<div><input id="most_sender" name="accordion-1" type="checkbox" />
	<label for="most_sender">'.$lang->ajaxfs_most_sender.'</label><div class="article ac-small">'.$top_poster.'</div></div>';
	}
	if($mybb->settings['ajaxfs_mostpoint'] ==1)
	{
	$user_info_option .='<div><input id="top_point" name="accordion-1" type="checkbox" />
	<label for="top_point">'.$lang->ajaxfs_top_point.'</label><div class="article ac-small">'.$top_ponit.'</div></div>';
	}
	if($mybb->settings['ajaxfs_mostthanked'] ==1)
	{
	$user_info_option .='<div><input id="top_thanks" name="accordion-1" type="checkbox" />
	<label for="top_thanks">'.$lang->ajaxfs_top_thanks.'</label><div class="article ac-small">'.$top_thanks.'</div></div>';
	}
	if($mybb->settings['ajaxfs_mostthanker'] ==1)
	{
	$user_info_option .='<div><input id="top_thankers" name="accordion-1" type="checkbox" />
	<label for="top_thankers">'.$lang->ajaxfs_top_thankers.'</label><div class="article ac-small">'.$top_thank_do.'</div></div>';
	}
	if($mybb->settings['ajaxfs_popularfile'] ==1)
	{
	$user_info_option .='<div><input id="most_download" name="accordion-1" type="checkbox" />
	<label for="most_download">'.$lang->ajaxfs_most_download.'</label><div class="article ac-small">'.$top_file.'</div></div>';
	}
	if($mybb->settings['ajaxfs_Topreferrers'] ==1)
	{
	$user_info_option .='<div><input id="most_reffer" name="accordion-1" type="checkbox" />
	<label for="most_reffer">'.$lang->ajaxfs_most_reffer.'</label><div class="article ac-small">'.$top_reffer.'</div></div>';
	}	
$ajaxfs_panel = '<head>
<link rel="stylesheet" href="'.$mybb->asset_url.'/jscripts/ajaxfs.css" type="text/css" media="screen" />
<script type="text/javascript" src="'.$mybb->asset_url.'/jscripts/ajaxfs.js"></script>
</head>
<div id="spin" style="position: fixed;top: 50%;left: 50%;margin-top: -50px;margin-left: -100px;"></div><br\><table border="0"  cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder" height="300px">
<tr>
<td class="thead" colspan="2" align="right"><strong>'.$lang->ajaxfs_panel.'</strong></td>
</tr>
<tr>
<td class="tcat" colspan="2" align="right">
<img src="images/ajfs_ref.png" id="ajfs_ref_posts" alt="'.$lang->ajaxfs_lastpost_tltp.'" onmouseover="ajfs_ref_post_tooltip(event)" onmouseout="ajfs_ref_post_tooltip_out(event)" onmousemove="ajfs_ref_post_tooltip_move(event)" /><img src="images/ajfs_ref_top.png" id="ajfs_ref_top" alt="'.$lang->ajaxfs_top_tltp.'" onmouseover="ajfs_ref_top_tooltip(event)" onmouseout="ajfs_ref_top_tooltip_out(event)" onmousemove="ajfs_ref_top_tooltip_move(event)" />
</td>
</tr>
	<tr>
		<td width="25%"   class="trow2" style="padding: 5px 5px 0px; vertical-align: top; background: rgb252, 254, 255 none repeat scroll 0% 0%;">
			<div class="accordion">
			'.$user_info_option.'
			</div>
			</td>
		<td  width="75%" class="trow2" style="padding: 5px 5px 0px;vertical-align:top;">
			<main id="majaxfs">
                '.$post_info_option.'
				'.$lastpost.'
				<section id="mosthit"></section>
				<section id="hotpost"></section>
				'.$other_cat_info.'
				</main></td>
			
	</tr>
</table><div style="text-align: left; font-size: 10px;"> Ajax Forum Stat by <a href="http://mypgr.ir" target="blank">Mostafa</a></div><br><br>';	
	if($mybb->settings['ajaxfs_position'] =='top')
	{
	$ajaxfs_down_panel='';
	$ajaxfs_top_panel = $ajaxfs_panel;

	}	
	if($mybb->settings['ajaxfs_position'] =='down')
	{
	$ajaxfs_top_panel='';
	$ajaxfs_down_panel = $ajaxfs_panel;

	}
	

}
}
	function Ajax_fs_do_action()
	{
	global $mybb, $db,$lang,$parser;
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	$lang->load("ajaxfs");
	if(($mybb->input['action'] != "tooltip" AND 
		$mybb->input['action'] != "usertooltip" AND 
		$mybb->input['action'] != "ajxfs_lastpost" AND 
		$mybb->input['action'] != "ajxfs_hotposts" AND 
		$mybb->input['action'] != "ajxfs_mosthitpost" AND 
		$mybb->input['action'] != "ajxfs_Tops_lastuser" ) || $mybb->request_method != "post")
	{
		return false;
	}
 /********************************************** POST Tool TIP ***********************************************/
	if($mybb->input['action']=="tooltip")
	{
	$pid=$db->escape_string($mybb->input['pid']);
	$query_post = $db->query ("SELECT tid,dateline,username FROM ".TABLE_PREFIX."posts WHERE pid='$pid'");
	$query_post_fetch=$db->fetch_array($query_post);
	$tid=$query_post_fetch['tid'];
	$query_thread = $db->query ("SELECT username,views,replies,attachmentcount,numratings,subject,dateline,fid,poll FROM ".TABLE_PREFIX."threads WHERE tid='$tid'");
	$query_thread_fetch=$db->fetch_array($query_thread);
	$starter = $query_thread_fetch['username'];
	$views = intval($query_thread_fetch['views']);
	$replies = intval($query_thread_fetch['replies']);
	$attachment = intval($query_thread_fetch['attachmentcount']);
	$threadrating = intval($query_thread_fetch['numratings']);
	$threadsubject = htmlspecialchars_uni($query_thread_fetch['subject']);
	$startdate = my_date($mybb->settings['dateformat'],$query_thread_fetch['dateline']);
	$starttime = my_date($mybb->settings['timeformat'], $query_thread_fetch['dateline']);
	$fid=$query_thread_fetch['fid'];
	$query_forum = $db->query ("SELECT name FROM ".TABLE_PREFIX."forums WHERE fid='$fid'");
	$query_forum_fetch=$db->fetch_array($query_forum);
	$forumname=$query_forum_fetch['name'];
	$lastdate = my_date($mybb->settings['dateformat'],$query_post_fetch['dateline']);
	$lasttime = my_date($mybb->settings['timeformat'],$query_post_fetch['dateline']);
	$lastposter = $query_post_fetch['username'];
	$poll = intval($query_thread_fetch['poll']);
	if($attachment!=0)
	{
	$attachment_info=$lang->ajaxfs_attachment_true;
	}
	else
	{
	$attachment_info=$lang->ajaxfs_attachment_false;
	}
	if($poll!=0)
	{
	$poll_info=$lang->ajaxfs_poll_true;
	}
	else
	{
	$poll_info=$lang->ajaxfs_poll_false;
	}
	$tooltip='
	<p style="align:right;">'.$lang->ajaxfs_thread_subject .' : '.$threadsubject.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_forum_name .' : '.$forumname.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_start_thread .' : '.$starter.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_time_start .' : '.$startdate.','.$starttime.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_last_post .' : '.$lastposter.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_time_last .' : '.$lastdate.','.$lasttime.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_views .' : '.$views.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_replies .' : '.$replies.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_attachment .' : '.$attachment_info.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_rating .' : '.$threadrating.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_poll .' : '.$poll_info.'</p><br\>
	';
	echo $tooltip;
	exit();
	}
/********************************************** POST Tool TIP ***********************************************/
/********************************************** USER Tool TIP ***********************************************/
	if($mybb->input['action']=="usertooltip")
	{
	$uid=$db->escape_string($mybb->input['uid']);
	$query_user = $db->query ("SELECT postnum,reputation,regdate,lastactive,lastvisit,lastpost FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
	$query_user_fetch=$db->fetch_array($query_user);
	$registertime_data = my_date($mybb->settings['dateformat'],$query_user_fetch['regdate']);
	$lastactive_data = my_date($mybb->settings['dateformat'],$query_user_fetch['lastactive']);
	$lastvisit_data = my_date($mybb->settings['dateformat'],$query_user_fetch['lastvisit']);
	$lastpost_data = my_date($mybb->settings['dateformat'],$query_user_fetch['lastpost']);
	$registertime_time = my_date($mybb->settings['timeformat'],$query_user_fetch['regdate']);
	$lastactive_time = my_date($mybb->settings['timeformat'],$query_user_fetch['lastactive']);
	$lastvisit_time = my_date($mybb->settings['timeformat'],$query_user_fetch['lastvisit']);
	$lastpost_time = my_date($mybb->settings['timeformat'],$query_user_fetch['lastpost']);
	$postnum=$query_user_fetch['postnum'];
	$reputation=$query_user_fetch['reputation'];
	$usertooltip='
	<p style="align:right;">'.$lang->ajaxfs_register_time .' : '.$registertime_data.','.$registertime_time.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_user_lastactive .' : '.$lastactive_data.','.$lastactive_time.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_user_lastvisit .' : '.$lastvisit_data.','.$lastvisit_time.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_user_lastpost .' : '.$lastpost_data.','.$lastpost_time.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_user_postnum .' : '.$postnum.'</p><br\>
	<p style="align:right;">'.$lang->ajaxfs_user_reputation .' : '.$reputation.'</p><br\>
	';
	echo $usertooltip;
	
	}
/********************************************** USER Tool TIP ***********************************************/
/********************************************** Refresh in forum ***********************************************/

	/************************************** HOT POSTS *************************************/
	if($mybb->input['action']=="ajxfs_hotposts")
	{
/************************************************ HOT POSTS **********************************************/
$hotpost="<table cellspacing='0' id='Ajxfstable'><tr><th>{$lang->ajaxfs_lastpost_title}</th><th>{$lang->ajaxfs_rep}</th></tr>";
		$lang->load("ajaxfs");
		$post_number=$mybb->settings['ajaxfs_number_post'];
		$query = $db->query ("SELECT fid,tid,subject,replies FROM ".TABLE_PREFIX."threads
		WHERE visible='1' ORDER BY replies DESC LIMIT 0,{$post_number}");

$post_counter=0;
	$results=array();
	while($row =$db->fetch_array($query))
	{
		$results[]=$row;
	}

	foreach($results as $fetch)
	{

		$fid=$fetch['fid'];
		$query_fp=$db->query ("SELECT canviewthreads,canview FROM  ".TABLE_PREFIX."forumpermissions WHERE fid='$fid' AND gid='$gid'");
		$canread=1;
		$fpn=$db->num_rows($query_fp);
		if($fpn!=0)
		{
		$fetch_forum_permissins=$db->fetch_array($query_fp);
		if($fetch_forum_permissins['canviewthreads']==1)
		{
		$canread=1;
		}
		else
		{
		$canread=0;
		}
		}
		if($fpn==0 AND $canread==1 AND ($fpn==0 || $fetch_forum_permissins['canview']==1))
		{
		$threadlink = get_thread_link($fetch['tid']);
		$tid=$fetch['tid'];
		$uid=$mybb->user['uid'];
		$query_view = $db->query("SELECT uid FROM ".TABLE_PREFIX."threadsread WHERE tid='$tid' AND uid='$uid'");
		if($db->num_rows($query_view)==0)
		{
		$read_image='<img src="'.$mybb->settings['bburl'].'/images/fs_unread.gif">';
		}
		else
		{
		$read_image='<img src="'.$mybb->settings['bburl'].'/images/fs_read.gif">';
		}
	$thread_sub=(substr($fetch['subject'],0,90));
	$linenumber=$post_counter+1;
	$hotpost .='<tr '.$even.'><td><span id="pagenumber">'.sprintf('%02d', $linenumber).'</span>&nbsp;&nbsp;&nbsp;'.$read_image.'&nbsp;&nbsp;&nbsp;<a href="'.$threadlink.'" target="_blank">'.$thread_sub.'</a></td><td>'.$fetch['replies'].'</td></tr>';

	$post_counter++;
	}
	}
	$hotpost.='</table></section>';
/************************************************ HOT POSTS **********************************************/		
	echo $hotpost;
	exit();
	}
	/************************************** HOT POSTS *************************************/
	/************************************** MOST HIT POST *************************************/
if($mybb->input['action']=="ajxfs_mosthitpost")
	{
		/*************************************************** Most Viewed ***************************************/

$most_hit="<table cellspacing='0' id='Ajxfstable'><tr><th>{$lang->ajaxfs_lastpost_title}</th><th>{$lang->ajaxfs_view}</th></tr>";
		$lang->load("ajaxfs");
	$query = $db->query("SELECT fid,tid,subject,views FROM ".TABLE_PREFIX."threads WHERE visible='1' ORDER BY views DESC LIMIT 0,15");
	
if($db->num_rows($query)==0)
{
$post_number=0;
}
else
{
if($db->num_rows($query)<=$mybb->settings['ajaxfs_number_post'])
{
$post_number=$db->num_rows($query);
}
else
{
$post_number=$mybb->settings['ajaxfs_number_post'];
}

}
$post_counter=0;

	$results=array();
	while($row =$db->fetch_array($query))
	{
		$results[]=$row;
	}

	foreach($results as $fetch)
	{
		$fid=$fetch['fid'];
		$query_forum_permissins=$db->query("SELECT canviewthreads,canview FROM  ".TABLE_PREFIX."forumpermissions WHERE fid='$fid' AND gid='$gid'");
		$canread=1;
		if($db->num_rows($query_forum_permissins)!=0)
		{
		$fetch_forum_permissins=$db->fetch_array($query_forum_permissins);
		if($fetch_forum_permissins['canviewthreads']==1)
		{
		$canread=1;
		}
		else
		{
		$canread=0;
		}
		}
		if($db->num_rows($query_forum_permissins)==0 AND $canread==1 AND ($db->num_rows($query_forum_permissins)==0 || $fetch_forum_permissins['canview']==1)) 
		{
		$threadlink = get_thread_link($fetch['tid']);
		$tid=$fetch['tid'];
		$uid=$mybb->user['uid'];
		$query_view = $db->query("SELECT uid FROM ".TABLE_PREFIX."threadsread WHERE tid='$tid' AND uid='$uid'");
		if($db->num_rows($query_view)==0)
		{
		$read_image='<img src="'.$mybb->settings['bburl'].'/images/fs_unread.gif">';
		}
		else
		{
		$read_image='<img src="'.$mybb->settings['bburl'].'/images/fs_read.gif">';
		}
	$thread_sub=htmlspecialchars_uni(substr($fetch['subject'],0,90));
$linenumber=$post_counter+1;
	$most_hit .='<tr '.$even.'><td><span id="pagenumber">'.sprintf('%02d', $linenumber).'</span>&nbsp;&nbsp;&nbsp;'.$read_image.'&nbsp;&nbsp;&nbsp;<a href="'.$threadlink.'" target="_blank">'.$thread_sub.'</a></td><td>'.$fetch['views'].'</td></tr>';
	$post_counter++;
	}
	}
	$most_hit .='</table>';
	
	echo $most_hit;
	exit();

	}
	/************************************** MOST HIT POST *************************************/
	/*********************************************** LAST POST *************************************************/
if($mybb->input['action']=="ajxfs_lastpost")
{
	
/*********************************************** LAST POST *************************************************/
$userid=intval($mybb->user['uid']);
$gid=($userid)?$mybb->user['usergroup']:1;
$postnums=$mybb->settings['ajaxfs_number_post'];
$bburl=$mybb->settings['bburl'];


$query = $db->query("SELECT fid,uid,username,tid,pid
FROM ".TABLE_PREFIX."posts WHERE visible='1'
AND pid IN(SELECT MAX(pid) FROM ".TABLE_PREFIX."posts GROUP BY tid ) ORDER BY dateline DESC LIMIT 0,{$postnums}");
$lastpost="<table cellspacing='0' id='Ajxfstable'>
<tr>
<th>{$lang->ajaxfs_lastpost_title}</th>
<th>{$lang->ajaxfs_lastpost_starter}</th>
<th>{$lang->ajaxfs_lastpost_writer}</th>
</tr>";



	$post_counter=0;
	$rows=array();
	while($result = $db->fetch_array($query))
	{
		$rows[]=$result;
	}
	foreach($rows as $row)
	{
		$fid=$row['fid'];
		$query_fp = $db->simple_select('forumpermissions', 'canviewthreads,canview', "fid='$fid' AND gid='$gid'");
		$query_fpn=$db->num_rows($query_fp);

		$canread=1;
		if($query_fpn!=0)
		{
		$fetch_fp=$db->fetch_array($query_fp);
		if($fetch_fp['canviewthreads']==1)
		{
		$canread=1;
		}
		else
		{
		$canread=0;
		}
		}
		if(($query_fpn==0 AND $canread==1) || $fetch_fp['canview']==1)
		{
		$tid=$row['tid'];

		$query = $db->simple_select("threads", "firstpost,lastposteruid", "tid = '{$tid}'");
		$thread =$db->fetch_array($query);

		////Get Last poster

		$lpuid=$thread['lastposteruid'];
		$query_users = $db->query("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='{$lpuid}'");
		$fetch_users =$db->fetch_array($query_users);
		$lastposter=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$profilelink_lastposter=get_profile_link($lpuid);
		////Get Last poster

		////Get Starter
		
		$query = $db->simple_select("posts", "uid", "pid = '{$thread['firstpost']}'");
		$post = $db->fetch_array($query);
		$strateruid=$post['uid'];
		
		$query_users = $db->query("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$strateruid'");
		$fetch_users =$db->fetch_array($query_users);
		$strater=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);
		$profilelink_starter=get_profile_link($strateruid);

		////Get Starter

		$query_view = $db->query("SELECT uid FROM ".TABLE_PREFIX."threadsread WHERE tid='$tid' AND uid='$userid'");
		$postlink = get_post_link($row['pid']);
		$post_pid=$row['pid'];
		if($db->num_rows($query_view)==0)
		{
		$read_image='<img src="'.$bburl.'/images/fs_unread.gif">';
		}
		else
		{
		$read_image='<img src="'.$bburl.'/images/fs_read.gif">';
		}

		$query_thread_sub = $db->query ("SELECT subject FROM ".TABLE_PREFIX."threads WHERE tid='$tid'");
		$fetch_thread_sub=$db->fetch_array($query_thread_sub);
		$thread_sub=htmlspecialchars_uni(substr($fetch_thread_sub['subject'],0,90));
		$post_counter++;
		$linenumber=$post_counter;
		$lastpost .='<tr>
		<td><span id="pagenumber">'.sprintf('%02d', $linenumber).'</span>&nbsp;&nbsp;&nbsp;'.$read_image.'&nbsp;&nbsp;&nbsp;<a href="'.$postlink.'" target="_blank" id="'.$post_pid.'" class="postitems" >  '.$thread_sub.'</a></td>
		<td><a href="'.$profilelink_starter.'" target="_blank" id="'.$strateruid.'" class="usersitems" >'.$strater.'</a></td>
		<td><a href="'.$profilelink_lastposter.'" target="_blank" id="'.$lastposteruid.'" class="usersitems" >'.$lastposter.'</a></td>
		</tr>';
		}
	}
	$lastpost .='</table>';	

/*********************************************** LAST POST *************************************************/

	echo $lastpost;
	exit();
	
	}
/********************************************** Refresh in forum ***********************************************/

	if($mybb->input['action']=="ajxfs_Tops_lastuser")
	{
	$query_user = $db->query ("SELECT uid FROM ".TABLE_PREFIX."users ORDER BY uid DESC LIMIT 0,".(int)$mybb->settings['ajaxfs_number_tops']);
	$num_rows=$db->num_rows($query_user);
	for($i=0;$i<$num_rows;$i++)
	{
		$fetch_user =$db->fetch_array($query_user);
		$profile_user_link=$bburl.'/member.php?action=profile&uid='.$fetch_user['uid'];
		$uid=$fetch_user['uid'];
		$query_users = $db->query ("SELECT username,usergroup,displaygroup FROM ".TABLE_PREFIX."users WHERE uid='$uid'");
		$fetch_users =$db->fetch_array($query_users);
		$username=format_name($fetch_users['username'],$fetch_users['usergroup'],$fetch_users['displaygroup']);

	$lastuser .='<font style="hieght:18.75px;align:right;text-align:center;float:right;vertical-align:top;"><a href="'.$profile_user_link.'" target="_blank"  id="'.$uid.'" class="usersitems" >'.$username.'</a></font><br/>';

	}
	$feedback=$lastuser;
	echo $feedback;
	exit();
	}
	}
	function ajaxfs_deactivate()
	{
	global $mybb, $db, $templates;
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets("index", '#'.preg_quote('{$ajaxfs_top_panel}').'#i', '',0);
	find_replace_templatesets("index", '#'.preg_quote('{$ajaxfs_down_panel}').'#i', '',0);
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='ajaxfs'");
	$db->delete_query("settings","name IN ('ajaxfs_enable','ajaxfs_lastpost','ajaxfs_mostview','ajaxfs_hottopics','ajaxfs_lastuser','ajaxfs_mostposter','ajaxfs_mostpoint','ajaxfs_mostthanked','ajaxfs_mostthanker','ajaxfs_popularfile','ajaxfs_Topreferrers','ajaxfs_customforum','ajaxfs_customforum_in','ajaxfs_number_post','ajaxfs_number_tops','ajaxfs_position')");
	rebuild_settings();

	}


?>