<?php
error_reporting(E_ALL);

define('DBFILE', '../../database/database.db');
define('MASK', 1);

require_once('../user/common.php');
include_once('../user/connect.php');

require_once('common.php');

if (isset($_GET['valid']) == true)
  {
    $valid = $_GET['valid'];

    if (isset($_POST['key']) == true &&
	isset($_POST['value']) == true &&
	$valid == 'edit')
      {
	$key = $_POST['key'];
	$value = $_POST['value'];
	$ctrl = setVariable($key, $value);
      }
    else
      $ctrl = false;

    header('Location:index.php?error=' . (($ctrl == false) ? '1' : '0'));
    exit(0);
  }
?>
<html>
<head>
<title>Variable Interface</title>
<link rel="stylesheet" type="text/css" href="../../common/styles/common.css"/>
<link rel="stylesheet" type="text/css" href="../../common/styles/interface.css"/>
</head>
<body>
<div>

<a href="index.php?action=destroy">Sign Out</a>

<?php
if (isset($_GET['error']) == true)
  {
    if ($_GET['error'] == '1')
      {
?>

<blockquote class="error">
An error has been found during the request asked.
</blockquote>

<?php
      }
    else
      {
?>

<blockquote class="notice">
The request has been successed.
</blockquote>

<?php
      }
  }

if (isset($_GET['form']) == true)
  {
    $form = $_GET['form'];

    if (isset($_GET['key']) == true &&
	$form == 'edit')
      {
	$key = $_GET['key'];
?>

<form action="index.php?valid=edit" method="post">
<input type="hidden" name="key" value="<?php echo $key; ?>"/>
<table>
<caption>Edit a variable</caption>
<tbody>
<tr>
<th>Key</th><td><?php echo $key; ?></td>
</tr>
<tr>
<th>Value</th><td><textarea name="value"><?php echo getVariable($key); ?></textarea></td>
</tr>
<tr>
<td/>
<td><input type="submit" value="Update"/> <a href="index.php?">Cancel</a></td>
</tbody>
</table>
</form>

<?php
      }
  }
?>

<table>
<caption>List of all the variables</caption>
<thead>
<tr>
<th>Key</th>
<th>Value</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php

$tab = getAllVariables();

foreach ($tab as $item)
  {
    list($key, $value) = $item;
?>
<tr>
<td><?php echo $key; ?></td>
<td><?php echo $value; ?></td>
<td><a href="index.php?form=edit&key=<?php echo $key; ?>">Edit</a></td>
</tr>
<?php
  }
?>
</tbody>
</table>

</div>
</body>
</html>
