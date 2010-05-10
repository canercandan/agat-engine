<?php
/**
 * MyBB 1.4
 * Copyright © 2008 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id: functions_search.php 4384 2009-06-19 11:49:42Z Tomm $
 */

/**
 * Build a select box list of forums the current user has permission to search
 *
 * @param int The parent forum ID to start at
 * @param int The selected forum ID
 * @param int Add select boxes at this call or not
 * @param int The current depth
 * @return string The forum select boxes
 */
function make_searchable_forums($pid="0", $selitem='', $addselect="1", $depth='')
{
	global $db, $pforumcache, $permissioncache, $mybb, $selecteddone, $forumlist, $forumlistbits, $theme, $templates, $lang, $forumpass;
	$pid = intval($pid);

	if(!is_array($pforumcache))
	{
		// Get Forums
		$query = $db->simple_select("forums", "pid,disporder,fid,password,name", "linkto='' AND active!=0", array('order_by' => "pid, disporder"));
		while($forum = $db->fetch_array($query))
		{
			$pforumcache[$forum['pid']][$forum['disporder']][$forum['fid']] = $forum;
		}
	}
	if(!is_array($permissioncache))
	{
		$permissioncache = forum_permissions();
	}
	if(is_array($pforumcache[$pid]))
	{
		foreach($pforumcache[$pid] as $key => $main)
		{
			foreach($main as $key => $forum)
			{
				$perms = $permissioncache[$forum['fid']];
				if(($perms['canview'] == 1 || $mybb->settings['hideprivateforums'] == 0) && $perms['cansearch'] != 0)
				{
					if($selitem == $forum['fid'])
					{
						$optionselected = "selected";
						$selecteddone = "1";
					}
					else
					{
						$optionselected = '';
						$selecteddone = "0";
					}
					if($forum['password'] != '')
					{
						if($mybb->cookies['forumpass'][$forum['fid']] == md5($mybb->user['uid'].$forum['password']))
						{
							$pwverified = 1;
						}
						else
						{
							$pwverified = 0;
						}
					}
					if(empty($forum['password']) || $pwverified == 1)
					{
						$forumlistbits .= "<option value=\"{$forum['fid']}\">$depth {$forum['name']}</option>\n";
					}
					if($pforumcache[$forum['fid']])
					{
						$newdepth = $depth."&nbsp;&nbsp;&nbsp;&nbsp;";
						$forumlistbits .= make_searchable_forums($forum['fid'], $selitem, 0, $newdepth);
					}
				}
			}
		}
	}
	if($addselect)
	{
		$forumlist = "<select name=\"forums[]\" size=\"15\" multiple=\"multiple\">\n<option value=\"all\" selected=\"selected\">$lang->search_all_forums</option>\n<option value=\"all\">----------------------</option>\n$forumlistbits\n</select>";
	}
	return $forumlist;
}

/**
 * Build a comma separated list of the forums this user cannot search
 *
 * @param int The parent ID to build from
 * @param int First rotation or not (leave at default)
 * @return return a CSV list of forums the user cannot search
 */
function get_unsearchable_forums($pid="0", $first=1)
{
	global $db, $forum_cache, $permissioncache, $mybb, $unsearchableforums, $unsearchable, $templates, $forumpass;
	
	$pid = intval($pid);
	
	if(!is_array($forum_cache))
	{
		// Get Forums
		$query = $db->simple_select("forums", "fid,parentlist,password,active", '', array('order_by' => 'pid, disporder'));
		while($forum = $db->fetch_array($query))
		{
			$forum_cache[$forum['fid']] = $forum;
		}
	}
	if(!is_array($permissioncache))
	{
		$permissioncache = forum_permissions();
	}
	foreach($forum_cache as $fid => $forum)
	{
		if($permissioncache[$forum['fid']])
		{
			$perms = $permissioncache[$forum['fid']];
		}
		else
		{
			$perms = $mybb->usergroup;
		}

		$pwverified = 1;
		if($forum['password'] != '')
		{
			if($mybb->cookies['forumpass'][$forum['fid']] != md5($mybb->user['uid'].$forum['password']))
			{
				$pwverified = 0;
			}
		}

		$parents = explode(",", $forum['parentlist']);
		if(is_array($parents))
		{
			foreach($parents as $parent)
			{
				if($forum_cache[$parent]['active'] == 0)
				{
					$forum['active'] = 0;
				}
			}
		}

		if($perms['canview'] != 1 || $perms['cansearch'] != 1 || $pwverified == 0 || $forum['active'] == 0)
		{
			if($unsearchableforums)
			{
				$unsearchableforums .= ",";
			}
			$unsearchableforums .= "'{$forum['fid']}'";
		}
	}
	$unsearchable = $unsearchableforums;
	return $unsearchable;
}

