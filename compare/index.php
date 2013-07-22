<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<title>Those annoying dupes</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link rel="stylesheet" type="text/css" href="../styles.css">
	<meta name="description" content="Quick and easy way to compare Spotify playlists" />
	<meta property="og:title" content="Compare your Spotify playlists" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://thoseannoyingdupes.com/compare/" />
	<meta property="og:image" content="http://thoseannoyingdupes.com/dupes_250x250.jpg" />
	<meta property="og:site_name" content="Those annoying dupes" />
	<meta property="fb:admins" content="anita.berge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>

<?php 	include('../java.js');
		include('../functions.php');
?>

<div id="top" class="center">
	<h2>Compare Spotify playlists</h2>
	<b>Simply choose your desired Spotify playlists and drag/copy them to the fields below to compare</b><br/>
	- will not lists dupes within a playlist, just the ones that the different playlists have in common.<br/>
	- if you compare more than 2 playlists it will show what playlists that have that track (applies to dupes only).<br/>
	- you can choose to show the unique tracks for the playlists<br/>
	- playlists can be compared with track lists<br/>&nbsp;<br/>
	<div class="menu">
		<a href="../">Remove dupes</a>
		&nbsp;|&nbsp;
		Compare
        &nbsp;|&nbsp;
    	<a href="../randomizer/">Randomize</a>
        &nbsp;|&nbsp;
      	<a href="../split/">Split up</a>
        &nbsp;|&nbsp;
   	<a href="/">Home</a> 
	</div>
</div>
<div id="middle" class="center">
	<div id="col1" class="col">
		<form name='playlist' action='' method='POST'>
			<b>Drag/copy your <i>playlists</i> here - limited</b><br/>
			&nbsp;&nbsp;<font size='-2'>- will only accept up to 1000 tracks<br/>
			&nbsp;&nbsp;- does not handle local tracks<br/>&nbsp;<br/>
			<div id="newlink">  
				<div class="feed">  
					<b>Playlists:</b></font><br/>
					<input type='text' name='playlist[]' size='37' value="">
					<br/>&nbsp;<br/>
				</div>
				<div class="feed">  
					<input type='text' name='playlist[]' size='37' value="">
					<br/>&nbsp;<br/>
				</div>
			</div>
			<div id="addnew">  
				<a href="javascript:add_pl_feed()">Add another playlist</a><br/><br/>
			</div>  
			<div id="newplaylist" style="display:none">  
				<div class="feed"> 
					<input type="text" name="playlist[]" size='37' value=""><br/><br/>
				</div>  
			</div>  
			<input type='radio' name='compare' value ='dupes' checked >
				Show me the duplicates<br/>
			<input type='radio' name='compare' value ='uniques'>
				Show me the unique tracks<br/><br/>
		<input type="submit" name="submit" value="Submit"/>
		<input type='submit' name='reset' value="Reset"/>
		<br/><br/><b>and/or</b><br/>&nbsp;<br/>&nbsp;<br/>
			<b>...drag/copy your <i>tracks</i> here - no limit!</b><br/>
			&nbsp;&nbsp;<font size='-2'>- as many as you want, local ones too<br/>&nbsp;<br/>
			<b>Track list(s):</b></font><br/>
			<div id="newlink2">  
				<div class="feed2">  
					<textarea name='tracks[]' rows='6' cols='35' wrap='off'></textarea>
					<br/>&nbsp;<br/>
				</div>
			</div>
			<div id="addnew2">  
				<a href="javascript:add_tr_feed()">Add another track list</a><br/><br/> 
			</div>  
			<div id="newtracklist" style="display:none">  
				<div class="feed2"> 
					<textarea name='tracks[]' rows='6' cols='35' wrap='off'></textarea>
					<br/>&nbsp;<br/>
				</div>  
			</div>  
			<input type='radio' name='compare' value ='dupes'>
				Show me the duplicates<br/>
			<input type='radio' name='compare' value ='uniques'>
				Show me the unique tracks<br/><br/>
		<input type="submit" name="submit" value="Submit"/>
		<input type='submit' name='reset' value="Reset"/>
	</div>
	<div id="col2" class="col">
		<?php include('compare.php'); ?>
	</div>
</div>
<?php include('../footer.php');?>