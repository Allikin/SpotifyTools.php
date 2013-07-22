<?php
if ((isset($_POST['submit'])) && (($_POST['playlist'] > '') || ($_POST['tracks'] > ''))) {
	$correct_format = false;
	$message = '';
	$playlist_empty = false;
	$tracks_empty = false;
	if ($_POST['playlist'] > '') {
		$playlist = trim($_POST['playlist']);
		if (correctPlaylistFormat($playlist)) {
        	$correct_format = true;
			$playlist_uri = getPlaylistURI($playlist); // returns a Spotify playlist URI: spotify:user:<user>:playlist:<uri>
			$uris = playlistGetSpotifyURIs($playlist_uri);
            $uris = $uris['Tracks']['Addresses'];
			if (count($uris) > 999) {
				echo "<div class='warning'>The playlist option  (top) will only accept 1000 tracks from your playlist. If your list has more than 1000 tracks, please use the bottom option.</div><br/>";
			}
		}
	} else { 
		$message = "";
		$playlist_empty = true;
	}
	
	if (($_POST['tracks'] > '') && ($playlist_empty || !$correct_format)) {
		$tracks = $_POST['tracks'];
		$correct_format = true;
		if (correctTracksFormat($tracks)) {
    		$correct_format = true;
			$list = preg_split('/[\n\r]+/',$tracks);
			$list = array_filter($list,'strlen');
			$uris = tracksGetSpotifyURIs($list); // returns an array of Spotif URIs: spotify:track:<URI>
		}
	} else {
		$message = "";
		$tracks_empty = true;
	}
	
	if (($correct_format) && !($playlist_empty && $tracks_empty)) { 
		shuffle($uris); //randomize the URIs
		echo "<h3>Your randomized playlist</h3><br/>
						Select the tracks by clicking inside the textarea and use CTRL-C or right click to copy, or just drag them to an empty playlist.<br/>&nbsp;<br/></p>
		<textarea name='randomlist' rows='10' cols='52' wrap='off' onClick='select(this)' wrap='off' readonly>";
		foreach ($uris as $uri) {
			echo $uri."\r\n";
		}
		echo "</textarea><br/><br/>";
	} else {
		$message = "<p style='font-size:12px;'>&nbsp;<br/>Please apply valid playlist (top field) or tracks (bottom field) by HTTP or Spotify URI.</p>";
	}
	echo $message;
}	
?>

