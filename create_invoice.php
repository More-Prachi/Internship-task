<?php

	require "../dbConfig/dbconfiguration.php";
	include_once ("../admin_api/TCPDF-main/tcpdf.php");
	// require "../dbConfig/userValidationToken.php";

	// $val = checkToken($auth_token);

	// if($val['status']==1){
		$store_id = $_POST['store_id'];
		$vendor_id = $_POST['vendor_id'];
		$store_name = $_POST['store_name'];
		$store_addr = $_POST['store_addr'];
		$store_gst = $_POST['store_gst'];
		$party_name = $_POST['party_name'];
		$party_email = $_POST['party_email'];
		$party_contact = $_POST['party_contact'];
		$gst = $_POST['total_gst'];
		$mrp_total = $_POST['mrp_total'];
		$net_total = $_POST['net_total'];
		$invoice_time = $_POST['invoice_time'];
		$invoice_date = $_POST['invoice_date'];
		$invoice_id = time().'V'.$vendor_id.'S'.$store_id;

		$product_id_arr = $_POST['product_id'];
		$product_name_arr = $_POST['product_name'];
		$attr_id_arr = $_POST['attr_id'];
		$qty_arr = $_POST['qty'];
		$gst_arr = $_POST['gst'];
		$mrp_arr = $_POST['mrp'];
		$dis_price_arr = $_POST['dis_price'];

		// $invoice_url = $media_storage_path_seller_api.'invoice_pdf/'.$invoice_id.'.pdf';

		$invoice_url = "https://localville.in/apis/seller_api/".'invoice_pdf/'.$invoice_id.'.pdf';

		if (isset($store_id) && isset($vendor_id) && isset($store_name) && isset($party_name) && isset($mrp_total) && isset($product_id_arr)) {
			$qry = "INSERT INTO `invoice`(`invoice_id`, `store_id`, `vendor_id`, `invoice_url`, `store_name`, `store_address`, `store_gst`, `party_name`, `party_email`, `party_contact`, `gst`, `mrp_total`, `net_total`, `invoice_time`, `invoice_date`, `active`) VALUES ('$invoice_id','$store_id','$vendor_id','$invoice_url','$store_name','$store_addr','$store_gst','$party_name','$party_email','$party_contact','$gst','$mrp_total','$net_total','$invoice_time','$invoice_date', '1')";
			$run = mysqli_query($conn, $qry);
			$last_invoice_id = mysqli_insert_id($conn);

			if ($run) {
				foreach ($product_id_arr as $key => $value) {
					$p_id = $product_id_arr[$key];
					$p_name = $product_name_arr[$key];
					$attr_id = $attr_id_arr[$key];
					$qty = $qty_arr[$key];
					$gst_val = $gst_arr[$key];
					$mrp_val = $mrp_arr[$key];
					$dis_price = $dis_price_arr[$key];

					$product_qry = "INSERT INTO `invoice_product`(`invoice_id`, `product_id`, `product_name`, `attr_id`, `qty`, `gst`, `mrp`, `discounted_price`) VALUES ('$last_invoice_id','$p_id','$p_name','$attr_id','$qty','$gst_val','$mrp_val','$dis_price')";
					$product_run = mysqli_query($conn, $product_qry);
				}
                
				$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor('LocalVille');
				$pdf->SetTitle($store_name);
				$pdf->AddPage();

				$total_qty = 0;
				$total_gross_amt = 0;
				$total_discount = 0;
				$total_taxable = 0;
				$total_igst = 0;
				$total_amount = 0;

				foreach ($product_id_arr as $key => $value) {
					$p_id = $product_id_arr[$key];
					$p_name = $product_name_arr[$key];
					$attr_id = $attr_id_arr[$key];
					$qty = $qty_arr[$key];
					$gst_val = $gst_arr[$key];
					$mrp_val = $mrp_arr[$key];
					$dis_price = $dis_price_arr[$key];

					$total_qty = $total_qty + $qty;
					$total_gross_amt = $total_gross_amt + round($mrp_val,2);
					$total_discount = $total_discount + round(($mrp_val - $dis_price),2);
					$total_taxable = $total_taxable + round($dis_price/(1+($gst_val/100)),2);
					$total_igst = $total_igst + round(($dis_price - ($dis_price/(1+($gst_val/100)))), 2);
					$total_amount = $total_amount + round($dis_price,2);
				}
				$update_invoice_qry = "UPDATE invoice SET gst = '$total_igst', mrp_total = '$total_gross_amt', net_total = '$total_amount' WHERE id = '$last_invoice_id'";
				$run_update_qry = mysqli_query($conn, $update_invoice_qry);

				$html = '
				<!DOCTYPE html>
				<html>
				<head>
				  <meta charset="UTF-8">
				  <title>Invoice</title>
				  <style type="text/css">
				    body {
				      font-family: Arial, sans-serif;
				      font-size: 12px;
				      margin: 0;
				      padding: 0;
				    }
				    header {
				      display: flex;
				      justify-content: space-between;
				      margin-bottom: 40px;
				    }
				    .logo {
				      flex: 1;
				    }
				    .company-info {
				      flex: 2;
				    }
				    .company-info h2 {
				      margin-top: 0;
				    }
				    .invoice-info {
				      /*flex: 1;*/
				      text-align: right;
				    }
				    table {
				      border-collapse: collapse;
				      width: 100%;
				    }
				    table thead th {
				      background-color: #f4f4f4;
				      border: 1px solid #ccc;
				      padding: 10px;
				      text-align: left;
				    }
				    table tbody td {
				      border: 1px solid #ccc;
				      padding: 10px;
				    }
				    table tfoot td {
				      border: none;
				      padding: 60px;
				      text-align: right;
				    }
				    .notes {
				      margin-top: 40px;
				    }
				    .notes h4 {
				      margin-top: 0;
				    }
				    @media print {
				      body {
				        font-size: 10px;
				    }
				    .logo img {
				        max-width: 100px;
				    }
				    .company-info h2 {
				        font-size: 16px;
				    }
				    table {
				        font-size: 10px;
				    }
				    .notes {
				        font-size: 8px;
				    }
				}
				tr { border: none; }
				td {
				  border-right: solid 8px #000; 
				  border-left: solid 8px #000;
				}
				th {
				  border-right: solid 8px #000; 
				  border-left: solid 8px #000;
				  border-bottom:solid 8px #000;
				  border-top: solid 8px #000;
				}
				.sn{
				    width: 30px;
				}
				.item-name{
				    width: 150px;
				}
				.qty{
				    width: 30px;
				}
				.gross-amt{
				    width: 60px;
				}
				.igst{
				    width: 60px;
				}
				.disc{
				    width: 50px;
				}
				.taxable-val{
				    width: 80px;
				}
				.amount{
				    width: 70px;
				}
				.tfoot-border{
				    border-right: solid 8px #000; 
				  border-left: solid 8px #000;
				  border-bottom:solid 8px #000;
				  border-top: solid 8px #000;
				}
				  </style>
				</head>
				<body>
				  <header>
				    <div class="logo">
				      <img src="localville.png" alt="Company Logo" style="height: 30px;">
				    </div>
				    
				    <div>
				        <table>
				            <tr>
				                <td style="border-left: none; border-right: none;">
				                <div class="company-info">
				                  <h2 style="line-height: 5px; color: #6D44BC;">'.$store_name.'</h2>
				                  <p>
				                    '.$store_addr.'
				                  </p>
				                  <p>
				                    <b>GSTIN: '.$store_gst.'</b><br>
				                    <b>Invoice #:</b> '.$invoice_id.'<br>
				                    <b>Issue:</b> '.$invoice_date.' '.$invoice_time.'<br>
				                  </p>
				                </div>
				                </td>
				                <td style="border-left: none; border-right: none;">
				                    <div style="text-align:right; line-height: 9px;">
				                        <h3>Billing To:</h3>
				                      <p style="line-height: 15px;">
				                          '.$party_name.'<br>
				                          '.$party_email.'<br>
				                          '.$party_contact.'
				                      </p>
				                    </div>
				                </td>
				            </tr>
				        </table>
				    </div>
				  </header>
				  <main>
				    <p></p>
				    <table>
				      <thead>
				        <tr>
				          <th style="width:30px"><h4>S.N.</h4></th>
				          <th style="width:150px"><h4>Item Name</h4></th>
				          <th style="width:30px"><h4>Qty</h4></th>
				          <th style="width:60px"><h4>Gross<br>Amount (<span style="font-family:dejavusans;">&#8377;</span>)</h4></th>
				          <th style="width:50px"><h4>Disc. (<span style="font-family:dejavusans;">&#8377;</span>)</h4></th>
				          <th style="width:80px"><h4>Taxable Value (<span style="font-family:dejavusans;">&#8377;</span>)</h4></th>
				          <th style="width:60px"><h4>IGST (<span style="font-family:dejavusans;">&#8377;</span>)</h4></th>
				          <th style="width:70px"><h4>Amount (<span style="font-family:dejavusans;">&#8377;</span>)</h4></th>
				        </tr>
				      </thead>
				      <tbody>';

				$count = 0;
				foreach ($product_id_arr as $key => $value) {
					$count++;
					$p_id = $product_id_arr[$key];
					$p_name = $product_name_arr[$key];
					$attr_id = $attr_id_arr[$key];
					$qty = $qty_arr[$key];
					$gst_val = $gst_arr[$key];
					$mrp_val = $mrp_arr[$key];
					$dis_price = $dis_price_arr[$key];

					$html .=
				      ' <tr>
				          <td class="sn">'.$count.'.</td>
				          <td class="item-name">
				              '.$p_name.'
				              <br><span style="font-size:7px;"><b>IGST: </b>'.$gst_val.'%</span>
				          </td>
				          <td class="qty">'.$qty.'</td>
				          <td class="gross-amt">'.round($mrp_val,2).'</td>
				          <td class="disc">'.round(($mrp_val - $dis_price),2).'</td>
				          <td class="taxable-val">'.round($dis_price/(1+($gst_val/100)),2).'</td>
				          <td class="igst">'.round(($dis_price - ($dis_price/(1+($gst_val/100)))), 2).'</td>
				          <td class="amount">'.round($dis_price,2).'</td>
				        </tr>';
				}
				        $html .= '
				      </tbody>
				      <tfoot>
				        <tr>
				            <td class="tfoot-border" style="width: 180px;">
				                <h4 style="text-align: center;">Total</h4>
				            </td>
				            <td class="qty tfoot-border"><h4>'.$total_qty.'</h4></td>
				          <td class="gross-amt tfoot-border"><h4>'.$total_gross_amt.'</h4></td>
				          <td class="disc tfoot-border"><h4>'.$total_discount.'</h4></td>
				          <td class="taxable-val tfoot-border"><h4>'.$total_taxable.'</h4></td>
				          <td class="igst tfoot-border"><h4>'.$total_igst.'</h4></td>
				          <td class="amount tfoot-border"><h4>'.$total_amount.'</h4></td>
				        </tr>
				      </tfoot>
				    </table>
				    <div style="text-align:right; line-height: 8px;">
				        <p>Grand Total <span style="font-size:7px;"> (Inc. IGST)</span></p>
				        <h2>'.$total_amount.'<span style="font-family:dejavusans;">&#8377;</span></h2>
				    </div>

				    <p><br></p> 

				    <div style="text-align:right; line-height: 8px;">
				        <p>'.$store_name.'</p>
				        <h3>Authorised Signatory</h3>
				    </div>

				    <p><br></p> 
				    <p><br></p> 
				    <p><br></p> 
				    
				    <table>
				        <tr>
				            <td style="width:330px; border-left: none; border-right:none;">
				                <div class="notes">
				                  <p><b>Notes:</b></p>
				                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed pharetra posuere odio, a consequat mauris posuere non. Suspendisse in orci aliquet, malesuada lorem nec, tristique velit.</p>
				                </div>
				            </td>
				            <td style="width:220px;border-left: none; border-right:none;">
				                <div style="text-align:right; line-height: 12px;">
				                    <a href="www.localville.in" target="_blank" style="text-decoration: none;">
				                    <img src="localville.png" alt="Company Logo" style="height: 20px;">
				                </a>
				                    <p><b>Contact Us<br></b><a href="https://mail.google.com/mail/u/0/?fs=1&tf=cm&to=contactus@store.localville.in&su=General+Query">contactus@store.localville.in</a></p>
				                </div>
				            </td>
				        </tr>
				    </table>
				  </main>
				</body>
				</html>
				';

				$pdf->writeHTML($html, true, false, true, false, '');

				$pdf->Output($invoice_url, 'I');

				$response = array(
					"status"=>true,
					"message"=>"invoice created successfully.",
					"data"=>array(
						"invoice_id"=>$invoice_id,
						"invoice_url"=>$invoice_url,
					),
				);
			}
			else{
				$response = array(
					"status"=>false,
					"message"=>"Something went wrong.",
				);
			}
		} else{
			$response = array(
				"status"=>false,
				"message"=>"Please provide required inputs.",
			);
		}
		echo json_encode($response);
	// }
?>