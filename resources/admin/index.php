<?php
session_start();
error_reporting(E_ALL);

define('MASK', 0);

require_once('log/common.php');

if(!empty($_GET['submit']))
	{
	$username = $_POST['username'];
	$password = $_POST['password'];

	$existUser = False;
	$existUser = getUser($username, $password);
    $logStatus = "";	
	if($existUser == True)
		{
        $logStatus = "<div id=\"valid\">Veuillez patienter, chargement de l'administration...</div>";
        $_SESSION['username'] = $username;
        $_SESSION['admin'] = date('H:m');
        $_SESSION['logged'] = True;
		echo '<meta http-equiv="refresh" content="0; url=main.php" />';
		}
    else
        {
        $logStatus = "<div id=\"logError\">Votre nom d'utilisateur, ou votre mot de passe ne sont pas correct.<br><br>Veuillez vous identifier &agrave; nouveau.</div>";
        }
	}
?>
<html>
  <head>
    <title>AGAT - Identification</title>
    <link rel="stylesheet" type="text/css" href="../common/styles/common.css"/>
    <link rel="stylesheet" type="text/css" href="../common/styles/interface.css"/>
    <link rel="stylesheet" type="text/css" href="log/log.css"/>
  </head>
  <body>
  	<div id="identification">
  		<img src="images/admin.jpg"><br>
        <?php
        if(!empty($_GET['submit']))
            {
            echo $logStatus;
            }
        ?>
  		<form method="POST" action="?submit=true">
	  		<table id="id_form">
	  			<tr>
	  				<td>Nom d'utilisateur : </td>
	  				<td><input type="text" name="username" class="input"></td>
	  			</tr>
	  			<tr>
	  				<td>Mot de passe : </td>
	  				<td><input type="password" name="password" class="input"></td>
	  			</tr>
	  			<tr>
	  				<td>&nbsp;</td>
	  				<td><input type="submit" value="Valider"></td>
	  			</tr>
	  		</table>
	  	</form>
  	</div>
  </body>
</html>
