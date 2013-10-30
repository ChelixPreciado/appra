<?php
/**
 * Access from index.php:
 */
if(!defined("_access")) {
	die("Error: You don't have permission to access here...");
}

class Api_Model extends ZP_Model {
	
	public function __construct() {
		$this->Db = $this->db();
		
		$this->helpers();
	}
	
	
	public function getNearSchools($xmin, $ymin, $xmax, $ymax) {
		$query  = "SELECT lat, lon, title, descr from schools ";
		$query .= "where st_contains(ST_MakeEnvelope($xmin,$ymin,$xmax,$ymax, 4326)";
		$query .= ", the_geom);";
		
		$data  = $this->Db->query($query);
		
		die(var_dump($query));
		if(!$data) return false;
		
		die(var_dump($data));
		
		foreach($data as $key=> $value) {
			$stops = $this->getArray($value["stop_id"]);
			
			foreach($stops as $stopValue) {
				$stop 				   = $this->getStopsReport($stopValue);
				$data[$key]["stops"][] = $stop;	
			}
			
			$data[$key]["title"] 	 = utf8_decode($value["title"]);
			$data[$key]["descr"] 	 = utf8_decode($value["descr"]);
			$data[$key]["category"]  = utf8_decode($value["category"]);
			
			if($value["counter"] == 0) {
				$data[$key]["counter"] = "0";
			} elseif($value["counter"] == 1) {
				$data[$key]["counter"] = "1";
			}
			
			unset($data[$key]["stop_id"]);
		}
		
		return $data;
	}
}
