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
		public function __construct($name, $code, $created = null,  $author = null) {
			$this->name = $name;
			$this->code = $code;
			$this->author = $author;
			
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
			$this->tags[] = $name;
		}
		
		public function getTag($i) {
			return $this->tags[$i];
		}
		
		public function getTags() {
			return $this->tags;
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