<?php


require "../dbConfig/dbconfiguration.php";

$section_id = $_GET['section_id'];

if ( isset( $section_id ) )
 {
    $getCategoryQry = "SELECT * from cat_section WHERE id = '$section_id' ";

    $runQry = mysqli_query( $conn, $getCategoryQry );
    $data = mysqli_fetch_all( $runQry, MYSQLI_ASSOC );

    if ( $data ) {
        $response = array(
            'status' => true,
            'message' =>'Category section fetched successfully.',
            'data' => $data,
        );
    } else {
        $response = array (
            'status' => false,
            'message' =>'Category section does not exist.',
        );
    }
}

else{
    $getAllCategoryQry = "SELECT * from cat_section";
    $runQry = mysqli_query( $conn, $getAllCategoryQry );
    $data = mysqli_fetch_all( $runQry, MYSQLI_ASSOC );

    if ($data) {
        $response = array(
            "status" => true,
					"message" => "Category section fetched successfully.",
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