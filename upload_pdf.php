<?php

    require "../dbConfig/dbConfiguration.php";
    // require "../dbConfig/adminValidationToken.php";

    // $val = checkToken($auth_token);
  
    // if($val['status']==1)
	// {
            $pdf_title = $_POST['pdf_title'];
            $pdf_desc = $_POST['pdf_desc'];
            $pdf_file = $_FILES['pdf_file'];

            if (!empty($pdf_title) && !empty($pdf_desc) && isset($_FILES['pdf_file'])) {

                $fileNameParts = explode('.', $_FILES['pdf_file']['name']);
                $filename = $pdf_title . '_' . time() . rand() . '.pdf';

                $valid_ext = array('pdf');

                $location = "catalogue_pdf/" . $filename;

                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);

                if ($_FILES['pdf_file']['type'] === 'application/pdf' && in_array($file_extension, $valid_ext)) {

                    if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $location)) {

                        $fileURL = 'http://localhost/versatile_db/apis/admin_api/catalogue_pdf/' . $filename;

                        $sql = "INSERT INTO `catalogue` (`pdf_url`, `pdf_title`, `pdf_desc`, `active`)
                                VALUES ('$fileURL', '$pdf_title', '$pdf_desc', '1')";
                        $run_sql_qry = mysqli_query($conn, $sql);

                        if ($run_sql_qry) {
                            $response = array(
                                "status" => true,
                                "message" => "PDF uploaded successfully."
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
                            "message" => "Something went wrong."
                        );
                    }
                } else {
                    $response = array(
                        "status" => false,
                        "message" => "Invalid file type. Only PDF files are allowed."
                    );
                }
            } else {
                $response = array(
                    "status" => false,
                    "message" => "Please provide required fields."
                );
            }
            echo json_encode($response);
    // }
?>