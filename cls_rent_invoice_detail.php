<?php
include_once(__DIR__ . "/../config/connection.php");

class mdl_rentinvoicedetail 
{                        
    public $rent_invoice_detail_id;     
    public $rent_invoice_id;     
    public $description;     
    public $qty;     
    public $unit;     
    public $weight;     
    public $rate_per_unit;  
    public $manual_rent_per;
    public $amount;     
    public $remark;     
    public $detailtransactionmode;
    // Add fields for generated invoice data
    public $inward_no;
    public $inward_date;
    public $lot_no;
    public $item;
    public $marko;
    public $invoice_qty;
    public $unit_name;
    public $wt_per_kg;
    public $storage_duration;
    public $rent_per_storage_duration;
    public $rent_per;
    public $outward_date;
    public $charges_from;
    public $charges_to;
    public $actual_month;
    public $actual_day;
    public $invoice_for;
    public $invoice_day;
    public $invoice_amount;
    public $status;
    public $gst_status;
}

class bll_rentinvoicedetail                           
{   
    public $_mdl;
    public $_dal;
    
    public function __construct()    
    {
        $this->_mdl = new mdl_rentinvoicedetail(); 
        $this->_dal = new dal_rentinvoicedetail();
    }
    
    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
    }
    
 public function pageSearch()
{
    global $_dbh;
    $_grid = "";
    $_grid = "
    <table id=\"searchDetail\" class=\"table table-bordered table-striped\" style=\"width:100%;\">
    <thead id=\"tableHead\">
        <tr>
        <th>Action</th>";
    $_grid .= "<th> Description </th>";
    $_grid .= "<th> Qty </th>";
    $_grid .= "<th> Unit </th>";
    $_grid .= "<th> Rate / Unit </th>";
    $_grid .= "<th> Amount </th>";
    $_grid .= "<th> Remark </th>";
    $_grid .= "</tr>
    </thead>";
    
    $i = 0;
    $result = array();
    $main_id_name = "rent_invoice_id";

    if (isset($_POST[$main_id_name])) {
        $main_id = $_POST[$main_id_name];
    } elseif (isset($this->_mdl->$main_id_name)) {
        $main_id = $this->_mdl->$main_id_name;
    } else {
        $main_id = 0;
    }
        
    if ($main_id) {
        $sql = "CALL csms1_search_detail('t.description, t.qty, t.unit, t.weight, t.rate_per_unit, t.amount, t.remark, t.rent_invoice_detail_id, t.rent_invoice_id','tbl_rent_invoice_detail t','t.".$main_id_name."=".$main_id."')";
        $result = $_dbh->query($sql, PDO::FETCH_ASSOC);
    }
        
    $_grid .= "<tbody id=\"tableBody\">";
    if (!empty($result)) {
        foreach ($result as $_rs) {
            $detail_id_label = "rent_invoice_detail_id";
            $detail_id = $_rs[$detail_id_label];
            $_grid .= "<tr data-label=\"".$detail_id_label."\" data-id=\"".$detail_id."\" id=\"row".$i."\">";
            $_grid .= "
            <td data-label=\"Action\" class=\"actions\"> 
                <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Edit</button>
                <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Delete</button>
            </td>";
            $_grid .= "
            <td data-label=\"rent_invoice_id\" style=\"display:none\">".$_rs['rent_invoice_id']."</td>"; 
            $_grid .= "
            <td data-label=\"description\"> ".$_rs['description']." </td>"; 
            $_grid .= "
            <td data-label=\"qty\"> ".$_rs['qty']." </td>"; 
            $_grid .= "
            <td data-label=\"unit\"> ".$_rs['unit']." </td>"; 
            $_grid .= "
            <td data-label=\"weight\" style=\"display:none\">".$_rs['weight']."</td>"; 
            $_grid .= "
            <td data-label=\"rate_per_unit\"> ".$_rs['rate_per_unit']." </td>"; 
            $_grid .= "
            <td data-label=\"amount\"> ".$_rs['amount']." </td>"; 
            $_grid .= "
            <td data-label=\"remark\"> ".$_rs['remark']." </td>"; 
            $_grid .= "</tr>\n";
            $i++;
        }
    }
    
    if ($i == 0) {
        $_grid .= "<tr id=\"norecords\" class=\"norecords\">";
        $_grid .= "<td colspan=\"8\">No records available.</td>";
        $_grid .= "<td style=\"display:none\">&nbsp;</td>";
        $_grid .= "<td style=\"display:none\">&nbsp;</td>";
        $_grid .= "<td style=\"display:none\">&nbsp;</td>";
        $_grid .= "<td style=\"display:none\">&nbsp;</td>";
        $_grid .= "<td style=\"display:none\">&nbsp;</td>";
        $_grid .= "<td style=\"display:none\">&nbsp;</td>";
        $_grid .= "</tr>";
    }
    
    $_grid .= "</tbody>
    </table> ";
    return $_grid; 
}  
}

