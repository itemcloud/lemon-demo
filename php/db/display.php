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

/* -------------------------------------------------------- **
** -------------------- DOCUMENT CLASS -------------------- **
** -------------------------------------------------------- */

class Document {
	
	function displayDocumentHeader($meta) {
		$scripts = '';
		foreach($meta['scripts'] as $script => $src) { 
		 	$scripts .= '<script src="' . $src . '"></script>';
		}

		$header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'

			 . '<html>'
		 	 . '<head>'
		 	 . '<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />'
		 	 . '<title>' . $meta['title'] . '</title>'
		 	 . '<link rel="stylesheet" type="text/css" href="frame.css">'
		 	 . '<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">';

		$header .= $scripts;
		$header .= '</head>';
		$header .= '<body>';
		echo $header;
	}

	function displayDocumentFooter($links) {
		$left = '<div style="float: left; font-size: 14px; padding: 2px;">'
		       . $links['copyleft']
		       . '</div>';
			
		$right = '<div style="float: right; font-size: 1em">'
			. '<div class="clear" style="font-size: .8em;">'
			. $links['copyright']
			. '</div>'
			. '</div>';
			
		$footer = $left . $right . '<div class="clear"></div>';
		$this->displayWrapper('div', 'footer', 'footer_inner', $footer);
		echo "</body></html>";
	}

	function displayPageBanner ($user) {
		$auth = $user->auth;
		 
		$logo = "<div class=\"logo\" onClick=\"window.location='./'\">lemon</div>";		
		$user_links = "<div class=\"user_links\">";
		if($auth) {
			  $user_links .= '+ <a href="add.php">ADD</a>' . ' | '
			  	      . '<a href="./?user=' . $user->user_serial . '">MY ITEMS</a>' . ' | '
			  	      . '<a onclick="logout()"><u>SIGN OUT</u></a>';
		}
		else { $user_links .=  '<a href="./?connect=1">SIGN IN</a>'; }
		$user_links .= '</div>';

		$this->displayWrapper('div', 'banner', 'banner_inner', $user_links . $logo);
	}
	
	function joinForm () {
		 $joinForm = "<div id=\"joinForm\"></div>"
		       	   . "<script>joinForm('joinForm');</script>";
		 return $joinForm;
	}
}

/* -------------------------------------------------------- **
** ----------------------- PAGE CLASS --------------------- **
** -------------------------------------------------------- */

class pageManager extends Document {

	function __construct($itemData, $ROOTweb) {
		$this->meta = $itemData->meta;
		$this->items = $itemData->items;
		$this->classes = $itemData->classes;
		$this->ROOTweb = $ROOTweb;
		$this->addOns = NULL;
		$this->profileOutput = "";
	}
	
	function enableAddOns () {
		global $addOns;
		if(isset($addOns)) {
			$this->addOns = $addOns;
		}
	}
	
	function displayPageItems () {
		if(isset($this->meta['profile'])) {
			$profile = $this->meta['profile'];
  		    $this->profileOutput = $this->profileBanner($profile);
			
			if($this->addOns) {
				foreach($this->addOns as $addOn) {
					if(isset($addOn['profile-display'])){ 
						global $client;
						$addonClass = new $addOn['profile-display']($client->profile);
						$addonClass->updateOutputHTML($this);
					}
				}
			}
			$profileBanner = $this->profileOutput;
		    $this->displayWrapper('div', 'section', 'section_inner', $profileBanner);
		}	
		
		$itemsPage = $this->handlePageItems();
		if(isset($this->meta['owner'])) {
  		    $omniBox = $this->displayOmniBox();
			$itemsPage = $omniBox . $itemsPage;
		}
		$this->displayWrapper('div', 'section', 'section_inner splash-page', $itemsPage);	
	}

	function displayPageOmniBox () {
		$omniBox = $this->displayOmniBox($this->classes);
		$this->displayWrapper('div', 'section', 'section_inner splash-page', $omniBox);
	}
		
