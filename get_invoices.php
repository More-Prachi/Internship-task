<?php

	require "../dbConfig/dbconfiguration.php";
	

        define('ITEMS_PER_PAGE', 20);
	    $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? (int) $_GET['page'] : 1;
	    $offset = ($page - 1) * ITEMS_PER_PAGE;

		$vendor_id = $_GET['vendor_id'];
		$invoices = [];

        $total_invoices_count = "SELECT COUNT(id) FROM invoice WHERE vendor_id = '$vendor_id'";
        $run_total_invoices = mysqli_query($conn, $total_invoices_count);
        $total_invoices = mysqli_fetch_assoc($run_total_invoices);

        $total_order_qry = "SELECT SUM(order_total) as total_order FROM orders WHERE vendor_id = '$vendor_id'";
        $run_total_qry = mysqli_query($conn, $total_order_qry);
        $total_orders = mysqli_fetch_assoc($run_total_qry);

        $store_visit_qry = 0;

        $orders_delivered_qry = "SELECT COUNT(id) AS Completed_Orders FROM `orders` WHERE vendor_id = '$vendor_id' AND order_status = '4'";
        $run_orders_qry = mysqli_query($conn, $orders_delivered_qry);
        $completed_orders_result = mysqli_fetch_assoc($run_orders_qry);
        $completed_orders_count = $completed_orders_result['Completed_Orders'];

        $orders_return_qry = "SELECT COUNT(id) AS Cancelled_Orders FROM `orders` WHERE vendor_id = '$vendor_id' AND order_status = '5'";
        $run_cancelled_orders = mysqli_query($conn, $orders_return_qry);
        $cancelled_orders_result = mysqli_fetch_assoc($run_cancelled_orders);
        $cancelled_orders_count = $cancelled_orders_result['Cancelled_Orders'];

		$get_invoice_qry = "SELECT i.*, s.store_name, s.id as store_id, s.store_image FROM `invoice` AS i
							JOIN store AS s ON s.id=i.store_id WHERE i.vendor_id = '$vendor_id' AND i.active='1'
							ORDER BY i.id DESC LIMIT $offset, " . ITEMS_PER_PAGE;
		$runQry = mysqli_query($conn, $get_invoice_qry);
		$total_invoices = mysqli_num_rows($runQry);

		$total_amt = 0;
		$total_taxable_value = 0;
		$total_igst = 0;


			while ($data = mysqli_fetch_assoc($runQry)) {
				$invoice_id = $data['id'];
				$invoice_product_qry = "SELECT * FROM `invoice_product` WHERE `invoice_id`='$invoice_id' AND `active`='1'";
				$run_product_qry = mysqli_query($conn, $invoice_product_qry);
				$products = mysqli_fetch_all($run_product_qry, MYSQLI_ASSOC);
			
				$data['products'] = array();
			
				foreach ($products as $product) {
					$product_id = $product['product_id'];
					$product_attributes_qry = "SELECT * FROM `product_attr` WHERE `pid`='$product_id' AND `active`='1'";
					$run_product_attributes_qry = mysqli_query($conn, $product_attributes_qry);
					$product_attributes = mysqli_fetch_all($run_product_attributes_qry, MYSQLI_ASSOC);
			
					$product['attributes'] = $product_attributes;
					$data['products'][] = $product;
				}
			
				$total_amt = $total_amt + $data['net_total'];
				$total_taxable_value = $total_taxable_value + ($data['net_total'] - $data['gst']);
				$total_igst = $total_igst + $data['gst'];
			
				array_push($invoices, $data);
			}			

            $total_pages = ceil($conn->query("SELECT COUNT(*) FROM invoice WHERE vendor_id = '$vendor_id' AND active = '1'")->fetch_row()[0] / ITEMS_PER_PAGE);

			if ($runQry) {
				$response = array(
					"status"=>true,
					"message"=>"Invoices fetched successfully.",
					"data"=>array(
                        "cards_data" => array(
                            "total_invoices" => $total_invoices,
                            "total_orders_amount" => $total_orders["total_order"],
                            "store_visit" => $store_visit_qry,
                            "completed_orders" => $completed_orders_count,
                            "returned_orders" => $cancelled_orders_count,
                        ),
                        "invoice_table_data" => array(
                            "total_amount" => $total_amt,
                            "total_invoices" => $total_invoices,
                            "total_taxable_value" => $total_taxable_value,
                            "total_igst" => $total_igst,
                            "invoices" => $invoices,
                            'page' => $page,
                            'total_pages' => $total_pages,
                        )
					),
				);
			}else{
				$response = array(
					"status"=>false,
					"message"=>"Something went wrong.",
				);
			}
		echo json_encode($response);
	// }
?>