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
		
		$this->render("content", $vars);
	}
	
	public function getNearSchools($geom1 = false, $geom2 = false) {
		if($geom1 and $geom2) {
			$explode1 = explode(",", $geom1);
			$explode2 = explode(",", $geom2);
			
		
			
			if(count($explode1) == 2 and count($explode2) == 2) {
					die(var_dump($explode1[0]));
				$vars["agency"] = $this->Api_Model->getAgency($idAgency);
				$vars["routes"] = $this->Api_Model->getStopsByAgency($idAgency);
				
				echo json_encode($vars);
			}
		}
		
	}
}
