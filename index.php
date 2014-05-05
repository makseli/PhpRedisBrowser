<?php


$proje_isim = array(
	'0' => ' -  0 boş -',
	'1' => ' -  1 boş -',
	'2' => ' -  2 boş -',
	'3' => ' -  3 boş -',
	'4' => ' -  4 boş -',
	'5' => ' -  5 boş -',
	'6' => ' -  6 boş -',
	'7' => ' -  7 boş -',
	'8' => ' -  8 boş -',
	'9' => ' -  9 boş -',
	'10' => ' -  10 boş -',
	'11' => ' -  11 boş -',
	'12' => ' -  12 boş -',
	'13' => ' -  13 boş -',
	'14' => ' -  14 boş -',
	'15' => ' -  15 boş -',
);

/***************************************************************************
 *
 *
 *                PHP Redis Broser v0.3
  
    A simple, one-file PHP based admin console and browser for Redis  
  
    Try a demo at http://ohardt.com/php-rb/demo.php

    
 * 
 *  
 * LICENSE  
 * 
 *  

Copyright (c) 2012         Contact: ohardt at gmail dot com

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or 
sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.






 * 
 * 
 * IMPORTANT
 * 
 *   

Requires the excellent predis library
Get it here: https://github.com/nrk/predis/

Adjust the path to the library as needed

*/

require '../predis-0.8/lib/Predis/Autoloader.php';
Predis\Autoloader::register();






/***************************************************************************
 * 
 * 
 * TODO
 * 
 *   - more server commands
 *   - quick commands
 *   - pagination of keys
 *   - nicer data output screens
 *   - improve layout
 *   - clean up html/php mix/mess
 *   - better error checking
 *   - ...
 * 
 */
	

/***************************************************************************
 * 
 * 
 * HISTORY
 * 
 *   0.3
 *   - removed the session stuff 
 *
 *  
 *   0.2
 *   - initial public release
 * 
 * 
 */

 	
/***************************************************************************
 * 
 * 
 * CONFIG START 
 * 
 * enter your redis server details here
 * 
 */

				$CONFIG_HOST = array(
				
				    array(
					    'host'     		=> '127.0.0.1', 
					    'port'    	 	=> 6371, 
					    'database' 		=> 0,
					    'password' 		=> '',
				       	'alias' 		=> 'lokal_Redis',
				    ),
				);


	
	
	
