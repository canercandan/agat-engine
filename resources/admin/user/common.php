<?php

if (defined('DBFILE') == false)
  define('DBFILE', '../../database/database.db');

if (defined('DBMASK') == false)
  define('DBMASK', 0666);

function	getUser($id)
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'select username, password, id_group from users where id = \'' .
    sqlite_escape_string($id) . '\' limit 1;'
    ;
  if (is_null($qry = sqlite_query($db, $req)) == true)
    die($dberror);
  $res = sqlite_fetch_all($qry, SQLITE_NUM);
  sqlite_close($db);
  return ($res[0]);
}

function	getAllUsers()
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req = 'select id, username, password, id_group from users;';
  if (is_null($qry = sqlite_query($db, $req)) == true)
    die($dberror);
  $res = sqlite_fetch_all($qry, SQLITE_NUM);
  sqlite_close($db);
  return ($res);
}

function setUser($id, $infos)
{
  list($username, $password, $id_group) = $infos;
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'update users set ' .
    'username = \'' . sqlite_escape_string($username) . '\', ' .
    'password = \'' . sqlite_escape_string($password) . '\', ' .
    'id_group = \'' . sqlite_escape_string($id_group) . '\' ' .
    'where id = \'' . sqlite_escape_string($id) . '\';'
    ;
  if (is_null(sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_changes($db);
  sqlite_close($db);
  return ($num > 0);
}

function	addUser($infos, $dbfile = DBFILE)
{
  list($username, $password, $id_group) = $infos;
  if (is_null($db = sqlite_open($dbfile, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'insert into users (username, password, id_group) values (' .
    '\'' . sqlite_escape_string($username) . '\', ' .
    '\'' . sqlite_escape_string($password) . '\', ' .
    '\'' . sqlite_escape_string($id_group) . '\');'
    ;
  if (is_null(sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_changes($db);
  sqlite_close($db);
  return ($num > 0);
}

function	delUser($id)
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req = 'delete from users where id = \'' . sqlite_escape_string($id) . '\';';
  if (is_null(sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_changes($db);
  sqlite_close($db);
  return ($num > 0);
}

function	checkUser($username, $password)
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'select id from users where ' .
    'username = \'' . sqlite_escape_string($username) . '\' and ' .
    'password = \'' . sqlite_escape_string($password) . '\' ' .
    'limit 1;'
    ;
  if (is_null($qry = sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_num_rows($qry);
  $res = sqlite_fetch_all($qry, SQLITE_NUM);
  sqlite_close($db);
  return ($num > 0 ? $res[0][0] : -1);
}

?>
