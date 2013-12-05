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
	
	//Get Records [Apartaments-Home] [Rent-Sell]
	public function getRecords($xmin, $ymin, $xmax, $ymax, $filters = false) {
		$query  = "SELECT id_record, lat, lon, address, amount, type, operation, fields from records ";
		$query .= "where st_contains(ST_MakeEnvelope($xmin,$ymin,$xmax,$ymax, 4326)";
		$query .= ", the_geom);";
		
		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			if($data[$key]["type"] == true) {
				$data[$key]["type"] = "1";
			} elseif($data[$key]["type"] == 0) {
				$data[$key]["type"] = "0";
			}
			
			$data[$key]["address"] = utf8_decode($value["address"]);
			$data[$key]["fields"]  = explode(",", utf8_decode($value["fields"]));
		}
		
		return $data;
	}
	
	//Heat Map Density
	public function getHeatMapDensity($xmin, $ymin, $xmax, $ymax) {
		$query  = "SELECT ST_AsGeoJson((ST_Dump(geom)).geom) as polygon, densidad from population_density where ST_Overlaps(ST_MakeEnvelope";
		$query .= "($xmin,$ymin,$xmax,$ymax, 4326), geom) or ST_Contains(ST_MakeEnvelope";
		$query .= "($xmin,$ymin,$xmax,$ymax, 4326), geom);";

		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		$geojson = '{';
		$geojson .='"type": "FeatureCollection",';
		$geojson .='"crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },';
		$geojson .='"features": [';
		
		foreach($data as $key=> $value) {
			$geojson .= '{ "type": "Feature", "properties": { "densidad": ' . $value["densidad"]. ' },';
			$geojson .= '"geometry": ' . $value["polygon"];
			$geojson .= '},';
		}
		
		$geojson  = rtrim($geojson, ",");
		$geojson .= ']';
		$geojson .=  '}';
	
		return $geojson;
	}
	
	//Default method - parameter table
	public function defaultQuery($xmin, $ymin, $xmax, $ymax, $table = "schools") {
		
		/*when we want id,title and desc
		$pKey = NULL;
		
		if($table == "schools")      	  $pKey = "school_id";
		elseif($table == "tianguis")      $pKey = "gid";
		elseif($table == "malls")    	  $pKey = "mall_id";
		elseif($table == "markets")  	  $pKey = "market_id";
		elseif($table == "restaurants")   $pKey = "restaurant_id";
		elseif($table == "fire_stations") $pKey = "fire_station_id";
		$query  = "SELECT $pKey, lat, lon, title, descr from $table ";
		*/
		
		$query  = "SELECT lat, lon from $table ";
		$query .= "where st_contains(ST_MakeEnvelope($xmin,$ymin,$xmax,$ymax, 4326)";
		$query .= ", the_geom) limit 100;";
		
		$data = $this->Db->query($query);
		
		/*when return title and desc in query
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["title"] = utf8_decode(ucfirst(strtolower($value["title"])));
			$data[$key]["descr"] = utf8_decode(ucfirst(strtolower($value["descr"])));
		}
		*/
		
		return $data;
	}
}
