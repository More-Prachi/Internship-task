<?php

	require "../dbConfig/dbconfiguration.php";		

		
	$vendor_id = $_GET['vendor_id'];
		
	$invoice_party_qry = "SELECT DISTINCT party_name, party_email, party_contact FROM `invoice` WHERE vendor_id = '$vendor_id'";
	$run_party_qry = mysqli_query($conn, $invoice_party_qry);
	$data = mysqli_fetch_all($run_party_qry, MYSQLI_ASSOC);

	if ($data) {
		$response = array(
			"status"=>true,
			"message"=>"Party fetched successfully.",
			"data"=> $data
		);
	} else {
		$response = array(
			"status"=>false,
			"message"=>"Something went wrong.",
		);
	}

	echo json_encode($response);
?>