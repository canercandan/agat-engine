<?php
/**
 * MyBB 1.4
 * Copyright � 2008 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id: eaccelerator.php 4304 2009-01-02 01:11:56Z chris $
 */

/**
 * eAccelerator Cache Handler
 */
class eacceleratorCacheHandler
{
	/**
	 * Unique identifier representing this copy of MyBB
	 */
	var $unique_id;
	
	function eacceleratorCacheHandler($silent=false)
	{
		if(!function_exists("eaccelerator_get"))
		{
			// Check if our DB engine is loaded
			if(!extension_loaded("Eaccelerator"))
			{
				// Try and manually load it - DIRECTORY_SEPARATOR checks if running windows
				if(DIRECTORY_SEPARATOR == '\\')
				{
					@dl('php_eaccelerator.dll');
				} 
				else 
				{
					@dl('eaccelerator.so');
				}
				
				// Check again to see if we've been able to load it
				if(!extension_loaded("Eaccelerator") && !$silent)
				{
					// Throw our super awesome cache loading error
					die("eAccelerator needs to be configured with PHP to use the eAccelerator cache support");
					$mybb->trigger_generic_error("sql_load_error");
				}
			}
		}
		return false;
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
		$data = eaccelerator_get($this->unique_id."_".$name);
		if($data === false)
		{
			return false;
		}

		return @unserialize($data);
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
		eaccelerator_lock($this->unique_id."_".$name);
		$status = eaccelerator_put($this->unique_id."_".$name, serialize($contents));
		eaccelerator_unlock($this->unique_id."_".$name);
		return $status;
	}
	
	/**
	 * Delete a cache
	 *
	 * @param string The name of the cache
	 * @return boolean True on success, false on failure
	 */
	function delete($name)
	{
		return eaccelerator_rm($this->unique_id."_".$name);
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