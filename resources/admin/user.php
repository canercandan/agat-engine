<?php
### PAGE AREA ###
#VIEW
#EDIT USER
#ADD USER
#DELETE USER
#SAV UPDATE
#################
session_start();
error_reporting(E_ALL);
require_once('./user/common.php');
require_once('./group/common.php');
if (defined('DBFILE') == false)
    define('DBFILE', '../../database/database.db');
define('MASK', 0);

require_once("user/user_header.php");
require_once("common/body.php");

#VIEW
if(isset($_GET['valid']) == false && isset($_GET['form']) == false)
    {
    echo '<table class="userTable"><caption>Liste des utilisateurs</caption>
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Mot de passe</th>
                <th>Groupe</th>
                <th>Actions</th>
            </tr>   
        </thead>';

    $tab = getAllUsers();
    foreach ($tab as $item)
        {
        list($id, $username, $password, $id_group) = $item;
        list($name, $rights) = getGroup($id_group);
        echo '<tr>
            <td>'.$id.'</td>
            <td>'.$username.'</td>
            <td>'.$password.'</td>
            <td>'.$name.'</td>
            <td>
                <a href="user.php?form=edit&id='.$id.'">Modifier</a>
                <a href="user.php?form=remove&id='.$id.'">Supprimer</a>
            </td>
        </tr>';
        }
    echo '</table>
    <div id="addUser"><a href="?form=add">Ajouter un nouvelle utilisateur</a></div>';
    }
else if(isset($_GET['form']) == true)
    {
    $form = $_GET['form'];
    #EDIT USER
    if (isset($_GET['id']) == true && $form == 'edit')
        {
        $id = $_GET['id'];
        list($username, $password, $id_group) = getUser($id);

        echo '<form action="user.php?valid=edit" method="post">
        <input type="hidden" name="id" value="'.$id.'"/>
        <table class="userTable">
            <caption>Mettre &agrave; jour cette utilisateur</caption>
        <tbody>
            <tr>
                <th>Nom d\'utilisateur</th><td><input type="text" name="username" value="'.$username.'"/></td>
            </tr>
            <tr>
                <th>Mot de passe</th><td><input type="text" name="password" value="'.$password.'"/></td>
            </tr>
            <tr>
                <th>Groupe</th>
                <td><select name="id_group">';

        $groups_list = getAllGroups();
        foreach ($groups_list as $item)
            {
            list($id, $name, $rights) = $item;
            $checked = ($id == $id_group);
            echo '<option value="'.$id.'"';
            if($checked == true)
                echo 'selected="selected"';
            echo '/>'.$name.'</option>';
            }

        echo '</select>
                </td>
            </tr>
            <tr>
                <td><input type="submit" value="Update"/> <a href="user.php?">Annuler</a></td>
        </tbody>
        </table>
        </form>';   
        }
    #ADD USER
    else if($form == "add")
        {
        echo '<form action="user.php?valid=add" method="post">
        <table class="userTable">
            <caption>Add an user</caption>
        <tbody>
            <tr>
                <th>Nom d\'utilisateur</th>
                <td><input type="text" name="username"/></td>
            </tr>
            <tr>
                <th>Mot de passe</th>
                <td><input type="text" name="password"/></td>
            </tr>
            <tr>
                <th>Groupe</th>
                <td>
                    <select name="id_group">';

        $groups_list = getAllGroups();
        foreach ($groups_list as $item)
            {
            list($id, $name, $rights) = $item;
            echo '<option value="'.$id.'"/>'.$name.'</option>';
            }

        echo '</select>
                </td>
            </tr>
            <tr>
                <td/>
                <td><input type="submit" value="Cr&eacute;er"/> <a href="user.php?">Annuler</a></td>
        </tbody>
        </table>
        </form>';
        }
    #DELETE USER
    else if(isset($_GET['id']) == true && $form == "remove")
        {
	    $id = $_GET['id'];
	    list($username, $password, $id_group) = getUser($id);

        echo '<form action="user.php?valid=remove" method="post">
        <input type="hidden" name="id" value="'.$id.'"/>
        <table class="userTable">
                <caption>Voulez-vous supprimer l\'utilisateur suivant</caption>
            <tbody>
                <tr>
                    <th>Utilisateur</th>
                    <td>'.$username.'</td>
                </tr>
                <tr>
                    <td/>&nbsp;<td>
                    <td/><input type="submit" value="Confirmer"/> <a href="user.php?">Annuler</a></td>
                </tr>
            </tbody>
        </table>
        </form>';
        }
    }
#SAV UPDATE
else if (isset($_GET['valid']) == true)
  {
    $valid = $_GET['valid'];

    if (isset($_POST['username']) == true &&
    isset($_POST['password']) == true &&
    isset($_POST['id_group']) == true &&
    empty($_POST['username']) == false &&
    empty($_POST['password']) == false &&
    empty($_POST['id_group']) == false &&
    $valid == 'add')
      $ctrl = addUser(array($_POST['username'], $_POST['password'], $_POST['id_group']));
    else if (isset($_POST['id']) == true &&
         isset($_POST['username']) == true &&
         isset($_POST['password']) == true &&
         isset($_POST['id_group']) == true &&
         empty($_POST['username']) == false &&
         empty($_POST['password']) == false &&
         empty($_POST['id_group']) == false &&
         $valid == 'edit')
      $ctrl = setUser($_POST['id'], array($_POST['username'], $_POST['password'], $_POST['id_group']));
    else if (isset($_POST['id']) == true &&
         $valid == 'remove')
      $ctrl = delUser($_POST['id']);
    else
        $ctrl = false;
    echo '<meta http-equiv="refresh" content="0; url=user.php" />';
    //header('Location:index.php?error=' . (($ctrl == false) ? '1' : '0'));
    exit(0);
  }
require_once("common/footer.php");
?>
