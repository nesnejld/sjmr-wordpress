<?php
try {
	$mysql = file_get_contents('../mysql.json');
	$mysql = json_decode($mysql, true);
	// $servername = 'localhost';
	$servername = $mysql["servername"];
	// $username = 'sjmr';
	$username = $mysql["username"];
	// $password = 'passw0rd';
	$password = $mysql["password"];
	// $database = 'wordpress_duplicate';
	$database = $mysql["database"];

	$table = 'wp_jsdb_sjmr';
	if (false) {
		print(json_encode(array('error' => 'oops')));
		return;
	}
	function writelog($file, $line, $message)
	{
		print($message);
		print("<br>");
	}
	// writelog(__FILE__,__LINE__,'Hello');

	function runsql($sql)
	{
		global $servername, $username, $password, $database;
		error_log(__FILE__ . " " . __LINE__ . " " . date(DATE_RFC822));
		// Create connection
		$conn = new mysqli($servername, $username, $password, $database);
		// Check connection
		if ($conn->connect_error) {
			print(json_encode(array("error" => __FILE__, __LINE__, "Connection failed: " . $conn->connect_error)));
			exit;
		}
		// writelog(__FILE__ , __LINE__ ,$sql);
		$result = $conn->query($sql);
		if ($conn->query($sql) === FALSE) {
			writelog(__FILE__, __LINE__, "Error " . $conn->error);
		} else {
			// writelog(__FILE__ , __LINE__ , "Success");
		}
		if (!is_bool($result)) {
			$result = $result->fetch_all(MYSQLI_ASSOC);
		}
		$conn->close();
		return $result;
	}
	if (false) {
		print(json_encode(array('error' => __FILE__ . " " . __LINE__)));
		return;
	}
	try {
		$action = $_GET["action"];
		if (false) {
			print(json_encode(array('error' => __FILE__ . " " . __LINE__ . " " . $action)));
			return;
		}
		if ($action == 'gettable') {
			try {
				$sql = 'delete from  wp_jsdb_sjmr where POST not in (SELECT ID from wp_jsdb_posts where post_type="post" and post_status="publish")';
				runsql($sql);
				$result = runsql("SELECT * from " . $table . ' order by CAST(POST AS UNSIGNED)  desc, TYPE asc');
				// writelog(__FILE__,__LINE__,$result);
				$a = array();
				if (false) {
					print(json_encode(array('error' => __FILE__ . " " . __LINE__ . " " . $result)));
					return;
				}
				if (!is_bool($result)) {
					// if (count($result) > 0) {
					foreach ($result as $row) {
						// writelog(__FILE__,__LINE__, $row["POST"]." ".$row["TYPE"]." ".$row["LINK"]);
						array_push($a, array($row["POST"], $row["TYPE"], $row["LINK"], $row["PERMALINK"]));
					}
					// writelog(__FILE__,__LINE__,json_encode($a));
					print(json_encode($a));
					// } else {
					// print(json_encode(array("messsage" => "no rows")));
					// }
				}
			} catch (Exception $e) {
				print(json_encode(array('error' => $e->getMessage())));
			}
		}
		if ($action == 'getpost') {
			try {
				$POST = $_GET["POST"];
				$result = runsql("SELECT * from " . $table . ' where POST='.$POST.' order by TYPE asc');
				// writelog(__FILE__,__LINE__,$result);
				$a = array();
				if (false) {
					print(json_encode(array('error' => __FILE__ . " " . __LINE__ . " " . $result)));
					return;
				}
				if (!is_bool($result)) {
					// if (count($result) > 0) {
					foreach ($result as $row) {
						// writelog(__FILE__,__LINE__, $row["POST"]." ".$row["TYPE"]." ".$row["LINK"]);
						array_push($a, array($row["POST"], $row["TYPE"], $row["LINK"], $row["PERMALINK"]));
					}
					// writelog(__FILE__,__LINE__,json_encode($a));
					print(json_encode($a));
					// } else {
					// print(json_encode(array("messsage" => "no rows")));
					// }
				}
			} catch (Exception $e) {
				print(json_encode(array('error' => $e->getMessage())));
			}
		}
		if ($action == 'save') {
			$action = $_GET["action"];
			$POST = $_GET["POST"];
			$TYPE = $_GET["TYPE"];
			$LINK = $_GET["LINK"];
			if(is_null($LINK) || strlen($LINK) == 0) {
				$sql = "UPDATE " . $table . " SET LINK=NULL WHERE POST='" . $POST . "' AND TYPE='" . $TYPE . "'";
			} else {
			$sql = "UPDATE " . $table . " SET LINK='" . $LINK . "' WHERE POST='" . $POST . "' AND TYPE='" . $TYPE . "'";
			}
			if (true) {
				$result = runsql($sql);
				print(json_encode(array(
					'sql' => $sql,
					'result' => $result
				)));
			} else {
				print(json_encode(array(
					'action' => $action,
					'POST' => $POST,
					'TYPE' => $TYPE,
					'LINK' => $LINK
				)));
			}
		}
		if ($action == 'delete') {
			$action = $_GET["action"];
			$POST = $_GET["POST"];
			$TYPE = $_GET["TYPE"];
			$sql = "UPDATE " . $table . " SET LINK=NULL WHERE POST='" . $POST . "' AND TYPE='" . $TYPE . "'";
			if (true) {
				$result = runsql($sql);
				// $result=false;
				print(json_encode(array(
					'sql' => $sql,
					'result' => $result
				)));
			} else {
				print(json_encode(array(
					'action' => $action,
					'POST' => $POST,
					'TYPE' => $TYPE,
					'LINK' => $LINK
				)));
			}
		}
	} catch (Exception $e) {
		print(json_encode(array('error' => $e->getTraceAsString())));
	}
} catch (Exception $e) {
	print(json_encode(array('exception' => $e->getTraceAsString())));
} catch (Error $e) {
	print(json_encode(array('error' => $e->getMessage())));
}
