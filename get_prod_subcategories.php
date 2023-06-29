<?php

	require "../dbConfig/dbconfiguration.php";
	
		$category_id = $_GET['category_id'];
	
		if(isset($category_id)){
			$getCategoryQry = "SELECT * from p_category WHERE id = '$category_id' ";
			$runQry = mysqli_query($conn, $getCategoryQry);
			$data = mysqli_fetch_assoc($runQry);
	
			if ($data){
	
				$getProdSubCategoryQry = "SELECT * FROM p_subcategory WHERE cid = '$category_id'";
				$runProdQry = mysqli_query($conn,$getProdSubCategoryQry);
				$data = mysqli_fetch_all($runProdQry,MYSQLI_ASSOC);
	
				if($data){
					$response = array(
						"status"=>true,
						"message"=>"Product subcategories fetched successfully.",
						"data"=>$data,
					);
				}
				else{
					$response = array(
						"status"=>false,
						"message"=>"No products in this category.",
					);
				}
			}
			else{
				$response = array(
					"status" => false,
					"message" => "Please enter valid category id."
				);
			}	
		}
		else{
			$getAllProdCatQry = "SELECT * from p_subcategory";
			$runQry = mysqli_query($conn, $getAllProdCatQry);
			$data = mysqli_fetch_all($runQry, MYSQLI_ASSOC);
	
			if ($data) {
				$response = array(
					"status" => true,
					"message" => "Product subcategories fetched successfully.",
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