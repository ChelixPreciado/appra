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
		
		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["title"] = utf8_decode(ucfirst(strtolower($value["title"])));
			$data[$key]["descr"] = utf8_decode(ucfirst(strtolower($value["descr"])));
		}
		
		return $data;
	}
}
