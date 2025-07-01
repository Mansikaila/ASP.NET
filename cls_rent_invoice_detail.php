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
    public $invoice_month;
    public $invoice_day;
    public $invoice_amount;
    public $invoice_for;
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
    $_grid = "
    <table id=\"searchDetail\" class=\"table table-bordered table-striped\" style=\"width:100%;\">
    <thead id=\"tableHead\">
        <tr>
            <th>Action</th>
            <th>Inward No</th>
            <th>Inward Date</th>
            <th>Lot No</th>
            <th>Item</th>
            <th>Marko</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Weight (Kg)</th>
            <th>Storage Duration</th>
            <th>Rent</th>
            <th>Rent Per</th>
            <th>Outward Date</th>
            <th>Charges From</th>
            <th>Charges To</th>
            <th>Actual Month</th>
            <th>Actual Day</th>
            <th>Invoice Month</th>
            <th>Invoice Day</th>
            <th>Amount</th>
            <th>Invoice For</th>
        </tr>
    </thead>";
 
    $i = 0;
    $result = array();
    $main_id_name = "rent_invoice_id";
    if (isset($_POST[$main_id_name]))
        $main_id = $_POST[$main_id_name];
    else 
        $main_id = $this->_mdl->$main_id_name;
 
    if ($main_id) {
        $sql = "SELECT 
            t.inward_no,
            DATE_FORMAT(t.inward_date, '%d-%m-%Y') AS inward_date,
            t.lot_no,
            itm.item_name AS item,
            t.marko,
            t.invoice_qty AS qty,
            um.packing_unit_name AS unit,
            t.wt_per_kg AS weight,
            vsd.value AS storage_duration,
            t.rent_per_storage_duration,
            vrp.value AS rent_per,
            DATE_FORMAT(t.outward_date, '%d-%m-%Y') AS outward_date,
            DATE_FORMAT(t.charges_from, '%d-%m-%Y') AS charges_from,
            DATE_FORMAT(t.charges_to, '%d-%m-%Y') AS charges_to,
            t.actual_month,
            t.actual_day,
            t.invoice_month,
            t.invoice_day,
            t.invoice_amount AS amount,
            t.invoice_for,
            t.rent_invoice_detail_id
        FROM tbl_rent_invoice_detail t
        LEFT JOIN tbl_item_master itm ON t.item = itm.item_id
        LEFT JOIN view_storage_duration vsd ON t.storage_duration = vsd.id
        LEFT JOIN view_rent_type vrp ON t.rent_per = vrp.id
        LEFT JOIN tbl_packing_unit_master um ON t.unit_name = um.packing_unit_id
        WHERE t.$main_id_name = ?";
        $stmt = $_dbh->prepare($sql);
        $stmt->execute([$main_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $_grid .= "<td data-label=\"inward_no\">".htmlspecialchars($_rs['inward_no'] ?? '')."</td>";
            $_grid .= "<td data-label=\"inward_date\">".htmlspecialchars($_rs['inward_date'] ?? '')."</td>";
            $_grid .= "<td data-label=\"lot_no\">".htmlspecialchars($_rs['lot_no'] ?? '')."</td>";
            $_grid .= "<td data-label=\"item\">".htmlspecialchars($_rs['item'] ?? '')."</td>";
            $_grid .= "<td data-label=\"marko\">".htmlspecialchars($_rs['marko'] ?? '')."</td>";
            $_grid .= "<td data-label=\"qty\">".htmlspecialchars($_rs['qty'] ?? '')."</td>";
            $_grid .= "<td data-label=\"unit\">".htmlspecialchars($_rs['unit'] ?? '')."</td>";
            $_grid .= "<td data-label=\"weight\">".htmlspecialchars($_rs['weight'] ?? '')."</td>";
            $_grid .= "<td data-label=\"storage_duration\">".htmlspecialchars($_rs['storage_duration'] ?? '')."</td>";
            $_grid .= "<td data-label=\"rent_per_storage_duration\">".htmlspecialchars($_rs['rent_per_storage_duration'] ?? '')."</td>";
            $_grid .= "<td data-label=\"rent_per\">".htmlspecialchars($_rs['rent_per'] ?? '')."</td>";
            $_grid .= "<td data-label=\"outward_date\">".htmlspecialchars($_rs['outward_date'] ?? '')."</td>";
            $_grid .= "<td data-label=\"charges_from\">".htmlspecialchars($_rs['charges_from'] ?? '')."</td>";
            $_grid .= "<td data-label=\"charges_to\">".htmlspecialchars($_rs['charges_to'] ?? '')."</td>";
            $_grid .= "<td data-label=\"actual_month\">".htmlspecialchars($_rs['actual_month'] ?? '')."</td>";
            $_grid .= "<td data-label=\"actual_day\">".htmlspecialchars($_rs['actual_day'] ?? '')."</td>";
            $_grid .= "<td data-label=\"invoice_month\">".htmlspecialchars($_rs['invoice_month'] ?? '')."</td>";
            $_grid .= "<td data-label=\"invoice_day\">".htmlspecialchars($_rs['invoice_day'] ?? '')."</td>";
            $_grid .= "<td data-label=\"amount\">".htmlspecialchars($_rs['amount'] ?? '')."</td>";
            $_grid .= "<td data-label=\"invoice_for\">".htmlspecialchars($_rs['invoice_for'] ?? '')."</td>";
            $_grid .= "</tr>\n";
            $i++;
        }
    }
    if ($i == 0) {
        $_grid .= "<tr id=\"norecords\" class=\"norecords\">";
        $_grid .= "<td colspan=\"20\">No records available.</td>";
        $_grid .= "</tr>";
    }
    $_grid .= "</tbody></table>";
    return $_grid;
}
}

