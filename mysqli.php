<?php
header('Content-Type: text/html; charset=utf-8');
class mysqlquery {
	protected $mac_1;
  protected $int_1;
	protected $a_param_type = array ("s");	// stores the bind parameter Types: s : String, i : Integer, d : Double, b : Blob
	protected $a_bind_params = array ("11:11:11:11:11:11");	// stores the bind parameter Values: basically there WHERE
	protected $db_s_1 = 'sql'; 	// db server dns name
	protected $db_su_1 = 'demoUser';	// db username
	protected $db_sp_1 = 'demoPassword';	// db password
	protected $db_sd_1 = 'MAB_TRACK';	// db database name
	protected $serchtype_1 = "Valid_Until";
	protected $searchterm_1;
	protected $query_1 = "SELECT Mac_ID, Valid_From, Valid_Until, Aca_ID, User_ID , State
				FROM aca_mab
				WHERE Valid_Until = ?";		// used for testing
	protected $query_2 = "SELECT Mac_ID, Valid_From, Valid_Until, Aca_ID, User_ID , State
				FROM aca_mab
				WHERE Valid_Until != ?";	// used for testing
	protected $query_3 = "SELECT am.Mac_ID, au.Fname, au.Lname, a.ACA_Name, a.ACA_Bname, am.Valid_From, am.State, amm.Action, am.Ticket
				FROM aca_mab as am
				JOIN aca_user as au
				USING (User_ID)
				JOIN aca as a
				ON a.Aca_ID = au.Aca_ID
				JOIN aca_mab_metadata as amm
				ON am.Mac_ID = amm.Mac_ID
				WHERE am.Valid_Until = ?
				ORDER BY am.Valid_From ASC";	// general lookup
	protected $query_4 = "SELECT amm.Mac_ID, amm.Note, am.Ticket, amm.Action
				FROM aca_mab_metadata as amm
				JOIN aca_mab as am
				USING (Mac_ID)
				WHERE am.Valid_Until = '1000-01-01 00:00:00' AND amm.Mac_ID = ?
				ORDER BY amm.Mac_ID ASC";	//search METADATA table DB by MAC Address
	protected $query_5 = "SELECT am.Mac_ID, au.Fname, au.Lname, a.ACA_Name, a.ACA_Bname, am.Valid_From, am.State, amm.Action, am.Ticket
				FROM aca_mab as am
				JOIN aca_user as au
				USING (User_ID)
				JOIN aca as a
				ON a.Aca_ID = au.Aca_ID
				JOIN aca_mab_metadata as amm
				ON am.Mac_ID = amm.Mac_ID
				WHERE am.Valid_Until = '1000-01-01 00:00:00' AND   CONCAT( Fname,  ' ', Lname ) LIKE  ?
				ORDER BY am.Valid_From ASC";	//search MAB table DB by First or Last name
	protected $query_6 = "SELECT am.Mac_ID, au.Fname, au.Lname, a.ACA_Name, a.ACA_Bname, am.Valid_From, am.State, amm.Action, am.Ticket
				FROM aca_mab as am
				JOIN aca_user as au
				USING (User_ID)
				JOIN aca as a
				ON a.Aca_ID = au.Aca_ID
				JOIN aca_mab_metadata as amm
				ON am.Mac_ID = amm.Mac_ID
				WHERE am.Valid_Until = '1000-01-01 00:00:00' AND  am.Mac_ID LIKE  ?
				ORDER BY am.Valid_From ASC";	// search MAB table DB by MAC Address and Returns sigle row
	protected $query_7 = "SELECT au.User_ID, au.Fname, au.Lname, a.Aca_ID, a.ACA_Name, a.ACA_Bname
			  FROM aca_user as au
			  JOIN aca as a
			  ON a.Aca_ID = au.Aca_ID
        WHERE CONCAT( Fname,  ' ', Lname ) = ?";	// detailed search user table by First or Last name
 protected $query_8 = "SELECT am.Mac_ID, au.Fname, au.Lname, a.ACA_Name, a.ACA_Bname, am.Valid_From, am.State, amm.Action, am.Ticket
				FROM aca_mab as am
				JOIN aca_user as au
				USING (User_ID)
				JOIN aca as a
				ON a.Aca_ID = au.Aca_ID
				JOIN aca_mab_metadata as amm
				ON am.Mac_ID = amm.Mac_ID
				WHERE am.Valid_Until = '1000-01-01 00:00:00' AND  am.Mac_ID =  ?
				ORDER BY am.Valid_From ASC";	// search MAB table DB by MAC Address and Returns sigle row
  protected $procedure_1 = "CALL add_mac (?, ?, ?, ?, ?)";	// adds new user to all needed tables
	protected $results;

