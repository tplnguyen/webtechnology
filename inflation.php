<?php
	// Import the CsvDatabase class
	require "CsvDatabase.php";
	
	// Script can now use json
	header('Content-type: application/json');
	
	//Creates a new database
	$database = new CsvDatabase("data/datalist.csv");
	$method = $_SERVER['REQUEST_METHOD'];
	
	
	switch ($method) {
		case 'GET':
			getData();
			break;
		case 'POST':
			postData();
		case 'PUT':
			putData();
			break;
		case 'DELETE':
			deleteData();
			break;
		}
	
	
	// Prints the contents				request: curl -X GET "http://www.few.vu.nl/~tnn720/inflation.php"
	function getData() {
		global $database;
		$year = $_GET['year'];
		$month = $_GET['month'];
		$data = $database->retrieve();
		
		if (count($data) == 0) {
			header('HTTP/1.0 404 Not Found');
			}
		else {
			$single_month = select_month($data, $year, $month);
			echo json_encode($single_month);
			}
	}
	
	
	// Adds an entry to the database		request: curl -i -X PUT -d '{"year":"2014","month":"November","hcip":"3.2","muicp":"2.2","eicp":"2.4"}' http://www.few.vu.nl/~tnn720/inflation.php
	function putData() {				
		global $database;
		$raw_input = file_get_contents("php://input");
		$json = json_decode($raw_input, true);
		$database->create_or_replace($json);
	}

	
	// Deletes an entry from the database		request: curl -i -X DELETE 'http://www.few.vu.nl/~tnn720/inflation.php' -d '{"year":"2014", "month":"November"}'
	function deleteData() {			
		global $database;
		$raw_input = file_get_contents("php://input");
		$delete = json_decode($raw_input, true);
		$year = $delete['year'];
		$month = $delete['month'];
		$database->delete($year, $month);
	}
	
	
	// Selects a single month from the array of all months
	function select_month($data, $year, $month) {
		$result = array();
		foreach ($data as $line) {
			if (matches($line['year'], $year) && matches($line['month'], $month)) {
				array_push($result, $line);
			}
		}
		return $result;
	}
	
	
	function matches($value, $pattern) {
		return ($pattern == "" || $pattern == $value);
	}
?>