/**
 * Clean search keywords and make them safe for querying
 *
 * @param string The keywords to be cleaned
 * @return string The cleaned keywords
 */
function clean_keywords($keywords)
{
	$keywords = my_strtolower($keywords);
	$keywords = str_replace("%", "\\%", $keywords);
	$keywords = preg_replace("#\*{2,}#s", "*", $keywords);
	$keywords = str_replace("*", "%", $keywords);
	$keywords = preg_replace("#([\[\]\|\.\,:'])#s", " ", $keywords);
	$keywords = preg_replace("#\s+#s", " ", $keywords);

	// Search for "and" or "or" and remove if it's at the beginning
	if(my_strpos($keywords, "or") !== false && my_strpos($keywords, "or") == 0)
	{
		$keywords = substr_replace($keywords, "", 0, 2);
	}
	if(my_strpos($keywords, "and") !== false && my_strpos($keywords, "and") == 0)
	{
		$keywords = substr_replace($keywords, "", 0, 3);
	}

	return trim($keywords);
}

/**
 * Clean search keywords for fulltext searching, making them safe for querying
 *
 * @param string The keywords to be cleaned
 * @return string The cleaned keywords
 */
function clean_keywords_ft($keywords)
{
	if(!$keywords)
	{
		return false;
	}
	$keywords = my_strtolower($keywords);
	$keywords = str_replace("%", "\\%", $keywords);
	$keywords = preg_replace("#\*{2,}#s", "*", $keywords);
	$keywords = preg_replace("#([\[\]\|\.\,:])#s", " ", $keywords);
	$keywords = preg_replace("#\s+#s", " ", $keywords);

	if(my_strpos($keywords, "\"") !== false)
	{
		$inquote = false;
		$keywords = explode("\"", $keywords);
		foreach($keywords as $phrase)
		{
			if($phrase != '')
			{
				if($inquote)
				{
					$words[] = "\"".trim($phrase)."\"";
				}
				else
				{
					$split_words = preg_split("#\s{1,}#", $phrase, -1);
					if(!is_array($split_words))
					{
						continue;
					}
					foreach($split_words as $word)
					{
						if(!$word)
						{
							continue;
						}
						$words[] = trim($word);
					}
				}
			}
			$inquote = !$inquote;
		}
	}
	else
	{
		$split_words = preg_split("#\s{1,}#", $keywords, -1);
		if(!is_array($split_words))
		{
			continue;
		}
		foreach($split_words as $word)
		{
			if(!$word)
			{
				continue;
			}
			$words[] = trim($word);
		}

	}
	$keywords = '';
	foreach($words as $word)
	{
		if($word == "or")
		{
			$boolean = '';
		}
		elseif($word == "and")
		{
			$boolean = "+";
		}
		elseif($word == "not")
		{
			$boolean = "-";
		}
		else
		{
			$keywords .= " ".$boolean.$word;
			$boolean = '';
		}
	}
	$keywords = "+".trim($keywords);
	return $keywords;
}

/* Database engine specific search functions */

/**
 * Perform a thread and post search under MySQL or MySQLi
 *
 * @param array Array of search data
 * @return array Array of search data with results mixed in
 */
