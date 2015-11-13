<?php
	require("functions.php");
	require("class.snippet_editor.php");
	
	function finish($status, $msg = "") {
		$obj = new stdClass();
		$obj->status = $status;
		$obj->msg = $msg;
		die(json_encode($obj));
	}
	
	/** DEBUGGING **/
		if($_GET) foreach($_GET as $k => $v) {
			$_POST[$k] = $v;
		}
	/** DEBUGGING **/
	
	$file = 'codes_simple.xml';
	
	if(checkPOST("method") === true) {
		switch($_POST["method"]) {
			case "deleteTag":
				$check = checkPOST("name", "snippet");
				if($check !== true) finish("ERROR", "Missing parameters for tag deletion: " . $check);

				$editor = new SnippetEditor($file);
				
				$snippet = $editor->getSnippet($_POST["snippet"]);
				
				if($snippet) {
					$success = $snippet->removeTag($_POST["name"]);
					if(!$success) finish("ERROR", "Tag '" . $_POST["name"] . "' not found in snippet '" . $_POST["snippet"] . "'");
					if($editor->updateSnippet($_POST["snippet"])) finish("OK");
					
					finish("ERROR", "Could not save file '" . $file . "'");
				}
				
				finish("ERROR", "Snippet '" . $_POST["snippet"] . " not found in file '" . $file . "'");
			default:
				finish("ERROR", "Unkown method '" . $_POST["method"] . "'");
		}
	}
	
	finish("ERROR", "Method parameter is missing.");
?>