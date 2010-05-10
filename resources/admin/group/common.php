<?php

if (defined('DBFILE') == false)
  define('DBFILE', '../../database/database.db');

if (defined('DBMASK') == false)
  define('DBMASK', 0666);

function getGroup($id)
    {
    if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
        die($dberror);
    $req = 'select name, rights from groups where id = \'' .sqlite_escape_string($id) . '\' limit 1;';

    if (is_null($qry = sqlite_query($db, $req)) == true)
       die($dberror);
    $res = sqlite_fetch_all($qry, SQLITE_NUM);
    sqlite_close($db);
    return ($res[0]);
    }

function getAllGroups()
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req = 'select id, name, rights from groups;';
  if (is_null($qry = sqlite_query($db, $req)) == true)
    die($dberror);
  $res = sqlite_fetch_all($qry, SQLITE_NUM);
  sqlite_close($db);
  return ($res);
}

function	setGroup($id, $infos)
{
  list($name, $rights) = $infos;
  $mask = 0;
  if (count($rights) > 0)
    foreach ($rights as $value)
      $mask |= 1 << $value;
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'update groups set ' .
    'name = \'' . sqlite_escape_string($name) . '\', ' .
    'rights = \'' . sqlite_escape_string($mask) . '\' ' .
    'where id = \'' . sqlite_escape_string($id) . '\';'
    ;
  if (is_null(sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_changes($db);
  sqlite_close($db);
  return ($num > 0);
}

function	addGroup($infos, $dbfile = DBFILE)
{
  list($name, $rights) = $infos;
  $mask = 0;
  if (count($rights) > 0)
    foreach ($rights as $value)
      $mask |= 1 << $value;
  if (is_null($db = sqlite_open($dbfile, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'insert into groups (name, rights) values (' .
    '\'' . sqlite_escape_string($name) . '\', ' .
    '\'' . sqlite_escape_string($mask) . '\');'
    ;
  if (is_null(sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_changes($db);
  sqlite_close($db);
  return ($num > 0);
}

function	delGroup($id)
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req = 'delete from groups where id = \'' . sqlite_escape_string($id) . '\';';
  if (is_null(sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_changes($db);
  sqlite_close($db);
  return ($num > 0);
}

?>