// AJAX: Outward-based (generate_details) grid for invoice generation
if (isset($_POST['action']) && $_POST['action'] == 'generate_details') {
    try {
        global $_dbh;
        $lot_no = $_POST['lot_no'] ?? null;
        $customer = $_POST['customer'] ?? null;
        $invoice_for = $_POST['status'] ?? null;
        $invoice_type = $_POST['invoice_type'] ?? null;

        $where_conditions = [];
        $params = [];

        if ($customer) {
            $where_conditions[] = "im.customer = ?";
            $params[] = $customer;
        }
        if ($lot_no) {
            if (is_array($lot_no) && !empty($lot_no)) {
                $placeholders = implode(',', array_fill(0, count($lot_no), '?'));
                $where_conditions[] = "i.lot_no IN ($placeholders)";
                $params = array_merge($params, $lot_no);
            } elseif (!is_array($lot_no)) {
                $where_conditions[] = "i.lot_no = ?";
                $params[] = $lot_no;
            }
        }
        if ($invoice_for == 4) {
            $where_conditions[] = "i.storage_duration = ?";
            $params[] = 4;
        }
        if ($invoice_type) {
            if ($invoice_type == 2) {
                $where_conditions[] = "itm.item_gst = ?";
                $params[] = 1;
            } elseif ($invoice_type == 3) {
                $where_conditions[] = "itm.item_gst = ?";
                $params[] = 2;
            } elseif ($invoice_type == 1) {
                $where_conditions[] = "itm.item_gst = ?";
                $params[] = 3;
            }
        } 
          $sql = "SELECT 
            im.inward_no AS in_no,
            DATE_FORMAT(im.inward_date, '%d-%m-%Y') AS in_date,
            im.inward_date AS inward_date_db,
            i.lot_no,
            itm.item_id AS item_id,
            itm.item_name AS item,
            um.packing_unit_id AS unit_id,
            um.packing_unit_name AS unit,
            i.marko,
            i.inward_qty AS qty,
            i.inward_wt AS weight,
            vsd.id AS storage_duration_id,
            vsd.value AS storage_duration,
            i.rent_per_storage_duration,
            vrp.id AS rent_per_id,
            vrp.value AS rent_per,
            DATE_FORMAT(COALESCE(MAX(om.outward_date), CURDATE()), '%d-%m-%Y') AS out_date,
            COALESCE(MAX(om.outward_date), CURDATE()) AS outward_date_db, 
            DATE_FORMAT(im.inward_date, '%d-%m-%Y') AS charges_from,
            im.inward_date AS charges_from_db,
            DATE_FORMAT(COALESCE(MAX(om.outward_date), CURDATE()), '%d-%m-%Y') AS charges_to,
            COALESCE(MAX(om.outward_date), CURDATE()) AS charges_to_db,
            TIMESTAMPDIFF(MONTH, im.inward_date, COALESCE(MAX(om.outward_date), CURDATE())) AS act_month,
            (DATEDIFF(COALESCE(MAX(om.outward_date), CURDATE()), im.inward_date) % 30) AS act_day,
            CASE 
                WHEN LOWER(vsd.value) LIKE '%weekly%' COLLATE utf8mb4_unicode_ci THEN CEIL(DATEDIFF(COALESCE(MAX(om.outward_date), CURDATE()), im.inward_date) / 7)
                ELSE TIMESTAMPDIFF(MONTH, im.inward_date, COALESCE(MAX(om.outward_date), CURDATE()))
            END AS invoice_for,
            CASE 
                WHEN LOWER(vsd.value) LIKE '%weekly%' COLLATE utf8mb4_unicode_ci THEN 0
                ELSE DATEDIFF(COALESCE(MAX(om.outward_date), CURDATE()), im.inward_date)
            END AS invoice_day,
            (i.inward_qty - IFNULL(SUM(o.out_qty), 0)) AS stock_qty,
            NULL AS amount,
            CASE 
                WHEN (i.inward_qty - IFNULL(SUM(o.out_qty), 0)) = 0 THEN 'Outward'
                ELSE 'Stock'
            END AS status,
            itm.item_gst AS gst_status
            FROM tbl_inward_detail i
            JOIN tbl_inward_master im ON i.inward_id = im.inward_id
            LEFT JOIN tbl_item_master itm ON i.item = itm.item_id
            LEFT JOIN view_rent_type vrp ON i.rent_per = vrp.id
            LEFT JOIN view_storage_duration vsd ON i.storage_duration = vsd.id  
            LEFT JOIN tbl_packing_unit_master um ON i.packing_unit = um.packing_unit_id
            LEFT JOIN tbl_outward_detail o ON o.inward_detail_id = i.inward_detail_id
            LEFT JOIN tbl_outward_master om ON o.outward_id = om.outward_id";
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }
        $sql .= " GROUP BY i.inward_detail_id";
        if ($invoice_for == 2) {
            $sql .= " HAVING stock_qty > 0 AND stock_qty < i.inward_qty";
        } elseif ($invoice_for == 3) {
            $sql .= " HAVING stock_qty = 0";
        }
        $stmt = $_dbh->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        function format_duration($value, $unit) {
            return $value . ' ' . ($value == 1 ? $unit : $unit . 's');
        }
        // === AMOUNT CALCULATION AND FORMATTING LOGIC START ===
        foreach ($results as &$row) {
            $qty = floatval($row['qty']);
            $rent = floatval($row['rent_per_storage_duration']);
            $storage_duration = strtolower(trim($row['storage_duration']));
            $inv_for = intval($row['invoice_for']);
            $inv_day = intval($row['invoice_day']);
            $act_month = intval($row['act_month']);
            $act_day = intval($row['act_day']);
            $charges_from = new DateTime($row['charges_from']);
            $charges_to = new DateTime($row['charges_to']);
            $interval = $charges_from->diff($charges_to);
            $days_diff = $interval->days + 1;
            $total_days = $days_diff;
            $row['act_month'] = floor($days_diff / 30);
            $row['act_day'] = $days_diff % 30;
            $amount = 0;
            if (strpos($storage_duration, 'daily') !== false) {
                $amount = $rent * $qty * $inv_day;
                $row['invoice_for'] = format_duration(0, 'day');
                $row['invoice_day'] = format_duration($inv_day, 'day');
            } elseif (strpos($storage_duration, 'weekly') !== false) {
                $amount = $rent * $qty * $inv_for;
                $row['invoice_for'] = format_duration($inv_for, 'week');
                $row['invoice_day'] = format_duration(0, 'day');
            } elseif (strpos($storage_duration, 'fortnightly') !== false && $storage_duration !== '1 month 15 days') {
                $fortnights = ceil($total_days / 15);
                $amount = $qty * $rent * $fortnights;
                $row['invoice_for'] = format_duration($fortnights, 'fortnight');
                $row['invoice_day'] = format_duration($fortnights * 15, 'day');
            } elseif ($storage_duration === '1 month 1 day') {
                if ($days_diff >= 31) {
                    $rent_per_day = $rent / 30;
                    $inv_for = 1;
                    $inv_day = $days_diff - 30;
                    $amount = ($qty * $rent * $inv_for) + ($qty * $rent_per_day * $inv_day);
                    $row['invoice_for'] = format_duration($inv_for, 'month');
                    $row['invoice_day'] = format_duration($inv_day, 'day');
                } else {
                    $amount = 0;
                    $row['invoice_for'] = format_duration(0, 'month');
                    $row['invoice_day'] = format_duration($days_diff, 'day');
                }
            } elseif ($storage_duration === '1 month 7 days') {
                $rent_per_day = $rent / 30;
                if ($days_diff < 8) {
                    $inv_for = 0;
                    $inv_day = 0; 
                    $amount = 0;
                } elseif ($days_diff <= 30) {
                    $inv_for = 1;
                    $leftover_days = $days_diff - 30;
                    $inv_day = ($leftover_days >= 7) ? 7 : 0;
                    $amount = ($qty * $rent * $inv_for) + ($qty * $rent_per_day * $inv_day);
                } else {
                    $inv_for = floor($days_diff / 30);
                    $leftover_days = $days_diff % 30;
                    $inv_day = ($leftover_days >= 7) ? 7 : 0;
                    $amount = ($qty * $rent * $inv_for) + ($qty * $rent_per_day * $inv_day);
                }
                $row['invoice_for'] = format_duration($inv_for, 'month');
                $row['invoice_day'] = format_duration($inv_day, 'day');
            } elseif ($storage_duration === '1 month 15 days') {
            $rent_per_day = $rent / 30;
            if ($days_diff < 15) {
                $inv_for = 1;
                $inv_day = 0;
                $amount = $qty * $rent * $inv_for;
                $row['invoice_for'] = format_duration($inv_for, 'month');
                $row['invoice_day'] = format_duration($inv_day, 'day');
            } elseif ($days_diff <= 30) {
                $inv_for = 1;
                $inv_day = $days_diff == 30 ? 0 : $days_diff;
                $amount = ($qty * $rent * $inv_for) + ($qty * $rent_per_day * $inv_day);
                $row['invoice_for'] = format_duration($inv_for, 'month');
                $row['invoice_day'] = format_duration($inv_day, 'day');
            } else {
                $inv_for = floor($days_diff / 30);
                $inv_day = $days_diff % 30;
                if ($inv_day <= 15) {
                    $amount = ($qty * $rent * $inv_for) + ($qty * $rent_per_day * $inv_day);
                    $row['invoice_for'] = format_duration($inv_for, 'month');
                    $row['invoice_day'] = format_duration(0, 'day');
                } else {
                    $inv_for += 1;
                    $inv_day = 0;
                    $amount = $qty * $rent * $inv_for;
                    $row['invoice_for'] = format_duration($inv_for, 'month');
                    $row['invoice_day'] = format_duration($inv_day, 'day');
                }
            }
            } elseif (strpos($storage_duration, 'monthly') !== false) {
                $inv_for = ceil($total_days / 30);
                $row['invoice_for'] = format_duration($inv_for, 'month');
                $row['invoice_day'] = format_duration(0, 'day');
                $amount = $qty * $rent * $inv_for;
            }
            $row['amount'] = number_format($amount, 2, '.', ',');
        }
        unset($row);
        // === AMOUNT CALCULATION AND FORMATTING LOGIC END ===

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    } catch (Exception $e) {
        error_log("Error in generate_details: " . $e->getMessage() . ", Inputs: " . json_encode($_POST));
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

class dal_rentinvoicedetail
{
    public function dbTransaction($_mdl)
    {
        global $_dbh;

        // Always set a valid integer value for rent_invoice_detail_id
        $detail_id = intval($_mdl->rent_invoice_detail_id); // 0 if empty/null

        $_dbh->exec("set @p0 = $detail_id");
        $_pre = $_dbh->prepare("CALL rent_invoice_detail_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $_pre->bindParam(1, $_mdl->rent_invoice_id);
        $_pre->bindParam(2, $_mdl->description);
        $_pre->bindParam(3, $_mdl->qty);
        $_pre->bindParam(4, $_mdl->unit);
        $_pre->bindParam(5, $_mdl->weight);
        $_pre->bindParam(6, $_mdl->rate_per_unit);
        $_pre->bindParam(7, $_mdl->manual_rent_per);
        $_pre->bindParam(8, $_mdl->amount);
        $_pre->bindParam(9, $_mdl->remark);
        $_pre->bindParam(10, $_mdl->inward_no);
        $_pre->bindParam(11, $_mdl->inward_date);
        $_pre->bindParam(12, $_mdl->lot_no);
        $_pre->bindParam(13, $_mdl->item);
        $_pre->bindParam(14, $_mdl->marko);
        $_pre->bindParam(15, $_mdl->invoice_qty);
        $_pre->bindParam(16, $_mdl->unit_name);
        $_pre->bindParam(17, $_mdl->wt_per_kg);
        $_pre->bindParam(18, $_mdl->storage_duration);
        $_pre->bindParam(19, $_mdl->rent_per_storage_duration);
        $_pre->bindParam(20, $_mdl->rent_per);
        $_pre->bindParam(21, $_mdl->outward_date);
        $_pre->bindParam(22, $_mdl->charges_from);
        $_pre->bindParam(23, $_mdl->charges_to);
        $_pre->bindParam(24, $_mdl->actual_month);
        $_pre->bindParam(25, $_mdl->actual_day);
        $_pre->bindParam(26, $_mdl->invoice_for);
        $_pre->bindParam(27, $_mdl->invoice_day);
        $_pre->bindParam(28, $_mdl->invoice_amount);
        $_pre->bindParam(29, $_mdl->status);
        $_pre->bindParam(30, $_mdl->detailtransactionmode);
        $_pre->execute();
    }
    
}
?>