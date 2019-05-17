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

$_ROOTdir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);
$_ROOTweb = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";

include($_ROOTdir .'/php/db' . '/config.php'); //DATABASE: Configuration
require_once($_ROOTdir .'/php/db' . '/core.php'); //DATABASE: Core MySQL Connection, DateService
require_once($_ROOTdir .'/php/db' . '/client.php'); //DATABASE: Client extends Core, itemManager, uploadManager
require_once($_ROOTdir .'/php/db' . '/display.php'); //DISPLAY: PageManager extends Document

//DATABASE: MySQL Connection
$client = new Client();
$client->openConnection();

$auth = $client->authorizeUser(); //AUTHORIZE USER ACCOUNT
$profile = $client->handleProfileRequest(); //CHECK FOR PROFILE REQUEST IN URL
$owner = ($profile['user_id'] == $client->profile['user_id'] && $auth) ? true : false;

$itemManager = $client->itemManager();
$classes = $itemManager->classes;
$types = $itemManager->types;

$message = (isset($_POST['itc_class_id'])) ? $itemManager->handleItemUpload($client) : false;
$client->closeConnection();
	      
$pageManager = new pageManager($itemManager, $_ROOTweb);
$pageManager->displayDocumentHeader([
	'title' => 'i t e m c l o u d',
	'scripts' => ['./js/welcome.js',
		  './js/lib.js']
]);

$pageManager->displayPageBanner($client);
if (!$auth && !$items){ $pageManager->displayJoinForm(); }
else { $pageManager->displayPageOmniBox($classes, $message); }

$pageManager->displayDocumentFooter([
	'copyright' => 'Copyright &copy;2019',
	'copyleft' => 'Powered by <a href="http://www.itemcloud.org">'
				. '<img src="img/itemcloud-icon.png" style="width: 140px; vertical-align:middle"/></a><sup>&trade;</sup>'
]);
?>