  	function __construct($sqlQuery,$param_type, $param_bind) {
		 $this->a_param_type = $param_type;
		 $this->a_bind_params = $param_bind;
		 if ($sqlQuery == "query_1") {
		   $this->sqlquery($this->query_1);
	  } elseif ($sqlQuery == "query_2") {
		  $this->sqlquery($this->query_2);
	  } elseif ($sqlQuery == "query_3") {
		  $this->sqlquery($this->query_3);
	  } elseif ($sqlQuery == "query_4") {
		  $this->mac2int_1($this->a_bind_params[0]);
			$this->a_bind_params[0] = $this->int_1;	// adds formating needed for sql searches
		  $this->sqlquery($this->query_4);
	  } elseif ($sqlQuery == "query_5") {
			$this->a_bind_params[0] = '%' . $this->a_bind_params[0] . '%';	// adds formating needed for sql searches
		  $this->sqlquery($this->query_5);
	  } elseif ($sqlQuery == "query_6") {
			$this->mac2int_1($this->a_bind_params[0]);
			$this->a_bind_params[0] = '%' . $this->int_1 . '%';	// adds formating needed for sql searches
		  $this->sqlquery($this->query_6);
		} elseif ($sqlQuery == "procedure_1") {
			$this->mac2int_1($this->a_bind_params[3]);
			$this->a_bind_params[3] = $this->int_1;	// adds formating needed for sql searches
			$this->existCheck($this->procedure_1);
		} elseif ($function == "iseTicket_1") {
		  $this->iseTicket_1();
	  }

  }
	function existCheck($Query) {
		$temp_type = $this->a_param_type;	// copies type data to a temp array
		$temp_bind = $this->a_bind_params;	// copies bind data to a temp array
		$this->a_param_type = array($this->a_param_type[1]); // leaves only one "i" in the type array
		$this->a_bind_params = array($this->a_bind_params[3]);	// leaves the MAC in the params array
		$this->sqlquery($this->query_8);	// checks for MAC Address

		//echo "existCheck result " . $this->results;
		/*
		print "existCheck result " . $this->results[0]['State'] . "<br />";	// debug
		print "existCheck result " . $this->results[0]['Mac_ID'] . "<br />";	// debug
		print "existCheck result ";	// debug
		print_r($this->a_param_type);	// debug
		print "<br />";	// debug
		print "existCheck result ";	// debug
		print_r($this->a_bind_params);	// debug
		print "<br />";	// debug
		print "existCheck result ";	// debug
		print_r($temp_type);	// debug
		print "<br />";	// debug
		print "existCheck result ";	// debug
		print_r($temp_bind);	// debug
		print "<br />";	// debug
		*/
		if (isset($this->results[0]['Mac_ID']) && $this->results[0]['State'] ==  "PASSIVE") {
			/*
			print "existCheck result " . $this->results[0]['User_ID'] . "<br />";	// debug
			print "existCheck result " . $this->results[0]['Fname'] . " " . $this->results[0]['Lname'] . "<br />";
			*/
		} elseif (! isset($this->results[0]['Mac_ID'])) {
			$this->a_param_type = $temp_type;
			$this->a_bind_params = $temp_bind;
			/*
			print "existCheck result ";	// debug
			print_r($this->a_param_type);	// debug
			print "<br />";	// debug
			print "existCheck result ";	// debug
			print_r($this->a_bind_params);	// debug
			*/
			$this->sqlquery($this->procedure_1);
		}


	}
   function sqlquery($Query) {
	  //$this->searchterm_1 = $sqlWhere;



	  $db = new mysqli($this->db_s_1, $this->db_su_1, $this->db_sp_1, $this->db_sd_1);
 	  $stmt = $db->prepare($Query);

		$a_params = array();
		$param_type = '';
		$n = count($this->a_param_type);
		for($i = 0; $i < $n; $i++) {
			$param_type .= $this->a_param_type[$i];
		}
		$a_params[] = & $param_type;
		for($i = 0; $i < $n; $i++) {
			$a_params[] = & $this->a_bind_params[$i];
		}
		//print_r($a_params);	// debug
		call_user_func_array(array($stmt, 'bind_param'), $a_params);


	  //$stmt->bind_param('s', $this->searchterm_1);
	  $stmt->execute();
	  $stmt->store_result();	// store the result (to get properties)
	  $num_of_rows = $stmt->num_rows; // set the number of rows
	  $num_of_fields = $stmt->field_count;	// set the number of fields
	  //echo "NUMBER OF ROWS    " . $num_of_rows . "<br />";	// debug
	  //echo "NUMBER OF FIELDS    " . $num_of_fields . "<br />";	// debug
	  //$stmt->bind_result($id, $first_name, $last_name, $username, $a2 ,$a3 , $a4, $a5, $a6, $a7, $a11);	// Bind the result to variables
	  $x = 0;
	  $meta = $stmt->result_metadata();
	  $parameters = array();
	  while($field = $meta->fetch_field()) {
    		$parameters[] = &$row[$field->name];
	  }
	   //$stmt->bind_result(array_values($array));	// Bind the result to variables
	   call_user_func_array(array($stmt, 'bind_result'), $parameters);
	   while($stmt->fetch()) {
		   $x = array();
                   foreach($row as $key => $val ) {
                        // This next line isn't necessary for your project.
                        // It can be removed. I use it to ensure
                        // that the "excerpt" of the post doesn't end in the middle
                        // of a word.
                        if ( $key === 'excerpt') $val = $this->cleanExcerpt($row[$key]);

			   $x[$key] = $val;
		    }
                    $this->results[] = $x;

	   }
	   foreach($this->results as $i => $item) {
		   if ($this->results[$i]['Mac_ID']) {
			   $this->int2mac_1($this->results[$i]['Mac_ID']);
			   $this->mac_1 = str_split($this->mac_1, 2);
			   $this->mac_1 = implode(':', $this->mac_1);
			   $this->mac_1 = strtoupper($this->mac_1);
			   $this->results[$i]['Mac_ID'] = $this->mac_1;
		   }
	   }
	   //print_r ($this->results);
	   //return $results;
	   $stmt->free_result();
	   $stmt->close();
  }
  function __get($name){
	  return $this->$name;
  }	// used to get properties
  function __set($name,$value){
	  return $this->$name = $value;
  }	// used to set properties
  function mac2int_1($mac1) {
	  $this->int_1 = base_convert($mac1, 16, 10);
  }
  function int2mac_1($int_1) {
	  //echo "THIS WAS PASSED    " . $int_1; //	debug
	  $this->mac_1 =  base_convert($int_1, 10, 16);
	  //echo "CONVERTED TO BASE 16     " . $this->mac_1;	// debug

  }
  function stmt_bind_assoc (&$stmt, &$out) {
    $data = mysqli_stmt_result_metadata($stmt);
    $fields = array();
    $out = array();

    $fields[0] = $stmt;
    $count = 1;

    while($field = mysqli_fetch_field($data)) {
        $fields[$count] = &$out[$field->name];
        $count++;
    }
    call_user_func_array(mysqli_stmt_bind_result, $fields);
}
}

