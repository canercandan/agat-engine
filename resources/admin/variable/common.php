<?php

if (defined('DBFILE') == false)
  define('DBFILE', '../database/database.db');

if (defined('DBMASK') == false)
  define('DBMASK', 0666);

function	getVariable($key)
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'select value from variables where key = \'' .
    sqlite_escape_string($key) . '\' limit 1;'
    ;
  if (is_null($qry = sqlite_query($db, $req)) == true)
    die($dberror);
  $res = sqlite_fetch_all($qry, SQLITE_NUM);
  sqlite_close($db);
  return ($res[0][0]);
}

function	getAllVariables()
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req = 'select key, value from variables;';
  if (is_null($qry = sqlite_query($db, $req)) == true)
    die($dberror);
  $res = sqlite_fetch_all($qry, SQLITE_NUM);
  sqlite_close($db);
  return ($res);
}

function	setVariable($key, $value)
{
  if (is_null($db = sqlite_open(DBFILE, DBMASK, $dberror)) == true)
    die($dberror);
  $req =
    'update variables set value = \'' . sqlite_escape_string($value) .
    '\' where key = \'' . sqlite_escape_string($key) . '\';'
    ;
  if (is_null(sqlite_query($db, $req)) == true)
    die($dberror);
  $num = sqlite_changes($db);
  sqlite_close($db);
  return ($num > 0);
}

?>