<?php
/**
 * MyBB 1.4
 * Copyright � 2008 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id: warnings.php 4324 2009-03-05 21:23:18Z Tikitiki $
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'warnings.php');

$templatelist = '';
require_once "./global.php";
require_once MYBB_ROOT."/inc/functions_warnings.php";
require_once MYBB_ROOT."inc/functions_modcp.php";

require_once MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;

$lang->load("warnings");

if($mybb->settings['enablewarningsystem'] == 0)
{
	error($lang->error_warning_system_disabled);
}

// Expire old warnings
expire_warnings();

// Actually warn a user
if($mybb->input['action'] == "do_warn" && $mybb->request_method == "post")
{
	// Verify incoming POST request
	verify_post_check($mybb->input['my_post_key']);

	if($mybb->usergroup['canwarnusers'] != 1)
	{
		error_no_permission();
	}
	
	// Check we haven't exceeded the maximum number of warnings per day
	if($mybb->usergroup['maxwarningsday'] != 0)
	{
		$timecut = TIME_NOW-60*60*24;
		$query = $db->simple_select("warnings", "COUNT(wid) AS given_today", "issuedby='{$mybb->user['uid']}' AND dateline>'$timecut'");
		$given_today = $db->fetch_field($query, "given_today");
		if($given_today >= $mybb->usergroup['maxwarningsday'])
		{
			error($lang->sprintf($lang->reached_max_warnings_day, $mybb->usergroup['maxwarningsday']));
		}
	}

	$user = get_user(intval($mybb->input['uid']));
	if(!$user['uid'])
	{
		error($lang->error_invalid_user);
	}

	if($user['uid'] == $mybb->user['uid'])
	{
		error($lang->cannot_warn_self);
	}

	if($user['warningpoints'] >= $mybb->settings['maxwarningpoints'])
	{
		error($lang->user_reached_max_warning);
	}

	$group_permissions = user_permissions($user['uid']);

	if($group_permissions['canreceivewarnings'] != 1)
	{
		error($lang->error_cant_warn_group);
	}

	if(!modcp_can_manage_user($user['uid']))
	{
		error($lang->error_cant_warn_user);
	}

	// Is this warning being given for a post?
	if($mybb->input['pid'])
	{
		$post = get_post(intval($mybb->input['pid']));
		$thread = get_thread($post['tid']);
		if(!$post['pid'] || !$thread['tid'])
		{
			error($lang->error_invalid_post);
		}
		$forum_permissions = forum_permissions($thread['fid']);
		if($forum_permissions['canview'] != 1)
		{
			error_no_permission();
		}
	}

	$plugins->run_hooks("warnings_do_warn_start");

	if(!trim($mybb->input['notes']))
	{
		$warn_errors[] = $lang->error_no_note;
	}

	// Using a predefined warning type
	if($mybb->input['type'] != "custom")
	{
		$query = $db->simple_select("warningtypes", "*", "tid='".intval($mybb->input['type'])."'");
		$warning_type = $db->fetch_array($query);
		if(!$warning_type['tid'])
		{
			$warn_errors[] = $lang->error_invalid_type;
		}
		$points = $warning_type['points'];
		$warning_title = "";
		if($warning_type['expirationtime'])
		{
			$warning_expires = TIME_NOW+$warning_type['expirationtime'];
		}
	}
	// Issuing a custom warning
	else
	{
		if($mybb->settings['allowcustomwarnings'] == 0)
		{
			$warn_errors[] = $lang->error_cant_custom_warn;
		}
		else
		{
			if(!$mybb->input['custom_reason'])
			{
				$warn_errors[] = $lang->error_no_custom_reason;
			}
			else
			{
				$warning_title = $mybb->input['custom_reason'];
			}
			if(!is_numeric($mybb->input['custom_points']) || $mybb->input['custom_points'] > $mybb->settings['maxwarningpoints'] || $mybb->input['custom_points'] < 0)
			{
				$warn_errors[] = $lang->sprintf($lang->error_invalid_custom_points, $mybb->settings['maxwarningpoints']);
			}
			else
			{
				$points = round((int)$mybb->input['custom_points']);
			}
			// Build expiry date
			if($mybb->input['expires'])
			{
				$warning_expires = intval($mybb->input['expires']);
				if($mybb->input['expires_period'] == "hours")
				{
					$warning_expires = $warning_expires*3600;
				}
				else if($mybb->input['expires_period'] == "days")
				{
					$warning_expires = $warning_expires*86400;
				}
				else if($mybb->input['expires_period'] == "weeks")
				{
					$warning_expires = $warning_expires*604800;
				}
				else if($mybb->input['expires_period'] == "months")
				{
					$warning_expires = $warning_expires*2592000;
				}
				// Add on current time and we're there!
				if($mybb->input['expires_period'] != "never" && $warning_expires)
				{
					$warning_expires += TIME_NOW;
				}
			}
		}
	}

	if($warning_expires <= TIME_NOW)
	{
		$warning_expires = 0;
	}

	// Are we notifying the user?
	if(!$warn_errors && $mybb->input['send_pm'] == 1 && $group_permissions['canusepms']  != 0 && $user['receivepms'] != 0 && $mybb->settings['enablepms'] != 0)
	{
		// Bring up the PM handler
		require_once MYBB_ROOT."inc/datahandlers/pm.php";
		$pmhandler = new PMDataHandler();

		$pm = array(
			"subject" => $mybb->input['pm_subject'],
			"message" => $mybb->input['pm_message'],
			"fromid" => $mybb->user['uid'],
			"toid" => array($user['uid'])
		);

		$pm['options'] = array(
			"signature" => $mybb->input['pm_options']['signature'],
			"disablesmilies" => $mybb->input['pm_options']['disablesmilies'],
			"savecopy" => $mybb->input['pm_options']['savecopy'],
			"readreceipt" => $mybb->input['pm_options']['readreceipt']
		);

		$pmhandler->set_data($pm);

		// Now let the pm handler do all the hard work.
		if(!$pmhandler->validate_pm())
		{
			$pm_errors = $pmhandler->get_friendly_errors();
			if($warn_errors)
			{
				$warn_errors = array_merge($warn_errors, $pm_errors);
			}
			else
			{
				$warn_errors = $pm_errors;
			}
		}
		else
		{
			$pminfo = $pmhandler->insert_pm();
		}
	}

	// No errors - save warning to database
	if(!is_array($warn_errors))
	{
		// Build warning level & ensure it doesn't go over 100.
		$current_level = round($user['warningpoints']/$mybb->settings['maxwarningpoints']*100);
		$new_warning_level = round(($user['warningpoints']+$points)/$mybb->settings['maxwarningpoints']*100);
		if($new_warning_level > 100)
		{
			$new_warning_level = 100;
		}

		$new_warning = array(
			"uid" => $user['uid'],
			"tid" => intval($warning_type['tid']),
			"pid" => intval($post['pid']),
			"title" => $db->escape_string($warning_title),
			"points" => intval($points),
			"dateline" => TIME_NOW,
			"issuedby" => $mybb->user['uid'],
			"expires" => $warning_expires,
			"expired" => 0,
			"revokereason" => '',
			"notes" => $db->escape_string($mybb->input['notes'])
		);
		$db->insert_query("warnings", $new_warning);

		// Update user
		$updated_user = array(
			"warningpoints" => $user['warningpoints']+$points
		);

		// Fetch warning level
		$query = $db->simple_select("warninglevels", "*", "percentage<=$new_warning_level", array("order_by" => "percentage", "order_dir" => "desc"));
		$new_level = $db->fetch_array($query);

		if($new_level['lid'])
		{
			$action = unserialize($new_level['action']);
			switch($action['type'])
			{
				// Ban the user for a specified time
				case 1:
					if($action['length'] != 0)
					{
						$expiration = TIME_NOW+$action['length'];
					}
					// Fetch any previous bans for this user
					$query = $db->simple_select("banned", "*", "uid='{$user['uid']}' AND gid='{$action['usergroup']}' AND lifted>".TIME_NOW);
					$existing_ban = $db->fetch_array($query);

					// Only perform if no previous ban or new ban expires later than existing ban
					if(($expiration > $existing_ban['lifted'] && $existing_ban['lifted'] != 0) || $expiration == 0 || !$existing_ban['uid'])
					{
						if(!$warning_title)
						{
							$warning_title = $warning_type['title'];
						}
						
						// Never lift the ban?
						if($action['length'] == 0)
						{
							$bantime = '---';
						}
						else
						{
							$bantimes = fetch_ban_times();
							foreach($bantimes as $date => $string)
							{
								if($date == '---')
								{
									continue;
								}
								
								$time = 0;
								list($day, $month, $year) = explode('-', $date);
								if($day > 0)
								{
									$time += 60*60*24*$day;
								}
								
								if($month > 0)
								{
									$time += 60*60*24*30*$month;
								}
								
								if($year > 0)
								{
									$time += 60*60*24*365*$year;
								}
								
								if($time == $action['length'])
								{
									$bantime = $date;
									break;
								}
							}
						}
						
						$new_ban = array(
							"uid" => intval($user['uid']),
							"gid" => $db->escape_string($action['usergroup']),
							"oldgroup" => $db->escape_string($user['usergroup']),
							"oldadditionalgroups" => $db->escape_string($user['additionalgroups']),
							"olddisplaygroup" => $db->escape_string($user['displaygroup']),
							"admin" => $mybb->user['uid'],
							"dateline" => TIME_NOW,
							"bantime" => $db->escape_string($bantime),
							"lifted" => $expiration,
							"reason" => $db->escape_string($warning_title)
						);
						// Delete old ban for this user, taking details
						if($existing_ban['uid'])
						{
							$db->delete_query("banned", "uid='{$user['uid']}' AND gid='{$action['usergroup']}'");
							// Override new ban details with old group info
							$new_ban['oldgroup'] = $db->escape_string($existing_ban['oldgroup']);
							$new_ban['oldadditionalgroups'] = $db->escape_string($existing_ban['oldadditionalgroups']);
							$new_ban['olddisplaygroup'] = $db->escape_string($existing_ban['olddisplaygroup']);
						}
						
						$db->insert_query("banned", $new_ban);
						$updated_user['usergroup'] = $action['usergroup'];
						$updated_user['additionalgroups'] = $updated_user['displaygroup'] = "";
						$ban_length = fetch_friendly_expiration($action['length']);
						$lang_str = "expiration_".$ban_length['period'];
						$group_name = $groupscache[$action['usergroup']]['title'];
						$period = $lang->sprintf($lang->result_period, $ban_length['time'], $lang->$lang_str);
						$friendly_action = "<br /><br />".$lang->sprintf($lang->redirect_warned_banned, $group_name, $period);
					}
					break;
				// Suspend posting privileges
				case 2:
					if($action['length'] != 0)
					{
						$expiration = TIME_NOW+$action['length'];
					}
					// Only perform if the expiration time is greater than the users current suspension period
					if($expiration == 0 || $expiration > $user['suspensiontime'])
					{
						if(($user['suspensiontime'] != 0 && $user['suspendposting']) || !$user['suspendposting'])
						{
							$updated_user['suspensiontime'] = $expiration;
							$updated_user['suspendposting'] = 1;
							$period = fetch_friendly_expiration($action['length']);
							$lang_str = "expiration_".$period['period'];
							$period = $lang->sprintf($lang->result_period, $period['time'], $lang->$lang_str);
							$friendly_action = "<br /><br />".$lang->sprintf($lang->redirect_warned_suspended, $period);
						}
					}
					break;
				// Moderate new posts
				case 3:
					if($action['length'] != 0)
					{
						$expiration = TIME_NOW+$action['length'];
					}
					// Only perform if the expiration time is greater than the users current suspension period
					if($expiration == 0 || $expiration > $user['moderationtime'])
					{
						if(($user['moderationtime'] != 0 && $user['moderateposts']) || !$user['suspendposting'])
						{
							$updated_user['moderationtime'] = $expiration;
							$updated_user['moderateposts'] = 1;
							$period = fetch_friendly_expiration($action['length']);
							$lang_str = "expiration_".$period['period'];
							$period = $lang->sprintf($lang->result_period, $period['time'], $lang->$lang_str);
							$friendly_action = "<br /><br />".$lang->sprintf($lang->redirect_warned_moderate, $period);
						}
					}
					break;
			}
		}

		// Save updated details
		$db->update_query("users", $updated_user, "uid='{$user['uid']}'");
		$cache->update_moderators();

		$lang->redirect_warned = $lang->sprintf($lang->redirect_warned, $user['username'], $new_warning_level, $friendly_action);

		if($post['pid'])
		{
			redirect(get_post_link($post['pid']), $lang->redirect_warned);
		}
		else
		{
			redirect(get_profile_link($user['uid']), $lang->redirect_warned);
		}
	}

	if($warn_errors)
	{
		$warn_errors = inline_error($warn_errors);
		$mybb->input['action'] = "warn";
	}
}

// Warn a user
if($mybb->input['action'] == "warn")
{
	if($mybb->usergroup['canwarnusers'] != 1)
	{
		error_no_permission();
	}

	// Check we haven't exceeded the maximum number of warnings per day
	if($mybb->usergroup['maxwarningsday'] != 0)
	{
		$timecut = TIME_NOW-60*60*24;
		$query = $db->simple_select("warnings", "COUNT(wid) AS given_today", "issuedby='{$mybb->user['uid']}' AND dateline>'$timecut'");
		$given_today = $db->fetch_field($query, "given_today");
		if($given_today >= $mybb->usergroup['maxwarningsday'])
		{
			error($lang->sprintf($lang->reached_max_warnings_day, $mybb->usergroup['maxwarningsday']));
		}
	}

	$user = get_user(intval($mybb->input['uid']));
	if(!$user['uid'])
	{
		error($lang->error_invalid_user);
	}

	if($user['uid'] == $mybb->user['uid'])
	{
		error($lang->cannot_warn_self);
	}

	if($user['warningpoints'] >= $mybb->settings['maxwarningpoints'])
	{
		error($lang->user_reached_max_warning);
	}

	$group_permissions = user_permissions($user['uid']);

	if($group_permissions['canreceivewarnings'] != 1)
	{
		error($lang->error_cant_warn_group);
	}

	if(!modcp_can_manage_user($user['uid']))
	{
		error($lang->error_cant_warn_user);
	}

	// Giving a warning for a specific post
	if($mybb->input['pid'])
	{
		$post = get_post(intval($mybb->input['pid']));
		$thread = get_thread($post['tid']);
		if(!$post['pid'] || !$thread['tid'])
		{
			error($lang->error_invalid_post);
		}
		$forum_permissions = forum_permissions($thread['fid']);
		if($forum_permissions['canview'] != 1)
		{
			error_no_permission();
		}
		$post['subject'] = $parser->parse_badwords($post['subject']);
		$post['subject'] = htmlspecialchars_uni($post['subject']);
		$post_link = get_post_link($post['pid']);
		eval("\$post = \"".$templates->get("warnings_warn_post")."\";");

		// Fetch any existing warnings issued for this post
		$query = $db->query("
			SELECT w.*, t.title AS type_title, u.username
			FROM ".TABLE_PREFIX."warnings w
			LEFT JOIN ".TABLE_PREFIX."warningtypes t ON (t.tid=w.tid)
			LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=w.issuedby)
			WHERE w.pid='{$mybb->input['pid']}'
			ORDER BY w.expired ASC, w.dateline DESC
		");
		$first = true;
		while($warning = $db->fetch_array($query))
		{
			if($warning['expired'] != $last_expired || $first)
			{
				if($warning['expired'] == 0)
				{
					eval("\$warnings .= \"".$templates->get("warnings_active_header")."\";");
				}
				else
				{
					eval("\$warnings .= \"".$templates->get("warnings_expired_header")."\";");
				}
			}
			$last_expired = $warning['expired'];
			$first = false;

			$post_link = "";
			$issuedby = build_profile_link($warning['username'], $warning['uid']);
			$date_issued = my_date($mybb->settings['dateformat'], $warning['dateline']).", ".my_date($mybb->settings['timeformat'], $warning['dateline']);
			if($warning['type_title'])
			{
				$warning_type = $warning['type_title'];
			}
			else
			{
				$warning_type = $warning['title'];
			}
			$warning_type = htmlspecialchars_uni($warning_type);
			if($warning['points'] > 0)
			{
				$warning['points'] = "+{$warning['points']}";
			}
			$points = $lang->sprintf($lang->warning_points, $warning['points']);
			if($warning['expired'] != 1)
			{
				if($warning['expires'] == 0)
				{
					$expires = $lang->never;
				}
				else
				{
					$expires = my_date($mybb->settings['dateformat'], $warning['expires']).", ".my_date($mybb->settings['timeformat'], $warning['expires']);
				}
			}
			else
			{
				if($warning['daterevoked'])
				{
					$expires = $lang->warning_revoked;
				}
				else if($warning['expires'])
				{
					$expires = $lang->already_expired;
				}
			}
			$alt_bg = alt_trow();
			$plugins->run_hooks("warnings_warning");
			eval("\$warnings .= \"".$templates->get("warnings_warning")."\";");
		}
		if($warnings)
		{
			eval("\$existing_warnings = \"".$templates->get("warnings_warn_existing")."\";");
		}
	}

	$plugins->run_hooks("warnings_warn_start");

	// Coming here from failed do_warn?
	if($warn_errors)
	{
		$notes = htmlspecialchars_uni($mybb->input['notes']);
		$type_checked[$mybb->input['type']] = "checked=\"checked\"";
		$pm_subject = htmlspecialchars_uni($mybb->input['pm_subject']);
		$message = htmlspecialchars_uni($mybb->input['pm_message']);
		if($mybb->input['send_pm'])
		{
			$send_pm_checked = "checked=\"checked\"";
		}
		$custom_reason = htmlspecialchars_uni($mybb->input['custom_reason']);
		$custom_points = intval($mybb->input['custom_points']);
		$expires = intval($mybb->input['expires']);
		$expires_period[$mybb->input['expires_period']] = "selected=\"selected\"";
	}
	else
	{
		$notes = $custom_reason = $custom_points = $expires = '';
		$expires = 1;
		$custom_points = 2;
		$pm_subject = $lang->warning_pm_subject;
		$message = $lang->sprintf($lang->warning_pm_message, $user['username'], $mybb->settings['bbname']);
	}

	$lang->nav_profile = $lang->sprintf($lang->nav_profile, $user['username']);
	add_breadcrumb($lang->nav_profile, get_profile_link($user['uid']));
	add_breadcrumb($lang->nav_add_warning);

	$user_link = build_profile_link($user['username'], $user['uid']);

	$current_level = round($user['warningpoints']/$mybb->settings['maxwarningpoints']*100);

	// Fetch warning levels
	$levels = array();
	$query = $db->simple_select("warninglevels", "*");
	while($level = $db->fetch_array($query))
	{
		$level['action'] = unserialize($level['action']);
		switch($level['action']['type'])
		{
			case 1:
				if($level['action']['length'] > 0)
				{
					$ban_length = fetch_friendly_expiration($level['action']['length']);
					$lang_str = "expiration_".$ban_length['period'];
					$period = $lang->sprintf($lang->result_period, $ban_length['time'], $lang->$lang_str);
				}
				$group_name = $groupscache[$level['action']['usergroup']]['title'];
				$level['friendly_action'] = $lang->sprintf($lang->result_banned, $group_name, $period);
				break;
			case 2:
				if($level['action']['length'] > 0)
				{
					$period = fetch_friendly_expiration($level['action']['length']);
					$lang_str = "expiration_".$period['period'];
					$period = $lang->sprintf($lang->result_period, $period['time'], $lang->$lang_str);
				}
				$level['friendly_action'] = $lang->sprintf($lang->result_suspended, $period);
				break;
			case 3:
				if($level['action']['length'] > 0)
				{
					$period = fetch_friendly_expiration($level['action']['length']);
					$lang_str = "expiration_".$period['period'];
					$period = $lang->sprintf($lang->result_period, $period['time'], $lang->$lang_str);
				}
				$level['friendly_action'] = $lang->sprintf($lang->result_moderated, $period);
				break;
		}
		$levels[$level['percentage']] = $level;
	}
	krsort($levels);

	// Fetch all current warning types
	$query = $db->simple_select("warningtypes", "*", "", array("order_by" => "title"));
	while($type = $db->fetch_array($query))
	{
		$type['title'] = htmlspecialchars_uni($type['title']);
		$new_warning_level = round(($user['warningpoints']+$type['points'])/$mybb->settings['maxwarningpoints']*100);
		if($new_warning_level > 100)
		{
			$new_warning_level = 100;
		}
		if($type['points'] > 0)
		{
			$type['points'] = "+{$type['points']}";
		}
		$points = $lang->sprintf($lang->warning_points, $type['points']);

		if(is_array($levels))
		{
			foreach($levels as $level)
			{
				if($new_warning_level >= $level['percentage'])
				{
					$new_level = $level;
					break;
				}
			}
		}
		$level_diff = $new_warning_level-$current_level;
		if($new_level['friendly_action'])
		{
			$result = "<div class=\"smalltext\" style=\"clear: left; padding-top: 4px;\">Result:<br />".$new_level['friendly_action']."</div>";
		}
		eval("\$types .= \"".$templates->get("warnings_warn_type")."\";");
		unset($new_level);
		unset($result);
	}

	if($mybb->settings['allowcustomwarnings'] != 0)
	{
		eval("\$custom_warning = \"".$templates->get("warnings_warn_custom")."\";");
	}

	if($group_permissions['canusepms']  != 0 && $mybb->user['receivepms'] != 0 && $mybb->settings['enablepms'] != 0)
	{
		$smilieinserter = $codebuttons = "";

		if($mybb->settings['bbcodeinserter'] != 0 && $mybb->settings['pmsallowmycode'] != 0 && $mybb->user['showcodebuttons'] != 0)
		{
			$codebuttons = build_mycode_inserter();
			if($mybb->settings['pmsallowsmilies'] != 0)
			{
				$smilieinserter = build_clickable_smilies();
			}
		}
		eval("\$pm_notify = \"".$templates->get("warnings_warn_pm")."\";");
	}
	eval("\$warn = \"".$templates->get("warnings_warn")."\";");
	$plugins->run_hooks("warnings_warn_end");
	output_page($warn);
	exit;
}

// Revoke a warning
if($mybb->input['action'] == "do_revoke" && $mybb->request_method == "post")
{
	// Verify incoming POST request
	verify_post_check($mybb->input['my_post_key']);

	if($mybb->usergroup['canwarnusers'] != 1)
	{
		error_no_permission();
	}

	$query = $db->simple_select("warnings", "*", "wid='".intval($mybb->input['wid'])."'");
	$warning = $db->fetch_array($query);

	if(!$warning['wid'])
	{
		error($lang->error_invalid_warning);
	}
	else if($warning['daterevoked'])
	{
		error($lang->warning_already_revoked);
	}

	$user = get_user($warning['uid']);

	$group_permissions = user_permissions($user['uid']);
	if($group_permissions['canreceivewarnings'] != 1)
	{
		error($lang->error_cant_warn_group);
	}

	$plugins->run_hooks("warnings_do_revoke_start");

	if(!trim($mybb->input['reason']))
	{
		$warn_errors[] = $lang->no_revoke_reason;
		$warn_errors = inline_error($warn_errors);
		$mybb->input['action'] = "view";
	}
	else
	{
		// Warning is still active, lower users point count
		if($warning['expired'] != 1)
		{
			$new_warning_points = $user['warningpoints']-$warning['points'];
			if($new_warning_points < 0)
			{
				$new_warning_points = 0;
			}

			$updated_user = array(
				"warningpoints" => $new_warning_points
			);
			
			
			// check if we need to revoke any consequences with this warning
			$current_level = round($user['warningpoints']/$mybb->settings['maxwarningpoints']*100);
			$new_warning_level = round($new_warning_points/$mybb->settings['maxwarningpoints']*100);
			$query = $db->simple_select("warninglevels", "action", "percentage>$new_warning_level AND percentage<=$current_level");
			if($db->num_rows($query))
			{
				// we have some warning levels we need to revoke
				$max_expiration_times = $check_levels = array();
				find_warnlevels_to_check($query, $max_expiration_times, $check_levels);
				
				// now check warning levels already applied to this user to see if we need to lower any expiration times
				$query = $db->simple_select("warninglevels", "action", "percentage<=$new_warning_level");
				$lower_expiration_times = $lower_levels = array();
				find_warnlevels_to_check($query, $lower_expiration_times, $lower_levels);
				
				// now that we've got all the info, do necessary stuff
				for($i = 1; $i <= 3; ++$i)
				{
					if($check_levels[$i])
					{
						switch($i)
						{
							case 1: // Ban
								// we'll have to resort to letting the admin/mod remove the ban manually, since there's an issue if stacked bans are in force...
								continue;
							case 2: // Revoke posting
								$current_expiry_field = 'suspensiontime';
								$current_inforce_field = 'suspendposting';
								break;
							case 3:
								$current_expiry_field = 'moderationtime';
								$current_inforce_field = 'moderateposts';
								break;
						}
						
						// if the thing isn't in force, don't bother with trying to update anything
						if(!$user[$current_inforce_field])
						{
							continue;
						}
						
						if($lower_levels[$i])
						{
							// lessen the expiration time if necessary
							
							if(!$lower_expiration_times[$i])
							{
								// doesn't expire - enforce this
								$updated_user[$current_expiry_field] = 0;
								continue;
							}
							
							if($max_expiration_times[$i])
							{
								// if the old level did have an expiry time...
								if($max_expiration_times[$i] <= $lower_expiration_times[$i])
								{
									// if the lower expiration time is actually higher than the upper expiration time -> skip
									continue;
								}
								// both new and old max expiry times aren't infinite, so we can take a difference
								$expire_offset = ($lower_expiration_times[$i] - $max_expiration_times[$i]);
							}
							else
							{
								// the old level never expired, not much we can do but try to estimate a new expiry time... which will just happen to be starting from today...
								$expire_offset = TIME_NOW + $lower_expiration_times[$i];
								// if the user's expiry time is already less than what we're going to set it to, skip
								if($user[$current_expiry_field] <= $expire_offset)
								{
									continue;
								}
							}
							
							$updated_user[$current_expiry_field] = $user[$current_expiry_field] + $expire_offset;
							// double-check if it's expired already
							if($updated_user[$current_expiry_field] < TIME_NOW)
							{
								$updated_user[$current_expiry_field] = 0;
								$updated_user[$current_inforce_field] = 0;
							}
						}
						else
						{
							// there's no lower level for this type - remove the consequence entirely
							$updated_user[$current_expiry_field] = 0;
							$updated_user[$current_inforce_field] = 0;
						}
					}
				}
			}
			
			
			// Update user
			$db->update_query("users", $updated_user, "uid='{$warning['uid']}'");
		}

		// Update warning
		$updated_warning = array(
			"expired" => 1,
			"daterevoked" => TIME_NOW,
			"revokedby" => $mybb->user['uid'],
			"revokereason" => $db->escape_string($mybb->input['reason'])
		);
		$db->update_query("warnings", $updated_warning, "wid='{$warning['wid']}'");

		redirect("warnings.php?action=view&wid={$warning['wid']}", $lang->redirect_warning_revoked);
	}
}

// Detailed view of a warning
if($mybb->input['action'] == "view")
{
	if($mybb->usergroup['canwarnusers'] != 1)
	{
		error_no_permission();
	}

	$query = $db->query("
		SELECT w.*, t.title AS type_title, u.username, p.subject AS post_subject
		FROM ".TABLE_PREFIX."warnings w
		LEFT JOIN ".TABLE_PREFIX."warningtypes t ON (t.tid=w.tid)
		LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=w.issuedby)
		LEFT JOIN ".TABLE_PREFIX."posts p ON (p.pid=w.pid)
		WHERE w.wid='".intval($mybb->input['wid'])."'
	");
	$warning = $db->fetch_array($query);

	if(!$warning['wid'])
	{
		error($lang->error_invalid_warning);
	}

	$user = get_user(intval($warning['uid']));
	if(!$user['username'])
	{
		$user['username'] = $lang->guest;
	}

	$group_permissions = user_permissions($user['uid']);
	if($group_permissions['canreceivewarnings'] != 1)
	{
		error($lang->error_cant_warn_group);
	}

	$plugins->run_hooks("warnings_view_start");

	$lang->nav_profile = $lang->sprintf($lang->nav_profile, $user['username']);
	if($user['uid'])
	{
		add_breadcrumb($lang->nav_profile, get_profile_link($user['uid']));
		add_breadcrumb($lang->nav_warning_log, "warnings.php?uid={$user['uid']}");
	}
	else
	{
		add_breadcrumb($lang->nav_profile);
		add_breadcrumb($lang->nav_warning_log);
	}
	add_breadcrumb($lang->nav_view_warning);

	$user_link = build_profile_link($user['username'], $user['uid']);

	$post_link = "";
	if($warning['post_subject'])
	{
		$warning['post_subject'] = $parser->parse_badwords($warning['post_subject']);
		$warning['post_subject'] = htmlspecialchars_uni($warning['post_subject']);
		$post_link = get_post_link($warning['pid'])."#pid{$warning['pid']}";
		eval("\$warning_info = \"".$templates->get("warnings_view_post")."\";");
	}
	else
	{
		eval("\$warning_info = \"".$templates->get("warnings_view_user")."\";");
	}

	$issuedby = build_profile_link($warning['username'], $warning['issuedby']);
	$notes = nl2br(htmlspecialchars_uni($warning['notes']));
	
	$date_issued = my_date($mybb->settings['dateformat'], $warning['dateline']).", ".my_date($mybb->settings['timeformat'], $warning['dateline']);
	if($warning['type_title'])
	{
		$warning_type = $warning['type_title'];
	}
	else
	{
		$warning_type = $warning['title'];
	}
	$warning_type = htmlspecialchars_uni($warning_type);
	if($warning['points'] > 0)
	{
		$warning['points'] = "+{$warning['points']}";
	}
	
	$revoked_date = '';
	
	$points = $lang->sprintf($lang->warning_points, $warning['points']);
	if($warning['expired'] != 1)
	{
		if($warning['expires'] == 0)
		{
			$expires = $lang->never;
		}
		else
		{
			$expires = my_date($mybb->settings['dateformat'], $warning['expires']).", ".my_date($mybb->settings['timeformat'], $warning['expires']);
		}
		$status = $lang->warning_active;
	}
	else
	{
		if($warning['daterevoked'])
		{
			$expires = $status = $lang->warning_revoked;
		}
		else if($warning['expires'])
		{
			$revoked_date = '('.my_date($mybb->settings['dateformat'], $warning['expires']).' '.my_date($mybb->settings['timeformat'], $warning['expires']).')';
			$expires = $status = $lang->already_expired;
		}
	}

	if(!$warning['daterevoked'])
	{
		eval("\$revoke = \"".$templates->get("warnings_view_revoke")."\";");
	}
	else
	{
		$date_revoked = my_date($mybb->settings['dateformat'], $warning['daterevoked']).", ".my_date($mybb->settings['timeformat'], $warning['daterevoked']);
		$revoked_user = get_user($warning['revokedby']);
		if(!$revoked_user['username'])
		{
			$revoked_user['username'] = $lang->guest;
		}
		$revoked_by = build_profile_link($revoked_user['username'], $revoked_user['uid']);
		$revoke_reason = nl2br(htmlspecialchars_uni($warning['revokereason']));
		eval("\$revoke = \"".$templates->get("warnings_view_revoked")."\";");
	}

	eval("\$warning = \"".$templates->get("warnings_view")."\";");
	output_page($warning);
}

// Showing list of warnings for a particular user
if(!$mybb->input['action'])
{
	if($mybb->usergroup['canwarnusers'] != 1)
	{
		error_no_permission();
	}

	$user = get_user(intval($mybb->input['uid']));
	if(!$user['uid'])
	{
		error($lang->error_invalid_user);
	}
	$group_permissions = user_permissions($user['uid']);
	if($group_permissions['canreceivewarnings'] != 1)
	{
		error($lang->error_cant_warn_group);
	}

	$lang->nav_profile = $lang->sprintf($lang->nav_profile, $user['username']);
	add_breadcrumb($lang->nav_profile, get_profile_link($user['uid']));
	add_breadcrumb($lang->nav_warning_log);

	if(!$mybb->settings['postsperpage'])
	{
		$mybb->settings['postperpage'] = 20;
	}
		
	// Figure out if we need to display multiple pages.
	$perpage = $mybb->settings['postsperpage'];
	$page = intval($mybb->input['page']);

	$query = $db->simple_select("warnings", "COUNT(wid) AS warning_count", "uid='{$user['uid']}'");
	$warning_count = $db->fetch_field($query, "warning_count");

	$pages = ceil($warning_count/$perpage);

	if($page > $pages || $page <= 0)
	{
		$page = 1;
	}
	if($page)
	{
		$start = ($page-1) * $perpage;
	}
	else
	{
		$start = 0;
		$page = 1;
	}

	$multipage = multipage($warning_count, $perpage, $page, "warnings.php?uid={$user['uid']}");

	$warning_level = round($user['warningpoints']/$mybb->settings['maxwarningpoints']*100);
	if($warning_level > 100)
	{
		$warning_level = 100;
	}
	
	if($user['warningpoints'] > $mybb->settings['maxwarningpoints'])
	{
		$user['warningpoints'] = $mybb->settings['maxwarningpoints'];
	}
	
	if($warning_level > 0)
	{
		$lang->current_warning_level = $lang->sprintf($lang->current_warning_level, $warning_level, $user['warningpoints'], $mybb->settings['maxwarningpoints']);
	}
	else
	{
		$lang->current_warning_level = "";
	}

	// Fetch the actual warnings
	$query = $db->query("
		SELECT w.*, t.title AS type_title, u.username, p.subject AS post_subject
		FROM ".TABLE_PREFIX."warnings w
		LEFT JOIN ".TABLE_PREFIX."warningtypes t ON (t.tid=w.tid)
		LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=w.issuedby)
		LEFT JOIN ".TABLE_PREFIX."posts p ON (p.pid=w.pid)
		WHERE w.uid='{$user['uid']}'
		ORDER BY w.expired ASC, w.dateline DESC
		LIMIT {$start}, {$perpage}
	");
	$first = true;
	while($warning = $db->fetch_array($query))
	{
		if($warning['expired'] != $last_expired || $first)
		{
			if($warning['expired'] == 0)
			{
				eval("\$warnings .= \"".$templates->get("warnings_active_header")."\";");
			}
			else
			{
				eval("\$warnings .= \"".$templates->get("warnings_expired_header")."\";");
			}
		}
		$last_expired = $warning['expired'];
		$first = false;

		$post_link = "";
		if($warning['post_subject'])
		{
			$warning['post_subject'] = $parser->parse_badwords($warning['post_subject']);
			$warning['post_subject'] = htmlspecialchars_uni($warning['post_subject']);
			$post_link = "<br /><small>{$lang->warning_for_post} <a href=\"".get_post_link($warning['pid'])."#pid{$warning['pid']}\">{$warning['post_subject']}</a></small>";
		}
		$issuedby = build_profile_link($warning['username'], $warning['issuedby']);
		$date_issued = my_date($mybb->settings['dateformat'], $warning['dateline']).", ".my_date($mybb->settings['timeformat'], $warning['dateline']);
		if($warning['type_title'])
		{
			$warning_type = $warning['type_title'];
		}
		else
		{
			$warning_type = $warning['title'];
		}
		$warning_type = htmlspecialchars_uni($warning_type);
		if($warning['points'] > 0)
		{
			$warning['points'] = "+{$warning['points']}";
		}
		$points = $lang->sprintf($lang->warning_points, $warning['points']);
		if($warning['expired'] != 1)
		{
			if($warning['expires'] == 0)
			{
				$expires = $lang->never;
			}
			else
			{
				$expires = my_date($mybb->settings['dateformat'], $warning['expires']).", ".my_date($mybb->settings['timeformat'], $warning['expires']);
			}
		}
		else
		{
			if($warning['daterevoked'])
			{
				$expires = $lang->warning_revoked;
			}
			else if($warning['expires'])
			{
				$expires = $lang->already_expired;
			}
		}
		$alt_bg = alt_trow();
		$plugins->run_hooks("warnings_warning");
		eval("\$warnings .= \"".$templates->get("warnings_warning")."\";");
	}

	if(!$warnings)
	{
		eval("\$warnings = \"".$templates->get("warnings_no_warnings")."\";");
	}
	eval("\$warnings = \"".$templates->get("warnings")."\";");
	$plugins->run_hooks("warnings_end");
	output_page($warnings);
}



function find_warnlevels_to_check(&$query, &$max_expiration_times, &$check_levels)
{
	global $db;
	// we have some warning levels we need to revoke
	$max_expiration_times = array(
		1 => -1,	// Ban
		2 => -1,	// Revoke posting
		3 => -1		// Moderate posting
	);
	$check_levels = array(
		1 => false,	// Ban
		2 => false,	// Revoke posting
		3 => false	// Moderate posting
	);
	while($warn_level = $db->fetch_array($query))
	{
		// revoke actions taken at this warning level
		$action = unserialize($warn_level['action']);
		if($action['type'] < 1 || $action['type'] > 3)	// prevent any freak-ish cases
		{
			continue;
		}
		
		$check_levels[$action['type']] = true;
		
		$max_exp_time = &$max_expiration_times[$action['type']];
		if($action['length'] && $max_exp_time != 0)
		{
			$expiration = $action['length'];
			if($expiration > $max_exp_time)
			{
				$max_exp_time = $expiration;
			}
		}
		else
		{
			$max_exp_time = 0;
		}
	}
}

?>