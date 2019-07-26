<?PHP
/*DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

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
$_ROOTdir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/";
$_ROOTweb = "/".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";

include($_ROOTdir .'php/db' . '/config.php'); //DATABASE: Configuration
require_once($_ROOTdir .'php/db' . '/core.php'); //DATABASE: Core MySQL Connection, DateService
require_once($_ROOTdir .'php/db' . '/client.php'); //DATABASE: Client extends Core, itemManager, uploadManager
require_once($_ROOTdir .'php/db' . '/display.php'); //DISPLAY: PageManager extends Document

//ADDONS
foreach (glob($_ROOTdir . "/php/addons/*.php") as $filename){
   require_once($filename);
}

//DATABASE: MySQL Connection
$client = new Client();
$client->enableAddOns();
$client->openConnection();

//AUTHORIZE USER ACCOUNT
$auth = $client->authorizeUser();
$itemManager = $client->itemManager();
$itemManager->enableAddOns();

$itemManager->meta['message'] = (isset($_POST['itc_class_id'])) ? $itemManager->handleItemUpload($client) : false;
$client->closeConnection();
	      
$pageManager = new pageManager($itemManager, $_ROOTweb);
$itemManager->enableAddOns();

$pageManager->displayDocumentHeader([
	'title' => 'lemon',
	'scripts' => ['./js/lib.js',
				  './js/welcome.js'],
	'styles' => ['./frame.css',
				 './addon.css']
]);

$pageManager->displayPageBanner($client, $auth);
if (!$auth && !$items){ $pageManager->displayJoinForm(); }
else { $pageManager->displayPageOmniBox(); }

$pageManager->displayDocumentFooter([
	'copyright' => 'Copyright &copy;2019',
	'copyleft' => 'Powered by <a href="http://www.itemcloud.org">'
				. '<img src="img/itemcloud-icon.png" style="width: 140px; vertical-align:middle"/></a><sup>&trade;</sup>'
]);
?>
