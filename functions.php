<?php

function correctPlaylistFormat($data) {
	$correct = false;
	$patterns = array(	'#http://open.spotify.com/user/#',
						'#spotify:user:#');
	foreach ($patterns as $pattern) {
	if (preg_match($pattern,$data)) {
			$correct = true;
		} 
	}
	return $correct;
}

function correctTracksFormat($data) {
	$correct = false;
	$patterns = array(	'#http://open.spotify.com/track/#',
						'#http://open.spotify.com/local/#',
						'#spotify:track:#',
						'#spotify:local:#');	
						
// Only checking if the pattern occures somewhere in the input, so pretty bad quality check. :P
	foreach ($patterns as $pattern) {
		if (preg_match($pattern,$data)) {
			$correct = true;
		} 
	}
	return $correct;
}

function getPlaylistURI($playlist) {
	$playlist = preg_replace('#http://open.spotify.com/#','spotify:',$playlist);
	$playlist_uri = preg_replace('#/#',':',$playlist);
	return($playlist_uri);
}

function tracksGetSpotifyURIs($tracks) {
	$pattern = array(	'#http://open.spotify.com/track/#',
						'#http://open.spotify.com/local/#');
	$substitute = array('spotify:track:',
						'spotify:local:');
	foreach ($tracks as $i => $track) {
		$tracks[$i] = preg_replace($pattern,$substitute,trim($track));
	}
	return($tracks);
}

function playlistGetSpotifyURIs($playlist_uri) {
	$playlist = Array(
			'Name' 		=> '',
			'Tracks' 	=> Array(
				'Names' 	=> Array(),
				'Addresses' => Array(),
				'Duration' 	=> Array()
				)
			);
	if ($testSocket = @fsockopen("embed.spotify.com", 80, $errno, $errstr, 2)) {
		$url = 'https://embed.spotify.com/?uri='.$playlist_uri;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$page = curl_exec($ch);
		curl_close($ch);
		fclose($testSocket); 
	// The regex below genereates a multidimentional array of the info I need. Im sure the regex can be much more elegant, but it does the job.
		preg_match_all('/data-track=\"(.*?)\"(.*?)-ms="(.*?)"(.*?)track-title(.*?)\.(.*?)\"(.*?)artist (.*?)rel="(.*?)\"/s',$page,$tracks);

	// I need the indexes 1 (data-track=URI), 3 (ms=duration), 6 (track-title) and 9 (artist)
		foreach ($tracks[1] as $i => $track) {	
			$playlist['Tracks']['Addresses'][$i] = 'spotify:track:'.$track;
			$playlist['Tracks']['Duration'][$i] = $tracks[3][$i];
			$playlist['Tracks']['Names'][$i] = $tracks[6][$i]." by ".$tracks[9][$i]; // will prolly rewrite to keep these seperate
		}
		preg_match('/title>(.*?) by/',$page,$name);
		$playlist['Name'] = $name[1];

// Oops, embed.spotify.com is down
	} else {
		$playlist['Tracks']['Names'][0] = '&nbsp;<br/><i>Unfortunately the playlist option is not possible at the moment (Spotify site down), please use the bottom option or try again later.</i>';
		$playlist['Tracks']['Addresses'][0] = '999';
		$playlist['Tracks']['Duration'][0] = 9999;
	}
	return($playlist);
}

function removeDupes($tracks) {
	foreach ($tracks['Addresses'] as $i => $uri) {
		$match_index = array_search($uri,$tracks['Addresses']);
		
	// If the current track address matches with other addresses than the current it has to be a dupe, unset it
		if ($match_index <> $i) {
			unset($tracks['Addresses'][$match_index]);
			if (isset($tracks['Names'][$match_index])) {	// will not be submitted with tracklist, so need to check if it's set
				unset($tracks['Names'][$match_index]); 
			}
			if (isset($tracks['Duration'][$match_index])) {	// will not be submitted with tracklist, so need to check if it's set
				unset($tracks['Duration'][$match_index]);
			}	
		}
	}
	return($tracks);
}

function findDupes($tracks) {
	$j = 0;			// index counter for our dupes array
	$last_uri = ""; // for URI comparison

// If duration is set, we know we're working with a playlist
	if (isset($tracks['Duration']) && ($tracks['Duration'][0] > 0)) {
		$last_dur = 0;		// for comparison when we're checking 
		$last_artist = "";	// for dupes that don't share URI

	// The list is sorted by name, then duration...		
		array_multisort($tracks['Names'], SORT_ASC, SORT_STRING,
						$tracks['Duration'], SORT_ASC, SORT_NUMERIC,
						$tracks['Addresses'], SORT_ASC, SORT_STRING);
		foreach ($tracks['Duration'] as $i => $dur) {
			$trackname_and_artist = explode(' by ',$tracks['Names'][$i]); 	// seperate track name and artist (as they're not kept separated atm)
			$artist = $trackname_and_artist[1];								// get the artist name
			
		// ...and if both duration and artist is the same as for the last track, there's a good chance this is a dupe
		// If the URI is the same as the last one, it MUST be a dupe 
			if ((($dur > 0) && ($dur == $last_dur) && ($artist == $last_artist)) || ($tracks['Addresses'][$i] == $last_uri)) {
				$dupes['Names'][$j] = $tracks['Names'][$i]; 
				$dupes['Addresses'][$j] = $tracks['Addresses'][$i]; 

			// If the URIs don't match it's just a possible dupe, let's keep them both and mark them with pretty stars
				if ($tracks['Addresses'][$i] <> $last_uri) {
					$dupes['Names'][$j] = '*'.$tracks['Names'][$i];
					$dupes['Names'][$j+1] = '**'.$tracks['Names'][$i-1]; 
					$dupes['Addresses'][$j+1] = $tracks['Addresses'][$i-1]; 
					$j++;
				}
				$j++;
			}
			$last_dur = $dur;						// 
			$last_artist = $artist;					// keep for comparison
			$last_uri = $tracks['Addresses'][$i];	// 
		}
	} else {
	
	// If it's just a tracks list we have no names/artist and duration to compare with, so we're stuck with just matching the URI
		foreach ($tracks['Addresses'] as $i => $uri) {
			$match_index = array_search($uri,$tracks['Addresses']);
			
		// If the current track address/URI matches with other addresses/URIs than the current it has to be a dupe
			if ($match_index <> $i) {
				$dupes['Addresses'][$j] = $tracks['Addresses'][$i];
				$j++;
			}
		}
	}
	unset($i,$dur,$last_dur,$last_uri,$last_artist);
	if (isset($dupes)) {
		return($dupes);
	} else {
		return(false);
	}
}

