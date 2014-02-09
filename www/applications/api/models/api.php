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
	
	//Get Records with Draw Polygon [geojson var construct varchar] [Apartaments-Home] [Rent-Sell]
	public function getRecordsDraw($geojson, $filters = false) {
		$query  = "SELECT id_record, lat, lon, address, amount, type, operation, fields from records ";
		$query .= "where st_contains($geojson";
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
			$geojson .= '{ "type": "Feature", "properties": { "population": ' . $value["densidad"]. ', "color": "' . $this->getColorPopulation($value["densidad"]) . '" },';
			$geojson .= '"geometry": ' . $value["polygon"];
			$geojson .= '},';
		}
		
		$geojson  = rtrim($geojson, ",");
		$geojson .= ']';
		$geojson .=  '}';
	
		return $geojson;
	}
	
	//Heat Map Density Draw Polygon [geojson var construct varchar]
	public function getHeatMapDensityDraw($geojson) {
		$query  = "SELECT ST_AsGeoJson((ST_Dump(geom)).geom) as polygon, densidad from population_density where ST_Overlaps(";
		$query .= "$geojson, geom) or ST_Contains(";
		$query .= "$geojson, geom);";

		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		$geojson = '{';
		$geojson .='"type": "FeatureCollection",';
		$geojson .='"crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },';
		$geojson .='"features": [';
		
		foreach($data as $key=> $value) {
			$geojson .= '{ "type": "Feature", "properties": { "population": ' . $value["densidad"]. ', "color": "' . $this->getColorPopulation($value["densidad"]) . '" },';
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
		$query  = "SELECT lat, lon from $table ";
		$query .= "where st_contains(ST_MakeEnvelope($xmin,$ymin,$xmax,$ymax, 4326)";
		$query .= ", the_geom) limit 100;";
		
		$data = $this->Db->query($query);
		
		return $data;
	}
	
	//Default method Draw Polygon [geojson var construct varchar] - parameter table
	public function defaultQueryDraw($geojson, $table = "schools") {
		$query  = "SELECT lat, lon from $table ";
		$query .= "where st_contains($geojson";
		$query .= ", the_geom) limit 100;";
		
		$data = $this->Db->query($query);
		
		return $data;
	}
	
	public function getColorPopulation($population) {
		if($population > -1    and $population < 1000) return "#ffebd6";
		if($population > 999   and $population < 2000)  return  "#f5cbae";
		if($population > 1999  and $population < 5000)  return  "#eba988";
		if($population > 4999  and $population < 10000) return  "#e08465";
		if($population > 9999  and $population < 20000) return  "#d65d45";
		if($population > 19999 and $population < 30000) return  "#cc3527";
		if($population > 29999) return  "#c40a0a";
		
		return "#000";
	}
	
	
	/*Update fields in Database text->json*/
	public function fields() {
		$query   = "SELECT id_record, fields_clean from records;";
		$results = $this->Db->query($query);
		
		foreach($results as $result) {
			$array = explode(",", $result["fields_clean"]);
			$json  = '{';
				$json .= '"area" : ' . trim(str_replace(" m2 construcciÃ³n", "", $array[0])) . ',';
				$json .= '"rooms" : ' . trim(str_replace(" RecÃ¡maras", "", $array[1])) . ',';
				$json .= '"bathroom" : ' . trim(str_replace(" BaÃ±os", "", $array[2])) . ',';
				$json .= '"parking" : ' . trim(str_replace(" Estacionamientos", "", $array[3]));
			$json .= '}';
			
			die(var_dump($json));
		}
	}
}
