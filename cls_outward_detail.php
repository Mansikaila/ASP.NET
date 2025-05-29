<?php
    class mdl_outwarddetail 
{                        
public $outward_detail_id;     
                  
    public $outward_id;     
                  
    public $inward_id;     
                  
    public $inward_detail_id;     
                  
    public $out_qty;     
                  
    public $out_wt;     
                  
    public $loading_charges;     
                  
    public $detailtransactionmode;
}

class bll_outwarddetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_outwarddetail(); 
        $this->_dal =new dal_outwarddetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
    }
   public function pageSearch()
{
    global $_dbh;
    $_grid = "";
    $_grid = '
    <table  id="searchDetail" class="table table-bordered table-striped" style="width:100%;">
    <thead id="tableHead">
        <tr>
            <th>Inward No.</th>
            <th>Lot No.</th>
            <th>Inward Date</th>
            <th>Item</th>
            <th>Variety</th>
            <th>Stock Qty.</th>
            <th>Out Qty.</th>
            <th>Unit</th>
            <th>Out. Wt. (Kg.)</th>
            <th>Loading Charges</th>
            <th>Location</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="tableBody">';
    $i = 0;
    $result = [];
    $main_id_name = "outward_id";
    if (isset($_POST[$main_id_name]))
        $main_id = $_POST[$main_id_name];
    else 
        $main_id = $this->_mdl->$main_id_name;

    if ($main_id) {
        // Join query to get all required fields
        $sql = "
        SELECT 
            od.outward_detail_id,
            od.outward_id,
            od.inward_id,
            od.inward_detail_id,
            od.out_qty,
            od.out_wt,
            od.loading_charges,
            id.lot_no,
            id.stock_qty,
            id.unit, -- adjust if unit is in another table
            id.location,
            im.inward_no,
            DATE_FORMAT(im.inward_date, '%d-%m-%Y') AS inward_date,
            im.item,
            im.variety
        FROM tbl_outward_detail od
        LEFT JOIN tbl_inward_detail id ON od.inward_detail_id = id.inward_detail_id
        LEFT JOIN tbl_inward im ON od.inward_id = im.inward_id
        WHERE od.outward_id = :outward_id
        ORDER BY od.outward_detail_id ASC
        ";
        $stmt = $_dbh->prepare($sql);
        $stmt->execute(['outward_id' => $main_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($result)) {
        foreach ($result as $_rs) {
            $detail_id_label = "outward_detail_id";
            $detail_id = $_rs[$detail_id_label];
            $_grid .= "<tr data-label=\"$detail_id_label\" data-id=\"$detail_id\" id=\"row{$i}\">";
            $_grid .= "<td data-label=\"Inward No.\">" . htmlspecialchars($_rs['inward_no'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Lot No.\">" . htmlspecialchars($_rs['lot_no'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Inward Date\">" . htmlspecialchars($_rs['inward_date'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Item\">" . htmlspecialchars($_rs['item'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Variety\">" . htmlspecialchars($_rs['variety'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Stock Qty.\">" . htmlspecialchars($_rs['stock_qty'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Out Qty.\">" . htmlspecialchars($_rs['out_qty'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Unit\">" . htmlspecialchars($_rs['unit'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Out. Wt. (Kg.)\">" . htmlspecialchars($_rs['out_wt'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Loading Charges\">" . htmlspecialchars($_rs['loading_charges'] ?? '') . "</td>";
            $_grid .= "<td data-label=\"Location\">" . htmlspecialchars($_rs['location'] ?? '') . "</td>";
            // Action buttons
            $_grid .= "<td class=\"actions\">
                <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"{$detail_id}\" data-index=\"{$i}\">Edit</button>
                <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"{$detail_id}\" data-index=\"{$i}\">Delete</button>
            </td>";
            $_grid .= "</tr>\n";
            $i++;
        }
    } else {
        $_grid .= "<tr id=\"norecords\" class=\"norecords\">";
        $_grid .= "<td colspan=\"12\" class=\"text-center\">No records available.</td>";
        $_grid .= "</tr>";
    }
    $_grid .= "</tbody></table>";
    return $_grid;
}
}
 class dal_outwarddetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        
        $_dbh->exec("set @p0 = ".$_mdl->outward_detail_id);
        $_pre=$_dbh->prepare("CALL outward_detail_transaction (@p0,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->outward_id);
        $_pre->bindParam(2,$_mdl->inward_id);
        $_pre->bindParam(3,$_mdl->inward_detail_id);
        $_pre->bindParam(4,$_mdl->out_qty);
        $_pre->bindParam(5,$_mdl->out_wt);
        $_pre->bindParam(6,$_mdl->loading_charges);
        $_pre->bindParam(7,$_mdl->detailtransactionmode);
        $_pre->execute();
        
    }
}