//$db = new mysqlquery("1000-01-01 00:00:0");
if (isset($_GET['sqlQuery']) & isset($_GET['sqlWhere'])) {
	$param = array($_GET['sqlWhere']);
	$param_type = array();
	$param_bind = array();
	foreach ($param as $x) {
		array_push($param_type, "s");
		array_push($param_bind, $_GET['sqlWhere']);
	}
	/*
	print_r($param_type);
	print "<br />";
	print_r($param_bind);
	print "<br />";
	*/
	$db = new mysqlquery($_GET['sqlQuery'], $param_type, $param_bind);	// sets class property
	//$db = new mysqlquery($_GET['sqlQuery'], $_GET['sqlWhere']);	// sets class property
	//print_r($db->results);	// debug
	echo json_encode($db->results);
}

if (isset($_GET['sqlQuery']) & isset($_GET['sqlFname']) & isset($_GET['sqlLname'])
															& isset($_GET['sqlMAC']) & isset($_GET['sqlIncedent'])
																& isset($_GET['sqlACA'])) {

	$param = array($_GET['sqlACA'], $_GET['sqlFname'], $_GET['sqlLname'], $_GET['sqlMAC'], $_GET['sqlIncedent']);
	$param_type = array();
	$param_bind = array();
	foreach ($param as $x) {
		if ($x == $param[0] || $x == $param[3]) {
			array_push($param_type, "i");
			array_push($param_bind, $x);
		} else {
			array_push($param_type, "s");
			array_push($param_bind, $x);
		}

	}
	/*
	print_r($param_type);
	print "<br />";
	print_r($param_bind);
	print "<br />";
	*/
	$db = new mysqlquery($_GET['sqlQuery'], $param_type, $param_bind);	// sets class property
	echo json_encode($db->results);
}
?>
