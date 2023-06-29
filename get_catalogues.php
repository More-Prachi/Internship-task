<?php
	require "../dbConfig/dbConfiguration.php";

    $getCatalogueQry = "SELECT * from `catalogue` WHERE `active` = '1' ORDER BY `id` DESC";
    $runQry = mysqli_query( $conn, $getCatalogueQry );
    $data = mysqli_fetch_all( $runQry, MYSQLI_ASSOC );

    if ( $runQry ) {
        $response = array(
            'status'=>true,
            'message'=>'Catalogues fetched successfully.',
            'data'=> $data,
        );
    } else {
        $response = array(
            'status'=>false,
            'message'=>'Something went wrong.',
        );
    }
    echo json_encode( $response );
?>