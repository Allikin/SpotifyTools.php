<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<title>Randomize your Spotify playlist</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../styles.css" />
	<meta name="description" content="Randomize your Spotify playlist" />
	<meta property="og:title" content="Randomize your Spotify playlist" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://thoseannoyingdupes.com/randomizer/" />
	<meta property="og:image" content="http://thoseannoyingdupes.com/dupes_250x250.jpg" />
	<meta property="og:site_name" content="Playlist randomizer" />
	<meta property="fb:admins" content="anita.berge" />
</head>
<body topmargin="20px">
<div id="fb-root"></div>
<?php 
    include('../java.js');
    include('../functions.php');
?>
<div id="top" class="center">
    <h2>Randomize Spotify playlist</h2>
	<b>Simply choose your desired Spotify playlist and drag/copy it to one of the fields below</b><br/>
    <br/>
    - generates a randomized list of tracks<br/>
    &nbsp;<br/>&nbsp;<br/>&nbsp;<br/>
    <div class="menu">
		<a href="../">Remove dupes</a>
		&nbsp;|&nbsp;
    	<a href="../compare/">Compare</a>
    	&nbsp;|&nbsp;
     	Randomize
    	&nbsp;|&nbsp;
     	<a href="../split/">Split up</a>
        &nbsp;|&nbsp;
    	<a href="/">Home</a> 
	</div>
</div>
<div id="middle" class="center">
    <div id="col1" class="col">
		<form name='playlist' action='' method='POST'>
			<b>Drag/copy your <i>playlist</i> here - limited</b><br/>
			&nbsp;&nbsp;<font size='-2'>- will only accept up to 1000 tracks<br/>
			&nbsp;&nbsp;- does not handle local tracks</font><br/>&nbsp;<br/>
				<input type='text' name='playlist' size='37' value=""><br/>&nbsp;<br/>&nbsp;<br/>
			<b>or</b><br/>&nbsp;<br/>&nbsp;<br/>
			<b>...drag/copy your <i>tracks</i> here - no limit!</b><br/>
			&nbsp;&nbsp;<font size='-2'>- as many as you want, local ones too</font><br/>&nbsp;<br/>
				<textarea name='tracks' rows='10' cols='35' wrap='off'></textarea><br/>&nbsp;<br/>
			<input type="submit" name="submit" value="Submit"/>
			<input type='submit' name='reset' value="Reset"/>
    </div>
	<div id="col2" class="col">
			<?php include('randomize.php'); ?>
    </div>
</div>
<?php include('../footer.php');?>