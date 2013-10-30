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
	
	
	/*Agencies*/
	public function getAgencies() {
		$query = "select * from agency";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["agency_name"] = utf8_decode($value["agency_name"]);
		}
		
		return $data;
	}
	
	public function getAgency($idAgency) {
		$query = "select * from agency where agency_id='" . $idAgency . "'";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["agency_name"] = utf8_decode($value["agency_name"]);
		}
		
		return $data;
	}
		
	
	
	/*Routes*/
	public function getRoutes($idAgency) {
		$query = "select * from routes where agency_id='" . $idAgency . "'";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["route_short_name"] = utf8_decode($value["route_short_name"]);
			$data[$key]["route_long_name"]  = utf8_decode($value["route_long_name"]);
			$data[$key]["route_desc"] 	    = utf8_decode($value["route_desc"]);
		}
		
		return $data;
	}
	
	public function getRoute($idRoute) {
		$query = "select * from routes where route_id='" . $idRoute . "'";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["route_short_name"] = utf8_decode($value["route_short_name"]);
			$data[$key]["route_long_name"]  = utf8_decode($value["route_long_name"]);
			$data[$key]["route_desc"] 	    = utf8_decode($value["route_desc"]);
		}
		
		return $data;
	}
	
	
	
	/*Stops*/
	public function getNearStops($lat, $lon, $limit = 5) {		
		$query  = "select  stops.*, to_stop_id,  ST_Distance(the_geom, (ST_GeomFromText('POINT(' || Cast('" . $lon;
		$query .= "' AS REAL) || ' ' || Cast('" . $lat;
		$query .= "' AS REAL) || ')', 4326))) as distance FROM stops";
		$query .= " left join transfers on stop_id=from_stop_id order by distance asc limit " . $limit;
		
		$stops = $this->Db->query($query);

		if(!$stops) return false;
		
		foreach($stops as $key=> $value) {
			unset($stops[$key]["textsearch"]);
			unset($stops[$key]["the_geom"]);
			$stops[$key]["stop_name"] = utf8_decode($value["stop_name"]);
			$stops[$key]["stop_desc"] = utf8_decode($value["stop_desc"]);
		}
		
		foreach($stops as $key => $stop) {
			$route  = $this->getRoute($stop["route_id"]);
			$agency = $this->getAgency($route[0]["agency_id"]);
			
			$data["stops"][$key]["route"]  = $route[0];
			$data["stops"][$key]["agency"] = $agency[0];
			$data["stops"][$key]["stop"]   = $stop;
		}
		
		return $data;
	}
	
	public function getStops($idRoute) {
		$query = "select stops.*,to_stop_id from stops left join transfers on stop_id=from_stop_id where route_id='" . $idRoute . "'";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			unset($data[$key]["textsearch"]);
			unset($data[$key]["the_geom"]);
			$data[$key]["stop_name"] = utf8_decode($value["stop_name"]);
			$data[$key]["stop_desc"] = utf8_decode($value["stop_desc"]);
		}
		
		return $data;
	}
	
	public function getStop($idStop) {
		$query = "select * from stops where stop_id='" . $idStop . "'";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			unset($data[$key]["textsearch"]);
			unset($data[$key]["the_geom"]);
			$data[$key]["stop_name"] = utf8_decode($value["stop_name"]);
			$data[$key]["stop_desc"] = utf8_decode($value["stop_desc"]);
		}
		
		return $data;
	}
	
	public function getStopsBySearchByAgency($idAgency, $text) {
		$query = "select * from routes where agency_id='" . $idAgency . "'";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["route_short_name"] = utf8_decode($value["route_short_name"]);
			$data[$key]["route_long_name"]  = utf8_decode($value["route_long_name"]);
			$data[$key]["route_desc"] 	    = utf8_decode($value["route_desc"]);
		}
		
		$stopsb = true;
		
		foreach($data as $key => $result) {
			$query = "select stops.*,to_stop_id from stops left join transfers on stop_id=from_stop_id where route_id='" . $result["route_id"] . "' and to_tsquery('" . $text . "') @@ textsearch";
			$stops = $this->Db->query($query);
			
			if(is_array($stops)) {
				$stopsb = false;
				
				foreach($stops as $key2 => $value) {
					unset($stops[$key2]["textsearch"]);
					unset($stops[$key2]["the_geom"]);
					$stops[$key2]["stop_name"] = utf8_decode($value["stop_name"]);
					$stops[$key2]["stop_desc"] = utf8_decode($value["stop_desc"]);
				}
			
				$data[$key]["stops"] = $stops;
			} else {
				unset($data[$key]);
			}
		}
		
		if($stopsb) return false;
		
		return $data;
	}
	
	public function getStopsByAgency($idAgency) {
		$query = "select * from routes where agency_id='" . $idAgency . "'";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$data[$key]["route_short_name"] = utf8_decode($value["route_short_name"]);
			$data[$key]["route_long_name"]  = utf8_decode($value["route_long_name"]);
			$data[$key]["route_desc"] 	    = utf8_decode($value["route_desc"]);
		}
		
		foreach($data as $key => $result) {
			$query = "select stops.*,to_stop_id from stops left join transfers on stop_id=from_stop_id where route_id='" . $result["route_id"] . "'";
			$stops = $this->Db->query($query);
			
			foreach($stops as $key2 => $value) {
				unset($stops[$key2]["textsearch"]);
				unset($stops[$key2]["the_geom"]);
				$stops[$key2]["stop_name"] = utf8_decode($value["stop_name"]);
				$stops[$key2]["stop_desc"] = utf8_decode($value["stop_desc"]);
			}
		
			$data[$key]["stops"] = $stops;
		}
		
		return $data;
	}
	
	public function getStopsBySearch($text) {
		$query = "select stops.*,to_stop_id from stops left join transfers on stop_id=from_stop_id where to_tsquery('" . $text . "') @@ textsearch";
		$stops = $this->Db->query($query);
		
		if(!$stops) return false;
		
		foreach($stops as $key=> $value) {
			unset($stops[$key]["textsearch"]);
			unset($stops[$key]["the_geom"]);
			$stops[$key]["stop_name"] = utf8_decode($value["stop_name"]);
			$stops[$key]["stop_desc"] = utf8_decode($value["stop_desc"]);
		}
		
		foreach($stops as $key => $stop) {
			$route  = $this->getRoute($stop["route_id"]);
			$agency = $this->getAgency($route[0]["agency_id"]);
			
			$data["stops"][$key]["route"]  = $route[0];
			$data["stops"][$key]["agency"] = $agency[0];
			$data["stops"][$key]["stop"]   = $stop;
		}
		
		return $data;
	}
	
	public function getStopsReport($idStop) {
		$query = "select * from stops where stop_id='" . $idStop . "'";
		$stops = $this->Db->query($query);
		
		if(!$stops) return false;
		
		foreach($stops as $key=> $value) {
			unset($stops[$key]["textsearch"]);
			unset($stops[$key]["the_geom"]);
			$stops[$key]["stop_name"] = utf8_decode($value["stop_name"]);
			$stops[$key]["stop_desc"] = utf8_decode($value["stop_desc"]);
		}
		
		foreach($stops as $key => $stop) {
			$route  = $this->getRoute($stop["route_id"]);
			$agency = $this->getAgency($route[0]["agency_id"]);
			
			$data["stops"][$key]["route"]  = $route[0];
			$data["stops"][$key]["agency"] = $agency[0];
			$data["stops"][$key]["stop"]   = $stop;
		}
		
		return $data;
	}
	
	/*Reports*/
	public function addReport($data = false) {
		if($data and is_array($data)) {
			//Upload image => array postgres
			$this->Files = $this->core("Files");
			$upload = $this->Files->uploadImage("www/lib/uploads/images/", "file", "normal");
			
			if($upload) {
				$data["image_url"] = "{" . $upload . "}";
			} else {
				$data["image_url"] = "{www/lib/uploads/images/default.png}";
			}
			
			//set date & time
			$date = date("Y-m-d H:i:s", time());
			$time = date("H:i:s", time());
			
			//array stops to postgres and query to search similar reports
			$idStop = "{";
			$where  = "where";
			
			if(is_array($data["stop_id"])) {
				foreach($data["stop_id"] as $value) {
					$idStop .= $value . ",";
					$where  .= " '" . $value . "' = ANY(stop_id) or";
				}
			} else {
				$idStop .= $data["stop_id"] . ",";
				$where  .= " '" . $data["stop_id"] . "' = ANY(stop_id) or";
			}
			
			unset($data["stop_id"]);
			$idStop = rtrim($idStop, ',');
			$idStop = $idStop . "}";
			
			$where  = rtrim($where, 'or');
			$where .= " and last_modified_date=CAST('" . $date . "' AS DATE) and";
			$where .= " EXTRACT(HOURS FROM (CAST('" . $time . "' as time) - last_modified_time)) < 2";
			
			//Search similar reports in stops
			$result = $this->getReportByStop($where);
			
			if($result) {
				$array["similar"] = $result;
				
				if($upload) {
					$array["image_url"] = $upload;
				}
				
				return $array;
			}
			
			//data to insert
			$data["stop_id"]            = $idStop;
			$data["report_date"]        = "CAST('" . $date . "' AS DATE)";
			$data["report_time"]        = "CAST('" . $time . "' AS TIME)";
			$data["last_modified_date"] = "CAST('" . $date . "' AS DATE)";
			$data["last_modified_time"] = "CAST('" . $time . "' AS TIME)";
			$data["report_textdate"]    = decode(now(2));
			
			$result = $this->Db->insert("reports", $data, "report_id");
			
			return $result;
		} else {
			return false;
		}
	}
	
	public function editReport($idReport, $data = false) {
		if($data and is_array($data) and isset($data["image_url"])) {
			$date  = "CAST('" . date("Y-m-d H:i:s", time()) . "' AS DATE)";
			$time  = "CAST('" . date("H:i:s", time()) . "' AS TIME)";
			$query = "update reports set last_modified_date=" . $date . ", last_modified_time=" .$time .", image_url=array_append(image_url,'" . $data["image_url"] . "') where report_id=" . $idReport;
			$data  = $this->Db->query($query);
			
			return $idReport;
		} else {
			return false;
		}
	}
	
	public function getReport($idReport) {
		$query = "select reports.*, categories.name as category from reports left join categories on categories.category_id=reports.category_id  where report_id=" . $idReport . " and reports.status=true order by report_id desc";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$stops = $this->getArray($value["stop_id"]);
			
			foreach($stops as $stopValue) {
				$stop 				   = $this->getStopsReport($stopValue);
				$data[$key]["stops"][] = $stop;	
			}
			
			$data[$key]["title"]     = utf8_decode($value["title"]);
			$data[$key]["descr"]     = utf8_decode($value["descr"]);
			$data[$key]["image_url"] = $this->getArray($value["image_url"]);
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
	
	public function getReports($offset = 0, $limit = 20) {
		if($offset==0) {
			$query = "select reports.*, categories.name as category from reports left join categories on categories.category_id=reports.category_id where reports.status=true order by report_id desc limit " . $limit;
		} else {
			$query = "select reports.*, categories.name as category from reports left join categories on categories.category_id=reports.category_id where reports.status=true order by report_id desc limit " . $limit . " offset " . $offset;
		} 
		
		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {			
			$data[$key]["title"] 	 = utf8_decode($value["title"]);
			$data[$key]["descr"]	 = utf8_decode($value["descr"]);
			$data[$key]["image_url"] = $this->getArray($value["image_url"]);
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
	
	public function getReportByStop($where) {
		$query = "select reports.*, categories.name as category from reports left join categories on categories.category_id=reports.category_id  " . $where ." and reports.status=true order by report_id desc";
		$data  = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$stops = $this->getArray($value["stop_id"]);
			
			foreach($stops as $stopValue) {
				$stop 				   = $this->getStopsReport($stopValue);
				$data[$key]["stops"][] = $stop;	
			}
			
			$data[$key]["title"] 	 = utf8_decode($value["title"]);
			$data[$key]["descr"] 	 = utf8_decode($value["descr"]);
			$data[$key]["category"]  = utf8_decode($value["category"]);
			$data[$key]["image_url"] = $this->getArray($value["image_url"]);
			
			if($value["counter"] == 0) {
				$data[$key]["counter"] = "0";
			} elseif($value["counter"] == 1) {
				$data[$key]["counter"] = "1";
			}
			
			unset($data[$key]["stop_id"]);
		}
		
		return $data;
	}
	
	public function getStopsGeometry($idStop) {
		$query = "select stop_lat, stop_lon from stops where stop_id='" . $idStop . "'";
		$stop  = $this->Db->query($query);
		
		if(!$stop) {
			return false;
		} else {
			return $stop[0];
		}
	}
	
	public function getMapReport($limit = 100) {
		$query = "select report_id, reports.category_id, stop_id, categories.name as category from reports left join categories on categories.category_id=reports.category_id where reports.status=true order by report_id desc limit " . $limit;
		
		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {
			$stops = $this->getArray($value["stop_id"]);
			
			foreach($stops as $stopValue) {
				$stop 				   = $this->getStopsGeometry($stopValue);
				$data[$key]["stops"][] = $stop;	
			}
			
			if($value["category_id"] == 0) {
				$data[$key]["category_id"] = "0";
			} elseif($value["category_id"] == 1) {
				$data[$key]["category_id"] = "1";
			}
			
			unset($data[$key]["stop_id"]);
			
			$data[$key]["category"] = utf8_decode($value["category"]);
		}
		
		return $data;
	}
	
	public function getReportsGallery($offset = 0, $limit = 20) {
		if($offset==0) {
			$query = "select report_id, image_url[array_length(image_url, 1)], categories.name as category from reports left join categories on categories.category_id=reports.category_id where reports.status=true order by report_id desc limit " . $limit;
		} else {
			$query = "select report_id, image_url[array_length(image_url, 1)], categories.name as category from reports left join categories on categories.category_id=reports.category_id where reports.status=true order by report_id desc limit " . $limit . " offset " . $offset;
		} 
		
		$data = $this->Db->query($query);
		
		if(!$data) return false;
		
		foreach($data as $key=> $value) {			
			$data[$key]["category"] = utf8_decode($value["category"]);
		}
		
		return $data;
	}
	
	public function likeReport($idReport) {
		$query = "update reports set counter=(counter+1) where report_id=" . $idReport . " and status=true";
		$data  = $this->Db->query($query);
		
		return $idReport;
	}
	
	public function abuseReport($idReport) {
		//set date & time
		$date = date("Y-m-d H:i:s", time());
		$time = date("H:i:s", time());
			
		$data["report_id"]   = $idReport;	
		$data["report_date"] = "CAST('" . $date . "' AS DATE)";
		$data["report_time"] = "CAST('" . $time . "' AS TIME)";
		
		$result = $this->Db->insert("abuse", $data, "abuse_id");
		
		if($result) {
			$query = "update reports set counter=(counter-1) where report_id=" . $idReport . " and status=true";
			$data  = $this->Db->query($query);
			
			$query = "select counter from reports where report_id=" . $idReport;
			$data  = $this->Db->query($query);
			
			if($data[0]["counter"] < -5) {
				$query = "update reports set status=false where report_id=" . $idReport;
				$data  = $this->Db->query($query);
			}
			
			return $result;
		} else {
			return false;
		}
		
	}
	
	public function getCategories() {
		$query = "select * from  categories where type='report'";
		$data  = $this->Db->query($query);
		
		foreach($data as $key=> $value) {
			$data[$key]["name"] = utf8_decode($value["name"]);
		}
		
		return $data;
	}
	
	/*String methods*/
	public function getArray($string = "") {
		$string = str_replace("{", "", $string);
		$string = str_replace("}", "", $string);
		$string = explode(",", $string);
		
		return $string;
	}
}
