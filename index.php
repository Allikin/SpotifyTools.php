<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<title>Remove duplicates in Spotify</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link rel="stylesheet" type="text/css" href="styles.css" />
	<meta name="description" content="Quickest and easiest way to remove duplicates from your Spotify playlist; just drag it to the playlist field. Other tools this site offers are comparing, randomizing and splitting up playlists! />
	<meta name="version" content="6.0" />
	<meta property="og:title" content="Remove duplicates in Spotify playlists" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://thoseannoyingdupes.com/" />
	<meta property="og:image" content="http://thoseannoyingdupes.com/dupes_250x250.jpg" />
	<meta property="og:site_name" content="Those annoying dupes" />
	<meta property="fb:admins" content="anita.berge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>

<?php 
include('java.js');
include('functions.php');
?>

<div id="top" class="center">
	<h2>Remove duplicates in Spotify playlists</h2>
	<b>Simply choose your desired Spotify playlist and drag/copy it to one of the fields below to get: </b><br/>
	- a new list of tracks without duplicates <br/>
		- a list of duplicates by track URI <br/>
	- a list of duplicates by name and artist<br/>&nbsp;<br/>
	*<i>The playlist option will find possible duplicates that do not share the same URI</i>
	<div class="menu">
		Remove dupes 
		&nbsp;|&nbsp;
		<a href="compare/">Compare</a>
     	&nbsp;|&nbsp;
		<a href="randomizer/">Randomize</a>
   	    &nbsp;|&nbsp;
      	<a href="split/">Split up</a>
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
				<input type='text' name='playlist' size='37' value=""><br/>&nbsp;<br/>
			<input type="submit" name="submit" value="Submit"/>
			<input type='submit' name='reset' value="Reset"/><br/>&nbsp;<br/>
			<b>or<br/>&nbsp;<br/>
			...drag/copy your <i>tracks</i> here - no limit!</b><br/>
			&nbsp;&nbsp;<font size='-2'>- as many as you want, local ones too</font><br/>&nbsp;<br/>
				<textarea name='tracks' rows='10' cols='35' wrap='off'></textarea><br/>&nbsp;<br/>
			<input type="submit" name="submit" value="Submit"/>
			<input type='submit' name='reset' value="Reset"/>
	</div>
	<div id="col2" class="col">
		<?php 
			if (!isset($_POST['submit'])) {
				echo '<h3>Remove, compare, randomize, split up, get tidy!</h3>
					<p>
						Duplicates in Spotify playlists can be annoying to get rid of, as Spotify doesn\'t 
						present an easy way to do it. Sure, Spotify "removes" duplicates if you put your 
                        playlist(s) in a playlist folder, but this solution isn\'t ideal to say the least.
						<br/><br/>
						Although there are some nice apps online that will help you create a dupe free
						playlist, I know quite a few people that want to keep the tracks\' add date, thus
						they are forced to do the tedious job of going through their lists manually. 
						<br/><br/>
						I decided it would be a fun project to try to make this process less painful, and
                        this app is the result.
						<br/><br/>
						This application will help you remove Spotify duplicates quickly and easily. Like
						other apps it will make an entirely new playlist for you to copy, but it will also
						list the dupes alphabetically by title and artist, making it easy to find and 
						remove the tracks without having to mess with the added date. Also, a list of the
						Spotify duplicate URIs will be generated for you.
						<br/><br/>
						Notice that on the top there is a link for comparing Spotify playlists. This
						feature will list duplicates between playlists or unique tracks in them, according
						to your choice of output. I also added features for randomizing and 
						splitting up playlists.
						<br/><br/>
						If you like this app, please share it with your friends, and make sure you give it
						a thumbs up! :-)
					</p>
					<br/><br/>
					<p style="font-size:10px;font-style:italic;">
						Thanks to <a target="_blank" href="http://www.hardasarock.org">www.hardasarock.org</a> for
						helping out with this website.
					</p>';
					
			} else {
				include('dupes.php'); 
			}?>
	</div>
</div>
<?php include('footer.php');?>