/***************************************************************************
 * 
 * 
 * CONFIG END
 * 
 * no need to change anything beyond this point  
 * 
 */
		
		
		







		








		
	$db 			= ( isset( $_REQUEST['db'] ) ? ( $_REQUEST['db'] ) : 0 );		
	$server_alias 	= ( isset( $_REQUEST['sa'] ) ? ( $_REQUEST['sa'] ) : $CONFIG_HOST[0]['alias'] );		
					
	$client   		= new Predis\Client( $CONFIG_HOST );
	$redis			= $client->getClientFor( $server_alias );
	
	$is_demo 		= false;
	$script_name 	= isset( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : "index.php";

	

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>PHP Redis Browser 0.3</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script language="JavaScript">
/*function ScrollDown() {
	window.scrollBy(0,1000000);
	scrolldelay = setTimeout('pageScroll()',100);
}
window.onload=ScrollDown;*/
</script>
</head>
<body>
<style type="text/css">
ul li span {padding:3px!important}
ul li {margin-bottom:10px; }
</style>
<?php



/***************************************************************************
 * 
 * parse parameters for action, sort and pattern
 *  
 */
	
    $action = "b";
    
	if( isset( $_REQUEST['a'] ) ) {
	    $action = $_REQUEST['a'];
	}    
	
	$sort    = "no";
	if( isset( $_REQUEST["s"] ) ) {
		
		if( ( $_REQUEST["s"] !== "ttl" ) &&
			( $_REQUEST["s"] !== "key" ) && 
			( $_REQUEST["s"] !== "sz"  ) && 
			( $_REQUEST["s"] !== "1"   ) &&            // "1" is for sorting sets, lists 
			( $_REQUEST["s"] !== "no"  ) ) {
		    die;
		} 
		
		$sort = $_REQUEST["s"];
	}
	
	$pattern = "*";
	if( isset( $_REQUEST["p"] ) ) {
		
	    $pattern = $_REQUEST["p"];
	}
	



/***************************************************************************
 * 
 * display available servers
 *  
 */

	echo '<form name="db_alias_change" method="GET" action="' . $script_name . '">';

	if( $action == "i" ) echo "<input type='hidden' name='a' value ='i'>";

	echo 'Server: <select name="sa"  onchange="this.form.submit();">';
	 
 	foreach ( $CONFIG_HOST as $c ) {
 	    
 	    echo '<option value="' . $c['alias'] . '" ';
		if( $c['alias'] == $server_alias ) echo " selected ";
		echo '>' . $c['alias'] . '</option>';
 	    
 	}
?>
</select> 
</form>
&nbsp;&nbsp;&nbsp;&nbsp;


<?php

/*********************************************
 * 
 *    check if server is available
 * 
 */

	try {

	    $res = $redis->ping();
	    
	} catch( Predis\Network\ConnectionException $e ) {
		
		echo "Couldn't connect to server!";
		
	    die;
	    
	} catch( Predis\ServerException $e ) {
		
		echo "Authentication error!";
		
	    die;
	    
	} catch( Exception $e ) {
		
		echo "Error!";
			
	    die;
	}




/***************************************************************************
 * 
 * select database
 * ( demo version allows access to DB #0 only )
 *  
 */
 
	if( $is_demo ) {
	    $redis->select( 0 );
	} else {
    	$redis->select( $db );
	}




	
	




/***************************************************************************
 * 
 * handle actions
 *  
 */
	
	switch( $action ) {
		
	    case "s": {   // show

		
			if( isset( $_REQUEST["k"] ) ) {
				$k = base64_decode( $_REQUEST["k"] );
				
				display_key( $k );
				die;
			}
			break;
	    }

		
	    case "as": {   // add string
	    	
	    	if( ( isset( $_REQUEST['key'] ) ) && 
	    		( isset( $_REQUEST['val'] ) ) ) {
	        	$redis->set( $_REQUEST['key'], $_REQUEST['val'] );
    		}
	        break;
	    }
	    case "ah": {   // add hash
	    
	    	if( ( isset( $_REQUEST['key']  ) ) && 
	    		( isset( $_REQUEST['val']  ) ) &&
	    		( isset( $_REQUEST['hash'] ) ) ) {
		        $redis->hset( $_REQUEST['hash'], $_REQUEST['key'], $_REQUEST['val'] );
    		}
	        break;
	    }
        case "d": {   // delete

			if( ( isset( $_REQUEST['sel'] ) )    && 
			    ( count( $_REQUEST['sel'] ) > 0 ) ) {
			    	
				foreach( $_REQUEST['sel'] as $s ) {
	            	$redis->del( base64_decode( $s ) );
				}
		    }
            break;
        }
        
        case "f": {   // flush DB

        	$redis->flushdb();
            break;
        }
        
        case "p": {    // make persistent

			if( ( isset( $_REQUEST['sel'] ) )    && 
			    ( count( $_REQUEST['sel'] ) > 0 ) ) {
			    	
				foreach( $_REQUEST['sel'] as $s ) {
		            $redis->persist( base64_decode( $s ) );
				}
		    }            
            break;
        }
		case "i": {   // display server info

			display_info( $redis, $server_alias );		
			die; // done here	    
		}
	    
	}
		
	
/***************************************************************************
 * 
 * 
 * done with actions - display the rest of the header section 
 *
 */	 

	display_databases( $redis, 
					   $db,
					   $server_alias );



	echo '<center><form name="search" method="get" action="' . $script_name . '">';
	echo 'Pattern <input type="text" size=30 name="p" value="' . $pattern . '" />';
	echo '<input type="submit" value="Search" /> sort by: ';

	echo '<input type="radio" name="s"  value="key"' . ( $sort == 'key' ? "checked" : "" ) .  ' />Key ';
	echo '<input type="radio" name="s"  value="sz" '  . ( $sort == 'sz' ? "checked" : ""  ) .  ' />Size ';
	echo '<input type="radio" name="s"  value="ttl" ' . ( $sort == 'ttl' ? "checked" : "" ) .  ' />TTL ';
	echo '<input type="radio" name="s"  value="no"'  . ( $sort == 'no' ? "checked" : ""  ) .  ' />No ';
		
	echo '<input type=hidden name="db"  value=' . $db . ">";
	echo '<input type=hidden name="sa"  value=' . htmlspecialchars( $server_alias ) . ">";
	
	echo '</form></center>' . "<br/><br/>\n";
	
	
	
	
	
/***************************************************************************
 * 
 * 
 * done with the header section - display the keys 
 *
 */	 	


	$count_all_keys_in_db = $redis->dbsize();
	
	$all_keys 		= array();
	$matched_keys   = $redis->keys( $pattern );
	
	foreach( $matched_keys as $k ) {
	    
	    $sz = -1;
	    
	    $type = $redis->type( $k );
	    $ttl  = $redis->ttl ( $k );
	    
	    if( $type == "string" ) {
	        $sz = $redis->strlen( $k );
	    } else if( $type == "hash" ) {
	        $sz = $redis->hlen( $k );
	    } else if( $type == "set" ) {
	        $sz = $redis->scard( $k );
	    }
	    
	    if( !isset( $all_keys[$type] ) ) {
	        $all_keys[$type] = array();
	    }
	    
	    array_push( $all_keys[$type], array( "key" => $k, "ttl" => $ttl, "sz" => $sz ) );
	}
	
	// sort by type
	ksort( $all_keys );

	util_html_form_start( "form_select", $pattern, $sort, $server_alias, "post", false );

	echo "Showing " . count( $matched_keys ) . " of " . $count_all_keys_in_db . " keys";
	
?>	
&nbsp;&nbsp;
<select name="a">
	<option value="d">Delete selected</option>
	<option value="p">Persist selected</option>
	<option value="f">Flush DB</option>
	</select>
<input type="submit" value="Execute" onClick="return confirmSubmit()" /><br/><br/>


<table class="table">
<tr>
	<th align=center  width='40'> 
		<input type="checkbox" name="check_all" value="Check All" onClick="javascript:selectToggle('form_select');">
	</th>
	<th align=left width='50'> Type </th>
	<th align=left > Key </th>
	<th align=center width='75'> Size </th>
	<th align=center width='75'> TTL </th>
</tr>



<?php
		 
	if( count( $all_keys ) > 0 ) {
	
	
		foreach( $all_keys as $type => $keys ) {
		
			switch( $sort ) {
			    
			    case "key": uasort( $keys, 'util_custom_cmp_key' ); break;		    
				case "ttl": uasort( $keys, 'util_custom_cmp_ttl' ); break;		    
				case "sz" : uasort( $keys, 'util_custom_cmp_sz' );  break;		    
			} 
		
			rsort($keys);
		
			foreach( $keys as $k ) {
				
				$ttl_txt = util_format_ttl( $k['ttl'] );
				
				$sz_txt  = util_format_size( $type, $k['sz'] );
				
		    	echo '<tr><td align=center><input type="checkbox" name="sel[]" value="' . htmlspecialchars( base64_encode( $k['key'] ) ) . '" />' . "</td>";
				echo "<td>" . $type . "</td><td><a href='" . $script_name . "?db=" . $db ."&sa=" . $server_alias . "&a=s&k=" . htmlspecialchars( base64_encode( $k['key'] ) ) .  "'>" . $k['key'] . "</a> </td>";
				echo "<td  align=center>" . $sz_txt . "</td>";
				echo "<td  align=center>" . $ttl_txt . "</td>";			
				echo "</tr>\n";
			}
		}			    
	} else {
		
		// nothing found - let people know
		echo '<tr><td colspan="5" align="center"><br/><b>No results found</b><br/><br/></td></tr>';	    	    
	}
	
	
	echo "</table>";
	echo "</form>";	
	echo "<br/><br/>";


	util_html_form_start( "as", $pattern, $sort, $server_alias, "post", true );
?>
	<input type="text" name="key" value="<key>"			onfocus="this.value==this.defaultValue?this.value='':null"/>
	<input type="text" name="val" value="<value>"		onfocus="this.value==this.defaultValue?this.value='':null"/>
	<input type="submit" value="Add String" /><br/>
	</form>
<?php
	util_html_form_start( "ah", $pattern, $sort, $server_alias, "post", true );
?>
	<input type="text" name="hash" 	value="<key>" 		onfocus="this.value==this.defaultValue?this.value='':null"/>
	<input type="text" name="val" 	value="<field>"		onfocus="this.value==this.defaultValue?this.value='':null"/>
	<input type="text" name="key" 	value="<value>"		onfocus="this.value==this.defaultValue?this.value='':null"/>
	<input type="submit" value="Add Hash" /><br/>
	</form>

<SCRIPT LANGUAGE="JavaScript">

function selectToggle(n) {

	var fo = document.forms[n];

	t = fo.elements['check_all'].checked;

     for( var i=0; i < fo.length; i++ )  {
     
     	if( fo.elements[i].name == 'check_all' ) {
     		continue;
 		}
     
     	if( t ) { 
     		fo.elements[i].checked = "checked";      
     	} else {    
     		fo.elements[i].checked = "";
 		}
     }    
}     

function confirmSubmit() {
	
	if( document.form_select.a.selectedIndex !== 2 ) {
	    return true;
	}

	return confirm("Are you sure you wish to continue?");
}


</script>


</body>
</html>







<?php


/***************************************************************************
 * 
 * util functions
 * 
 * 
 */


function display_databases( $redis, $curr_db, $sa ) {

	global $is_demo, $script_name, $sort, $pattern, $proje_isim;

	
	echo "Databases:&nbsp;&nbsp;";

	$dbs = array();
		
	if( $is_demo ) {

		// only db #0 avail in demo mode	    
		$dbs = array( 0 => array() );
	    
	} else {

		$dbs = util_get_dbs( $redis );
	}
	
	foreach( $dbs as $n => $db_info ) {
		
		if( $n == $curr_db ) echo "<b>";
		
	    echo 	"<a href='" . $script_name . 
				"?db=" . $n . "&sort=" . $sort . 
				"&sa=" . urlencode( $sa ) .
				"&p=" . htmlspecialchars( $pattern ) . "'>[ " . $proje_isim[trim($n)] . " ]</a>&nbsp;&nbsp;&nbsp;";
				
		if( $n == $curr_db ) echo "</b>";
	}
	
	
	echo "<a href='" . $script_name . "?a=i&sa=" . $sa ."'>[ Info ]</a> <br/><br/>";
}



function display_info( $redis, $sa ) {
    
	display_databases( $redis, -1, $sa );

	$ts_lastsave = $redis->lastsave();
	$secs = time() - $ts_lastsave;
	
	echo "<center>Last save " . $secs . " seconds ago. <br/><br/>";	
	
	$info = $redis->info();
	
	echo '<table class="table">';
		
	foreach( $info as $k => $v ) {
	
		if( $k == 'allocation_stats' ) {
		    $v = str_replace( ",", "<br/>", $v );
		}
		
		if( substr( $k, 0, 2 ) == "db" ) {
			$v = "Keys: " . $v['keys'] . "<br/>Expires: " . $v['expires'];
		}
	
    	echo '<tr><td>' .  $k . "</td>";
    	echo '<td>' .  $v . "</td></tr>";
	    
	}
	echo "</table></body></html>";
}




function display_key( $k ) {
	
	global $redis, $proje_isim;
	
	$type   = $redis->type( $k );
	$retval = false;

	echo "<pre>";

	switch( $type ) {

		case "string": {
			
			$retval = $redis->get( $k );
			break;
		}   

		case "hash": {
			
			$retval = $redis->hgetall( $k );
			break;
		}		

		case "list": {
			
			$retval = $redis->lrange( $k, 0, -1 );
			break;
		}
		
		case "set": {
			
			$retval = $redis->smembers( $k );
			break;
		}
			
		case "zset": {
			
			$retval = $redis->zrange( $k, 0, -1, "WITHSCORES" );
			break;
		}
			
		default: {
		    $retval = "Data type not supported (yet)";
		    break;
		}	
	}
	
	
	
	echo 'DB: <a href="/index.php?sa='.$_GET['sa'].'"> Sunucuya Dön </a>'." \n";
	echo 'Proje :  <a href="\index.php?db='.$_GET['db'].'&sa='.$_GET['sa'].'">'.$proje_isim[$_GET['db']]. "</a> \n";
	echo "Key: <b> " .str_replace('_', '.', $k). " </b> \n";
	echo "Type: " . $type 	. "\n";

	/*/ unserialize?	
	if( isset( $_REQUEST["u"] ) ) {
		
		$retval = unserialize( $retval );
		
		echo "Unserialized\n";
		
	} else {
		if( isset( $_SERVER['REQUEST_URI'] ) ) {
			$u = $_SERVER['REQUEST_URI'] . "&u=1";
		    echo "<a href='". $u . "'>Unserialize</a>\n";
		}
	}*/
	
	
	if( ( isset( $_REQUEST["s"] ) ) && ( is_array( $retval ) ) ) {
		
		asort( $retval );
		
		echo "Sorted by values\n";
		
	} else {
		if( isset( $_SERVER['REQUEST_URI'] ) ) {
			$u = $_SERVER['REQUEST_URI'] . "&s=1";
			if( is_array( $retval ) ) {
		    	echo "<a href='". $u . "'>Sort array by values</a>\n";
			}
		}
	}


	echo "\n";
	
	//var_dump( $retval );
	
	echo '<div style="float:left; margin:0px auto;"><ul>';
	
	$tur = '';
	
	rsort($retval);
	
	foreach($retval as $log){
	
	
			$tur = substr($log, 0, 7);
			
				if($tur === 'httplog')
					$tur = 'list-group-item-info';
					
				else{

					$tur = 'list-group-item-warning';
					
					$kes = explode('Stack trace:', $log);
					if(count($kes) > 0)
						$log = $kes[0];
				}
				
				
				$log = str_replace(array('httplog', 'app_err', ' ZAMAN ', ' saat ', 'http://', 'array(', '"', "'"), '', $log);
				
				
				$ccds_arr = array(' IP ', ' TRYC ', ' URL ');// success, default  , ' ZAMAN '
				$ccdm_arr = array(' <span class="alert alert-danger">ip</span> ', ' <span class="alert alert-warning">TRYC</span> ', ' <span class="alert alert-success">URL</span> '); //, ' <span class="alert alert-default">saat</span> '
				
				$log = str_replace($ccds_arr, $ccdm_arr, $log);
				
		
		echo '<li class="'.$tur.'">'.$log.'</li>';
	
	}
	echo '</ul></div>';
}


function util_get_dbs( $r ) {
    
    $info = $r->info();
    $res = array();
    foreach( $info as $k => $v ) {
        
        if( substr( $k, 0, 2 ) == "db" ) {
        	$db_num = substr( $k, 2, strlen( $k ) );
        	
        	if( is_numeric( $db_num ) ) {
            	$res[ (int)$db_num ] = $v;
        	}
        }
    }
	return $res;	    
}


function util_format_ttl( $ttl ) {

	if( $ttl === -1 ) {
		return $ttl;
	}
	
	$m = ((int)( ( $ttl ) / 60 ));
	
	if( $m > 120 ) {
		$m = ( (int) ( $m / 60 ) ); 
		$s = "" . $m . "h";
	} else {
	    $s = "" . $m . "min";
	}

	$s = $ttl . " (" . $s . ")";
    
    return $s;
}


function util_format_size( $type, $sz ) {
	
	$s = $sz; 
	
	if( ( $type === "string" ) &&  
		( (int)$sz > 1100 ) ) {
	    $s = (int)( $sz / 1024 ) . "kb";
	}	    
	
	return $s;
}


function util_custom_cmp_key( $a, $b ) {
    
    return strcmp( $a['key'], $b['key'] );
}

	
function util_custom_cmp_ttl( $a, $b ) {
    
    if( $a['ttl'] === $b['ttl'] ) {
        return util_custom_cmp_key( $a, $b );
    }
    
    return ( $a['ttl'] - $b['ttl'] );
}

	
function util_custom_cmp_sz( $a, $b ) {
    
    if( $a['sz'] === $b['sz'] ) {
        return util_custom_cmp_key( $a, $b );
    }
    
    return ( $a['sz'] - $b['sz'] );
}		

	
function util_html_form_start( $action, $pattern, $sort, $sa, $type, $put_action ) {
	
	global $script_name;
	
	echo '<form name="' . $action . '" method="' . $type . '" action="' . $script_name . '">';
	
	echo '<input type="hidden" name="a"  value="' . $action 						. '" />';
	echo '<input type="hidden" name="p"  value="' . htmlspecialchars( $pattern ) 	. '" />';	    
	echo '<input type="hidden" name="s"  value="' . htmlspecialchars( $sort ) 		. '" />';
	echo '<input type="hidden" name="sa" value="' . htmlspecialchars( $sa ) 		. '" >';			    
}	



?>
