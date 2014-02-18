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
		$queryFilters = "";
		
		if(is_array($filters) and $filters[0] !== "") {
			foreach($filters as $filter) {
				$filter = explode("=", $filter);
				
				if(is_array($filter) and $filter[0] !== "") {
					$range = explode("&", $filter[1]);
					
					if(is_array($range) and isset($range[1]) and is_numeric($range[0]) and is_numeric($range[1])) {
						if($filter[0] == "amount")        $queryFilters .= " and amount>="    . $range[0] . " and amount<="    . $range[1];
						elseif($filter[0] == "area")      $queryFilters .= " and area>="      . $range[0] . " and area<="      . $range[1];
						elseif($filter[0] == "rooms")     $queryFilters .= " and rooms>="     . $range[0] . " and rooms<="     . $range[1];
						elseif($filter[0] == "bathrooms") $queryFilters .= " and bathrooms>=" . $range[0] . " and bathrooms<=" . $range[1];
						elseif($filter[0] == "parking")   $queryFilters .= " and parking>="   . $range[0] . " and parking<="   . $range[1];
					}
				}
			}
		}
		
		$query  = "SELECT id_record, lat, lon, address, amount, image_url, type, operation, area, rooms, bathrooms, parking from records ";
		$query .= "where st_contains(ST_MakeEnvelope($xmin,$ymin,$xmax,$ymax, 4326)";
		$query .= ", the_geom)" . $queryFilters . ";";
		
		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			if($data[$key]["area"]      === true) $data[$key]["area"]      = "1";
			if($data[$key]["rooms"]     === true) $data[$key]["rooms"]     = "1";
			if($data[$key]["bathrooms"] === true) $data[$key]["bathrooms"] = "1";
			if($data[$key]["parking"]   === true) $data[$key]["parking"]   = "1";
			
			if($data[$key]["type"] == true) {
				$data[$key]["type"] = "1";
			} elseif($data[$key]["type"] == 0) {
				$data[$key]["type"] = "0";
			}
			
			$data[$key]["address"] = utf8_decode($value["address"]);
		}
		
		return $data;
	}
	
	//Get Records with Draw Polygon [geojson var construct varchar] [Apartaments-Home] [Rent-Sell]
	public function getRecordsDraw($geojson, $filters = false) {
		$query  = "SELECT id_record, lat, lon, address, amount, image_url, type, operation, area, rooms, bathrooms, parking from records ";
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
	
	//Heatmap colors
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
	
	public function getByID($id_record = false) {
		$query  = "SELECT id_record, lat, lon, address, amount, image_url, type, operation, area, rooms, bathrooms, parking from records ";
		$query .= "where id_record=$id_record;";
		
		$data = $this->Db->query($query);
		
		if(!$data and !is_array($data)) return false;
		
		if($data[0]["area"]      === true) $data[0]["area"]      = "1";
		if($data[0]["rooms"]     === true) $data[0]["rooms"]     = "1";
		if($data[0]["bathrooms"] === true) $data[0]["bathrooms"] = "1";
		if($data[0]["parking"]   === true) $data[0]["parking"]   = "1";
		
		if($data[0]["type"] == true) {
			$data[0]["type"] = "1";
		} elseif($data[0]["type"] == 0) {
			$data[0]["type"] = "0";
		}
			
		$data[0]["address"] = utf8_decode($data[0]["address"]);
		
		return $data[0];
	}
	
	public function price() {
		$query = "SELECT gid, ST_AsGeoJson((ST_Dump(geom)).geom) as polygon, densidad from population_density;";
		$data  = $this->Db->query($query);
		
		foreach($data as $result) {
			die(var_dump($result));
		}
	}
}
