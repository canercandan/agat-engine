<?php
/**
 * MyBB 1.4
 * Copyright � 2008 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id: smtp.php 4343 2009-04-09 05:36:29Z Tikitiki $
 */

 // Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Initialisation directe de ce fichier non autoris&eacute;e.<br /><br />Veuillez vous assurer que IN_MYBB est d&eacute;fini.");
}

/**
 * SMTP mail handler class.
 */
 
if(!defined('MYBB_SSL'))
{
	define('MYBB_SSL', 1);
}

if(!defined('MYBB_TLS'))
{
	define('MYBB_TLS', 2);
}

class SmtpMail extends MailHandler
{
	/**
	 * The SMTP connection.
	 *
	 * @var resource
	 */
	var $connection;

	/**
	 * SMTP username.
	 *
	 * @var string
	 */
	var $username = '';

	/**
	 * SMTP password.
	 *
	 * @var string
	 */
	var $password = '';

	/**
	 * Hello string sent to the smtp server with either HELO or EHLO.
	 *
	 * @var string
	 */
	var $helo = 'localhost';

	/**
	 * User authenticated or not.
	 *
	 * @var boolean
	 */
	var $authenticated = false;

	/**
	 * How long before timeout.
	 *
	 * @var integer
	 */
	var $timeout = 5;

	/**
	 * SMTP status.
	 *
	 * @var integer
	 */
	var $status = 0;

	/**
	 * SMTP default port.
	 *
	 * @var integer
	 */
	var $port = 25;
	
	/**
	 * SMTP default secure port.
	 *
	 * @var integer
	 */
	var $secure_port = 465;

	/**
	 * SMTP host.
	 *
	 * @var string
	 */
	var $host = '';
	
	/**
	 * The last received response from the SMTP server.
	 *
	 * @var string
	 */
	var $data = '';
	
	/**
	 * The last received response code from the SMTP server.
	 *
	 * @var string
	 */
	var $code = 0;
	
	/**
	 * The last received error message from the SMTP server.
	 *
	 * @var string
	 */
	var $last_error = '';
	
	/**
	 * Are we keeping the connection to the SMTP server alive?
	 *
	 * @var boolean
	 */
	var $keep_alive = false;

	function SmtpMail()		
	{
		global $mybb;
		
		$this->__construct();
	}
	
	function __construct()
	{
		global $mybb;

		$protocol = '';
		switch($mybb->settings['secure_smtp'])
		{
			case MYBB_SSL:
				$protocol = 'ssl://';
				break;
			case MYBB_TLS:
				$protocol = 'tls://';
				break;
		}

		if(empty($mybb->settings['smtp_host']))
		{
			$this->host = @ini_get('SMTP');
		}
		else
		{
			$this->host = $mybb->settings['smtp_host'];
		}
		
		$this->helo = $this->host;

		$this->host = $protocol . $this->host;

		if(empty($mybb->settings['smtp_port']) && !empty($protocol) && !@ini_get('smtp_port'))
		{
			$this->port = $this->secure_port;
		}
		else if(empty($mybb->settings['smtp_port']) && @ini_get('smtp_port'))
		{
			$this->port = @ini_get('smtp_port');
		}
		else if(!empty($mybb->settings['smtp_port']))
		{
			$this->port = $mybb->settings['smtp_port'];
		}
	
		$this->password = $mybb->settings['smtp_pass'];
		$this->username = $mybb->settings['smtp_user'];
	}

