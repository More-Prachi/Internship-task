<?php
	require "../dbConfig/dbconfiguration.php";

    $invoice_id = $_POST['invoice_id'];
    $vendor_id = $_POST['vendor_id'];
    $party_name = $_POST['party_name'];
    $party_email = $_POST['party_email'];
    $party_contact = $_POST['party_contact'];

    if (!empty($invoice_id) && !empty($vendor_id)) {
        if(!empty($party_name) && !empty($party_email) && !empty($party_contact)){
            $insert_party_qry = "INSERT INTO invoice (party_name, party_email, party_contact) VALUES ('$party_name', '$party_email', '$party_contact') WHERE invoice_id = '$invoice_id' AND vendor_id = '$vendor_id'";
            $run_insert_qry = mysqli_query($conn, $insert_party_qry);

            if($run_insert_qry){
                $response = array(
                    "status" => true,
                    "message" => "Party added successfully."
                );
            } else {
                $response = array(
                    "status" => false,
                    "message" => "Something went wrong."
                );
            }
        } else {
            $response = array(
                "status" => false,
                "message" => "Party name, email & contact required."
            );
        }
    } else {
        $response = array(
            "status" => false,
            "message" => "Invoice id & vendor id required."
        );
    }
    // SQL statement
    $qry = $conn->prepare("INSERT INTO invoice (party_name, party_email, party_contact) VALUES (?,?,?)");
    $qry->bind_param("sss", $party_name, $party_email, $party_contact);

    // Execute the statement
    if ($qry->execute()) {
        $response = array(
            'status' => 'success', 
            'message' => 'Party added successfully.'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Failed to add party: ' 
            );
    }

    echo json_encode($response);

?>