// This function is to handle local tracks. We can't look it up against the same way as with an ordinary URI, 
// but we already have what we need in the address:
function getLocalTrackName($local_track) {
					  // this 	is replaced by 	   this
	$replace = Array(	'#spotify:local:#', 	// remove this
						'#/#',					// :
						'#\+#',					// space
						'#\%26#',				// &
						'#\%27#',				// '
						'#\%28#',				// (
						'#\%29#',				// )
						'#\%2C#',				// , 
						'#\%3A#',				// ; (is really ':' but replaced with ';' for the sake of exploding correctly)
						'#\%5B#',				// [
						'#\%5D#');				// ]
	$substitutes = Array('',':',' ','&','\'','(',')',',',';','[',']');

	$local_track = preg_replace($replace,$substitutes,$local_track);
	$track_array = explode(':',$local_track);

// If there were any colons (':') in the track name, they would have been replaced with a ';' - we need to fix that
	return(preg_replace('#;#',':',$track_array[2])." by ".preg_replace('#;#',':',$track_array[0]));
}

function tracksGetTrackNames($dupes) {
	$i = 0;
	if ($testSocket = @fsockopen("ws.spotify.com", 80, $errno, $errstr, 2)) {
		foreach ($dupes['Addresses'] as $i => $dupe) {
			if(preg_match('/:local:/', $dupe)) {
				$dupes['Names'][$i] = getLocalTrackName($dupe);
			} else {
				$url = trim("http://ws.spotify.com/lookup/1/?uri=".$dupe);
				$content = file_get_contents($url);
				if ($content) {
					$doc = new SimpleXmlElement($content); 
					$dupes['Names'][$i] = $doc->name." by ".$doc->artist->name;
				} else {
					$dupes['Names'][$i] = "Error looking up in the Spotify API";
				}
			}
		}
		fclose($testSocket); 
		array_multisort($dupes['Names'], SORT_ASC, SORT_STRING,
						$dupes['Addresses'], SORT_ASC, SORT_STRING);
	} else { 
		$dupes['Names'][0] = '&nbsp;<br/><i>Unfortunately it\'s not possible to lookup tracknames at this time (Spotify site down), please try again later.</i>';
		$dupes['Addresses'][0] = '999';
	}
	unset($i);
 	return($dupes);
}

function playlistGetTrackNames($dupes,$playlist) {
	$i = 0;
	foreach ($dupes as $i => $dupe) {
		$match_index = array_search($dupe,$playlist['Addresses']);
		if (!($match_index === false)) {
			$dupe_tracks['Names'][$i] = $playlist['Names'][$match_index];
			$dupe_tracks['Addresses'][$i] = $playlist['Addresses'][$match_index];
			$dupe_tracks['Duration'][$i] = $playlist['Duration'][$match_index];
		}
	}
 	return($dupe_tracks);
}

function inPlaylists($track_uri,$playlists) {
	$in_lists = Array();
	foreach ($playlists as $i => $playlist) {
		if (isset($playlist['Name']) && ($playlist['Tracks']['Addresses'][0] > "")) { 	// not sure why address would contain "", check prolly redundant
			if (!(array_search($track_uri,$playlists[$i]['Tracks']['Addresses']) === false)) {	
				$in_lists[] = $playlists[$i]['Name'];
			}
		} elseif (isset($playlist['Addresses']) && ($playlist['Addresses'][0] > "")) {	// not sure why address would contain "", check prolly redundant
			if (!(array_search($track_uri,$playlists[$i]['Addresses']) === false)) {
				$in_lists[] = "Track list ".($i+1);
			}
		}
	}
	return($in_lists);
}

// Not in use.. yet.
function findAntidupes($dupes, $tracks) {
	$match_indexes = Array();
	foreach ($dupes as $i => $dupe) {
		$match_indexes = array_search($dupe,$tracks['Addresses']);
		foreach ($match_indexes as $match_index) {
			if (!($match_index === false)) {						// Making sure it's actually not false as opposed to 0.
				unset($tracks['Addresses'][$match_index]);
				unset($tracks['Names'][$match_index]);
				if (isset($tracks['Duration'])) {
					unset($tracks['Duration'][$match_index]);
				}	
			}
		}
	}
	return($tracks);
}
?>