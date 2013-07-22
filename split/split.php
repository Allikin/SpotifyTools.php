<?php
if ((isset($_POST['submit'])) && ($_POST['tracks'] > '')) {
	$correct_format = true;
	$message = '';
	$split = 1000;
	$count = 0;
	$tracks = $_POST['tracks'];

	if (correctTracksFormat($tracks)) {
		$list = preg_split('/[\n\r]+/',$tracks);
		$list = array_filter($list,'strlen');
		$list = tracksGetSpotifyURIs($list); 		// returns an array of Spotif URIs: spotify:track:<URI>
	} else {
   		$correct_format = false;
		$messages[] = "<p style='font-size:12px;'>&nbsp;<br/>Please apply valid tracks by HTTP or Spotify URI.</p>";
	}
	
	if ($_POST['split'] > 0) {
		$split = $_POST['split'];
	}

	if ($correct_format) {
		if (isset($_POST['random'])) {
			shuffle($list); 						//randomize the list of tracks
		}
		
		// split list
		$number_of_tracks = count($list);
		foreach ($list as $uri) {
			$tracklist[$count] = $uri;
			$count++;
			if (($count == $split) || ($count == $number_of_tracks)){
				$number_of_tracks = $number_of_tracks - $count;
				$lists[] = $tracklist;
				unset($tracklist);
				$count = 0;
			}
		}
		
		echo "<h3>".count($list)." tracks split up into ".count($lists)." lists:</h3><br/>
				Select the tracks by clicking inside the textarea and use CTRL-C or right click to copy, or just drag them to an empty playlist.<br/>&nbsp;<br/></p>";
		foreach ($lists as $i => $newlist) {
			echo "List ".($i+1).": ".count($newlist)." tracks:
				<textarea name='randomlist' rows='10' cols='52' wrap='off' onClick='select(this)' wrap='off' readonly>";
			foreach ($newlist as $uri) {
				echo $uri."\r\n";
			}
			echo "</textarea><br/><br/>";
		}
	} else {
		foreach ($messages as $message) {
			echo $message;
		}
	}
	
}	
?>

