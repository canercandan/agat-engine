<?php
function savSQL($req)
    {
    if (is_null($db = sqlite_open('../database/database.db', 0666, $dberror)) == true)
        die($dberror);
    sqlite_query($db, $req);
    sqlite_close($db);
    }

function adminSavSQL($req)
    {
    if (is_null($db = sqlite_open('../../../../../../database/database.db', 0666, $dberror)) == true)
        die($dberror);
    sqlite_query($db, $req);
    sqlite_close($db);
    }

function execSQL($req)
    {
    if (is_null($db = sqlite_open('../database/database.db', 0666, $dberror)) == true)
        die($dberror);

    if (is_null($qry = sqlite_query($db, $req)) == true)
        die($dberror);

    $res = sqlite_fetch_all($qry, SQLITE_NUM);

    sqlite_close($db);

    return $res;
    }

function adminExecSQL($req)
    {
    if (is_null($db = sqlite_open('../../../database/database.db', 0666, $dberror)) == true)
        die($dberror);

    if (is_null($qry = sqlite_query($db, $req)) == true)
        die($dberror);

    $res = sqlite_fetch_all($qry, SQLITE_NUM);

    sqlite_close($db);

    return $res;
    }

function getContent($dbPath, $contentName)
	{
    if (is_null($db = sqlite_open($dbPath, 0666, $dberror)) == true)
        die($dberror);

	$req = "SELECT key, value FROM variables WHERE key = '".$contentName."' LIMIT 1;";

    if (is_null($qry = sqlite_query($db, $req)) == true)
        die($dberror);

    $res = sqlite_fetch_all($qry, SQLITE_NUM);

    foreach($res as $items)
        {
        list($id, $value) = $items;
        }

    sqlite_close($db);
    return $value;
	}
?>
