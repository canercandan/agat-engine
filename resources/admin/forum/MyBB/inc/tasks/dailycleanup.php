<?php
/**
 * MyBB 1.4
 * Copyright � 2008 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id: dailycleanup.php 4304 2009-01-02 01:11:56Z chris $
 */

function task_dailycleanup($task)
{
	global $mybb, $db, $cache, $lang;
	
	require_once MYBB_ROOT."inc/functions_user.php";

	// Clear out sessions older than 24h
	$cut = TIME_NOW-60*60*24;
	$db->delete_query("sessions", "uid='0' AND time < '{$cut}'");

	// Delete old read topics
	if($mybb->settings['threadreadcut'] > 0)
	{
		$cut = TIME_NOW-($mybb->settings['threadreadcut']*60*60*24);
		$db->delete_query("threadsread", "dateline < '{$cut}'");
		$db->delete_query("forumsread", "dateline < '{$cut}'");
	}
	
	// Check PMs moved to trash over a week ago & delete them
	$timecut = TIME_NOW-(60*60*24*7);
	$query = $db->simple_select("privatemessages", "pmid, uid, folder", "deletetime<='{$timecut}' AND folder='4'");
	while($pm = $db->fetch_array($query))
	{
		$user_update[$pm['uid']] = $uid;
		$pm_update[] = $pm['pmid'];
	}
	
	if(!empty($pm_update))
	{
		$db->delete_query("privatemessages", "pmid IN(".implode(',', $pm_update).")");
	}
	
	if(!empty($user_update))
	{
		foreach($user_update as $uid)
		{
			update_pm_count($uid);
		}
	}
	
	$cache->update_most_replied_threads();
	$cache->update_most_viewed_threads();
	$cache->update_birthdays();
	
	add_task_log($task, $lang->task_dailycleanup_ran);
}
?>