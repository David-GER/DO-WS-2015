<?php
require("functions.php");
require("class.snippet_editor.php");
$editor = new SnippetEditor('codes_simple.xml');
	
if (checkGET("delete") === true) {
	$editor->deleteSnippet($_GET["delete"]);
	header("Location:index.php");
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Theme Template for Bootstrap</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link href="css/theme.css" rel="stylesheet">

    <!--  This will enable numbers for every code line.  -->
    <style>
        .prettyprint ol.linenums > li {
            list-style-type: decimal;
        }
    </style>


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body role="document">

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Code Php Xml</a>
        </div>

        <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home</a></li>
                <li><a href="add.php">Add</a></li>
            </ul>

            <form class="navbar-form navbar-left" role="search" method="get">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" placeholder="Search">
                </div>
                <button class="btn btn-default" id="search_button">Submit</button>
            </form>
        </div>

        <!--/.nav-collapse -->
    </div>
</nav>

<div class="container theme-showcase" role="main">

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
        <h1>Code Php XML</h1>

        <p>Browse through, create, update or delete code snippets loaded from an Xml file.</p>
    </div>

 

    <?php

    /**
     * TODO
     *
     * - Search function (widget in navbar)
     * - Ordering by functionality (created, name, etc)
     * - Edit, Delete Buttons
     * - Way to edit existing code in form
     *
     * - Make more pretty?
     *
     * Maybe:
     * - Is it possible to clean up code snippets (remove empty lines, auto format)?
     * - pagination
     * - Tags
     * - Be able to upload whole file to change?
     */

	
	
	// Edit, Delete
	/*
	if (checkGET("edit") === true) {
		$snippet = $editor->getSnippet($_GET["edit"]);
		
		if($snippet) {
			if(checkPOST("code") === true) $snippet->setCode($_POST["code"]);
			$editor->updateSnippet($_GET["edit"]);
			
			if(checkPOST("name") === true) $editor->renameSnippet($_GET["edit"], $_POST["name"]);
		}
	} elseif (checkGET("delete") === true) {
		 $editor->deleteSnippet($_GET["delete"]);
		 $editor->displaySnippets($editor->getSnippets());
	} else*/if (isset($_GET["search"])) {
		$searchword = $_GET["search"];
		$searchResults = $editor->search($searchword);
		$searchResultCount = sizeof($searchResults);
		
		if ($searchResultCount > 0) {
			echo 'Your search for "'.$searchword.'" returned '.$searchResultCount.' results.';
			$editor->displaySnippets($searchResults);
		} else {
			echo 'Your search for "'.$searchword.'" returned '.$searchResultCount.' results.';
		}
	} else {
		$editor->displaySnippets($editor->getSnippets());
	}
	
	// Snippet Output
	
	
	/*
	for($i = 0, $c = count($snippets); $i < $c; $i++) {
		$snippet = $snippets[$i];
		$author = $snippet->getAuthor();
		$updates = $snippet->getUpdates();
		$tags = $snippet->getTags();
		
		echo '<div class="snippet_wrapper"><div class="snippet page-header"><h1>' . $snippet->getName() . '</h1><a href="?edit='
			. $snippet->getName() . '"><span>Edit</span></a> <a href="?delete='
			. $snippet->getName() . '"><span>Delete</span></a></div>';

		echo '<span class="created">Created ' . $snippet->getCreated() . "</span><br/>\n";

		if ($author) {
			echo '<span class="author">By ' . $author . "</span><br/>\n";
		}

		if ($updates) {
			foreach($updates as $update)
				echo '<span class="updated">Updated ' . $update . "</span><br/>\n";
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
	}*/
	

	/*
    if (file_exists($fileName)) {
        $xml = simplexml_load_file($fileName);

        if (isset($_GET["add"])) {
            if (isset($_POST["name"]) && isset($_POST["code"]) && isset($_POST["author"])) {

                $newSnippet = $xml->addChild('snippet');
                $newSnippet->addAttribute('name', $_POST["name"]);
                $newSnippet->addChild('author', $_POST["author"]);
                $newSnippet->addChild('created', time());

                /*
                 * Source: http://stackoverflow.com/a/20511976/2658408
                 *
                 * Didn't create a new class because for some reason it is not possible
                 * to add an already existing node object (of my extended new class)
                 * as a child to a parent node.
                 */
                /*$codeA = $newSnippet->addChild('codeA');

                if ($codeA !== NULL) {
                    $node = dom_import_simplexml($codeA);
                    $no = $node->ownerDocument;
                    $node->appendChild($no->createCDATASection($_POST["code"]));
                }

                //saves the new xml construct back to the file after adding a new element.
                $xml->asXML($fileName);
            }
        } else if (isset($_GET["delete"])) {
			
			unset($xml->xpath('/codes/snippet[@name="'.$_GET["delete"].'"]')[0][0]);
			dom_import_simplexml($xml)->ownerDocument->save($fileName);
			
        }

        foreach($xml->children() as $snippet ) {
            $tagName = $snippet->getName();

            if ($tagName != 'snippet') continue;

            echo '<div class="page-header"><h1>' . $snippet['name'] . '</h1><a href="?edit=' . $snippet['name'] . '"><span>Edit</span></a> <a href="?delete=' . $snippet['name'] . '"><span>Delete</span></a></div>';


            if (!empty($snippet->created)) {
                echo '<span class="author">Created ' . $snippet->created . "</span><br/>\n";
            }

            if (!empty($snippet->author)) {
                echo '<span class="created">By ' . $snippet->author . "</span><br/>\n";
            }

            if (!empty($snippet->updated)) {
                echo '<span class="updated">Updated ' . $snippet->updated . "</span><br/>\n";
            }

            echo '<div><pre class="prettyprint lang-c linenums"><code>' .  htmlspecialchars($snippet->codeA) . '</code></pre></div>';
            echo "<br/>\n";
        }

    } else {
        exit('Could not read codex.xml!');
    }*/

    ?>

</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.2/ace.js"></script>-->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="js/bootstrap.min.js"></script>


<script src="js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
<!--<script src="js/ace/ext-chromevox.js" type="text/javascript" charset="utf-8"></script>-->

<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/chrome");
    editor.getSession().setMode("ace/mode/c_cpp");
    editor.setShowPrintMargin(false)

    /**
     * Source: http://stackoverflow.com/a/11013863/2658408
     *
     * This puts the editor's input in a hidden input field before
     * the editor.
     */

    var $editor = $('#editor');
    $editor.closest('form').submit(function () {
        var code = editor.getValue();
        $editor.prev('input[type=hidden]').val(code);
    });
	
	function resetTagFilter() {
		$(".tag").removeClass("btn-info");
		$(".snippet_wrapper").show();
	}
	
	$(".tag").click(function(){
		var tagName = $(this).children(".name").text();
		
		if(!$(this).hasClass("btn-info")) {
			resetTagFilter();
		
			console.log("Search for tag '" + tagName + "'");
			
			$.ajax({
				method: "POST",
				url: "ajax.php",
				data: {method: "filter", tag: tagName},
				success: function(data) {
					try {
						var json = JSON.parse(data);
						if(json.status !== "OK") {
							console.error("Ajax error: " + json.msg);
							return;
						}
						
						var names = JSON.parse(json.msg);
						
						$(".snippet_wrapper").each(function(){
							if(names.indexOf($(this).find("h1").text()) === -1)
								$(this).hide();
							
							$(".tag:contains(" + tagName + ")").addClass("btn-info");
						});
					} catch(e) {
						console.error("JSON Parser exception: " + e + ", Output was: " + data);
					}
				}
			});
		} else resetTagFilter();
	});
	
	/**
	 * delete tags
	 */
	$(".tag .delete").click(function(e){
		e.stopPropagation();
		
		var tag = $(this).closest(".tag");
		var tagName = tag.children(".name").text();
		var snippetName = tag.prevAll(".snippet:first").children("h1").text();
		
		console.log("Remove tag '" + tagName + "' from snippet '" + snippetName + "'");
		
		$.ajax({
			method: "POST",
			url: "ajax.php",
			data: {method: "deleteTag", name: tagName, snippet: snippetName},
			success: function(data) {
				try {
					var json = JSON.parse(data);
					if(json.status !== "OK") console.error("Ajax error: " + json.msg);
					else tag.remove();
				} catch(e) {
					console.error("JSON Parser exception: " + e + ", Output was: " + data);
				}
			}
		});
	});	
</script>

</body>
</html>
