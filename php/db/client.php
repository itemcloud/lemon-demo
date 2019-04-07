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
** --------------------- CLIENT CLASS --------------------- **
** -------------------------------------------------------- */

class Client extends Core {
	
	function authorizeUser () {
		$this->auth = false;
		$this->owner = false;
			
		///-- serial --///
		$this->user_serial = false;
		$this->profile = false;

		$uid = $this->usercookie;
		
		if (isset($_COOKIE[$uid])) {
			$this->auth = true;
			
			///-- serial --///
			$this->user_serial = $_COOKIE[$uid];

			///-- getUser() --///
			$user = $this->getUser($_COOKIE[$uid]);
			$this->profile = $user;		
		} return $this->auth;		
	}

	function getUser ($user_id) {
		$stream = $this->stream;
		
	  	if(!$stream){ return false; }
	  
		$input = "SELECT * FROM user WHERE user_id='$user_id'";
		$query = $stream->query($input);
		return $query->fetch_assoc();
	}

	function handleProfileRequest() {
		if(!isset($_GET['user'])) { return false; }
		return $this->getUser($_GET['user']);
	}
	
	function signIn ($e, $p) {
		$stream = $this->stream;

		$user_auth = "SELECT * FROM user WHERE email='$e' AND password='" . md5($p) . "'";
		$result = mysqli_query($stream, $user_auth);

		if($result) {
			$result = $result->fetch_assoc();
			$user_serial = $result['user_id'];
			setcookie('ICC:UID', $user_serial, time()+(36000*24), '/', '');
		} else {
			echo "FAIL";
		}
	}

	function registerUser($e, $p) {
		$stream = $this->stream;

		$user_auth = "SELECT * FROM user WHERE email='$e'";
		$result = mysqli_query($stream, $user_auth);

		if($result->fetch_assoc()) {
			echo "ACTIVE";
		} else {
			$user_insert = "INSERT INTO user (email, password, date) VALUES('" . $e . "', '" . md5($p) . "', '" . date('Y-m-d h:i:s') . "')";

			$insert_query = mysqli_query($stream, $user_insert);
			$user_serial = mysqli_insert_id($stream);

			if($user_serial) {
				setcookie('ICC:UID', $user_serial, time()+(36000*24), '/', '');
			} else { echo "FAIL"; }
		}
	}
	function itemManager() {
		return new itemManager($this->stream);
	}
}

/* -------------------------------------------------------- **
** --------------------- ITEMS CLASS ---------------------- **
** -------------------------------------------------------- */

class itemManager {
	var $stream;

	function __construct ($stream) {
		   $this->stream = $stream;
	}

	function handleItemRequest() {
		if(isset($_GET['connect'])) {
		  return;
		} elseif(isset($_POST['delete'])) {
			$this->deleteUserItem($_POST['delete']); 
			$items = $this->getAllItems();
		} elseif(isset($_GET['id'])){
			$items = $this->getItemById($_GET['id']);
		} elseif(isset($_GET['user'])) {
			$items = $this->getUserItems($_GET['user']);
		} else {
			$items = $this->getAllItems();
		} return $items;
	}

	function insertItem($type, $title, $info, $file) {
		$stream = $this->stream;

		$title = $stream->real_escape_string($title);
		$info = $stream->real_escape_string($info);
		$quest = "INSERT INTO item (class_id, title, info, file)"
				. " VALUES('$type', '$title', '$info', '$file')";
		
		$stream->query($quest);
		$item_id = $stream->insert_id;
		return $item_id;
	}

	function insertUserItem($user_serial, $item_id) {
		$stream = $this->stream;

		$quest = "INSERT INTO user_items (user_id, item_id)"
				. " VALUES('$user_serial', '$item_id')";
		
		$stream->query($quest);
		$item_id = $stream->insert_id;
		return $item_id;
	}
	
	function deleteItem($delete_id) {
		$stream = $this->stream;
		
		$quest = "DELETE FROM item WHERE item_id='$delete_id'";
		$stream->query($quest);		
	}

