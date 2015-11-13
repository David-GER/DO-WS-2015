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
            <a class="navbar-brand" href="#">Code Php Xml</a>
        </div>

        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>

            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
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

    <form class="form-horizontal" method="post" action="?add">
        <input type="hidden" name="type">
        <div class="form-group">
            <label for="inputName" class="col-sm-1 control-label">Name</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputName" name="name"
                       placeholder="Enter a name for the code.">
            </div>
        </div>
        <div class="form-group">
            <label for="inputAuthor" class="col-sm-1 control-label">Author</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputAuthor" name="author" placeholder="Enter your name">
            </div>
        </div>
        <div class="form-group">
            <label for="editor" class="col-sm-1 control-label">Code</label>

            <div class="col-sm-10">
                <input type="hidden" name="code">

                <div id="editor" style="height: 250px;">//Code here</div>

            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
                <button type="submit" class="btn btn-default">Save</button>
            </div>
        </div>
    </form>


    <script src="js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.getSession().setMode("ace/mode/javascript");
    </script>

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

	require("functions.php");
	require("class.snippet_editor.php");

	$editor = new SnippetEditor('codes_simple.xml');
	
	// Insert, Edit, Delete
	if (isset($_GET["add"]) === true) {
        if (checkPOST("name", "code") === true) {
			$editor->insertSnippet(new Snippet(
				$_POST["name"],
				$_POST["code"],
				null,
				checkPOST("author") === true ? $_POST["author"] : null
			));
		}
	} elseif (checkGET("edit") === true) {
		$snippet = $editor->getSnippet($_GET["edit"]);
		
		if($snippet) {
			if(checkPOST("code") === true) $snippet->setCode($_POST["code"]);
			$editor->updateSnippet($_GET["edit"]);
			
			if(checkPOST("name") === true) $editor->renameSnippet($_GET["edit"], $_POST["name"]);
		}
	} elseif (checkGET("delete") === true) $editor->deleteSnippet($_GET["delete"]);
	
	// Snippet Output
	$snippets = $editor->getSnippets();
	
	for($i = 0, $c = count($snippets); $i < $c; $i++) {
		$snippet = $snippets[$i];
		$author = $snippet->getAuthor();
		$updates = $snippet->getUpdates();
		$tags = $snippet->getTags();
		
		echo '<div class="page-header"><h1>' . $snippet->getName() . '</h1><a href="?edit='
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
				echo '<span class="tag">Updated ' . $tag . "</span><br/>\n";
		}

		echo '<div><pre class="prettyprint lang-c linenums"><code>' .  htmlspecialchars($snippet->getCode()) . '</code></pre></div>';
		echo "<br/>\n";
	}
	

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

</script>

</body>
</html>
