<?php
/**
 * MyBB 1.4
 * Copyright � 2008 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id: php_info.php 4304 2009-01-02 01:11:56Z chris $
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

if($mybb->input['action'] == 'phpinfo')
{
	$plugins->run_hooks("admin_tools_php_info_phpinfo");
	
	// Log admin action
	log_admin_action();

	phpinfo();
	exit;
}

$page->add_breadcrumb_item($lang->php_info, "index.php?module=tools/php_info");

$plugins->run_hooks("admin_tools_php_info_begin");

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_tools_php_info_start");
	
	$page->output_header($lang->php_info);
	
	echo "<iframe src=\"index.php?module=tools/php_info&amp;action=phpinfo\" width=\"100%\" height=\"500\" frameborder=\"0\">{$lang->browser_no_iframe_support}</iframe>";
	
	$page->output_footer();
}

?>