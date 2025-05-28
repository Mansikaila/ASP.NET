<?php
include('config/connection.php');
$customer = $_GET['customer'] ?? '';

$sql = "SELECT 
            m.inward_id,
            m.inward_no,
            m.inward_date,
            c.customer_name AS broker,
            m.customer,
            d.lot_no,
            im.item_name AS item,
            d.variety,
            d.inward_qty,
            um.packing_unit_name AS packing_unit,  
            d.inward_wt,
            d.inward_qty AS stock_qty,
            ROUND(d.inward_wt / 1000, 3) AS stock_wt, 
            0 AS out_qty,
            0 AS out_wt,
            um.loading_charge,
            d.location
        FROM tbl_inward_master m
        INNER JOIN tbl_inward_detail d ON m.inward_id = d.inward_id
        LEFT JOIN tbl_packing_unit_master um ON d.packing_unit = um.packing_unit_id
        LEFT JOIN tbl_customer_master c ON m.customer = c.customer_id
        LEFT JOIN tbl_item_master im ON d.item = im.item_id
        WHERE d.inward_qty > 0"
        . ($customer ? " AND m.customer = :customer" : "") . "
        ORDER BY m.inward_date DESC, d.lot_no ASC";

$stmt = $_dbh->prepare($sql);
if ($customer) $stmt->bindParam(':customer', $customer, PDO::PARAM_STR);
$stmt->execute();
$inwards = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($inwards);
?>