	function handlePageItems() {

		if($this->addOns) {
			foreach($this->addOns as $addOn) {
				if(isset($addOn['page-display'])) { 
					$addonClass = new $addOn['page-display']();
					$returnPage = $addonClass->addonPageItems($this);
					if($returnPage) { return $returnPage; }
				}
			}
		}
		
		if(isset($_GET['connect'])) {
				$page = "<div class=\"item-section\">"
					. "<div class=\"alertbox-show\">You are currently signed in.</div>"
			    	. "</div>";
		} else if(isset($_POST['delete'])) {
				$page = "<div class=\"item-section\">"
		       	    . $this->displayItemBlog()
					. "</div>";	
		} else if(isset($_GET['id'])) {	      	     
				$page = "<div class=\"item-section\">"
					. $this->displayItem()
					. "</div>";
		} else if (isset($_GET['user'])) {
				$page = "<div class=\"item-section\">"
		       	    . $this->displayItemBlog()
					. "</div>";
		} else {
				$page = "<div class=\"item-section\">"
					//. $this->displayItemGrid(3)
					. $this->displayItemBlog()
					. "</div>";
		} return $page;
	}

	function displayWrapper ($tag, $class, $class_inner, $items) {
		 $wrap = "<$tag class='$class'><$tag class='$class_inner'>$items</$tag><div class='clear'></div></$tag>";
		 echo $wrap;
	}

	function displayItem() {
		$box_class = "item-page";
		if(!isset($this->items)){ return "No items found."; }	
		$item_html = $this->handleItemType(reset($this->items), $box_class, null);
		return $item_html;
	}
	
	function displayItemXML() {
		$item_JSON = json_encode($this->items);
		$item_XML = $this->handleXML($this->items);
		echo "<div class=\"clear\"></div></div><hr />JSON:<br/><textarea style=\"width:100%; height: 400px; resize: none;font-size: 16px\">" . $item_JSON . "</textarea>" . "XML<br /><textarea style=\"width:100%; height: 400px; resize: none;font-size: 16px\">" . $item_XML . "</textarea>"; 
	}
		
	function displayItemGrid($maxcount) {
		$box_class = "item-box";
		$info_limit = 240;
		
		$start = 1;
		$col_max = $maxcount;
		$col_holder= array();
		$num = $start;

		if(!isset($this->items)){ return "No items found."; }
		
		foreach($this->items as $i) {			
			if($num > $col_max) { $num = $start; }
			$item_html = $this->handleItemType($i, $box_class, $info_limit);
			$col_holder[$num][] = $item_html;
			$num++;
		}

		$grid = NULL;
		foreach($col_holder as $col_group) {
			$grid .= "<div style='float: left; width: 400px;'>";
			foreach($col_group as $column) {
				$grid.= $column;
			}
			$grid .= "</div>";
		} return $grid;
		
	}	

	function displayItemBlog() {
		$box_class = "item-page";
		$info_limit = 2800;
		$item_html = "";

		if(!isset($this->items)){ return "No items found."; }

		foreach($this->items as $item) {
			$item_html .= $this->handleitemType($item, $box_class, $info_limit);
		}
		return $item_html;
		
	}
	
	function profileBanner($profile) {
		$date = new DateService($profile['date']);
		$user_banner_html = (isset($profile['user_img'])) ? " background-image: url(" . $this->ROOTweb . $profile['user_img'] . ")" : ""; 
		 
		$n = "\n";
		$banner_html = "<a href=\"./?user=" . $profile['user_id'] . "\">$n"
			. "<div style=\"width: 100%; background-color: #111;\">$n"
			. "<span class=\"item-user\" style=\"padding: 50px; margin: 20px;"
			. $user_banner_html
			. "\"></span>$n"
			. "<div style=\"position: relative; text-align: left; top: 20px;\">$n"
			. "<div style=\"font-size: 2em\">" . $profile['user_name'] . "</div>" 
			. "<div class=\"bubble\" style=\"border-radius: 20px; background-color: #222; color: #666; display: inline-block; padding: 8px; font-size: 8px; text-align: center;\">MEMBER SINCE<br />" . $date->date_time . "</div></div>$n"
			. "<div class=\"clear\"></div>$n"
			. "</div>$n"
			. "</a>$n";
			
		return $banner_html;
	}