	function getUserItems($user_serial) {
		$stream = $this->stream;
		$quest = "SELECT item.*, user_items.user_id"
		       . " FROM item, user_items"
		       . " WHERE user_items.user_id=$user_serial"
		       . " AND item.item_id=user_items.item_id"
		       . " ORDER BY user_items.date DESC";
		
		$item_loot = mysqli_query($stream, $quest);
		$item_loot_array = NULL;
		if($item_loot) {
			while($loot=$item_loot->fetch_assoc()) {
				$item_loot_array[] = $loot;
			}
		}			
		return $item_loot_array;
	}

	function getAllItems() {
		$stream = $this->stream;
		$quest = "SELECT item.*, user_items.user_id"
		       . " FROM item, user_items"
		       . " WHERE item.item_id=user_items.item_id"
		       . " ORDER BY user_items.date DESC";
		
		$item_loot = mysqli_query($stream, $quest);
		$item_loot_array = NULL;
		if($item_loot) {
			while($loot=$item_loot->fetch_assoc()) {
				$item_loot_array[] = $loot;
			}
		}			
		return $item_loot_array;
	}
	
	function getItemsByType() {
		return $items;
	}

	function getItemsByUser() {
		return $items;
	}

	function getItemById($item_id) {
		$stream = $this->stream;
		$quest = "SELECT item.*, user_items.user_id"
		       	 . " FROM item, user_items"
			 . " WHERE item.item_id='$item_id'"
			 . " AND item.item_id=user_items.item_id";
		
		$item_loot = mysqli_query($stream, $quest);
		$item_loot_array = NULL;
		if($item_loot) {
			while($loot=$item_loot->fetch_assoc()) {
				$item_loot_array[] = $loot;
			}
		}			
		return $item_loot_array;
	}		
	
	function deleteUserItem ($delete_id) {
		$stream = $this->stream;
		
		$quest = "DELETE FROM user_items WHERE item_id='$delete_id'";
		$stream->query($quest);
		
		$this->deleteItem($_POST['delete']);
	}

	function getItemClasses() {
		$stream = $this->stream;
		$class_quest = "SELECT item_class.*, item_nodes.*"
					. " FROM item_class, item_nodes"
					. " WHERE item_nodes.class_id=item_class.class_id";
		
		$class_loot = mysqli_query($stream, $class_quest);		
		if($class_loot) {
			while($class=$class_loot->fetch_assoc()) {
				$class_id = $class['class_id'];				
				if(!isset($class_loot_array[$class_id])) {
					$class_loot_array[$class_id]['class_name'] = $class['class_name'];
					$class_loot_array[$class_id]['class_id'] = $class_id;
				    $class_loot_array[$class_id]['types'] = array();
				    $class_loot_array[$class_id]['ext'] = array();				
					$class_loot_array[$class_id]['nodes'] = array();
				}
				$class_loot_array[$class_id]['nodes'][] = $class;
			}
			
			foreach($class_loot_array as $loot_array) {
				$class_id = $loot_array['class_id'];				
				$type_quest = "SELECT * FROM item_type"
					. " WHERE class_id='" . $class_id . "'";
					
				$type_loot = mysqli_query($stream, $type_quest);				
				if($type_loot) {
					while($type=$type_loot->fetch_assoc()) {
						array_push($class_loot_array[$class_id]['types'], $type['file_type']);
						array_push($class_loot_array[$class_id]['ext'], $type['ext']);
					}
				}
			}
		}
		
		reset($class_loot_array);
		return $class_loot_array;
	}

	function getItemTypes() {
		$stream = $this->stream;
		$quest = " SELECT item_class.*, item_nodes.*"
		       . " FROM item_class, item_nodes"
		       . " WHERE item_nodes.class_id=item_class.class_id";
		
		$type_loot = mysqli_query($stream, $quest);
		$type_loot_array = NULL;
		if($type_loot) {
			while($loot=$type_loot->fetch_assoc()) {
				$loot_str = $loot['class_name'];
				$type_loot_array[$loot_str][] = $loot;
			}
		}			
		return $type_loot_array;
	}

