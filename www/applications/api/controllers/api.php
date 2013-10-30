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
			$geom1 = explode(",", $geom1);
			$geom2 = explode(",", $geom2);
			
			if(count($geom1) == 2 and count($geom2) == 2) {
				$vars["results"] = $this->Api_Model->getNearSchools($geom1[0], $geom1[1], $geom2[0], $geom2[1]);
				
				echo json_encode($vars);
			}
		}
	}
}
