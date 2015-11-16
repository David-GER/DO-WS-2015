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
		
		function search($name = null, $tag = null) {
			$arr = $this->snippets;
			
			// Search for name
			if(is_string($name)) {
				for($i = 0, $c = count($arr); $i < $c; $i++) {
					if(!preg_match("/.*" . preg_quote($name, "/") . ".*/", $arr[$i]->getName()))
						unset($arr[$i]);
				}
				
				$arr = array_values($arr);
			}
			
			// Search for tag
			if(is_string($tag)) {
				for($i = 0, $c = count($arr); $i < $c; $i++) {
					if($arr[$i]->hasTag($tag) === false) unset($arr[$i]);
				}
				
				$arr = array_values($arr);
			}
			
			return $arr;
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
		
		function updateSnippet($snippet) {
			//$snippet = $this->getSnippet($x);
			
			// Check if snippet exists
			if(!$snippet) return false;
			
			$element = $this->xml->xpath('/codes/snippet[@name="'.$snippet->getName().'"]')[0];
			$element["name"] = $snippet->getName();
			$element->codeA[0]->setCData($snippet->getCode());
			$element->author[0] = $snippet->getAuthor(); 
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
		
		function renameSnippet($snippet, $newName) {
			//$snippet = $this->getSnippet($oldName);
			
			// Check if snippet exists
			if(!$snippet) return false;
			
			$snippetExists = $this->getSnippet($newName);
			
			// Check if renaming is possible
			if($snippetExists) return false;
			
			$this->deleteSnippet($snippet->getName());
			$snippet->setName($newName);
			$this->insertSnippet($snippet);			
		}
		
		function displaySnippets($snippets) {
			for($i = 0, $c = count($snippets); $i < $c; $i++) {
				$snippet = $snippets[$i];
				$author = $snippet->getAuthor();
				$latest_update = $snippet->getLatestUpdate();
				$updates = $snippet->getUpdates();
				$tags = $snippet->getTags();
				
				echo '<div class="snippet_wrapper"><div class="snippet page-header"><h1>' . $snippet->getName() . '</h1><a href="add.php?edit='
					. $snippet->getName() . '"><span>Edit</span></a> <a href="?delete='
					. $snippet->getName() . '"><span>Delete</span></a></div>';
		
				echo '<span class="created">Created ' . $snippet->getCreated() . "</span><br/>\n";
		
				if ($author) {
					echo '<span class="author">By ' . $author . "</span><br/>\n";
				}
		
				if ($latest_update) {
					echo '<span class="updated">Updated ' . $latest_update . "</span><br/>\n";
					/*foreach($updates as $update)
						echo '<span class="updated">Updated ' . $update . "</span><br/>\n";*/
				}
		
				if ($tags) {
					foreach($tags as $tag)
						echo
							'<span class="tag btn btn-default btn-sm" style="margin: 5px 5px 5px 0;">
								<span class="name">' . $tag . '</span>
									<a style="display: inline-block; margin-right: -5px; padding-left: 5px;">
										<i class="delete glyphicon glyphicon-remove" style="vertical-align: text-top;"></i>
									</a> 
							</span>';
				}
		
				echo '<div><pre class="prettyprint lang-c linenums"><code>' .  htmlspecialchars($snippet->getCode()) . '</code></pre></div>';
				echo "</div>\n";
			}
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