	function handleItemUpload($classes, $client) {
		 if (isset($_POST['itc_class_id'])) {
			$insertOk = "1";
			$target_dir = "files/";			
			$filesize = 10485760; //10MB
			
			$posted_class = $_POST['itc_class_id'];			
			$title = (isset($_POST['itc_title'])) ? $_POST['itc_title'] : "";
			$info = (isset($_POST['itc_info'])) ? $_POST['itc_info'] : "";
			$file = (isset($_POST['itc_file'])) ? $_POST['itc_file'] : "";

			$class_form = $classes[$posted_class];
			$class_id = $class_form['class_id'];
			
			//only handle the posted class
			if($posted_class == $class_id) {
				foreach($class_form['nodes'] as $nodes){					
					if(isset($_FILES["itc_file"]) && $nodes['node_name'] == 'file') {
						if(count($class_form['ext'])) {
							$file_extensions = $class_form['ext'];
						}
					} else if(!$_POST['itc_'.$nodes['node_name']] && $nodes['required'] != NULL){
						//detect required nodes								
						$message = "Sorry, your item could not be added.";	
						$insertOk = "0"; return $message;
					}
				}
			}
				
			 if(isset($_FILES["itc_file"])) {
				$tmp_file = new uploadManager(
					$_FILES["itc_file"],
					$target_dir,
					$filesize,
					$file_extensions);

				$tmp_file->handleUploadRequest();
				$tmp_file->uploadFile();
					
				$file = $tmp_file->target_file;	
				if($tmp_file->uploadOk == "0") {
					$insertOk = "0";
				} $message = $tmp_file->errorStatus;

				if($class_id != 4 && !$title) {
				   $title = $_FILES["itc_file"]["name"];
				}
			}

			if($insertOk) {
				$id = $this->insertItem($class_id, $title, $info, $file);
				if(isset($id)) {
				  $this->insertUserItem($client->profile['user_id'], $id);
				    return "Go to new item: " . "<a href=\"./?id=$id\">ITEM $id</a>";
				}
			} else {
					return $message;
			}
		}
	}
}

/* -------------------------------------------------------- **
** -------------------- UPLOAD CLASS ---------------------- **
** -------------------------------------------------------- */

class uploadManager {

	function __construct($FILE, $target_dir, $filesize, $filetypes) {
		
		$this->tmp_file = $FILE;
		$this->target_file = $target_dir . mt_rand(99, 999) . "_" . basename($FILE["name"]);
		$this->filesize = $filesize;
		$this->filetypes = $filetypes;
		$this->uploadOk = 1;
		$this->imageFileType = strtolower(pathinfo(basename($FILE["name"]),PATHINFO_EXTENSION));
		$this->errorStatus = "Sorry, there was an error uploading your file.";
	}

	function handleUploadRequest() {
		
		// Check if file already exists
		if (file_exists($this->target_file)) {
			$this->errorStatus = "Sorry, file already exists.";
			$this->uploadOk = 0;
		}
		// Check file size
		if ($this->uploadOk && $this->tmp_file["size"] > $this->filesize) {
			$this->errorStatus = "Sorry, your file is too large.";
			$this->uploadOk = 0;
		}
		
		// Allow certain file formats
		if(!in_array($this->imageFileType, $this->filetypes)) {
			$this->errorStatus = "Choose a "
					   . implode(", ", $this->filetypes)
					   . " file.";
			$this->uploadOk = 0;
		}
	}

	function checkImage() {
		
		// Check if image file is a actual image or fake image
		if($this->tmp_file["tmp_name"]) {
			$check = getimagesize($this->tmp_file["tmp_name"]);
			if($check !== false) {
				$this->uploadOk = 1;
			} else {
				$this->errorStatus = "File is not an image.";
				$this->uploadOk = 0;
			}
		}
	}
	
	function uploadFile () {
		if($this->uploadOk) {
		  if (move_uploaded_file($this->tmp_file["tmp_name"], $this->target_file)) {
				$this->uploadOk = 1;
					$this->errorStatus = "Uploaded";
			} else {
				$this->errorStatus = "Sorry, something went wrong.";
				$this->uploadOk = 0;						
			}
		} else { $errorStatus = "Sorry, someting went wrong."; }
	}
}
?>