	function displayOmniBox() {
		$classes = $this->classes;
		$class_js_array = json_encode($classes);
		$class_id = (isset($_POST['itc_class_id'])) ? $_POST['itc_class_id'] : key($classes);
		
		$javascript_omni_box = "<script>var OmniController = new OmniBox(" . $class_js_array . ", 'itemOmniBox');\n OmniController.toggle('" . $class_id . "');\n</script>";
		$message = (isset($this->meta['message'])) ? "<center><div class=\"alertbox-show\">" . $this->meta['message'] . "</div></center>" : "";
		
		$createForm  = "<div class=\"item-section\"><div class=\"item-page\" id=\"itemOmniBox\">" . "</div></div>";
		$createForm .= $javascript_omni_box;
		return $message . $createForm . $javascript_omni_box;
	}	
	
	function handleXML() {		
		$item_html = "<xml><items>";
		foreach($this->items as $i) {			
			$item_html .= "<item type=\"" . $i['class_id'] . "\" date=\"" . $i['date'] . "\">"
			   . "<title>" . $i['title'] . "</title>"
			   . "<info>" . $i['info'] . "</info>"
			   . "<file>" . $i['file'] . "</file>"
			   . "</item>";
		}
		$item_html .= "</items></xml>";
		return $item_html;
	}
			
	function handleItemType ($item, $box_class, $info_limit) {
		global $client;
		$user_id = $client->user_serial;
			
		switch ($item['class_id']) {
			case 2: // item_type: link
				$itemDisplay = new ItemDisplay($item, $this->ROOTweb, $box_class, $user_id);
				$itemDisplay->fileOutput = $itemDisplay->linkOverride();
				break;
			case 3: // item_type: download
				$itemDisplay = new ItemDisplay($item, $this->ROOTweb, $box_class, $user_id);
				$itemDisplay->fileOutput = $itemDisplay->downloadOverride();
				break;
			case 4: // item_type: photo
				$itemDisplay = new ItemDisplay($item, $this->ROOTweb, $box_class, $user_id);
				$itemDisplay->fileOutput = $itemDisplay->photoOverride();
				break;
			case 5: // item_type: audio
				$itemDisplay = new ItemDisplay($item, $this->ROOTweb, $box_class, $user_id);
				$itemDisplay->fileOutput = $itemDisplay->audioOverride();
				break;
			case 6: // item_type: video
				$itemDisplay = new ItemDisplay($item, $this->ROOTweb, $box_class, $user_id);
				$itemDisplay->fileOutput = $itemDisplay->videoOverride();
				break;
			default:
				$itemDisplay = new ItemDisplay($item, $this->ROOTweb, $box_class, $user_id);
				break;
		}
		
		if($this->addOns) {
			foreach($this->addOns as $addOn) {
				if(isset($addOn['item-display'])) {
					$addonClass = new $addOn['item-display']();
					$itemDisplay->updateAddOns($addonClass);
				}
			}
		}
		
		$itemDisplay->output = $itemDisplay->displayHTML();
		return $itemDisplay->output;
	}

	function displayJoinForm () {
		$joinForm = $this->joinForm();
		$this->displayWrapper('div', 'section', 'section_inner splash-page', $joinForm);
	}
}
	
/* -------------------------------------------------------- **
** -------------------- ITEM DISPLAY ---------------------- **
** -------------------------------------------------------- */

class ItemDisplay {
	function __construct ($item, $webroot, $box_class, $user_id) {		
		$this->item = $item;
		
		$this->item_id = $item['item_id'];
		$this->class_id = $item['class_id'];
		$this->item_user_id = $item['user_id'];
		$this->box_class = $box_class;
		
		$this->user_id = $user_id;
		$this->owner = ($this->user_id && $this->item_user_id == $this->user_id) ? $this->user_id : false;
		
		$this->webroot = $webroot;		
		$this->item_user_img = (isset($item['profile']['user_img'])) ? $item['profile']['user_img'] : "";
		$this->dateService = new DateService($item['date']);
		
		$this->title = $item['title'];
		$this->info = $item['info'];
		$this->file = $item['file'];

		$this->titleOutput = $this->titleDisplayHTML();
		$this->infoOutput = $this->infoDisplayHTML();
		$this->fileOutput = $this->fileDisplayHTML();
		$this->metaOutput = $this->itemMetaLinks();
		$this->userTools = $this->itemUserTools();
		
		$this->output = $this->displayHTML();
	}
	
	function updateAddOns ($addons) {
		$addons->updateOutputHTML($this);
	}
	
