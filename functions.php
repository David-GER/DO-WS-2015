<?php
	function checkGET(){
		return checkArr($_GET, func_get_args());
	}
	
	function checkPOST(){
		return checkArr($_POST, func_get_args());
	}
	
	function checkArr($arr, $args){
		if(count($args) === 0) return true;
		
		$unset = array();
		foreach($args as $arg){
			if(!is_string($arg)) continue;
			if(!isset($arr[$arg]) || $arr[$arg] == "")
				array_push($unset, $arg);
		}
		
		if(empty($unset)) return true;
		else return implode(", ", $unset);
	}
	
	function dump($v){
		echo "<pre>";
		var_dump($v);
		echo "</pre>";
	}
?>