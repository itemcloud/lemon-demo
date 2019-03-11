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

$_ROOTdir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']).'/php/db';
require_once($_ROOTdir . '/client.php');
require_once($_ROOTdir . '/display.php');

$client = new Client();
$client->openConnection();

$auth = $client->authorizeUser();
$profile = $client->handleProfileRequest();
$owner = ($profile['user_id'] == $client->profile['user_id']) ? true : false;

$itemManager = $client->itemManager();
$types = $itemManager->getItemTypes();
$classes = $itemManager->getItemClasses();
$message = (isset($_POST['itc_type'])) ? $itemManager->handleItemUpload($client) : false;
$client->closeConnection();
	      
$pageManager = new pageManager(NULL);
$pageManager->displayDocumentHeader([
	'title' => 'i t e m c l o u d',
	'scripts' => ['./js/welcome.js',
		  './js/lib.js']
]);

$pageManager->displayPageBanner($client);
if (!$auth){ $pageManager->displayJoinForm(); }
else { $pageManager->displayCreateForm($classes, $message); }

$pageManager->displayDocumentFooter([
	'copyright' => 'Copyright &copy;2019',
	'copyleft' => 'Powered by <a href="http://www.itemcloud.org">'
				. '<img src="img/itemcloud-icon.png" style="width: 140px; vertical-align:middle"/></a><sup>&trade;</sup>'
]);
?>
