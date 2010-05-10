<?php
### PAGE AREA ###
#VIEW_GROUP
#EDIT_GROUP
#ADD_GROUP
#DELETE_GROUP
#SAV_GROUP
#################
session_start();
error_reporting(E_ALL);
require_once('group/common.php');
#include_once('../user/connect.php');
if (defined('DBFILE') == false)
    define('DBFILE', '../../database/database.db');
define('MASK', 0);

require_once("group/group_header.php");
require_once("common/body.php");

$rights_list = array("User" => 0, "Variable" => 1);

if (isset($_GET['error']) == true)
    {
    if ($_GET['error'] == '1')
        {
        echo '<blockquote class="error">Une erreur s\'est produite . Veuillez recommencer.</blockquote>';
        }
    else
        {
        echo '<blockquote class="notice">La t&acirc;che s\'est ex&eacute;cut&eacute;e avec succ&egrave;s.</blockquote>';
        }
    }

#VIEW_GROUP
if(isset($_GET['valid']) == false && isset($_GET['form']) == false)
    {
    echo '<table class="userTable">
            <caption>Liste des groupes</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom du groupe</th>
                    <th>Droits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>';

    $tab = getAllGroups();

    foreach ($tab as $item)
        {
        list($id, $name, $rights) = $item;
        echo '<tr>
            <td>'.$id.'</td>
            <td>'.$name.'</td>
            <td>';

        foreach ($rights_list as $key => $value)
            {
            $mask = 1 << $value;
            if (($rights & $mask) == $mask)
              echo $key . ' ';
            }
        echo '</td>
            <td>
                <a href="group.php?form=edit&id='.$id.'">Modifier</a>
                <a href="group.php?form=remove&id='.$id.'">Supprimer</a>
            </td>
        </tr>';
        }
    echo '</tbody></table>
    <div id="addUser"><a href="?form=add">Ajouter un nouvelle utilisateur</a></div>';
    }
else if(isset($_GET['form']) == true)
    {
    $form = $_GET['form'];
    #EDIT_GROUP
    if (isset($_GET['id']) == true && $form == 'edit')
        {
	        $id = $_GET['id'];
	        list($name, $rights) = getGroup($id);

        echo '<form action="group.php?valid=edit" method="post">
        <input type="hidden" name="id" value="'.$id.'"/>
        <table class="userTable">
                <caption>Mise &agrave; jour</caption>
            <tbody>
                <tr>
                    <th>Nom du groupe</th>
                    <td><input type="text" name="name" value="'.$name.'"/></td>
                </tr>
                <tr>
                    <th>Droits</th>
                    <td>';

        foreach ($rights_list as $key => $value)
            {
            $mask = 1 << $value;
            $checked = ($rights & $mask) == $mask;
            $ck = '';
            if($checked == true)
                $ck =' checked="checked"';
            echo '<label><input type="checkbox" name="rights[]" value="'.$value.'"'.$ck.'/> '.$key.'</label>';
            }

        echo '</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="Mettre &agrave; jour"/> <a href="group.php?">Annuler</a></td>
                </tr>
            </tbody>
        </table>
        </form>';
        }
    #ADD_GROUP
    else if($form == "add")
        {
        echo '<form action="group.php?valid=add" method="post">
        <table class="userTable">
                <caption>Ajouter un nouveau groupe</caption>
            <tbody>
                <tr>
                    <th>Nom du groupe</th>
                    <td><input type="text" name="name"/></td>
                </tr>
                <tr>
                    <th>Droits</th>
                    <td>';
        foreach ($rights_list as $key => $value)
            {
            echo '<label><input type="checkbox" name="rights[]" value="'.$value.'"/> '.$key.'</label>';
            }

        echo '</td>
                </tr>
                <tr>
                    <td>&nbsp;<td/>
                    <td><input type="submit" value="Cr&eacute;er un groupe"/> <a href="group.php?">Annuler</a></td>
                </tr>
            </tbody>
        </table>
        </form>';
        }
    #DELETE_GROUP
    else if(isset($_GET['id']) == true && $form == "remove")
        {
        $id = $_GET['id'];
        list($name, $rights) = getGroup($id);
        echo '<form action="group.php?valid=remove" method="post">
            <input type="hidden" name="id" value="'.$id.'"/>
            <table class="userTable">
                    <caption>Suppression d\'un groupe</caption>
                <tbody>
                    <tr>
                        <th>Nom du groupe</th>
                        <td>'.$name.'</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<td/>
                        <td>
                            <input type="submit" value="Supprimer"/> <a href="group.php?">Annuler</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>';
        }
    }
#SAV_GROUP
else if (isset($_GET['valid']) == true)
    {
    $valid = $_GET['valid'];

    if (isset($_POST['name']) == true &&
    empty($_POST['name']) == false &&
    $valid == 'add')
      $ctrl = addGroup(array($_POST['name'],
		         (isset($_POST['rights']) == true) ? $_POST['rights'] : array()));
    else if (isset($_POST['id']) == true &&
         isset($_POST['name']) == true &&
         empty($_POST['name']) == false &&
         $valid == 'edit')
      $ctrl = setGroup($_POST['id'], array($_POST['name'],
				      (isset($_POST['rights']) == true) ? $_POST['rights'] : array()));
    else if (isset($_POST['id']) == true &&
         $valid == 'remove')
      $ctrl = delGroup($_POST['id']);
    else
      $ctrl = false;

    if($ctrl == false) 
        $error = '1';
    else
        $error = '0';

    echo '<meta http-equiv="refresh" content="0; url=group.php?error='.$error.'" />';
    exit(0);
    }
require_once("common/footer.php");

?>
