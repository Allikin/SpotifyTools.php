<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<title>Split up your Spotify playlist</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../styles.css" />
	<meta name="description" content="Split up your Spotify playlist" />
	<meta property="og:title" content="Split up your Spotify playlist" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://thoseannoyingdupes.com/splitter/" />
	<meta property="og:image" content="http://thoseannoyingdupes.com/dupes_250x250.jpg" />
	<meta property="og:site_name" content="Split up Spotify playlist" />
	<meta property="fb:admins" content="anita.berge" />
</head>
<body topmargin="20px">
<div id="fb-root"></div>
<?php 
    include('../java.js');
    include('../functions.php');
?>
<div id="top" class="center">
    <h2>Split up Spotify playlist</h2>
	<b>Simply choose your desired Spotify playlist and drag/copy its tracks to the field below</b><br/>
    <br/>
    - generates a number of new lists based on how many tracks you want in each list<br/>
    &nbsp;<br/>&nbsp;<br/>&nbsp;<br/>
    <div class="menu">
		<a href="../">Remove dupes</a>
		&nbsp;|&nbsp;
    	<a href="../compare/">Compare</a>
    	&nbsp;|&nbsp;
     	<a href="../randomize/">Randomize</a>
    	&nbsp;|&nbsp;
    	Split up
        &nbsp;|&nbsp;
    	<a href="/">Home</a> 
	</div>
</div>
<div id="middle" class="center">
    <div id="col1" class="col">
		<form name='playlist' action='' method='POST'>
			<b>Drag/copy your <i>tracks</i> here</b><br/>
				<textarea name='tracks' rows='10' cols='35' wrap='off'></textarea><br/>&nbsp;<br/>
				<input type="checkbox" name="random"> check to randomize above list<br/><br/>
				<input style="padding:2px 0px 1px 5px;" type="number" name='split' value='1000' min='1' max='99999' size='5' maxlength='4' /> tracks per list <br/><br/>
			<input type="submit" name="submit" value="Submit"/>
			<input type='submit' name='reset' value="Reset"/>
    </div>
	<div id="col2" class="col">
			<?php include('split.php'); ?>
    </div>
</div>
<?php include('../footer.php');?>