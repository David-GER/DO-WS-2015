<?php	
	class Snippet {
		// Mandatory
		private $name;
		private $code;
		private $created;
		
		// Optional;
		private $author;
		
		// Any number
		private $updates = array();
		private $tags = array();
		
		// Constructor
		public function __construct($name, $code, $created = null,  $author = null, $tagsString = null) {
			$this->name = $name;
			$this->code = $code;
			$this->author = $author;

			if ($tagsString != null) {
				$this->tags = $this->parseTagString($tagsString);
			}

			if(!$created) {
				$this->created = new DateTime();
			} elseif(is_long($created)) {
				$this->created = (new DateTime())->setTimestamp($created);
			} else $this->created = new DateTime($created);
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function setName($name) {
			$this->name = $name;
		}
		
		public function getCode() {
			return $this->code;
		}
		
		public function setCode($code) {
			$this->code = $code;
		}
		
		public function getCreated() {
			return self::getDateString($this->created);
		}
		
		public function getAuthor() {
			return $this->author;
		}
		
		public function addTag($name) {
			if(!in_array($name, $this->tags)) $this->tags[] = $name;
		}
		
		public function removeTag($name) {
			if(($i = $this->hasTag($name)) === false) return false;
			
			unset($this->tags[$i]);
			$this->tags = array_values($this->tags);
			return true;
		}
		
		public function getTag($i) {
			return $this->tags[$i];
		}
		
		public function getTags() {
			return $this->tags;
		}

		private function parseTagString($tags) {
			return explode(";", $tags);
		}
		
		public function hasTag($name) {
			for($i = 0, $c = count($this->tags); $i < $c; $i++) {
				if($this->tags[$i] == $name) return $i;
			}
			return false;
		}
		
		public function addUpdate($time) {
			$this->updates[] = new DateTime($time);
		}
		
		public function getUpdate($i) {
			return self::getDateString($this->updates[$i]);
		}
		
		public function getUpdates() {
			$updates = array();
			for($i = 0, $c = count($this->updates); $i < $c; $i++)
				$updates[] = $this->getUpdate($i);
			return $updates;
		}
		
		public static function getDateString($date) {
			return $date->format("D M d H:i:s T Y");
		}
	}
?>