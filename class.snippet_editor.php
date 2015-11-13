<?php
	class SnippetEditor {
		private $xml;
		private $snippets = array();
		
		function __construct($file) {
			$this->xml = simplexml_load_file($file);
			
			$snippets = $xml->xpath('/codes/snippet');
			
			for($i = 0, $c = count($snippets); $i < $c; $i++) {
				$this->snippets[$nippets[$i]["name"]] = $snippets[$i];
			}
		}
	}
	
	class Snippet {
		// mandatory
		private $name;
		private $code;
		private $created;
		
		// optional;
		private $author;
		
		// any number
		private $updates = array();
		private $tags = array();
		
		// Constructor
		public function __construct($name, $code, $created = null,  $author = null) {
			$this->name = $name;
			$this->code = $code;
			
			$this->created = $created ? $created : time();
			$this->author = $author;
		}
		
		public function addUpdate($time) {
			this->updates[] = $time;
		}
		
		public function addTag($name) {
			this->tags[] = $name;
		}
	}
?>