<?php
/**
 * MyBB 1.4
 * Copyright � 2008 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id: xcache.php 4304 2009-01-02 01:11:56Z chris $
 */

/**
 * Xcache Cache Handler
 */
class xcacheCacheHandler
{
	/**
	 * Unique identifier representing this copy of MyBB
	 */
	var $unique_id;
	
	function xcacheCacheHandler($silent=false)
	{
		if(!function_exists("xcache_get"))
		{
			// Check if our DB engine is loaded
			if(!extension_loaded("XCache"))
			{
				// Try and manually load it - DIRECTORY_SEPARATOR checks if running windows
				if(DIRECTORY_SEPARATOR == '\\')
				{
					@dl('php_xcache.dll');
				} 
				else 
				{
					@dl('xcache.so');
				}
				
				// Check again to see if we've been able to load it
				if(!extension_loaded("XCache") && !$silent)
				{
					// Throw our super awesome cache loading error
					die("Xcache needs to be configured with PHP to use the Xcache cache support");
					$mybb->trigger_generic_error("sql_load_error");
				}
			}
		}
	}

	/**
	 * Connect and initialize this handler.
	 *
	 * @return boolean True if successful, false on failure
	 */
	function connect()
	{
		global $mybb;

		// Set a unique identifier for all queries in case other forums on this server also use this cache handler
		$this->unique_id = md5($mybb->settings['bburl']);

		return true;
	}
	
	/**
	 * Retrieve an item from the cache.
	 *
	 * @param string The name of the cache
	 * @param boolean True if we should do a hard refresh
	 * @return mixed Cache data if successful, false if failure
	 */
	
	function fetch($name, $hard_refresh=false)
	{
		if(!xcache_isset($this->unique_id."_".$name))
		{
			return false;
		}
		return xcache_get($this->unique_id."_".$name);
	}
	
	/**
	 * Write an item to the cache.
	 *
	 * @param string The name of the cache
	 * @param mixed The data to write to the cache item
	 * @return boolean True on success, false on failure
	 */
	function put($name, $contents)
	{
		return xcache_set($this->unique_id."_".$name, $contents);
	}
	
	/**
	 * Delete a cache
	 *
	 * @param string The name of the cache
	 * @return boolean True on success, false on failure
	 */
	function delete($name)
	{
		return xcache_set($this->unique_id."_".$name, "", 1);
	}
	
	/**
	 * Disconnect from the cache
	 */
	function disconnect()
	{
		return true;
	}
	
	function size_of($name)
	{
		global $lang;
		
		return $lang->na;
	}
}
?>