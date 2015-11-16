<?php
require("functions.php");
require("class.snippet_editor.php");

$editor = new SnippetEditor('codes_simple.xml');

if (isset($_GET["add"]) === true) {
	if (checkPOST("name", "code") === true) {
		$editor->insertSnippet(new Snippet(
			$_POST["name"],
			$_POST["code"],
			null,
			checkPOST("author") === true ? $_POST["author"] : null,
			checkPOST("tags") === true ? $_POST["tags"] : null
		));
	}
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
                <li><a href="index.php">Home</a></li>
                <li class="active"><a href="add.php">Add</a></li>
            </ul>

            <form class="navbar-form navbar-left" role="search" method="get" action="index.php">
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
        <h4>Code Php XML</h4>

        <p>Create a code snippets and load into XML file.</p>
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
            <label for="inputTags" class="col-sm-1 control-label">Tags</label>

            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputTags" name="tags" placeholder='Enter tags (separated by ";")'>
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