function perform_search_mysql($search)
{
	global $mybb, $db, $lang;

	$keywords = clean_keywords($search['keywords']);
	if(!$keywords && !$search['author'])
	{
		error($lang->error_nosearchterms);
	}

	if($mybb->settings['minsearchword'] < 1)
	{
		$mybb->settings['minsearchword'] = 3;
	}

	if($keywords)
	{
		// Complex search
		$keywords = " {$keywords} ";
		if(preg_match("# and|or #", $keywords))
		{
			$subject_lookin = " AND (";
			$message_lookin = " AND (";
			
			// Expand the string by double quotes
			$keywords_exp = explode("\"", $keywords);
			$inquote = false;

			foreach($keywords_exp as $phrase)
			{
				// If we're not in a double quoted section
				if(!$inquote)
				{
					// Expand out based on search operators (and, or)
					$matches = preg_split("#\s{1,}(and|or)\s{1,}#", $phrase, -1, PREG_SPLIT_DELIM_CAPTURE);
					$count_matches = count($matches);
					
					for($i=0; $i < $count_matches; ++$i)
					{
						$word = trim($matches[$i]);
						if(empty($word))
						{
							continue;
						}
						// If this word is a search operator set the boolean
						if($i % 2 && ($word == "and" || $word == "or"))
						{
							$boolean = $word;
						}
						// Otherwise check the length of the word as it is a normal search term
						else
						{
							$word = trim($word);
							// Word is too short - show error message
							if(my_strlen($word) < $mybb->settings['minsearchword'])
							{
								$lang->error_minsearchlength = $lang->sprintf($lang->error_minsearchlength, $mybb->settings['minsearchword']);
								error($lang->error_minsearchlength);
							}
							// Add terms to search query
							$subject_lookin .= " $boolean LOWER(t.subject) LIKE '%{$word}%'";
							if($search['postthread'] == 1)
							{
								$message_lookin .= " $boolean LOWER(p.message) LIKE '%{$word}%'";
							}
						}
					}
				}	
				// In the middle of a quote (phrase)
				else
				{
					$phrase = str_replace(array("+", "-", "*"), '', trim($phrase));
					if(my_strlen($phrase) < $mybb->settings['minsearchword'])
					{
						$lang->error_minsearchlength = $lang->sprintf($lang->error_minsearchlength, $mybb->settings['minsearchword']);
						error($lang->error_minsearchlength);
					}
					// Add phrase to search query
					$subject_lookin .= " $boolean LOWER(t.subject) LIKE '%{$phrase}%'";
					if($search['postthread'] == 1)
					{
						$message_lookin .= " $boolean LOWER(p.message) LIKE '%{$phrase}%'";
					}					
				}
				$inquote = !$inquote;
			}
			$subject_lookin .= ")";
			$message_lookin .= ")";
		}
		else
		{
			$keywords = str_replace("\"", '', trim($keywords));
			if(my_strlen($keywords) < $mybb->settings['minsearchword'])
			{
				$lang->error_minsearchlength = $lang->sprintf($lang->error_minsearchlength, $mybb->settings['minsearchword']);
				error($lang->error_minsearchlength);
			}
			$subject_lookin = " AND LOWER(t.subject) LIKE '%{$keywords}%'";
			if($search['postthread'] == 1)
			{
				$message_lookin = " AND LOWER(p.message) LIKE '%{$keywords}%'";
			}
		}
	}
	$post_usersql = '';
	$thread_usersql = '';
	if($search['author'])
	{
		$userids = array();
		if($search['matchusername'])
		{
			$query = $db->simple_select("users", "uid", "username='".$db->escape_string($search['author'])."'");
		}
		else
		{
			$search['author'] = my_strtolower($search['author']);
			$query = $db->simple_select("users", "uid", "LOWER(username) LIKE '%".$db->escape_string_like($db->escape_string($search['author']))."%'");
		}
		while($user = $db->fetch_array($query))
		{
			$userids[] = $user['uid'];
		}
		if(count($userids) < 1)
		{
			error($lang->error_nosearchresults);
		}
		else
		{
			$userids = implode(',', $userids);
			$post_usersql = " AND p.uid IN (".$userids.")";
			$thread_usersql = " AND t.uid IN (".$userids.")";
		}
	}
	$datecut = '';
	if($search['postdate'])
	{
		if($search['pddir'] == 0)
		{
			$datecut = "<=";
		}
		else
		{
			$datecut = ">=";
		}
		$now = TIME_NOW;
		$datelimit = $now-(86400 * $search['postdate']);
		$datecut .= "'$datelimit'";
		$post_datecut = " AND p.dateline $datecut";
		$thread_datecut = " AND t.dateline $datecut";
	}
	
	$thread_replycut = '';
	if($search['numreplies'] != '' && $search['findthreadst'])
	{
		if(intval($search['findthreadst']) == 1)
		{
			$thread_replycut = " AND t.replies >= '".intval($search['numreplies'])."'";
		}
		else
		{
			$thread_replycut = " AND t.replies <= '".intval($search['numreplies'])."'";
		}
	}

	$forumin = '';
	$fidlist = array();
	$searchin = array();
	if($search['forums'] != "all")
	{
		if(!is_array($search['forums']))
		{
			$search['forums'] = array(intval($search['forums']));
		}
		foreach($search['forums'] as $forum)
		{
			if(!$searchin[$forum])
			{
				$forum = intval($forum);
				switch($db->type)
				{
					case "pgsql":
						$query = $db->query("
							SELECT DISTINCT f.fid 
							FROM ".TABLE_PREFIX."forums f 
							LEFT JOIN ".TABLE_PREFIX."forumpermissions p ON (f.fid=p.fid AND p.gid='".$mybb->user['usergroup']."')
							WHERE (','||parentlist||',' LIKE ',%{$forum}%,') = true AND active!=0 AND (p.fid IS NULL OR p.cansearch=1)
						");
						break;
					case "sqlite3":
					case "sqlite2":
						$query = $db->query("
							SELECT DISTINCT f.fid 
							FROM ".TABLE_PREFIX."forums f 
							LEFT JOIN ".TABLE_PREFIX."forumpermissions p ON (f.fid=p.fid AND p.gid='".$mybb->user['usergroup']."')
							WHERE (','||parentlist||',' LIKE ',%{$forum}%,') > 0 AND active!=0 AND (p.fid = NULL OR p.cansearch=1)
						");
						break;
					default:
						$query = $db->query("
							SELECT DISTINCT f.fid 
							FROM ".TABLE_PREFIX."forums f 
							LEFT JOIN ".TABLE_PREFIX."forumpermissions p ON (f.fid=p.fid AND p.gid='".$mybb->user['usergroup']."')
							WHERE INSTR(CONCAT(',',parentlist,','),',$forum,') > 0 AND active!=0 AND (ISNULL(p.fid) OR p.cansearch=1)
						");
				}
				while($sforum = $db->fetch_array($query))
				{
					$fidlist[] = $sforum['fid'];
				}
			}
		}
		if(count($fidlist) == 1)
		{
			$forumin .= " AND t.fid='$forum' ";
			$searchin[$fid] = 1;
		}
		else
		{			
			if(count($fidlist) > 1)
			{
				$forumin = " AND t.fid IN (".implode(',', $fidlist).")";
			}
		}
	}
	$unsearchforums = get_unsearchable_forums();
	if($unsearchforums)
	{
		$permsql = " AND t.fid NOT IN ($unsearchforums)";
	}
	$inactiveforums = get_inactive_forums();
	if($inactiveforums)
	{
		$permsql .= " AND t.fid NOT IN ($inactiveforums)";
	}

	// Searching a specific thread?
	if($search['tid'])
	{
		$tidsql = " AND t.tid='".intval($search['tid'])."'";
	}
	
	$limitsql = '';
	if(intval($mybb->settings['searchhardlimit']) > 0)
	{
		$limitsql = "LIMIT ".intval($mybb->settings['searchhardlimit']);
	}

	// Searching both posts and thread titles
	$threads = array();
	$posts = array();
	$firstposts = array();
	if($search['postthread'] == 1)
	{
		// No need to search subjects when looking for results within a specific thread
		if(!$search['tid'])
		{
			$query = $db->query("
				SELECT t.tid, t.firstpost
				FROM ".TABLE_PREFIX."threads t
				WHERE 1=1 {$thread_datecut} {$thread_replycut} {$forumin} {$thread_usersql} {$permsql} AND t.visible >= '0' AND t.closed NOT LIKE 'moved|%' {$subject_lookin}
				{$limitsql}
			");
			while($thread = $db->fetch_array($query))
			{
				$threads[$thread['tid']] = $thread['tid'];
				if($thread['firstpost'])
				{
					$posts[$thread['tid']] = $thread['firstpost'];
				}
			}
		}
		$query = $db->query("
			SELECT p.pid, p.tid
			FROM ".TABLE_PREFIX."posts p
			LEFT JOIN ".TABLE_PREFIX."threads t ON (t.tid=p.tid)
			WHERE 1=1 {$post_datecut} {$thread_replycut} {$forumin} {$post_usersql} {$permsql} {$tidsql} AND p.visible >= '0' AND t.visible >= '0' AND t.closed NOT LIKE 'moved|%' {$message_lookin}
			{$limitsql}
		");
		while($post = $db->fetch_array($query))
		{
			$posts[$post['pid']] = $post['pid'];
			$threads[$post['tid']] = $post['tid'];
		}
		if(count($posts) < 1 && count($threads) < 1)
		{
			error($lang->error_nosearchresults);
		}
		$threads = implode(',', $threads);
		$posts = implode(',', $posts);

	}
	// Searching only thread titles
	else
	{
		$query = $db->query("
			SELECT t.tid, t.firstpost
			FROM ".TABLE_PREFIX."threads t
			WHERE 1=1 {$thread_datecut} {$thread_replycut} {$forumin} {$thread_usersql} {$permsql} AND t.visible >= '0' {$subject_lookin}
			{$limitsql}
		");
		while($thread = $db->fetch_array($query))
		{
			$threads[$thread['tid']] = $thread['tid'];
			if($thread['firstpost'])
			{
				$firstposts[$thread['tid']] = $thread['firstpost'];
			}
		}
		if(count($threads) < 1)
		{
			error($lang->error_nosearchresults);
		}

		$threads = implode(',', $threads);
		$firstposts = implode(',', $firstposts);
		if($firstposts)
		{
			$query = $db->simple_select("posts", "pid", "pid IN ($firstposts) AND visible >= '0' {$limitsql}");
			while($post = $db->fetch_array($query))
			{
				$posts[$post['pid']] = $post['pid'];
			}
			$posts = implode(',', $posts);
		}
	}
	return array(
		"threads" => $threads,
		"posts" => $posts,
		"querycache" => ''
	);
}

/**
 * Perform a thread and post search under MySQL or MySQLi using boolean fulltext capabilities
 *
 * @param array Array of search data
 * @return array Array of search data with results mixed in
 */
function perform_search_mysql_ft($search)
{
	global $mybb, $db, $lang;

	$keywords = clean_keywords_ft($search['keywords']);
	if(!$keywords && !$search['author'])
	{
		error($lang->error_nosearchterms);
	}

	// Attempt to determine minimum word length from MySQL for fulltext searches
	$query = $db->query("SHOW VARIABLES LIKE 'ft_min_word_len';");
	$min_length = $db->fetch_field($query, 'Value');
	if(is_numeric($min_length))
	{
		$mybb->settings['minsearchword'] = $min_length;
	}
	// Otherwise, could not fetch - default back to MySQL fulltext default setting
	else
	{
		$mybb->settings['minsearchword'] = 4;
	}

	if($keywords)
	{
		$keywords_exp = explode("\"", $keywords);
		$inquote = false;
		foreach($keywords_exp as $phrase)
		{
			if(!$inquote)
			{
				$split_words = preg_split("#\s{1,}#", $phrase, -1);
				foreach($split_words as $word)
				{
					$word = str_replace(array("+", "-", "*"), '', $word);
					if(!$word)
					{
						continue;
					}
					if(my_strlen($word) < $mybb->settings['minsearchword'])
					{
						$all_too_short = true;
					}
					else
					{
						$all_too_short = false;
						break;
					}
				}
			}
			else
			{
				$phrase = str_replace(array("+", "-", "*"), '', $phrase);
				if(my_strlen($phrase) < $mybb->settings['minsearchword'])
				{
					$all_too_short = true;
				}
				else
				{
					$all_too_short = false;
					break;
				}
			}
			$inquote = !$inquote;
		}
		// Show the minimum search term error only if all search terms are too short
		if($all_too_short == true)
		{
			$lang->error_minsearchlength = $lang->sprintf($lang->error_minsearchlength, $mybb->settings['minsearchword']);
			error($lang->error_minsearchlength);
		}
		$message_lookin = "AND MATCH(message) AGAINST('".$db->escape_string($keywords)."' IN BOOLEAN MODE)";
		$subject_lookin = "AND MATCH(subject) AGAINST('".$db->escape_string($keywords)."' IN BOOLEAN MODE)";
	}
	$post_usersql = '';
	$thread_usersql = '';
	if($search['author'])
	{
		$userids = array();
		if($search['matchusername'])
		{
			$query = $db->simple_select("users", "uid", "username='".$db->escape_string($search['author'])."'");
		}
		else
		{
			$search['author'] = my_strtolower($search['author']);
			$query = $db->simple_select("users", "uid", "LOWER(username) LIKE '%".$db->escape_string_like($db->escape_string($search['author']))."%'");
		}
		
		while($user = $db->fetch_array($query))
		{
			$userids[] = $user['uid'];
		}
		
		if(count($userids) < 1)
		{
			error($lang->error_nosearchresults);
		}
		else
		{
			$userids = implode(',', $userids);
			$post_usersql = " AND p.uid IN (".$userids.")";
			$thread_usersql = " AND t.uid IN (".$userids.")";
		}
	}
	$datecut = '';
	if($search['postdate'])
	{
		if($search['pddir'] == 0)
		{
			$datecut = "<=";
		}
		else
		{
			$datecut = ">=";
		}
		$now = TIME_NOW;
		$datelimit = $now-(86400 * $search['postdate']);
		$datecut .= "'$datelimit'";
		$post_datecut = " AND p.dateline $datecut";
		$thread_datecut = " AND t.dateline $datecut";
	}
	
	$thread_replycut = '';
	if($search['numreplies'] != '' && $search['findthreadst'])
	{
		if(intval($search['findthreadst']) == 1)
		{
			$thread_replycut = " AND t.replies >= '".intval($search['numreplies'])."'";
		}
		else
		{
			$thread_replycut = " AND t.replies <= '".intval($search['numreplies'])."'";
		}
	}

	$forumin = '';
	$fidlist = array();
	$searchin = array();
	if($search['forums'] != "all")
	{
		if(!is_array($search['forums']))
		{
			$search['forums'] = array(intval($search['forums']));
		}
		foreach($search['forums'] as $forum)
		{
			$forum = intval($forum);
			if(!$searchin[$forum])
			{
				switch($db->type)
				{
					case "pgsql":
					case "sqlite3":
					case "sqlite2":
						$query = $db->query("
							SELECT f.fid 
							FROM ".TABLE_PREFIX."forums f 
							LEFT JOIN ".TABLE_PREFIX."forumpermissions p ON (f.fid=p.fid AND p.gid='".$mybb->user['usergroup']."') 
							WHERE INSTR(','||parentlist||',',',$forum,') > 0 AND active!=0 AND (ISNULL(p.fid) OR p.cansearch=1)
						");
						break;
					default:
						$query = $db->query("
							SELECT f.fid 
							FROM ".TABLE_PREFIX."forums f 
							LEFT JOIN ".TABLE_PREFIX."forumpermissions p ON (f.fid=p.fid AND p.gid='".$mybb->user['usergroup']."') 
							WHERE INSTR(CONCAT(',',parentlist,','),',$forum,') > 0 AND active!=0 AND (ISNULL(p.fid) OR p.cansearch=1)
						");
				}
				while($sforum = $db->fetch_array($query))
				{
					$fidlist[] = $sforum['fid'];
				}
			}
		}
		if(count($fidlist) == 1)
		{
			$forumin .= " AND t.fid='$forum' ";
			$searchin[$fid] = 1;
		}
		else
		{
			
			if(count($fidlist) > 1)
			{
				$forumin = " AND t.fid IN (".implode(',', $fidlist).")";
			}
		}
	}
	$unsearchforums = get_unsearchable_forums();
	if($unsearchforums)
	{
		$permsql = " AND t.fid NOT IN ($unsearchforums)";
	}
	$inactiveforums = get_inactive_forums();
	if($inactiveforums)
	{
		$permsql .= " AND t.fid NOT IN ($inactiveforums)";
	}

	// Searching a specific thread?
	if($search['tid'])
	{
		$tidsql = " AND t.tid='".intval($search['tid'])."'";
	}
	
	$limitsql = '';
	if(intval($mybb->settings['searchhardlimit']) > 0)
	{
		$limitsql = "LIMIT ".intval($mybb->settings['searchhardlimit']);
	}

	// Searching both posts and thread titles
	$threads = array();
	$posts = array();
	$firstposts = array();
	if($search['postthread'] == 1)
	{
		// No need to search subjects when looking for results within a specific thread
		if(!$search['tid'])
		{
			$query = $db->query("
				SELECT t.tid, t.firstpost
				FROM ".TABLE_PREFIX."threads t
				WHERE 1=1 $thread_datecut $thread_replycut $forumin $thread_usersql $permsql AND t.visible>=0 AND t.closed NOT LIKE 'moved|%' $subject_lookin
				{$limitsql}
			");
			while($thread = $db->fetch_array($query))
			{
				$threads[$thread['tid']] = $thread['tid'];
				if($thread['firstpost'])
				{
					$posts[$thread['tid']] = $thread['firstpost'];
				}
			}
		}

		$query = $db->query("
			SELECT p.pid, p.tid
			FROM ".TABLE_PREFIX."posts p
			LEFT JOIN ".TABLE_PREFIX."threads t ON (t.tid=p.tid)
			WHERE 1=1 $post_datecut $thread_replycut $forumin $post_usersql $permsql $tidsql AND p.visible>=0 AND t.visible>=0 AND t.closed NOT LIKE 'moved|%' $message_lookin
			{$limitsql}
		");
		while($post = $db->fetch_array($query))
		{
			$posts[$post['pid']] = $post['pid'];
			$threads[$post['tid']] = $post['tid'];
		}
		if(count($posts) < 1 && count($threads) < 1)
		{
			error($lang->error_nosearchresults);
		}
		$threads = implode(',', $threads);
		$posts = implode(',', $posts);

	}
	// Searching only thread titles
	else
	{
		$query = $db->query("
			SELECT t.tid, t.firstpost
			FROM ".TABLE_PREFIX."threads t
			WHERE 1=1 $thread_datecut $thread_replycut $forumin $thread_usersql $permsql AND t.visible>=0 $subject_lookin
			{$limitsql}
		");
		while($thread = $db->fetch_array($query))
		{
			$threads[$thread['tid']] = $thread['tid'];
			if($thread['firstpost'])
			{
				$firstposts[$thread['tid']] = $thread['firstpost'];
			}
		}
		if(count($threads) < 1)
		{
			error($lang->error_nosearchresults);
		}

		$threads = implode(',', $threads);
		$firstposts = implode(',', $firstposts);
		if($firstposts)
		{
			$query = $db->simple_select("posts", "pid", "pid IN ($firstposts) AND visible >= '0' {$limitsql}");
			while($post = $db->fetch_array($query))
			{
				$posts[$post['pid']] = $post['pid'];
			}
			$posts = implode(',', $posts);
		}
	}
	return array(
		"threads" => $threads,
		"posts" => $posts,
		"querycache" => ''
	);
}
?>