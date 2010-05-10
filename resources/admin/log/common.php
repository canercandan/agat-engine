<?php
function getUser($username, $password)
    {
    if (is_null($db = sqlite_open('../../database/database.db', 0666, $dberror)) == true)
        die($dberror);
    $req = 'select username, password from users 
    where username = \''.sqlite_escape_string($username).'\' 
    and password = \''.sqlite_escape_string($password).'\' 
    limit 1;';

    if (is_null($qry = sqlite_query($db, $req)) == true)
        die($dberror);
    $res = sqlite_fetch_all($qry, SQLITE_NUM);

    if(isset($res[0]))
        {
        return True;
        }
    else 
        {
        return False;
        }
    }

?>
