<?PHP
/*
**  _ _                      _                 _
** (_) |_ ___ _ __ ___   ___| | ___  _   _  __| |
** | | __/ _ \ '_ ` _ \ / __| |/ _ \| | | |/ _` |
** | | ||  __/ | | | | | (__| | (_) | |_| | (_| |
** |_|\__\___|_| |_| |_|\___|_|\___/ \__,_|\__,_|
**          ITEMCLOUD (LEMON) Version 0.1
**
** Copyright (c) 2019, ITEMCLOUD http://www.itemcloud.org/
** All rights reserved.
** developers@itemcloud.org
**
** Open Source License
** -------------------
** Lemon is licensed under the terms of the Open Source GPL 3.0 license.
**
** @category   ITEMCLOUD
** @package    Lemon Build Version 0.1
** @copyright  Copyright (c) 2019 ITEMCLOUD (http://www.itemcloud.org)
** @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
*/

$_ROOTdir = $_SERVER['DOCUMENT_ROOT'] . '/lemon';
require_once($_ROOTdir . '/php/db/client.php');

//DATABASE::Connection
$client = new Client();
$client->openConnection();

$e = $_REQUEST['e'];
$p = $_REQUEST['p'];
if($e && $p) {
	$client->signIn($e, $p);
} else { echo "FAIL"; }
  
//DATABASE::Close
$client->closeConnection();
?>
