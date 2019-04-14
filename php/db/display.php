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

	function __construct($itemXML, $classObject, $ROOTweb) {
		$this->items = $itemXML;
		$this->classes = $classObject;
		$this->ROOTweb = $ROOTweb;
	}
	
	function displayPageItems ($profile, $owner) {
		global $message;
		
		if($profile) {
  		     $profileBanner = $this->profileBanner($profile);		 
		     $this->displayWrapper('div', 'section', 'section_inner', $profileBanner);
		}	
		$itemsPage = $this->handlePageItems();
		if($owner) {
  		    $omniBox = $this->displayOmniBox($this->classes, $message);
			$itemsPage = $omniBox . $itemsPage;
		}		
		$this->displayWrapper('div', 'section', 'section_inner splash-page', $itemsPage);	
	}

	function displayPageOmniBox ($classes, $message) {
		$omniBox = $this->displayOmniBox($classes, $message);
		$this->displayWrapper('div', 'section', 'section_inner splash-page', $omniBox);
	}
		
	function handlePageItems() {
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
		  } 	
		else if (isset($_GET['user'])) {
				$page = "<div class=\"item-section\">"
		       	    . $this->displayItemBlog()
					. "</div>";
		} else {
				$page = "<div class=\"item-section\" style=\"width: 1200px\">"
					. $this->displayItemGrid(3)
					//. $this->displayItemBlog()
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
	
	function profileBanner($user) {
		$date = new DateService($user['date']);
		 
		$n = "\n";
		$banner_html = "<a href=\"./?user=" . $user['user_id'] . "\">$n"
			. "<div style=\"width: 100%; background-color: #111;\">$n"
			. "<span class=\"item-user\" style=\"padding: 50px; margin: 20px;\"></span>$n"
			. "<div style=\"position: relative; text-align: left; top: 60px;\">$n"
			. "<div class=\"bubble\" style=\"border-radius: 20px; background-color: #222; color: #666; display: inline-block; padding: 8px; font-size: 8px; text-align: center;\">MEMBER SINCE<br />" . $date->date_time . "</div></div>$n"
			. "<div class=\"clear\"></div>$n"
			. "</div>$n"
			. "</a>$n";
			
		return $banner_html;
	}

	function displayOmniBox($classes, $message) {
		$class_js_array = json_encode($classes);
		$class_id = (isset($_POST['itc_class_id'])) ? $_POST['itc_class_id'] : key($classes);
		
		$javascript_omni_box = "<script>var OmniController = new OmniBox(" . $class_js_array . ", 'itemOmniBox');\n OmniController.toggle('" . $class_id . "');\n</script>";		
	
		$createForm = ($message) ? "<center><div class=\"alertbox-show\">$message</div></center>" : "";
		$createForm .= "<div class=\"item-section\"><div class=\"item-page\" id=\"itemOmniBox\">" . "</div></div>";
		$createForm .= $javascript_omni_box;
		return $createForm;
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
		global $client;	$user_id = $client->user_serial;		
		$itemDisplay = new ItemDisplay($item);
		$itemDisplay->user_id = $user_id;
		
		return $itemDisplay->htmlOutput($box_class, $this->ROOTweb, $info_limit);
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
	function __construct ($item) {
		$this->item_id = $item['item_id'];
		$this->class_id = $item['class_id'];
		$this->item_user_id = $item['user_id'];
		
		$this->title = $item['title'];
		$this->info = $item['info'];
		$this->file = $item['file'];
		
		$this->dateService = new DateService($item['date']);
	}
	
	function itemTitle () {
		$title_html = "<div class=\"item-title\" onclick=\"window.location='./?id=" . $this->item_id . "';\">" . $this->title . "</div><hr />";
		return $title_html;
	}
	
	function itemInfo ($limit) {
		$info_html = '<div class="item-info">' . nl2br(chopString($this->info, $limit)) . '</div><hr />';
		return $info_html;
	}
	
	function itemMetaLinks($webroot) {		
		$item_user_html = "<div onclick=\"window.location='./?user=" . $this->item_user_id . "';\"><span class=\"item-user\"></span></div>";
		$item_link_html = '<div class="item-link"><a href="./?id=' . $this->item_id . '">' . $webroot . '?item='  . $this->item_id . '</a></div>';
		$date_html = '<div class="item-date">' . $this->dateService->date_time . '</div>';
		
		return $item_user_html . "<div style='float: left;'>" . $item_link_html . $date_html . "</div>";
	}

	function htmlOutput ($box_class, $webroot, $info_limit) {
		if (!$info_limit) { $info_limit = strlen($this->info); }
		
		$item_html = "<div class='" . $box_class . "'>";
		$item_html .= "<div class='item-nodes'>";
		if($this->title) { $item_html .= $this->itemTitle();  }
		if($this->file) { $item_html .= $this->handleFileDisplay(); }
		if($this->info) { $item_html .= $this->itemInfo($info_limit); }
		$item_html .= "</div>";
		$item_html .= $this->itemMetaLinks($webroot);
		$item_html .= $this->handleUserTools();
		$item_html .= '<div class="clear"></div>';
		$item_html .= '</div>';
		return $item_html;
	}
	
	function handleUserTools() {
		$owner_id = ($this->user_id && $this->item_user_id == $this->user_id) ? $this->user_id : false;
		if($owner_id) { 
			return "<form id=\"itemForm" . $this->item_id . "\" action=\"index.php?user=" . $owner_id . "\" method=\"post\">"
			. "<input type=\"hidden\" name=\"delete\" value=\"" . $this->item_id ."\"/>"
			. "<div style=\"float: left;\" class=\"item-tools\" onclick=\"dom('itemForm" . $this->item_id . "').submit()\">delete</div>"
			. "</form>"; 
		}
	}		
	
	function handleFileDisplay() {
		switch ($this->class_id) {
			case 2: // item_type: link
				$file_display = $this->linkOverride();
				break;
			case 3: // item_type: download
				$file_display = $this->downloadOverride();
				break;
			case 4: // item_type: photo
				$file_display = $this->photoOverride();
				break;
			case 5: // item_type: audio
				$file_display = $this->audioOverride();
				break;
			case 6: // item_type: video
				$file_display = $this->videoOverride();
				break;
			default:
				$file_display = "";
				break;
		}	return $file_display;
	}
	
	function linkOverride () {
		$file_name_text = chopString($this->file, 54);

		$file_display = '<div class="item-link"><center>'
			  . '<div class="file_text">' . $file_name_text . '</div>'
			  . '<a href="' . $this->file . '" title="' . $this->file . '" target="_blank">'
			  . '<div class="file_button">Go to Page</div></a>'
			  . '</center></div><hr />';
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
				  . '</center></div><hr />';
		return $file_display;
	}
	
	function photoOverride () {
		$onlick = "onclick=\"window.location='./?id=" . $this->item_id . "';\"";
		$file_display = "<div $onlick class=\"item-link\"><div class=\"image-cell\"><img src=\"" . $this->file . "\" width=\"100%\"></div></div><hr />";
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
