<?php
/**
 * Access from index.php:
 */


class Api_Controller extends ZP_Controller {
	
	public function __construct() {
		$this->app("api");
		
		$this->Templates = $this->core("Templates");
		$this->Api_Model = $this->model("Api_Model");
		
		$this->Templates->theme();
		$this->layers = array("population", "price_rent", "price_sell", "fire_stations", "malls", "markets", "restaurants", "schools", "tianguis");
	}
	
	public function index() {
		$vars["view"] = $this->view("home", TRUE);
		
		$this->render("content", $vars);
	}
	
	//get single record by ID
	public function get($id_record = false) {
		$vars["result"] = false;
		
		if(is_numeric($id_record)) {
			$vars["result"] = $this->Api_Model->getByID($id_record);
		}
		
		echo json_encode($vars, JSON_NUMERIC_CHECK);
	}
	
	//get near results
	public function getNearResults($geom1 = false, $geom2 = false, $layers = false, $filters = false) {
		if($geom1 and $geom2) {
			$geom1   = explode(",", $geom1);
			$geom2   = explode(",", $geom2);
			$layers  = explode(",", $layers);
			$filters = explode(",", $filters);
			
			if(count($geom1) == 2 and count($geom2) == 2) {
				$vars["results"] = $this->Api_Model->getRecords($geom1[0], $geom1[1], $geom2[0], $geom2[1], $filters);
				
				if(is_array($layers) and $layers[0] !== "" and $layers[0] !== "false") {
					foreach($layers as $layer) {
						if($layer == "population" or $layer == "price_rent" or $layer == "price_sell") {
							$vars[$layer] = json_decode($this->Api_Model->getHeatMap($geom1[1], $geom1[0], $geom2[1], $geom2[0], $layer));
						} else {
							if(in_array($layer, $this->layers)) {
								$vars[$layer] = $this->Api_Model->defaultQuery($geom1[0], $geom1[1], $geom2[0], $geom2[1], $layer);
							} else {
								$vars[$layer] = false;
							}
						}
					}
				}
				
				echo json_encode($vars, JSON_NUMERIC_CHECK);
			}
		}
	}
	
	//get results with post parameter geometry [geojson] | layers
	public function getNearResultsDraw() {
		die(var_dump($_POST));
		
		if(isset($_POST["geometry"])) {
			$geometry    = $_POST["geometry"];
			$coordinates = $geometry["coordinates"][0];
			$geojson     = "ST_GeomFromText('POLYGON ((";
			
			$layers      = explode(",", $layers);
			die(var_dump($layers));
			
			if(is_array($coordinates)) {
				foreach($coordinates as $point) {
					$geojson .= $point[1] . " " . $point[0] . ",";
				}
				
				$geojson  = rtrim($geojson, ",");
				$geojson .= "))', 4326)";
				
				$vars["results"] = $this->Api_Model->getRecordsDraw($geojson);
				
				if(is_array($layers) and $layers[0] !== "") {
					foreach($layers as $layer) {
						if($layer == "population" or $layer == "price_rent" or $layer == "price_sell") {
							$vars[$layer] = json_decode($this->Api_Model->getHeatMapDraw($geojson, $layer));
						} else {
							if(in_array($layer, $this->layers)) {
								$vars[$layer] = $this->Api_Model->defaultQueryDraw($geojson, $layer);
							} else {
								$vars[$layer] = false;
							}
						}
					}
				}
				
				echo json_encode($vars, JSON_NUMERIC_CHECK);
			} else {
				echo false;
			}
		}
	}
}
