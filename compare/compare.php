<?php

if (isset($_POST['submit'])) {
	$pl_count = count($_POST['playlist']);
	$tr_count = count($_POST['tracks']);
	$correct_format = true;
	$find_track_names = false;
	$message = '';
	$playlist_inputs = Array();
	$tracks_inputs = Array();
	$playlists_count = 0;
	$tracklists_count = 0;
	$playlists = Array (
					Array(
						'Name' => "",
						'Tracks' => Array(
							'Names' => Array(""),
							'Addresses' => Array(""),
							'Duration' => Array(0)
							)
						)
					);
					
	$tracklists = Array(
						Array(
							'Names' => Array(""),
							'Addresses' => Array("")
							)
						);
	
	If ($_POST['compare'] == 'dupes') {
		$show_uniques = false;
	} else {
		$show_uniques = true;
		$playlists_uniqes = $playlists;
		$tracklist_uniques = $tracklists;
	}
						
// Loop through the submittet playlists¨, keep the ones that have content, continue if current playlist has the right format
	for ($i=0;$i<=($pl_count-1);$i++) {  						
		if ($_POST['playlist'][$i] > '') {						
			$pl_input = trim($_POST['playlist'][$i]);
			if (correctPlaylistFormat($pl_input)) {	
			
			// Make sure the user hasn't submitted same playlist twice:
				if (array_search($pl_input,$playlist_inputs) === false) {	// making sure it's actually retuning false and not 0		
					$playlist_inputs[] = $pl_input;							// store the input for the check above
					$playlist_uri = getPlaylistURI($pl_input); 				// returns a Spotify playlist URI: spotify:user:<user>:playlist:<uri>
					$playlists[$playlists_count] = playlistGetSpotifyURIs($playlist_uri); 	// get the list of names, addresses (URIs) and duration
					if (count($playlists[$playlists_count]['Tracks']['Addresses']) > 999) {
						echo "<div class='warning'>".$playlists[$playlists_count]['Name'].": The playlist option  (top) will only accept 1000 tracks from your playlist. If your list has more than 1000 tracks, please use the bottom option.</div><br/>";
					}
					$playlists[$playlists_count]['Tracks'] = removeDupes($playlists[$playlists_count]['Tracks']); //remove any dupes from current list
				} else {
					$message = "<p>&nbsp;<br/>Playlist ".($playlists_count+1)." - ".$playlists[$playlists_count-1]['Name'].": Please apply seperate playlists (comparing a playlist with itself is not much point. :)).</p>";
					$correct_format = false;
				}				
			} else {
				$message = "<p>&nbsp;<br/>Playlist ".($playlists_count+1).": Please apply a valid playlist by HTTP or Spotify URI.</p>";
				$correct_format = false;
			}
			$playlists_count++; 	// keep track of number of playlists - this also serves as an index counter for the playlists array
		}
	}
	
// Loop through the submittet track lists¨, keep the ones that have content, continue if current track list has the right format
	for ($i=0;$i<=($tr_count-1);$i++) {
		if ($_POST['tracks'][$i] > '') {
			$tracks_input = $_POST['tracks'][$i];
			if (correctTracksFormat($tracks_input)) {
			
			// Make sure the user hasn't submitted same track list twice:
				if (array_search($tracks_input, $tracks_inputs) === false) { 	// making sure it's actually retuning false and not 0
					$tracks_inputs[] = $tracks_input;							// store the input for the check above
					$list = preg_split('/[\n\r]+/',$tracks_input); 				// get rid of
					$list = array_filter($list,'strlen');						// garbage
					$tracklists[$tracklists_count]['Addresses'] = tracksGetSpotifyURIs($list); 		// returns an array of Spotif URIs: spotify:track:<URI>
					$tracklists[$tracklists_count] = removeDupes($tracklists[$tracklists_count]);	// remove any dupes from current list
				} else {
					$message = "<p style='font-size:12px;'>&nbsp;<br/>Track list ".($tracklists_count+1).": Please apply seperate lists of tracks (comparing a playlist with itself is not much point. :)).</p>";
					$correct_format = false;
				}
			} else {
				$message = "<p style='font-size:12px;'>&nbsp;<br/>Track list ".($tracklists_count+1).": Please apply valid track list by HTTP or Spotify URI.</p>";
				$correct_format = false;
			}
			$tracklists_count++; 		// keep track of number of track lists - this also serves as an index counter for the tracklists array
		}
	}

// Check that there's no lists with the wrong format and that at least one field is filled out
	if (($correct_format) && (($playlists_count > 0) || ($tracklists_count > 0))) { 
		$dupes = false;
		$uniques = false;
		if (isset($playlists)) {
			$playlists_old = $playlists; 	// Keep playlists for later use
		}
		if (isset($tracklists)) {
			$tracklists_old = $tracklists; 	// Keep  track lists for later use
		}

	// Create a playlist work copy
		if ($playlists_count == 1) {
			$playlist['Names'] = $playlists[0]['Tracks']['Names'];
			$playlist['Addresses'] = $playlists[0]['Tracks']['Addresses'];
			$playlist['Duration'] = $playlists[0]['Tracks']['Duration'];
			
	// If there's more than on playlist, they need to be merged. THis can probably be done more elegantly. *notes*
		} elseif ($playlists_count > 1) {
			for ($i=0; $i <= ($playlists_count-2);$i++) {
				$playlists[$i+1]['Tracks']['Names'] 	= array_merge(	$playlists[$i]['Tracks']['Names'],
																		$playlists[$i+1]['Tracks']['Names']);
				$playlists[$i+1]['Tracks']['Addresses'] = array_merge(	$playlists[$i]['Tracks']['Addresses'],
																		$playlists[$i+1]['Tracks']['Addresses']);
				$playlists[$i+1]['Tracks']['Duration'] 	= array_merge(	$playlists[$i]['Tracks']['Duration'],
																		$playlists[$i+1]['Tracks']['Duration']);								
			} 
			$playlist = $playlists[$i]['Tracks'];
		}
	
	// And the same again for track lists
		if ($tracklists_count == 1) {
			$tracklist['Addresses'] = $tracklists[0]['Addresses'];
		} elseif ($tracklists_count > 1) {
			for ($i=0; $i <= ($tracklists_count-2);$i++) {
				$tracklists[$i+1]['Addresses'] = array_merge($tracklists[$i]['Addresses'],
													 $tracklists[$i+1]['Addresses']);
			}
			$tracklist['Addresses'] = $tracklists[$i]['Addresses'];
		}

	// If both playlist field and track list field is used, I wanna use the playlist too lookup names for dupes before using the API (cause it's faster)
	// - This needs a bit of fiddeling
		if (isset($tracklist) && isset($playlist)) {
		// First out - merge the lists of addresses
			$tracklist['Addresses'] = array_merge(	$tracklist['Addresses'], 					
													$playlist['Addresses']);
			if ($dupe_tracks = findDupes($tracklist)) {		// returns false if there are no dupes, else returns the dupes
				$dupes = true; 								// yes, we have dupes :P
				$dupe_tracks = removeDupes($dupe_tracks); 	// remove dupes within the dupes
				if ($show_uniques) {
					$j = 0;
					foreach ($playlists_old as $i => $list) {
						$uniques = false;
						foreach ($list['Tracks']['Addresses'] as $k => $uri) {
							if (array_search($uri,$dupe_tracks['Addresses']) === false) {
								$uniques = true;
								$playlists_uniqes[$i]['Tracks']['Addresses'][$j] = $uri;
								$playlists_uniqes[$i]['Tracks']['Names'][$j] = $list['Tracks']['Names'][$k];
								$j++;
							}
						}
						$playlists_uniqes[$i]['Name'] = $playlists_old[$i]['Name'];
//						$playlist_uniques[$i]['Tracks'] = playlistGetTrackNames($playlist_uniques[$i]['Tracks']['Addresses'],$playlists_old[$i]['Tracks']); 	// get tracknames by using the playlist
						if ($uniques) {
							array_multisort($playlists_uniqes[$i]['Tracks']['Names'], SORT_ASC, SORT_STRING,
											$playlists_uniqes[$i]['Tracks']['Addresses'], SORT_ASC, SORT_STRING);
						} else {
							$playlists_uniqes[$i]['Tracks']['Addresses'][0] = 'No unique tracks.';
						}
					}
					$j = 0;
					foreach ($tracklists_old as $i => $list) {
						foreach ($list['Addresses'] as $uri) {
							if (array_search($uri,$dupe_tracks['Addresses']) === false) {
								$uniques = true;
								$tracklists_uniqes[$i]['Addresses'][$j] = $uri;
								$j++;
							}
						}
// 						$tracklists_uniqes[$i] = tracksGetTrackNames($tracklists_uniqes[$i]);  // not listing names atm
						if (!$uniques) {
							$tracklists_uniqes[$i]['Addresses'][0] = 'No unique tracks.';
						}
					}
				
				} else {   	
					$playlist_dupes = playlistGetTrackNames($dupe_tracks['Addresses'],$playlist); 	// get tracknames by using the playlist
					if (count($playlist_dupes['Addresses']) > 0) {
					// If > 1 track list is submitted, we may have dupes that aren't in the playlist, so we need to handle that by finding the ones that aren't
						$tracklist_dupes['Addresses'] = array_diff($dupe_tracks['Addresses'],$playlist_dupes['Addresses']);
						if (count($tracklist_dupes['Addresses']) > 0) {
						// yup - these we need to handle the hard way by looking them up in the API, then merge them with the ones we've already named
							$tracklist_dupes = tracksGetTrackNames($tracklist_dupes);
							$dupe_tracks['Names'] = array_merge($playlist_dupes['Names'],$tracklist_dupes['Names']);
							$dupe_tracks['Addresses'] = array_merge($playlist_dupes['Addresses'],$tracklist_dupes['Addresses']);
						} else {
							$dupe_tracks['Names'] = $playlist_dupes['Names'];
							$dupe_tracks['Addresses'] = $playlist_dupes['Addresses'];
						}
					} else {
					// No matches in the playlist, looking up all the dupes against the API (ugh, slow)
						$find_track_names = true; //get the track names later
					}
				}
			}
	// If only track lists are submittet, we need to look up every dupe towards the API
		} elseif (isset($tracklist) && !isset($playlist)) {
			if ($dupe_tracks = findDupes($tracklist)) {		// returns false if there are no dupes, else returns the dupes
				$j = 0;
				$dupes = true;								// yes, we have dupes :P
				$dupe_tracks = removeDupes($dupe_tracks); 	// remove dupes within the dupes
				if ($show_uniques) {
					foreach ($tracklists_old as $i => $list) {
						foreach ($list['Addresses'] as $uri) {
							if (array_search($uri,$dupe_tracks['Addresses']) === false) {
								$uniques = true;
								$tracklists_uniqes[$i]['Addresses'][$j] = $uri;
								$j++;
							}
						}
//						$tracklists_uniqes[$i] = tracksGetTrackNames($tracklists_uniqes[$i]); 	//not listing names for now
						if (!$uniques) {
							$tracklists_uniqes[$i]['Addresses'][0] = 'No unique tracks.';
						}
					}
				} else {
						$find_track_names = true; // get the track names later
				}
			}

	// Lastly user may have submittet only playlists - quick and easy
		} elseif (!isset($tracklist) && isset($playlist)) {
			if ($dupe_tracks = findDupes($playlist)) {		// returns false if there are no dupes, else if a playlist (as opposed to track list) is submitted, dupes are retuned with names and all.  Nifty.
				$dupes = true;								// yes, we have dupes :P
				$dupe_tracks = removeDupes($dupe_tracks);	// remove dupes within the dupes
				if ($show_uniques) {
					foreach ($playlists_old as $i => $list) {
						$uniques = false;
						$j = 0;
						$new_playlist = Array();
						foreach ($list['Tracks']['Addresses'] as $k => $uri) {
							if (array_search($uri,$dupe_tracks['Addresses']) === false) {
								$uniques = true;
								$playlists_uniqes[$i]['Tracks']['Addresses'][$j] = $uri;
								$playlists_uniqes[$i]['Tracks']['Names'][$j] = $list['Tracks']['Names'][$k];
								$j++;
							}
						}
						$playlists_uniqes[$i]['Name'] = $playlists_old[$i]['Name'];
						if ($uniques) {
							array_multisort($playlists_uniqes[$i]['Tracks']['Names'], SORT_ASC, SORT_STRING,
											$playlists_uniqes[$i]['Tracks']['Addresses'], SORT_ASC, SORT_STRING);
						} else {
							$playlists_uniqes[$i]['Tracks']['Addresses'][0] = 'No unique tracks.';
						}
					}
				}
			}
		}

	// Now for the ouput
		if ($dupes) {
			$starred = false; 	// in case we get some starred tracks (possible dupes)
			$list_them = false; // in case we have more than two playlists / track lists compared
			
		// ws or embed.spotify.com down? let the user know
			if (isset($dupe_tracks['Names'][0]) && preg_match('/<i>/',$dupe_tracks['Names'][0])) { 	
				$message = $dupe_tracks['Names'][0].'<br/><br/>';									
			} else {
			/*	decided to take this check out for now. Who has more than 500 dupes anyway.. :P
				if (isset($trackNames[500]) && (preg_match('/<i>/',$trackNames[500]))) {	
					$message = $trackNames[500].'<br/>';
					unset($trackNames[500]);
				} 
			*/
				echo "<h3>Results for comparing these playlists:</h3>";

			// List the names of the playlists submitted, if any
				foreach ($playlists as $i => $list) {
					if ($list['Name'] > '') {
						echo "&nbsp;&nbsp;&nbsp;- ".$list['Name']."<br/>";
					} 
				}
				
			// List the track lists, if any, i.e. "Track list 1, Track list 2, etc" - not sure why, it's kinda irrelevant?
				if ($tracklists_count > 0) {
					foreach ($tracklists as $i => $list) {
						echo "&nbsp;&nbsp;&nbsp;- Track list ".($i+1)."<br/>";
					}
				}

				if ($show_uniques) {
					if ($playlists_count > 0) {
						foreach ($playlists_uniqes as $list) {
							if ($list['Tracks']['Addresses'][0] == 'No unique tracks.') {
								echo "<br/><c<b>No unique tracks in playlist '".$list['Name']."'</b><br/><br/>";
							} else {
								echo "<br/><br/><b>Unique tracks in playlist '".$list['Name']."' (".count($list['Tracks']['Addresses'])." tracks):</b><br/>
		
									<textarea name='newlist' rows='6' cols='52' onClick='select(this)' wrap='off' readonly>";
								foreach ($list['Tracks']['Addresses'] as $uri) {
											echo $uri."\r\n";
							}
								echo "</textarea><br/>
									<div class='small'>(these tracks are not in any other submitted track or playlist)</div><br/>
									<p>
										<i>Select the tracks by clicking inside the textarea and use CTRL-C or right click to copy, or just drag them to an empty playlist.</i>
										<br/>
									</p>";
							}
/*
								<p>&nbsp;<br/>
									<b>Unique tracks in playlist '".$list['Name']." by name and artist:</b>
									<br/><br/>";
							foreach ($list['Tracks']['Names'] as $i => $name) {
								echo $name."<br/>";
							}
							echo "	<br/><i>Sort your playlist alphabetically or use the search function (ctrl-f) to find the tracks easily.</i>
								<br/><br/>
							</p>";
*/
						}
					}
					if ($tracklists_count > 0) {
						foreach ($tracklists_uniqes as $i => $list) {
							if ($list['Addresses'][0] == 'No unique tracks.') {
								echo "<br/><b>No unique tracks in 'Track list ".($i + 1)."'</b><br/><br/>";
							} else {
								echo "<br/><br/><b>Unique tracks in 'Track list ".($i + 1)."' (".count($list['Addresses'])." tracks):</b><br/>
									<textarea name='newlist' rows='6' cols='52' onClick='select(this)' wrap='off' readonly>";
								foreach ($list['Addresses'] as $uri) {
									echo $uri."\r\n";
								}
								echo "</textarea><br/>
									<div class='small'>(these tracks are not in any other submitted track or playlist)</div><br/>
									<p>
										<i>Select the tracks by clicking inside the textarea and use CTRL-C or right click to copy, or just drag them to an empty playlist.</i>
										<br/>
									</p>";
							}
						}
					}
				} else {
				// List the dupe track URIs in a textarea
					echo "<br/><br/><b>Duplicates (".count($dupe_tracks['Addresses'])." tracks):</b><br/>
						<textarea name='newlist' rows='6' cols='52' onClick='select(this)' readonly>";
					foreach ($dupe_tracks['Addresses'] as $uri) {
						echo $uri."\r\n";
					}
					echo "</textarea><br/>
						<p>
							<i>Select the tracks by clicking inside the textarea and use CTRL-C or right click to copy, or just drag them to an empty playlist.</i>
							<br/>&nbsp;<br/>
						</p>
						<p>&nbsp;<br/>
							<b>Duplicates by track name and artist (".count($dupe_tracks['Addresses'])." tracks):</b>
							<br/><br/>";
						
				// List the tracks by name
					if ($find_track_names) {
						$dupe_tracks = tracksGetTrackNames($dupe_tracks);
					}
					foreach ($dupe_tracks['Names'] as $i => $name) {
					// If there are more than 2 playlists / track lists submitted we wanna show in which ones the dupe is.
						if (($playlists_count + $tracklists_count) > 2) {
							$list_them = true;
							unset($in_playlists,$in_tracklists);

						// I'm not sure what's going on here. There must be a reason I make this so complicated. Need to figure it out. *notes*
							if (($playlists_count > 0) && ($tracklists_count == 0)) {
								$in_playlists = inPlaylists($dupe_tracks['Addresses'][$i],$playlists_old);
							} elseif (($playlists_count > 0) && ($tracklists_count > 0)) {
								if (inPlaylists($dupe_tracks['Addresses'][$i],$playlists_old)) {
									$in_playlists = inPlaylists($dupe_tracks['Addresses'][$i],$playlists_old);
								}
								if (inPlaylists($dupe_tracks['Addresses'][$i],$tracklists_old)) {
									$in_tracklists = inPlaylists($dupe_tracks['Addresses'][$i],$tracklists_old);
								}
							} else {
								$in_tracklists = inPlaylists($dupe_tracks['Addresses'][$i],$tracklists_old);
							}
					
						// Unify what lists the track is in
							if (isset($in_playlists) && isset($in_tracklists)) {
								$in_playlists = array_merge($in_playlists, $in_tracklists);
							} elseif (isset($in_tracklists)) {
								$in_playlists = $in_tracklists;
							}
						}

					// List the track
						echo $name."<br/>";
						if (preg_match('/\*\*/',$name)) {  	// check if the track is starred
							$starred = true;				// ah, need to display special message later
						}
					
					// If we have more than two lists compared, display which ones the dupe is in
						if ($list_them) {
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(";
							foreach ($in_playlists as $j => $in_playlist) {
								echo $in_playlist;
						
							// display a comma if there are more lists to display:
								if (($j < count($in_playlists)-1) || (isset($in_tracklists) && ($j < count($in_tracklists)-1))){
									echo ", ";
								}
							}
							echo ")<br/><br/>";
						}
					}
					if ($starred) {
						echo "<br/>* and ** are possibly dupes. Could be within the same playlist. <br/>";
					}
					echo "	<br/><i>Sort your playlist alphabetically or use the filter function (ctrl-f) to find the tracks easily.</i>
							<br/><br/>
						</p>";
				}
				echo $message; // display error message (I should make this an array *notes*)
				$message = "";
			}
		} else {
			if ($show_uniques) {
				$message = "<p>&nbsp;<br/>Maybe you should try with more than one playlist? :-)</p>";
			} else {
				$message = "<p>&nbsp;<br/>No duplicates found</p>";
			}
		}
	} 
	echo $message; // display error message (I should make this an array *notes*)
}	
?>

