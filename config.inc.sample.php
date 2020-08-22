<?php
	// error login for debugging application (uncomment to add php errors to the log file)
	//error_reporting(-1); // reports all errors
	//ini_set("display_errors", "1"); // shows all errors
	//ini_set("log_errors", 1);
	//ini_set("error_log", "./log/record_log.txt");
	ini_set("memory_limit","1024M"); // loading lrger datasets
	//RENAME TO config.inc.php

	setlocale(LC_TIME, "nl_NL");


	//change database settings to your own!:
	// MySQL / MariaDB
	$_config['sql_type'] = 'mysql';
	
	$_config['sql_host'] = 'mysqlserver:port';
	$_config['sql_user'] = 'mysqluser';
	$_config['sql_pass'] = 'test';
	$_config['sql_db'] = 'testdb';
	$_config['sql_prefix'] = 'Mytableprefix_';

	// SQLite
	//$_config['sql_type'] = 'sqlite';
	//$_config['sql_db'] = 'owntracks.db3';
	
	$_config['log_enable'] = True;
	$_config['default_accuracy'] = 1000; //meters
	$_config['default_trackerID'] = 'all';
	
	//settings for geocoding and reverse geocoding
	$_config['geo_reverse_lookup_url'] = "http://nominatim.openstreetmap.org/reverse?format=json&zoom=17&accept-language=nl&addressdetails=0&email=myemail@email.com&";
	
	$_config['geo_reverse_boundingbox_url'] = "http://nominatim.openstreetmap.org/reverse?format=json&osm_type=W&email=myemail@email.com&osm_id=";

// The following function free us from requiring mysqlnd (don't loose it, it is used in the rpc.php)
function get_result($Statement)
{
    $RESULT = array();
    $Statement->store_result();
    for ($i = 0; $i < $Statement->num_rows; $i++) {
        $Metadata = $Statement->result_metadata();
        $PARAMS = array();
        while ($Field = $Metadata->fetch_field()) {
            $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
        }
        call_user_func_array(array( $Statement, 'bind_result' ), $PARAMS);
        $Statement->fetch();
    }
    return $RESULT;
}


