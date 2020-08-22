<?php
//http://owntracks.org/booklet/tech/http/
    # Obtain the JSON payload from an OwnTracks app POSTed via HTTP
    # and insert into database table.

    header("Content-type: application/javascript");
    require_once('config.inc.php');
	
	$fp = NULL; //fopen('./log/record_log.txt', 'a+'); do not open file if not needed
	function _log($msg){
		global $fp;
		global $_config;
		
		if($_config['log_enable'] === True){
			if(!$fp) { $fp = fopen('./log/record_log.txt', 'a+'); }
		
			return fprintf($fp, date('Y-m-d H:i:s') . " - ".$_SERVER['REMOTE_ADDR']." - %s\n", $msg);
		} else {
			return True;
		}
	}

	$response = array();

	//_log('rpc_dataFrom = '.$_GET['dateFrom']);
	//_log('rpc_dataTo = '.$_GET['dateTo']);

	if ($_config['sql_type'] == 'mysql') {
		require_once('lib/db/MySql.php');
		/** @var MySql $sql */
		$sql = new MySql($_config['sql_db'], $_config['sql_host'], $_config['sql_user'], $_config['sql_pass'], $_config['sql_prefix']);
	} elseif ($_config['sql_type'] == 'sqlite') {
		require_once('lib/db/SQLite.php');
		/** @var SQLite $sql */
		$sql = new SQLite($_config['sql_db']);
	} else {
		die('Invalid database type: ' . $_config['sql_type']);
	}

    if (array_key_exists('action', $_REQUEST)) {
	    
	    if ($_REQUEST['action'] === 'getMarkers') {
	    	$starttime = strtotime("Today 7:00");
	    	if (!array_key_exists('dateFrom', $_GET)) {
				$time_from = $starttime;
		    } elseif ($_GET['dateFrom'] == $_GET['dateTo']){
				$time_from = $starttime;
			} else {
				$time_from = strtotime($_GET['dateFrom']);
			}
			$endtime = strtotime("Today 19:00");
		    if (!array_key_exists('dateTo', $_GET)) {
				$time_to = $endtime;
		    } elseif ($_GET['dateFrom'] == $_GET['dateTo']){
				$time_to = $endtime;
			} else {
				$time_to = strtotime($_GET['dateTo']);
			}
		
		    if (array_key_exists('accuracy', $_GET) && $_GET['accuracy'] > 0) {
		        $accuracy = intVal($_GET['accuracy']);
		    } else {
		        $accuracy = $_config['default_accuracy'];
		    }
		
		
			$markers = $sql->getMarkers($time_from, $time_to, $accuracy);

			if ($markers === false) {
				$response['status'] = false;
				$response['error'] = 'Database query error';
				http_response_code(500);
			} else {
				$response['status'] = true;
				$response['markers'] = json_encode($markers);
			}
	    	
	    	
	    } elseif ($_REQUEST['action'] === 'deleteMarker') {
	    	
	    	if (!array_key_exists('epoch', $_REQUEST)) {
	    		$response['error'] = "No epoch provided for marker removal";
	    		$response['status'] = false;
	    		http_response_code(204);
	    	}else{
	    		$result = $sql->deleteMarker($_REQUEST['epoch']);
				if ($result === false) {
					$response['error'] = 'Unable to delete marker from database.';
					$response['status'] = false;
					http_response_code(500);
				} else {
					$response['msg'] = "Marker deleted from database";
					$response['status'] = true;
				}
			}
	    	
	    } elseif ($_REQUEST['action'] === 'geoDecode') {
	    	
	    	if (!array_key_exists('epoch', $_REQUEST)) {
	    		$response['error'] = "No epoch provided for marker removal";
	    		$response['status'] = false;
	    		http_response_code(204);
	    	} else {
	    		
				// GET MARKER'S LAT & LONG DATA
				$marker = $sql->getMarkerLatLon($_REQUEST['epoch']);

				if ($marker === false) {
					$response['error'] = 'Unable to get marker from database.';
					$response['status'] = false;
				} else {
				    $latitude = $marker['latitude'];
				    $longitude = $marker['longitude'];
				    
				    // GEO DECODE LAT & LONG
				    $geo_decode_url = $_config['geo_reverse_lookup_url'] . 'lat=' .$latitude. '&lon='.$longitude;
					$geo_decode_json = file_get_contents($geo_decode_url);
					$geo_decode = @json_decode($geo_decode_json, true);
				
					$place_id = intval($geo_decode['place_id']);
					$osm_id = intval($geo_decode['osm_id']);
					$location = strval($geo_decode['display_name']);
					
					if ($location == '') { $location = @json_encode($geo_decode); }
					
					//UPDATE MARKER WITH GEODECODED LOCATION
					$result = $sql->updateLocationData((int)$_REQUEST['epoch'], (float)$latitude, (float)$longitude, $location, $place_id, $osm_id);
					
					if ($result === false) {
						$response['error'] = 'Unable to update marker in database.';
						$response['status'] = false;
						http_response_code(500);
		    		} else {
						$response['msg'] = 'Marker\'s location fetched and saved to database';
						$response['location'] = $location;
						$response['status'] = true;
		    		}
	    		}
	    		
	    	}
	    	
	    } else {
	    	$response['error'] = "No action to perform";
	    	$response['status'] = false;
	    	http_response_code(404);
	    }
	    
	} else {
    	$response['error'] = "Invalid request type or no action";
    	$response['status'] = false;
    	http_response_code(404);
    }
	
	echo json_encode($response);
