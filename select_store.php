<?php
require '../dbConfig/dbconfiguration.php';
// require '../dbConfig/adminValidationToken.php';

// $val = checkToken($auth_token);
// if ($val['status'] == 1) {

    $vendor_id = $_GET['vendor_id'];
    
    if(!empty($vendor_id)){
        $getstoreQry = "SELECT * FROM store WHERE vendor_id = '$vendor_id' ";
        $runQry = mysqli_query($conn, $getstoreQry);
        $data = mysqli_fetch_all($runQry, MYSQLI_ASSOC);

        if ($data) {
            $response = array(
                'status' => true,
                'message' => 'store fetched sucessfully.',
                'data' => $data
                
            );
        }else{
            $response = array(
                'status' => true,
                'data' => $data
            );
        }
    }else{
        $response = array(
            'status' => false,
            'message' => 'Vendor id required.',
        );
    }

     
      echo json_encode($response);
// }
?>