	function titleDisplayHTML () {
		$title_html = "<div class=\"item-title\" onclick=\"window.location='./?id=" . $this->item_id . "';\">" . $this->title . "</div>";
		return $title_html;
	}
	
	function infoDisplayHTML () {
		$info_html = '<div class="item-info">' . nl2br(chopString($this->info, 240)) . '</div>';
		return $info_html;
	}
	
	function fileDisplayHTML () {
		$file_name_text = chopString($this->file, 34);
		$file_display = '<div class="item-link"><center>'
			  . '<div class="file_text">' . $file_name_text . '</div>'
			  . '<a href="' . $this->file . '" title="' . $this->file . '" target="_blank">'
			  . '<div class="file_button">Go to File</div></a>'
			  . '</center></div>';
		return $file_display;
	}
	
	function itemMetaLinks() {
		$item_link_html = '<div class="item-link"><a href="./?id=' . $this->item_id . '">' . $this->webroot . '?item='  . $this->item_id . '</a></div>';
		$date_html = '<div class="item-date">' . $this->dateService->date_time . '</div>';
		
		return "<div style='float: left;'>" . $item_link_html . $date_html . "</div>";
	}

	function itemUserTools() {
		if($this->owner) { 
			return "<form id=\"itemForm" . $this->item_id . "\" action=\"index.php?user=" . $this->item_user_id . "\" method=\"post\">"
			. "<input type=\"hidden\" name=\"delete\" value=\"" . $this->item_id ."\"/>"
			. "<div style=\"float: left;\" class=\"item-tools_grey\" onclick=\"dom('itemForm" . $this->item_id . "').submit()\">delete</div>"
			. "</form>"; 
		}
	}	
	
	function displayHTML() {
		$item_html = "<div onmouseover=\"dom('userTools" . $this->item_id . "').style.display='block';\" onmouseout=\"dom('userTools" . $this->item_id . "').style.display='none';\" class=\"" . $this->box_class . "\">";
		$item_html .= "<div style='position: relative;'><div id='userTools" . $this->item_id . "' style='position: absolute; right: 4px; top: 4px; display: none'>" . $this->userTools . "</div></div>";
		$item_html .= "<div class='item-nodes'>";
		if($this->title) { $item_html .= $this->titleOutput; }
		if($this->file) { $item_html .= $this->fileOutput; }
		if($this->info) { $item_html .= $this->infoOutput; }
		$item_html .= "</div>";
		$item_html .= "<div class='item-meta'>";
		$item_html .= $this->metaOutput;
		$item_html .= '<div class="clear"></div>';
		$item_html .= "</div>";
		$item_html .= '</div>';
		return $item_html;
	}
	
	function linkOverride () {
		$file_name_text = chopString($this->file, 54);

		$file_display = '<div class="item-link"><center>'
			  . '<div class="file_text">' . $file_name_text . '</div>'
			  . '<a href="' . $this->file . '" title="' . $this->file . '" target="_blank">'
			  . '<div class="file_button">Go to Page</div></a>'
			  . '</center></div>';
		return $file_display;
	}
	
	function downloadOverride () {
		$fn = $this->file;
		$fn = substr($fn, strrpos($fn, '/')+1, strlen($fn));
		$file_name_text = chopString($fn, 54);
		
		$file_display = '<div class="item-link"><center>'
				  . '<div class="file_text">' . $file_name_text . '</div>'
				  . '<a href="' . $this->file . '">'
				  . '<div class="file_button">Download File</div></a>'
				  . '</center></div>';
		return $file_display;
	}
	
	function photoOverride () {
		$onlick = "onclick=\"window.location='./?id=" . $this->item_id . "';\"";
		$file_display = "<div $onlick class=\"item-link\"><div class=\"image-cell\"><img src=\"" . $this->file . "\" width=\"100%\"></div></div>";
		return $file_display;
	}
	
	function audioOverride () {
		$file_display = '<audio style="width: 100%" controls><source src="' . $this->file . '" type="audio/mpeg">Download to play audio.</audio>';
		return $file_display;
	}
	
	function videoOverride () {
		$file_display =  '<video width="100%" controls><source src="' . $this->file . '" type="audio/mpeg">Download to play video.</video>';
		return $file_display;
	} 				
}
?>
