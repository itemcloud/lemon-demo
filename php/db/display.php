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

	function __construct($itemXML) {
		$this->items = $itemXML;
	}
	
	function displayPageItems ($profile) {
		if($profile) {
  		     $profileBanner = $this->profileBanner($profile);
		     $this->displayWrapper('div', 'section', 'section_inner', $profileBanner);
		}	
		$itemsPage = $this->handlePageItems();
		$this->displayWrapper('div', 'section', 'section_inner splash-page', $itemsPage);	
	}

	function handlePageItems() {
		if(isset($_GET['connect'])) {
				$page = "<div class=\"item-section\">"
				. "<div class=\"alertbox-show\">You are currently signed in.</div>"
			    	. "</div>";
		} else if(isset($_POST['delete'])) {
				$page = "<div class=\"item-section\">"
				. "<div class=\"alertbox-show\">The item has been deleted.</div>"
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
		  $page = "<div class=\"item-section\" style=\"width: 820px\">"
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

		foreach($this->items as $i) {			
			$item_html .= $this->handleItemType($i, $box_class, $info_limit);
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

	function displayCreateForm($classes, $message) {
		if($message) { $createForm = "<center><div class=\"alertbox-show\">$message</div></center>"; }
		else { $createForm = "<center><div class=\"alertbox-show\">+ Add an Item</div></center>"; }
		
		foreach($classes as $class_form) {
			
			$form_input = "<input type=\"hidden\" name=\"itc_type\" value=\"" . $class_form['class_id'] . "\"/>";

			$functions = " action=\"add.php\" method=\"post\"";			
			if(isset($class_form['types']) && count($class_form['types'])) {
				$functions = " action=\"add.php\" method=\"post\" enctype=\"multipart/form-data\"";				
			}
				
			foreach($class_form['nodes'] as $nodes) {
				if(count($class_form['types']) && $nodes['node_name'] == "file") {

					$types = $class_form['types'];
					$upload = "<input type=\"file\" name=\"itc_" . $nodes['node_name'] . "\" id=\"itc_" . $nodes['node_name'] . "\" accept=\"";

					//accepted filetypes
					$upload .= implode(", ", $types);
					$upload .= "\"/><div><small>Choose " . implode(", ",$types) . " only.</small></div>";
				
					$upload .="<input class=\"form_button\" type=\"submit\" name=\"submit\" value=\"+ Add " . $class_form['class_name'] . "\"/><br />";
				} else {
					if(!$nodes['required']) {
								$domid = "itc_" . $nodes['node_name'] . "_" . $class_form['class_id'];
								$domid_add = "itc_add_" . $nodes['node_name'] . "_" . $class_form['class_id'];

								$show = "this.style.display='none';"
								      . "dom('$domid').style.display = 'block'";
								$hide = "dom('$domid_add').style.display = 'block';"
								      . "dom('$domid').style.display = 'none';"
								      . "dom('$domid"."_txt').value = '';";
								
								$form_input .= "<div id=\"$domid_add\" onclick=\"$show\"><a>+ <u>Add " . ucfirst($nodes['node_name']) . "</u></a></div>";
								$form_input .= "<div id=\"$domid\" style=\"display: none\"><textarea id=\"$domid"."_txt\" class=\"form wider\" name=\"itc_" . $nodes['node_name'] . "\" onkeyup=\"auto_expand(this)\" maxlength=\"" . $nodes['length']  . "\" style=\"vertical-align: bottom\"></textarea> <span onClick=\"$hide\" class=\"item-tools\">x</span></div>";
					}
				  	else { $form_input .= "<textarea class=\"form wider\" name=\"itc_" . $nodes['node_name'] . "\" onkeyup=\"auto_expand(this)\" maxlength=\"" . $nodes['length']  . "\"></textarea>"; }
					

					$form_input .= "<hr />";
					
				    $upload = "<input class=\"form_button\" type=\"submit\" name=\"submit\" value=\"+ Add " . $class_form['class_name'] . "\"/>";	}
			}
		
			$createForm .= "<div class=\"item-section\"><div class=\"item-page\"><form$functions>"
			    . $form_input
			    . $upload
			    . "</form></div></div>";
		}

		$this->displayWrapper('div', 'section', 'section_inner splash-page', $createForm);
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
	
	function handleItemType ($i, $box_class, $info_limit) {
		global $client;
		global $_ROOTweb;
		$user_id = $client->user_serial;
		
		$title = $i['title'];
		$info = $i['info'];
		if ($info_limit) { $info = chopString($i['info'], $info_limit); }
		
		$fn = $i['file'];
		
		$fn = substr($fn, strrpos($fn, '/')+1, strlen($fn));
		$file = chopString($fn, 54);
		$file_display = "";
		
		$date = new DateService($i['date']);
		
		$tools = "";
		$owner_id = ($user_id && $i['user_id'] == $user_id) ? $user_id : false;
		if($owner_id) { $tools = "<form id=\"itemForm" . $i['item_id'] . "\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"delete\" value=\"" . $i['item_id'] ."\"/><div style=\"float: left;\" class=\"item-tools\" onclick=\"dom('itemForm" . $i['item_id'] . "').submit()\">delete</div></form>"; }
 
		switch ($i['class_id']) {
			case 1: // item_type: note
				$file_display;
				break;
			case 2: // item_type: link
				$file = chopString($i['file'], 54);

				$file_display = '<div class="item-link"><center>'
					      . '<div class="file_text">' . $file . '</div>'
					      . '<a href="' . $i['file'] . '" title="' . $i['info'] . '" target="_blank">'
					      . '<div class="file_button">Go to Page</div></a>'
					      . '</center></div><hr />';
				break;
			case 3: // item_type: file
				$file_display = '<div class="item-link"><center>'
					      . '<div class="file_text">' . $file . '</div>'
					      . '<a href="' . $i['file'] . '">'
					      . '<div class="file_button">Download File</div></a>'
					      . '</center></div><hr />';
				break;
			case 4: // item_type: photo
			     	$onlick = "onclick=\"window.location='./?id=" . $i['item_id'] . "';\"";
			     	$file_display = "<div $onlick class=\"item-link\"><div class=\"image-cell\"><img src=\"" . $i['file'] . "\" width=\"100%\"></div></div><hr />";
				break;
			case 5: // item_type: audio
				$file_display = '<audio style="width: 100%" controls><source src="' . $i['file'] . '" type="audio/mpeg">Download to play audio.</audio>';
				break;
			case 6: // item_type: video
				$file_display = '<video width="100%" controls><source src="' . $i['file'] . '" type="audio/mpeg">Download to play video.</video>';
				break;
			default:
				$file_display;
				break;
		}	
		
		$i_html  = "<div class='" . $box_class . "'>";
		if($i['title']) { $i_html .= "<div class=\"item-title\" onclick=\"window.location='./?id=" . $i['item_id'] . "';\">" . $i['title'] .  "</div><hr />"; }
		$i_html .= $file_display;
		if($i['info']) { $i_html .= '<div class="item-info">' . nl2br($info) . '</div><hr />'; }
		$i_html .= "<div onclick=\"window.location='./?user=" . $i['user_id'] . "';\"><span class=\"item-user\"></span></div>";
		$i_html .= '<div style="float: left;"><div class="item-link"><a href="./?id=' . $i['item_id'] . '">' . $_ROOTweb . '?item='  . $i['item_id'] . '</a></div>';
		$i_html .= '<div class="item-date">' . $date->date_time . '</div></div>' . $tools;
		$i_html .= '<div class="clear"></div>';
		//$i_html .= $tools;		
		$i_html .= '</div>';
		return $i_html;
	}

	function displayJoinForm () {
		$joinForm = $this->joinForm();
		$this->displayWrapper('div', 'section', 'section_inner splash-page', $joinForm);
	}
}
?>
