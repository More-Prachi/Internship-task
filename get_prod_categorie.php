<?php

require "../dbConfig/dbconfiguration.php";
$section_id = $_GET['section_id'];
	
		if(isset($section_id)){
            $getCatSectionQry = "SELECT * from cat_section WHERE id = '$section_id' ";
			$runQry = mysqli_query($conn, $getCatSectionQry);
			$data = mysqli_fetch_assoc($runQry);
	
            if ($data){
	
				$getProdCategoryQry = "SELECT * FROM p_category WHERE section = '$section_id'";
				$runProdQry = mysqli_query($conn,$getProdCategoryQry);
				$data = mysqli_fetch_all($runProdQry,MYSQLI_ASSOC);
	
				if($data){
					$response = array(
						"status"=>true,
						"message"=>"Product categories fetched successfully.",
						"data"=>$data,
					);
				}
				else{
					$response = array(
						"status"=>false,
						"message"=>"No products in this section.",
					);
				}
			}
            else{
				$response = array(
					"status" => false,
					"message" => "Please enter valid section id."
				);
			}	
		}
		else{
			$getAllProdCatQry = "SELECT * from p_category";
			$runQry = mysqli_query($conn, $getAllProdCatQry);
			$data = mysqli_fetch_all($runQry, MYSQLI_ASSOC);
	
			if ($data) {
				$response = array(
					"status" => true,
					"message" => "Product categories fetched successfully.",
					"data" => $data,
				);
			}
			else{
			$response = array(
					"status"=>false,
					"message"=>"Something went wrong.",
				);
			}
		}
	
		echo json_encode($response);
?>