	/**
	 * Sends the email.
	 *
	 * @return true/false whether or not the email got sent or not.
	 */
	function send()
	{
		global $lang, $mybb;

		if(!$this->connected())
		{
			$this->connect();
		}
		
		if($this->connected())
		{
			if(!$this->send_data('MAIL FROM:<'.$this->from.'>', '250'))
			{
				$this->fatal_error("Le serveur mail ne comprend pas la commande MAIL FROM. Raison: ".$this->get_error());
				return false;
			}
			
			// Loop through recipients
			$emails = explode(',', $this->to);
			foreach($emails as $to)
			{
				$to = trim($to);
				if(!$this->send_data('RCPT TO:<'.$to.'>', '250'))
				{
					$this->fatal_error("Le serveur mail ne comprend pas la commande RCPT TO. Raison: ".$this->get_error());
					return false;
				}
			}

			if($this->send_data('DATA', '354'))
			{
				$this->send_data('Date: ' . gmdate('r'));
				$this->send_data('To: ' . $this->to);
				
				$this->send_data('Subject: ' . $this->subject);

				// Only send additional headers if we've got any
				if(trim($this->headers))
				{
					$this->send_data(trim($this->headers));
				}
				
				$this->send_data("");

				// Queue the actual message
				$this->message = str_replace("\n.", "\n..", $this->message);
				$this->send_data($this->message);
			}
			else
			{
				$this->fatal_error("Le serveur mail ne comprend pas la commande DATA");
				return false;
			}

			$this->send_data('.', '250');

			if(!$this->keep_alive)
			{
				$this->close();
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Connect to the SMTP server.
	 *
	 * @return boolean True if connection was successful
	 */
	function connect()
	{
		global $lang, $mybb;

		$this->connection = @fsockopen($this->host, $this->port, $error_number, $error_string, $this->timeout);
		
		// DIRECTORY_SEPARATOR checks if running windows
		if(function_exists('stream_set_timeout') && DIRECTORY_SEPARATOR != '\\')
		{
			@stream_set_timeout($this->connection, $this->timeout, 0);
		}

		if(is_resource($this->connection))
		{
			$this->status = 1;
			$this->get_data();
			if(!$this->check_status('220'))
			{
				$this->fatal_error("Le serveur mail n'est pas pr&ecirc;t, il ne &eacute;pond pas avec un message d'&eacute;tat 220.");
				return false;
			}

			if(!empty($this->username) && !empty($this->password))
			{
				$data = $this->send_data('EHLO ' . $this->helo, '250');
				if(!$data)
				{
					$this->fatal_error("Le serveur mail ne comprend pas la commande EHLO");
					return false;
				}
				preg_match("#250-AUTH( |=)(.+)$#mi", $data, $matches);
				if(!$this->auth($matches[2]))
				{
					$this->fatal_error("MyBB est incapable de vous identifier suer le serveur SMTP");
					return false;
				}
			}
			else
			{
				if(!$this->send_data('HELO ' . $this->helo, '250'))
				{
					$this->fatal_error("Le serveur mail ne comprend pas la commande HELO");
					return false;
				}
			}
			return true;
		}
		else
		{
			$this->fatal_error("Impossibe de se connecter au serveur mail avec les d&eacute;tails fournis.<br /><br />{$error_number}: {$error_string}");
			return false;
		}
	}
	
	/**
	 * Authenticate against the SMTP server.
	 *
	 * @param string A list of authentication methods supported by the server
	 * @return boolean True on success
	 */
	function auth($auth_methods)
	{
		global $lang, $mybb;
		
		$auth_methods = explode(" ", $auth_methods);
		
		if(in_array("LOGIN", $auth_methods))
		{
			if(!$this->send_data("AUTH LOGIN", 334))
			{
				if($this->code == 503)
				{
					return true;
				}
				$this->fatal_error("Le serveur SMTP n'a pas correctement r&eacute;pondu &agrave; la commande AUTH LOGIN");
				return false;
			}
			
			if(!$this->send_data(base64_encode($this->username), '334'))
			{
				$this->fatal_error("Le serveur SMTP a refus&eacute; le nom d'utilisateur SMTP fourni. Raison: ".$this->get_error());
				return false;
			}
			
			if(!$this->send_data(base64_encode($this->password), '235'))
			{
				$this->fatal_error("Le serveur SMTP a refus&eacute; le mot de passe SMTP fourni. Raison: ".$this->get_error());
				return false;
			}
		}
		else if(in_array("PLAIN", $auth_methods))
		{
			if(!$this->send_data("AUTH PLAIN", '334'))
			{
				if($this->code == 503)
				{
					return true;
				}
				$this->fatal_error("Le serveur SMTP n'a pas r&eacute;pondu correctement &agrave; la commande AUTH PLAIN");
				return false;
			}
			$auth = base64_encode(chr(0).$this->username.chr(0).$this->password);
			if(!$this->send_data($auth, 235))
			{
				$this->fatal_error("Le serveur SMTP a refus&eacute; les nom d'utilisateur et mot de passe fournis. Raison: ".$this->get_error());
				return false;
			}
		}
		else
		{
			$this->fatal_error("Le serveur SMTP ne supporte aucune des des m&eacute;thodes AUTH que MyBB supporte");
			return false;
		}

		// Still here, we're authenticated
		return true;
	}

	/**
	 * Fetch data from the SMTP server.
	 *
	 * @return string The data from the SMTP server
	 */
	function get_data()
	{
		$string = '';

		while((($line = fgets($this->connection, 515)) !== false))
		{
			$string .= $line;
			if(substr($line, 3, 1) == ' ')
			{
				break;
			}
		}
		$this->data = $string;
		$this->code = substr(trim($this->data), 0, 3);
		return $string;
	}

	/**
	 * Check if we're currently connected to an SMTP server
	 *
	 * @return boolean true if connected
	 */
	function connected()
	{
		if($this->status == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Send data through to the SMTP server.
	 *
	 * @param string The data to be sent
	 * @param int The response code expected back from the server (if we have one)
	 * @return boolean True on success
	 */
	function send_data($data, $status_num = false)
	{
		if($this->connected())
		{
			if(fwrite($this->connection, $data."\r\n"))
			{
				if($status_num != false)
				{
					$rec = $this->get_data();
					if($this->check_status($status_num))
					{
						return $rec;
					}
					else
					{
						$this->set_error($rec);
						return false;
					}
				}
				return true;
			}
			else
			{
				$this->fatal_error("Impossible d'envoyer les donn&eacute;es au serveur SMTP");
				return false;
			}
		}
		return false;
	}

	/**
	 * Checks if the received status code matches the one we expect.
	 *
	 * @param int The status code we expected back from the server
	 * @param boolean True if it matches
	 */
	function check_status($status_num)
	{
		if($this->code == $status_num)
		{
			return $this->data;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Close the connection to the SMTP server.
	 */
	function close()
	{
		if($this->status == 1)
		{
			$this->send_data('QUIT');
			fclose($this->connection);
			$this->status = 0;
		}
	}
	
	/**
	 * Get the last error message response from the SMTP server
	 *
	 * @return string The error message response from the SMTP server
	 */
	function get_error()
	{
		if(!$this->last_error)
		{
			$this->last_error = "N/A";
		}
		
		return $this->last_error;
	}
	
	/**
	 * Set the last error message response from the SMTP server
	 *
	 * @param string The error message response
	 */
	function set_error($error)
	{
		$this->last_error = $error;
	}
}
?>