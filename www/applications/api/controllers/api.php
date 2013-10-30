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
	}
	
	public function index() {
		$vars["view"] = $this->view("home", TRUE);
		
		if(isset($_POST["text_search"]) and $_POST["text_search"] != "") {
			$text_search  = strtolower($_POST["text_search"]);
			$text_search  = str_replace(" ", "+", $text_search);
			$text_search  = removeAcute($text_search);
			
			$vars["search"]   = true;
			$vars["metrobus"] = $this->Api_Model->getStopsBySearchByAgency("MB", $text_search);
			$vars["metro"] 	  = $this->Api_Model->getStopsBySearchByAgency("METRO", $text_search);
			$vars["rtp"] 	  = $this->Api_Model->getStopsBySearchByAgency("RTP", $text_search);
			$vars["ste"] 	  = $this->Api_Model->getStopsBySearchByAgency("STE", $text_search);
			$vars["sub"] 	  = $this->Api_Model->getStopsBySearchByAgency("SUB", $text_search);
		} else {
			$vars["search"]   = false;
			$vars["metrobus"] = $this->Api_Model->getStopsByAgency("MB");
			$vars["metro"] 	  = $this->Api_Model->getStopsByAgency("METRO");
			$vars["rtp"] 	  = $this->Api_Model->getStopsByAgency("RTP");
			$vars["ste"] 	  = $this->Api_Model->getStopsByAgency("STE");
			$vars["sub"] 	  = $this->Api_Model->getStopsByAgency("SUB");
		}

		$this->render("content", $vars);
	}
	
	/*Reports*/
	public function addReport() {
		$data = $_POST;
		
		if(isset($data["new"])) {
			unset($data["new"]);
			
			$vars["report"] = $this->Api_Model->addReport($data);
		} elseif(isset($data["image_url"]) and isset($data["report_id"]) and is_numeric($data["report_id"])) {
			$vars["report"] = $this->Api_Model->editReport($data["report_id"], $data);
		} else {
			$vars["error"] = "Error en el request";
		}
		
		echo json_encode($vars);
	}
	
	public function report($idReport = false) {
		if($idReport and is_numeric($idReport)) {
			$vars["report"] = $this->Api_Model->getReport($idReport);
		} else {
			$vars["report"] = false;
		}
		
		echo json_encode($vars);
	}
	
	public function reports($offset = 0) {
		if(!is_numeric($offset)) {
			$offset = 0;
		}
		
		$vars["reports"] = $this->Api_Model->getReports($offset);
		
		echo json_encode($vars);
	}
	
	public function gallery($offset = 0) {
		if(!is_numeric($offset)) {
			$offset = 0;
		}
		
		$vars["reports"] = $this->Api_Model->getReportsGallery($offset);
		
		echo json_encode($vars);
	}
	
	public function map() {
		$vars["reports"] = $this->Api_Model->getMapReport();
		
		echo json_encode($vars);
	}
	
	public function likeReport($idReport = false) {
		if(is_numeric($idReport)) {
			if($idReport) {
				$vars["report"] = $this->Api_Model->likeReport($idReport);
			} else {
				$vars["report"] = false;
			}
		} else {
			$vars["report"] = false;
		}
		
		echo json_encode($vars);
	}
	
	public function abuseReport($idReport = false) {
		if(is_numeric($idReport)) {
			if($idReport) {
				$vars["abuse"] = $this->Api_Model->abuseReport($idReport);
			} else {
				$vars["abuse"] = false;
			}
		} else {
			$vars["abuse"] = false;
		}
		
		echo json_encode($vars);
	}
	
	public function categories() {
		$vars["categories"] = $this->Api_Model->getCategories();
		
		echo json_encode($vars);
	}
	
	/*Search near stop lon,lat*/	
	public function near($ll = "") {
		if($ll !== "") {
			$array = explode("," , $ll);
			
			if(count($array) == 2) {
				$stops = $this->Api_Model->getNearStops($array[0], $array[1]);
				
				if(!$stops) {
					$vars["stops"] = false;
				} else {				
					$vars = $stops;
				}
			} else {
				$vars["stops"] = false;
			}
		} else {
			$vars["stops"] = false;
		}
		
		echo json_encode($vars);
	}
	
	/*Search near stop lon,lat*/
	public function trip($orig = "", $dest = "") {
		if($orig !== "" and $dest !== "") {
			$array_orig = explode("," , $orig);
			$array_dest = explode("," , $dest);
			
			if(count($array_orig) == 2 and count($array_dest) == 2) {
				$stops_orig = $this->Api_Model->getNearStops($array_orig[0], $array_orig[1], 1);
				$stops_dest = $this->Api_Model->getNearStops($array_dest[0], $array_dest[1], 1);
				
				if(!$stops_orig and !$stops_dest) {
					$vars["stops"] = false;
				} else {				
					$vars["orig"] = $stops_orig;
					$vars["dest"] = $stops_dest;
				}
			} else {
				$vars["stops"] = false;
			}
		} else {
			$vars["stops"] = false;
		}
		
		echo json_encode($vars);
	}
	
	/*Agencies*/
	public function agencies() {
		$vars["agencies"] = $this->Api_Model->getAgencies();
		
		echo json_encode($vars);
	}
	
	public function agency($idAgency) {
		$vars["agency"] = $this->Api_Model->getAgency($idAgency);
		
		echo json_encode($vars);
	}
	
	
	/*Routes*/
	public function routes($idAgency) {
		$vars["agency"] = $this->Api_Model->getAgency($idAgency);
		$vars["routes"] = $this->Api_Model->getRoutes($idAgency);
		
		echo json_encode($vars);
	}
	
	public function route($idRoute) {
		$vars["route"]  = $this->Api_Model->getRoute($idRoute);
		$vars["agency"] = $this->Api_Model->getAgency($vars["route"][0]["agency_id"]);
		
		echo json_encode($vars);
	}
	
	
	
	/*Stops*/
	public function stops($idRoute) {
		$vars["route"]  = $this->Api_Model->getRoute($idRoute);
		$vars["stops"]  = $this->Api_Model->getStops($idRoute);
		$vars["agency"] = $this->Api_Model->getAgency($vars["route"][0]["agency_id"]);
		
		echo json_encode($vars);
	}
	
	public function stop($idStop) {
		$vars["stop"]   = $this->Api_Model->getStop($idStop);
		$vars["route"]  = $this->Api_Model->getRoute($vars["stop"][0]["route_id"]);
		$vars["agency"] = $this->Api_Model->getAgency($vars["route"][0]["agency_id"]);
		
		echo json_encode($vars);
	}
	
	public function stopsByAgency($idAgency) {
		$vars["agency"] = $this->Api_Model->getAgency($idAgency);
		$vars["routes"] = $this->Api_Model->getStopsByAgency($idAgency);
		
		echo json_encode($vars);
	}
	
	public function search($text_search = "") {
		if($text_search !== "") {
			$text  = urldecode($text_search);
			$text  = strtolower($text);
			$text  = str_replace(" ", "+", $text);
			$text  = removeAcute($text);				
			$stops = $this->Api_Model->getStopsBySearch($text);
			
			if(!$stops) {
				$vars["stops"] = false;
			} else {				
				$vars = $stops;
			}
		} else {
			$vars["stops"] = false;
		}
		
		echo json_encode($vars);
	}
}
