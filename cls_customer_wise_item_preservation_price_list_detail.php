    <?php
    include_once(__DIR__ . "/../config/connection.php");

    class mdl_customerwiseitempreservationpricelistdetail 
    {                        
        public $customer_wise_item_preservation_price_list_detail_id;     
        public $customer_wise_item_preservation_price_list_id;     
        public $packing_unit_id;     
        public $rent_per_qty_month;     
        public $rent_per_qty_season;  
        public $detailtransactionmode;
        public $_item_id;
        public $_customer_id;
        public $_company_year_id;
    }
    class bll_customerwiseitempreservationpricelistdetail
    {   
        public $_mdl;
        public $_dal;

        public function __construct()    
        {
            $this->_mdl = new mdl_customerwiseitempreservationpricelistdetail(); 
            $this->_dal = new dal_customerwiseitempreservationpricelistdetail();
        }

        public function dbTransaction()
        {
            $this->_dal->dbTransaction($this->_mdl);
        }

 // In bll_customerwiseitempreservationpricelistdetail class
public function getDetailsByMasterId($masterId, $matchType = null)
{
    global $_dbh;
    $details = [];
    try {
        // First get the master record to check its year
        $yearCheck = $_dbh->prepare("
            SELECT company_year_id, item_id, customer_id, company_id 
            FROM tbl_customer_wise_item_preservation_price_list_master 
            WHERE customer_wise_item_preservation_price_list_id = ?
        ");
        $yearCheck->execute([$masterId]);
        $masterData = $yearCheck->fetch(PDO::FETCH_ASSOC);

        if (!$masterData) {
            return $details;
        }

        // Always fetch details regardless of year
        $sql = "SELECT d.*, pum.packing_unit_name 
               FROM tbl_customer_wise_item_preservation_price_list_detail d
               JOIN tbl_packing_unit_master pum ON d.packing_unit_id = pum.packing_unit_id
               WHERE d.customer_wise_item_preservation_price_list_id = ?";
        $stmt = $_dbh->prepare($sql);
        $stmt->execute([$masterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error fetching details: " . $e->getMessage());
        return $details;
    }
}
    public function getPreviousYearDetails($item_id, $customer_id, $company_id, $current_company_year_id)
    {
        global $_dbh;
        $details = [];

        try {
            // Try to find a matching master record from previous years
            $sql = "SELECT m.customer_wise_item_preservation_price_list_id 
                   FROM tbl_customer_wise_item_preservation_price_list_master m
                   WHERE m.item_id = ? 
                   AND m.company_id = ? 
                   AND m.company_year_id != ?";

            $params = [$item_id, $company_id, $current_company_year_id];

            if ($customer_id) {
                $sql .= " AND m.customer_id = ?";
                $params[] = $customer_id;
            }

            $sql .= " ORDER BY m.company_year_id DESC, m.created_date DESC LIMIT 1";

            $stmt = $_dbh->prepare($sql);
            $stmt->execute($params);
            $prevMaster = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($prevMaster) {
                $sql = "SELECT d.*, pum.packing_unit_name 
                       FROM tbl_customer_wise_item_preservation_price_list_detail d
                       JOIN tbl_packing_unit_master pum ON d.packing_unit_id = pum.packing_unit_id
                       WHERE d.customer_wise_item_preservation_price_list_id = ?";
                $stmt = $_dbh->prepare($sql);
                $stmt->execute([$prevMaster['customer_wise_item_preservation_price_list_id']]);
                $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("Error fetching previous year details: " . $e->getMessage());
        }

        return $details;
    }
public function pageSearch()
{
    global $_dbh;
    $company_year_id = $_SESSION["sess_company_year_id"];

    $_grid = "
    <div id=\"gridContainer\" class=\"table-responsive\">
        <table id=\"dataGrid\" 
               class=\"table table-bordered table-striped text-center align-middle\" 
               style=\"table-layout:fixed; width:100%;\">
            <thead class=\"thead-dark\">
                <tr>
                    <th>Packing Unit Name</th>
                    <th>Rent/Month/Qty</th>
                    <th>Rent/Season/Qty</th>
                </tr>
            </thead>
            <tbody id=\"gridBody\">";
    
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
    $master_id = isset($_POST['master_id']) ? intval($_POST['master_id']) : 0;
    $match_type = isset($_POST['match_type']) ? $_POST['match_type'] : null;

    // If master_id is set and >0, fetch details by master id (can be from any year)
    if ($master_id > 0) {
        // First get the master record to check its year
        $stmt = $_dbh->prepare("
            SELECT company_year_id, item_id, customer_id 
            FROM tbl_customer_wise_item_preservation_price_list_master 
            WHERE customer_wise_item_preservation_price_list_id = ?
        ");
        $stmt->execute([$master_id]);
        $masterData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($masterData) {
            // Check if master record is from current year
            if ($masterData['company_year_id'] == $company_year_id) {
                // Fetch current year details
                $details = $this->getDetailsByMasterId($master_id);
            } else {
                // Fetch previous year details
                $details = $this->getPreviousYearDetails(
                    $masterData['item_id'],
                    $masterData['customer_id'],
                    $_SESSION["sess_company_id"],
                    $masterData['company_year_id']
                );
            }

            if (!empty($details)) {
                foreach ($details as $row) {
                    $detailId = $row['customer_wise_item_preservation_price_list_detail_id'] ?? '';
                    $isNew = '1'; // Always treat as new when viewing from another year
                    $_grid .= "
    <tr 
        data-id=\"{$row['packing_unit_id']}\"
        data-detail-id=\"{$detailId}\" 
        data-is-new=\"{$isNew}\">
        <td style=\"background-color: #f0f0f0;\">" . htmlspecialchars($row['packing_unit_name']) . "</td>
        <td contenteditable=\"true\" 
            class=\"editable rent-monthly\" 
            data-field=\"rent_per_qty_month\" 
            data-original=\"{$row['rent_per_qty_month']}\">
            {$row['rent_per_qty_month']}
        </td>
        <td contenteditable=\"true\" 
            class=\"editable rent-seasonal\" 
            data-field=\"rent_per_qty_season\" 
            data-original=\"{$row['rent_per_qty_season']}\">
            {$row['rent_per_qty_season']}
        </td>
    </tr>";
                }
            } else {
                $_grid .= "
                <tr class=\"norecords\">
                    <td colspan=\"3\">No packing units found for the selected item and customer.</td>
                </tr>";
            }
        } else {
            $_grid .= "
            <tr class=\"norecords\">
                <td colspan=\"3\">Master record not found.</td>
            </tr>";
        }
    } elseif ($item_id > 0 && $customer_id > 0) {
        // Default: show current year records only
        $sql = "
        SELECT 
            pum.packing_unit_id, 
            pum.packing_unit_name, 
            COALESCE(cwippl.rent_per_qty_month, '0.00') AS rent_per_qty_month,
            COALESCE(cwippl.rent_per_qty_season, '0.00') AS rent_per_qty_season,
            cwippl.customer_wise_item_preservation_price_list_detail_id
        FROM 
            tbl_packing_unit_master pum
        LEFT JOIN (
            SELECT 
                d.packing_unit_id, 
                d.rent_per_qty_month, 
                d.rent_per_qty_season,
                d.customer_wise_item_preservation_price_list_detail_id
            FROM 
                tbl_customer_wise_item_preservation_price_list_detail d
            INNER JOIN 
                tbl_customer_wise_item_preservation_price_list_master m 
                ON d.customer_wise_item_preservation_price_list_id = m.customer_wise_item_preservation_price_list_id
            WHERE m.item_id = :item_id 
              AND m.customer_id = :customer_id 
              AND m.company_year_id = :company_year_id
        ) cwippl ON pum.packing_unit_id = cwippl.packing_unit_id
        WHERE pum.status = 1
        ORDER BY pum.packing_unit_name
        ";

        $stmt = $_dbh->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->bindParam(':company_year_id', $company_year_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($result)) {
            foreach ($result as $row) {
                $detailId = $row['customer_wise_item_preservation_price_list_detail_id'] ?? '';
                $isNew = empty($detailId) ? '1' : '0';
                $_grid .= "
    <tr 
        data-id=\"{$row['packing_unit_id']}\"
        data-detail-id=\"{$detailId}\" 
        data-is-new=\"{$isNew}\">
        <td style=\"background-color: #f0f0f0;\">" . htmlspecialchars($row['packing_unit_name']) . "</td>
        <td contenteditable=\"true\" 
            class=\"editable rent-monthly\" 
            data-field=\"rent_per_qty_month\" 
            data-original=\"{$row['rent_per_qty_month']}\">
            {$row['rent_per_qty_month']}
        </td>
        <td contenteditable=\"true\" 
            class=\"editable rent-seasonal\" 
            data-field=\"rent_per_qty_season\" 
            data-original=\"{$row['rent_per_qty_season']}\">
            {$row['rent_per_qty_season']}
        </td>
    </tr>";
            }
        } else {
            $_grid .= "
            <tr class=\"norecords\">
                <td colspan=\"3\">No packing units found for the selected item and customer.</td>
            </tr>";
        }
    } else {
        $_grid .= "
        <tr class=\"norecords\">
            <td colspan=\"3\">Please select both Customer and Item.</td>
        </tr>";
    }

    $_grid .= "
            </tbody>
        </table>
    </div>";

    echo $_grid;
}
    }
    // Action handlers for AJAX
    if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];

        // For loading details grid (now supports master_id for previous-year fallback)
        if ($action === 'fetch_units' && isset($_POST['item_id']) && isset($_POST['customer_id'])) {
            if (isset($_POST['company_year_id']) && is_numeric($_POST['company_year_id'])) {
                $_SESSION["sess_company_year_id"] = intval($_POST['company_year_id']);
            }
            $bll = new bll_customerwiseitempreservationpricelistdetail();
            $bll->pageSearch();
            exit;
        }
    }
    class dal_customerwiseitempreservationpricelistdetail
    {
// In dal_customerwiseitempreservationpricelistdetail class
public function dbTransaction($_mdl)
{
    global $_dbh;

    try {
        // UPDATE operation - now includes company_year_id check
        if ($_mdl->detailtransactionmode === 'U' && !empty($_mdl->customer_wise_item_preservation_price_list_detail_id)) {
            $stmt = $_dbh->prepare("
                UPDATE tbl_customer_wise_item_preservation_price_list_detail d
                JOIN tbl_customer_wise_item_preservation_price_list_master m 
                  ON d.customer_wise_item_preservation_price_list_id = m.customer_wise_item_preservation_price_list_id
                SET d.rent_per_qty_month = ?, 
                    d.rent_per_qty_season = ?, 
                    d.packing_unit_id = ?
                WHERE d.customer_wise_item_preservation_price_list_detail_id = ?
                  AND m.company_year_id = ?
            ");
            $stmt->bindParam(1, $_mdl->rent_per_qty_month, PDO::PARAM_STR);
            $stmt->bindParam(2, $_mdl->rent_per_qty_season, PDO::PARAM_STR);
            $stmt->bindParam(3, $_mdl->packing_unit_id, PDO::PARAM_INT);
            $stmt->bindParam(4, $_mdl->customer_wise_item_preservation_price_list_detail_id, PDO::PARAM_INT);
            $stmt->bindParam(5, $_SESSION["sess_company_year_id"], PDO::PARAM_INT);
            $stmt->execute();
            return true;
        }

        // INSERT operation remains the same
        if ($_mdl->detailtransactionmode === 'I') {
            $_dbh->exec("SET @p_customer_wise_item_preservation_price_list_detail_id = NULL");

            $stmt = $_dbh->prepare("CALL customer_wise_item_preservation_price_list_detail_transaction(
                @p_customer_wise_item_preservation_price_list_detail_id,
                ?, ?, ?, ?, ?
            )");

            $stmt->bindParam(1, $_mdl->customer_wise_item_preservation_price_list_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $_mdl->packing_unit_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $_mdl->rent_per_qty_month, PDO::PARAM_STR);
            $stmt->bindParam(4, $_mdl->rent_per_qty_season, PDO::PARAM_STR);
            $stmt->bindParam(5, $_mdl->detailtransactionmode, PDO::PARAM_STR);
            $stmt->execute();

            $result = $_dbh->query("SELECT @p_customer_wise_item_preservation_price_list_detail_id AS new_id");
            $newId = $result->fetchColumn();
            $_mdl->customer_wise_item_preservation_price_list_detail_id = $newId;
            return true;
        }

    } catch (PDOException $e) {
        error_log("Error in customerwise detail transaction: " . $e->getMessage());
        throw $e;
    }
}


    }