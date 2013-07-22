<?php
if (($_POST['playlist'] > '') || ($_POST['tracks'] > '')) { // checks that at least one field is filled out
	$correct_format = false;
	$starred = false;
	$playlist_empty = true;
	$message = '';
	$playlist = Array(
				'Name' => "",
				'Tracks' => Array(
					'Names' => Array(""),
					'Addresses' => Array(""),
					'Duration' => Array(0)
					)
				);

	if ($_POST['playlist'] > '') {
		$pl_input = trim($_POST['playlist']);
		if (correctPlaylistFormat($pl_input)) {
			$playlist_empty = false;		
			$correct_format = true;
			$playlist_uri = getPlaylistURI($pl_input); 			// returns a Spotify playlist URI: spotify:user:<user>:playlist:<uri>
			$playlist = playlistGetSpotifyURIs($playlist_uri);	// get the list of names, addresses (URIs) and duration
			if (count($playlist['Tracks']['Addresses']) > 1000) {
				echo '<div class="warning">The playlist option  (top) will only accept 1000 tracks from your playlist. If your list has more than 1000 tracks, please use the bottom option.<br/><br/>
						Tip: Alternativly use the <a href="split/">split up</a> feature to split up your playlist into lists of maximum 1000 tracks (make sure you sort your playlist alphabetically first).</div><br/>';
			}
		}
	} elseif ($_POST['tracks'] > '') {
		$tracks = $_POST['tracks'];
		if (correctTracksFormat($tracks)) {
			$correct_format = true;
			$list = preg_split('/[\n\r]+/',$tracks); 	// get rid of
			$list = array_filter($list,'strlen');		// garbage
			$playlist['Tracks']['Addresses'] = tracksGetSpotifyURIs($list); // returns an array of Spotif URIs: spotify:track:<URI>
		}
	}
	
	if ($correct_format) {
		if ($tracks = findDupes($playlist['Tracks'])) {			// returns false if there are no dupes, else returns the dupes
			$new_playlist = removeDupes($playlist['Tracks']);	// create a new playlist without dupes 
			if ($playlist['Name'] > "") {
				echo "<h3>Results for '".$playlist['Name']."'</h3><br/>";
			} else {
				echo "<h3>Results</h3><br/>";
			}
			$count_removed = count($playlist['Tracks']['Addresses'])- count($new_playlist['Addresses']); // number of tracks removed from new playlist
			if ($count_removed > 0) {
				echo "<b>New playlist (".count($new_playlist['Addresses'])." tracks - ".$count_removed." duplicates removed):</b></p>
					<textarea name='newlist' rows='10' cols='52' onClick='select(this)' wrap='off' readonly>";
			// List the new playlist..
				foreach ($new_playlist['Addresses'] as $uri) {
					echo $uri."\r\n";
				}
				echo "</textarea><br/>
					<p>
						<i>Select the tracks by clicking inside the textarea and use CTRL-C or right click to copy, or just drag them to an empty playlist.  - Will reset the tracks' added date.</i><br/>&nbsp;<br/>
					</p>";
		// ..unless no dupes were removed
			} //if ($count_removed > 0) 
			else {
				echo "<br/>No duplicates removed; no new playlist generated.<br/><br/>";
			}
			
    		echo "<p><br/><b>Duplicates as tracks (".count($tracks['Addresses'])." tracks):</b></p>
				<textarea name='newlist' rows='6' cols='52' onClick='select(this)' wrap='off' readonly>";

			// List the dupes by track USI
            foreach ($tracks['Addresses'] as $uri) {
				echo $uri."\r\n";
			}
			echo "</textarea><br/>
				<p>
					<i>Select the tracks by clicking inside the textarea and use CTRL-C or right click to copy, or just drag them to an empty playlist.</i>
					<br/>&nbsp;<br/>
				</p>";
                    
		// embed or ws.spotify.com down? If so, let the user know
			if (isset($tracks['Names']) && preg_match('/<i>/',$tracks['Names'][0])) {
				$message = $tracks['Names'][0].'<br/><br/>'; 
			} else {
				if ($playlist_empty) {
				    $tracks = tracksGetTrackNames($tracks);			// gets the track names by looking up the URIs against the API
		    	}
				$rows = count($tracks['Addresses'])+1;
				echo "<p>&nbsp;<br/>
						<b>Duplicates by track name and artist (".count($tracks['Addresses'])." tracks):</b><br/><br/>
						<textarea name='newlist' rows='".$rows."' cols='52' wrap='off' readonly>";
/*
				if ($testSocket = @fsockopen("ws.spotify.com", 80, $errno, $errstr, 2)) {
					foreach ($tracks['Addresses'] as $i => $dupe) {
						if(preg_match('/:local:/', $dupe)) {
							$tracks['Names'][$i] = getLocalTrackName($dupe);
						} else {
							$url = trim("http://ws.spotify.com/lookup/1/?uri=".$dupe);
							$content = file_get_contents($url);
							if ($content) {
								$doc = new SimpleXmlElement($content); 
								echo $doc->name." by ".$doc->artist->name.'&#13;';
							} else {
								echo "Error looking up in the Spotify API";
							}
						}
					}
					fclose($testSocket); 
				} else { 
					$tracks['Names'][0] = '&nbsp;<br/><i>Unfortunately it\'s not possible to lookup tracknames at this time (Spotify site down), please try again later.</i>';
					$tracks['Addresses'][0] = '999';
				}
*/

			// List the dupes by name and artist
				if (!isset($tracks['Names'])) {
					echo 	"Something happened looking up the track names. \r\n
							Try refreshing the page.";
				} else {
					foreach ($tracks['Names'] as $name) {
						if (preg_match('/\* /',$name)) { 		// check if the track is starred
							$starredTracks[] = $name;
							$starred = true;					// ah, need to display special message later
						} else{
							echo $name."\n";
						}
					}
					if ($starred) {
						echo "\nPossible dupes (not removed from the new playlist):\n";
						foreach ($starredTracks as $name) {
							echo $name."\n";
						}
					}
				}

				echo "</textarea><br/><br/>".$message; 	// display error message (I should make this an array *notes*)
				echo "<br/><i>Sort your playlist alphabetically or use the filter function (ctrl-f) to find the tracks easily.</i><br/><br/>
					</p>";
			}
		} else {
			$message = "<p>&nbsp;<br/>No duplicates found</p>";
		}
	} else {
		$message = "<p>&nbsp;<br/>Please apply valid playlist (top field) or tracks (bottom field) by HTTP or Spotify URI.</p>";
	}
	echo $message;
}	
?>