// AJAX: Outward-based (generate_details) grid for invoice generation
if (isset($_POST['action']) && $_POST['action'] == 'generate_details') {
    try {
        global $_dbh;
        $lot_no = $_POST['lot_no'] ?? null;
        $customer = $_POST['customer'] ?? null;
        $invoice_for = $_POST['invoice_for'] ?? null;
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
        // Add item_gst filter based on invoice_type
        if ($invoice_type) {
            if ($invoice_type == 2) { // Tax Invoice
                $where_conditions[] = "itm.item_gst = ?";
                $params[] = 1; 
            } elseif ($invoice_type == 3) { // Bill of Supply
                $where_conditions[] = "itm.item_gst = ?";
                $params[] = 2; 
            } elseif ($invoice_type == 1) { // Regular
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
            END AS invoice_month,
            CASE 
                WHEN LOWER(vsd.value) LIKE '%weekly%' COLLATE utf8mb4_unicode_ci THEN 0
                ELSE DATEDIFF(COALESCE(MAX(om.outward_date), CURDATE()), im.inward_date)
            END AS invoice_day,
            (i.inward_qty - IFNULL(SUM(o.out_qty), 0)) AS stock_qty,
            NULL AS amount,
            CASE 
                WHEN (i.inward_qty - IFNULL(SUM(o.out_qty), 0)) = 0 THEN 'Outward'
                ELSE 'Stock'
            END AS invoice_for,
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

        // === AMOUNT CALCULATION LOGIC START ===
        foreach ($results as &$row) {
            $qty = floatval($row['qty']);
            $rent = floatval($row['rent_per_storage_duration']);
            $storage_duration = strtolower(trim($row['storage_duration']));
            $inv_month = intval($row['invoice_month']);
            $inv_day = intval($row['invoice_day']);
            $act_month = intval($row['act_month']);
            $act_day = intval($row['act_day']);
            $charges_from = new DateTime($row['charges_from']);
            $charges_to = new DateTime($row['charges_to']);
            $interval = $charges_from->diff($charges_to);
            $days_diff = $interval->days + 1; // Include end date
            $total_days = $days_diff; // Use inclusive days for calculations
            $row['act_month'] = floor($days_diff / 30); // Override act_month
            $row['act_day'] = $days_diff % 30; // Override act_day
            $amount = 0;

            if (strpos($storage_duration, 'daily') !== false) {
                $amount = $rent * $qty * $inv_day;
            } elseif (strpos($storage_duration, 'weekly') !== false) {
                $amount = $rent * $qty * $inv_month;
            } elseif (strpos($storage_duration, 'fortnightly') !== false && $storage_duration !== '1 month 15 days') {
                $fortnights = ceil($total_days / 15);
                $amount = $qty * $rent * $fortnights;
                $row['invoice_month'] = $fortnights;
                $row['invoice_day'] = 15;
            } elseif ($storage_duration === '1 month 1 day') {
                // Handle "1 Month 1 Day" specifically
                if ($days_diff >= 31) {
                    $rent_per_day = $rent / 30; // Calculate daily rent
                    $inv_month = 1; // First 31 days count as 1 month
                    $inv_day = $days_diff - 30; // Remaining days
                    $amount = ($qty * $rent * $inv_month) + ($qty * $rent_per_day * $inv_day);
                    $row['invoice_month'] = $inv_month;
                    $row['invoice_day'] = $inv_day;
                } else {
                    // Skip calculation if less than 31 days
                    $amount = 0;
                    $row['invoice_month'] = 0;
                    $row['invoice_day'] = $days_diff;
                }
            } elseif ($storage_duration === '1 month 15 days') {
                // Skip calculation for "1 Month 15 Days"
                $amount = 0;
                $row['invoice_month'] = 0;
                $row['invoice_day'] = $days_diff;
            } elseif (strpos($storage_duration, 'monthly') !== false) {
                $row['invoice_month'] = ceil($total_days / 30);
                $row['invoice_day'] = 0;
                $amount = $qty * $rent * $row['invoice_month'];
            }
            $row['amount'] = round($amount, 2);
        }
        unset($row);
        // === AMOUNT CALCULATION LOGIC END ===
        
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

        try {
            $_dbh->exec("SET @p0 = " . (int)$_mdl->rent_invoice_detail_id);
            $_pre = $_dbh->prepare("CALL rent_invoice_detail_transaction (@p0, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
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
            $_pre->bindParam(26, $_mdl->invoice_month);
            $_pre->bindParam(27, $_mdl->invoice_day);
            $_pre->bindParam(28, $_mdl->invoice_amount);
            $_pre->bindParam(29, $_mdl->invoice_for);
            $_pre->bindParam(30, $_mdl->detailtransactionmode);
            $_pre->execute();
        } catch (PDOException $e) {
            error_log("Error in dal_rentinvoicedetail::dbTransaction: " . $e->getMessage());
            throw $e;
        }
    }
}
?>