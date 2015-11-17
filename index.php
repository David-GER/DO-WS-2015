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

    <title>CodeSnippetEditor</title>

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
            <a class="navbar-brand" href="index.php">CodeSnippetEditor</a>
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
        <h1 data-toggle="modal" data-target="#myModal">CodeSnippetEditor</h1>

        <p>Browse through, create, update or delete code snippets loaded from an XML file.</p>
    </div>

 

    <?php

    /**
     * TODO
     *
     * - Make more pretty?
     *
     * Maybe:
     * - Is it possible to clean up code snippets (remove empty lines, auto format)?
     * - pagination
	 * - Ordering by functionality (created, name, etc)
     * - Be able to upload whole file to change?
     */

	
	//Search
	if (isset($_GET["search"])) {
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
	?>

</div>

<div id="confirm_delete" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm deletion</h4>
      </div>
      <div class="modal-body">
        <p>Do you really want to delete this entry?</p>
      </div>
      <div class="modal-footer">
        <a class="btn btn-default" data-dismiss="modal">Cancel</a>
        <a class="btn btn-danger btn-ok">Delete</a>
      </div>
    </div>
  </div>
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


<!--<script src="js/ace/ext-chromevox.js" type="text/javascript" charset="utf-8"></script>-->

<script>

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
	
	/**
	 * confirm entry deletion
	 */
	$('#confirm_delete').on('show.bs.modal', function(e) {
		console.log("HERE");
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
	});
</script>

</body>
</html>
