<?php
	require("class.snippet.php");

	class XML extends SimpleXMLElement {		
		public function setCData($content) {
			$this[0] = "";
			
			$xml = dom_import_simplexml($this); 
			$dom = $xml->ownerDocument; 
			$xml->appendChild($dom->createCDATASection($content)); 
			
			return $this;
		}
	}

	class SnippetEditor {
		private $file;
		private $xml;
		private $snippets = array();
		
		function __construct($file) {
			if(!is_file($file)) {
				file_put_contents($file, "");
			}
			
			$this->file = $file;
			$this->xml = new XML(file_get_contents($file));
			
			$snippets = $this->xml->xpath('/codes/snippet');
			
			for($i = 0, $c = count($snippets); $i < $c; $i++) {
				
				// Check for duplicate
				if($this->getSnippet($snippets[$i]["name"]) !== null) {
					trigger_error("A name duplicate was detected while parsing the snippet file (name="
						. $snippets[$i]["name"] . "). This may lead to unexpected behaviour");
				}
				
				// Construct snippet
				$this->snippets[$i] = new Snippet(
					trim($snippets[$i]["name"]),
					(string) $snippets[$i]->codeA[0],
					trim((string) $snippets[$i]->created[0]),
					trim((string) $snippets[$i]->author[0]));
				
				// Add "updated" elements
				for($j = 0, $d = count($snippets[$i]->updated); $j < $d; $j++) {
					$this->snippets[$i]->addUpdate(trim((string) $snippets[$i]->updated[$j]));
				}
				
				// Add "tag" elements
				for($j = 0, $d = count($snippets[$i]->tag); $j < $d; $j++) {
					$this->snippets[$i]->addTag(trim((string) $snippets[$i]->tag[$j]));
				}
			}
		}
		
		function getSnippet($x) {
			// If no int: Get index from name
			if(!is_int($x)) $x = $this->getSnippetIndexFromName($x);
			if($x >= 0) return $this->snippets[$x];
		}
		
		function getSnippets() {
			return $this->snippets;
		}
		
		function getSnippetIndexFromName($name) {
			for($i = 0, $c = count($this->snippets); $i < $c; $i++) {
				if($this->snippets[$i]->getName() == $name) return $i;
			}
			return -1;
		}
		
		function insertSnippet($snippet) {
			
			// Skip insert if snippet already exists
			$snippetExists = $this->getSnippet($snippet->getName());
			if($snippetExists) return false;
			
			$this->snippets[] = $snippet;
			
			// Add snippet to xml
			$child = $this->xml->addChild("snippet");
			
			// Set attributes
			$child["name"] = $snippet->getName();
			
			// Append children
			$child->addChild("created", $snippet->getCreated());
			$child->addChild("codeA")->setCData($snippet->getCode());
			
			$author = $snippet->getAuthor();
			$updates = $snippet->getUpdates();
			$tags = $snippet->getTags();
			
			if($author) $child->addChild("author", $author);
			
			for($i = 0, $c = count($updates); $i < $c; $i++) {
				$child->addChild("updated", $updates[$i]);
			}
			
			for($i = 0, $c = count($tags); $i < $c; $i++) {
				$child->addChild("tag", $tags[$i]);
			}
			
			// Save...
			return $this->save();
		}
		
		function updateSnippet($x) {
			$snippet = $this->getSnippet($x);
			
			// Check if snippet exists
			if(!$snippet) return false;
			
			$element = $this->xml->xpath('/codes/snippet[@name="'.$snippet->getName().'"]')[0];
			$element["name"] = $snippet->getName();
			$element->codeA[0]->setCData($snippet->getCode());
			$element->updated[] = Snippet::getDateString(new DateTime());
			
			// Update tags
			$tags = $snippet->getTags();
			$snippetTags = count($tags);
			$elementTags = count($element->tag);
			
			for($i = 0; $i < $elementTags; $i++) {
				unset($element->tag[0]);
			}
			
			for($i = 0; $i < $snippetTags; $i++) {
				$element->addChild("tag", $tags[$i]);
			}
			
			
			// Save...
			return $this->save();
		}
		
		function deleteSnippet($x) {
			$snippet = $this->getSnippet($x);
			
			// Check if snippet exists
			if(!$snippet) return false;
			
			$index = $this->getSnippetIndexFromName($snippet->getName());
			unset($this->snippets[$index]);
			unset($this->xml->xpath('/codes/snippet[@name="'.$snippet->getName().'"]')[0][0]);
			
			// Reindex
			$this->snippets = array_values($this->snippets);
			
			// Save...
			$this->save();
		}
		
		function renameSnippet($oldName, $newName) {
			$snippet = $this->getSnippet($oldName);
			
			// Check if snippet exists
			if(!$snippet) return false;
			
			$snippetExists = $this->getSnippet($newName);
			
			// Check if renaming is possible
			if($snippetExists) return false;
			
			$this->deleteSnippet($oldName);
			$snippet->setName($newName);
			$this->insertSnippet($snippet);			
		}
		
		// Save file
		private function save() {
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($this->xml->asXML());
			
			return $dom->save($this->file) == true;
		}
	}
?>