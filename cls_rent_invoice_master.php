<?php  
include_once(__DIR__ . "/../config/connection.php");
include("cls_rent_invoice_detail.php"); 
class mdl_rentinvoicemaster 
{   
    public $generator_table_layout;
    public $generator_fields_names;
    public $generator_fields_types;
    public $generator_field_scale;
    public $generator_dropdown_table;
    public $generator_label_column;
    public $generator_value_column;
    public $generator_where_condition;
    public $generator_fields_labels;
    public $generator_field_display;
    public $generator_field_required;
    public $generator_allow_zero;
    public $generator_allow_minus;
    public $generator_chk_duplicate;
    public $generator_field_data_type;
    public $generator_field_is_disabled;
    public $generator_after_detail;
    protected $fields = [];

    public function __get($name) {
        return $this->fields[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }
    public function __construct() {
        global $_dbh;
        global $database_name;
        global $tbl_generator_master;
        global $tbl_rent_invoice_master;
        $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
        $select->bindParam(1,  $tbl_rent_invoice_master);
        $select->execute();
        $row = $select->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $generator_options = json_decode($row["generator_options"]);
            if ($generator_options) {
                $this->generator_table_layout=$generator_options->table_layout;
                $this->generator_fields_names=$generator_options->field_name;
                $this->generator_fields_types=$generator_options->field_type;
                $this->generator_field_scale=$generator_options->field_scale;
                $this->generator_dropdown_table=$generator_options->dropdown_table;
                $this->generator_label_column=$generator_options->label_column;
                $this->generator_value_column=$generator_options->value_column;
                $this->generator_where_condition=$generator_options->where_condition;
                $this->generator_fields_labels=$generator_options->field_label;
                $this->generator_field_display=$generator_options->field_display;
                $this->generator_field_required=$generator_options->field_required;
                $this->generator_allow_zero=$generator_options->allow_zero;
                $this->generator_allow_minus=$generator_options->allow_minus;
                $this->generator_chk_duplicate=$generator_options->chk_duplicate;
                $this->generator_field_data_type=$generator_options->field_data_type;
                $this->generator_field_is_disabled=$generator_options->is_disabled;
                $this->generator_after_detail=$generator_options->after_detail;
            }
        }
    }

                    /** FOR DETAIL **/
                    public $_array_itemdetail;
                     public $_array_itemdelete;
                    /** \FOR DETAIL **/
                    
}

class bll_rentinvoicemaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_rentinvoicemaster(); 
        $this->_dal =new dal_rentinvoicemaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       /** FOR DETAIL **/
               
        $_bllitem= new bll_rentinvoicedetail();
        if($this->_mdl->_transactionmode!="D")
        {
            if(!empty($this->_mdl->_array_itemdetail)) {
                    for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
                    {
                            $detailrow=$iterator->current();
                        if(is_array($detailrow)) {
                            foreach($detailrow as $name=>$value) {
                                $_bllitem->_mdl->{$name}=$value;
                            }
                        }
                        $_bllitem->_mdl->rent_invoice_id = $this->_mdl->_rent_invoice_id;
                        $_bllitem->dbTransaction();
                    }
            }
                if(!empty($this->_mdl->_array_itemdelete)) {
                for($iterator= $this->_mdl->_array_itemdelete->getIterator();$iterator->valid();$iterator->next())
                    {
                            $detailrow=$iterator->current();
                        if(is_array($detailrow)) {
                            foreach($detailrow as $name=>$value) {
                                $_bllitem->_mdl->{$name}=$value;
                            }
                        }
                        $_bllitem->_mdl->rent_invoice_id = $this->_mdl->_rent_invoice_id;
                        $_bllitem->dbTransaction();
                    }
                }
        }
    /** \FOR DETAIL **/
        
            
       if($this->_mdl->_transactionmode =="D")
       {
            if(!$_SESSION["sess_message"] || $_SESSION["sess_message"]=="") {
               $_SESSION["sess_message"]="Record Deleted Successfully.";
               $_SESSION["sess_message_cls"]="alert-success";
            }
            header("Location:../srh_rent_invoice_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_rent_invoice_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_rent_invoice_master.php");
       }

    }
  public function fillModel()
    {
        $this->_dal->fillModel($this->_mdl);
      
    }
     
    public function pageSearch()
{
    global $_dbh;
    global $database_name;
    
    $where_condition = "t.company_id=".COMPANY_ID;
    
    // DIRECT QUERY APPROACH (recommended)
    $sql = "SELECT 
        t.invoice_no, 
        t.invoice_date, 
        t6.value as val6, 
        t7.customer_name as val7, 
        CONCAT(t9.hsn_code_name, ' - ', t9.description) as val9, 
        t.basic_amount, 
        t16.value as val16, 
        t.net_amount, 
        t.sp_note, 
        t.rent_invoice_id
    FROM 
        tbl_rent_invoice_master t 
        INNER JOIN view_debit_cash t6 ON t.debit_cash=t6.id 
        INNER JOIN tbl_customer_master t7 ON t.customer=t7.customer_id 
        INNER JOIN tbl_hsn_code_master t9 ON t.hsn_code=t9.hsn_code_id 
        INNER JOIN view_tax_amount t16 ON t.tax_amount=t16.id
    WHERE 
        {$where_condition}";
    
    // Debugging - uncomment to see the actual query
    // error_log("SQL Query: ".$sql);
    // echo "SQL Query: ".htmlspecialchars($sql); exit();
    try {
        $stmt = $_dbh->query($sql);
        
        // Build HTML table
        echo '<table id="searchMaster" class="ui celled table display">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Invoice No<br><input type="text" data-index="3" placeholder="Search Invoice No" /></th>
                    <th>Invoice Date<br><input type="text" data-index="4" placeholder="Search Invoice Date" /></th>
                    <th>Debit/Cash<br><input type="text" data-index="6" placeholder="Search Debit/Cash" /></th>
                    <th>Customer<br><input type="text" data-index="7" placeholder="Search Customer" /></th>
                    <th>HSN Code<br><input type="text" data-index="9" placeholder="Search HSN Code" /></th>
                    <th>Basic Amount<br><input type="text" data-index="12" placeholder="Search Basic Amount" /></th>
                    <th>Tax Amount<br><input type="text" data-index="16" placeholder="Search Tax Amount" /></th>
                    <th>Net Amount<br><input type="text" data-index="20" placeholder="Search Net Amount" /></th>
                    <th>Sp Note<br><input type="text" data-index="21" placeholder="Search Sp Note" /></th>
                </tr>
            </thead>
            <tbody>';
        
        $hasRecords = false;
        foreach($stmt as $_rs) {
            $hasRecords = true;
            echo '<tr>
                <td>
                    <form method="post" action="frm_rent_invoice_master.php" style="display:inline; margin-right:5px;">
                        <i class="fa fa-edit update" style="cursor: pointer;"></i>
                        <input type="hidden" name="rent_invoice_id" value="'.htmlspecialchars($_rs["rent_invoice_id"]).'" />
                        <input type="hidden" name="transactionmode" value="U" />
                    </form>
                    <form method="post" action="classes/cls_rent_invoice_master.php" style="display:inline;">
                        <i class="fa fa-trash delete" style="cursor: pointer;"></i>
                        <input type="hidden" name="rent_invoice_id" value="'.htmlspecialchars($_rs["rent_invoice_id"]).'" />
                        <input type="hidden" name="transactionmode" value="D" />
                    </form>
                </td>';
            
            // Output each field with proper escaping
            echo '<td>'.htmlspecialchars($_rs["invoice_no"]).'</td>';
            
            $invoiceDate = '';
            if(!empty($_rs["invoice_date"])) {
                $invoiceDate = date("d/m/Y", strtotime($_rs["invoice_date"]));
                $invoiceDate .= '<br><small>'.date("h:i:s a", strtotime($_rs["invoice_date"])).'</small>';
            }
            echo '<td>'.$invoiceDate.'</td>';
            
            // Output remaining fields
            echo '<td>'.htmlspecialchars($_rs["val6"]).'</td>';
            echo '<td>'.htmlspecialchars($_rs["val7"]).'</td>';
            echo '<td>'.htmlspecialchars($_rs["val9"]).'</td>';
            echo '<td>'.htmlspecialchars($_rs["basic_amount"]).'</td>';
            echo '<td>'.htmlspecialchars($_rs["val16"]).'</td>';
            echo '<td>'.htmlspecialchars($_rs["net_amount"]).'</td>';
            echo '<td>'.htmlspecialchars($_rs["sp_note"]).'</td>';
            
            echo '</tr>';
        }
        
        if(!$hasRecords) {
            echo '<tr><td colspan="10">No records found</td></tr>';
        }
        
        echo '</tbody></table>';
        
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Database Error: '.htmlspecialchars($e->getMessage()).'</div>';
        error_log("Database Error in pageSearch(): ".$e->getMessage());
    }
}
    
    
    
    public function checkDuplicate() {
        global $_dbh;
        global $database_name;
        $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";
        if(isset($_POST["column_name"]))
            $column_name=$_POST["column_name"];
        if(isset($_POST["column_value"]))
            $column_value=$_POST["column_value"];
        if(isset($_POST["id_name"]))
            $id_name=$_POST["id_name"];
        if(isset($_POST["id_value"]))
            $id_value=$_POST["id_value"];
        if(isset($_POST["table_name"]))
            $table_name=$_POST["table_name"];
        try {
            $sql="CAll ".$database_name."_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',@is_duplicate)";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_duplicate = $result->fetchColumn();
            echo $is_duplicate;
            exit;
        }
        catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            echo 0;
            exit;
        }
        echo 0;
        exit;
    }
}
 class dal_rentinvoicemaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        try {
            $_dbh->exec("set @p0 = ".$_mdl->_rent_invoice_id);
            $_pre=$_dbh->prepare("CALL rent_invoice_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
            
                if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                    foreach($_mdl->generator_fields_names as $i=>$fieldname)
                    {
                        if($i==0)
                            continue;
                        $field=$_mdl->{"_".$fieldname};
                        $_pre->bindValue($i,$field);
                    }
                }
                $_pre->bindValue($i+1,$_mdl->_transactionmode);
                $_pre->execute();
            } catch (PDOException $e) {
                $_SESSION["sess_message"]=$e->getMessage();
                $_SESSION["sess_message_cls"]="alert-danger";
            }
        
           /*** FOR DETAIL ***/
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_rent_invoice_id=$insertedId;
            }
            /*** /FOR DETAIL ***/
    
    }
public function fillModel($_mdl)
{
    global $_dbh;
    $_pre = $_dbh->prepare("CALL rent_invoice_master_fillmodel (?)");
    $_pre->bindParam(1, $_REQUEST["rent_invoice_id"]);
    $_pre->execute();
    $_rs = $_pre->fetchAll(PDO::FETCH_ASSOC); // Use FETCH_ASSOC for clarity
    if (!empty($_rs)) {
        if (is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)) {
            foreach ($_mdl->generator_fields_names as $i => $fieldname) {
                $_mdl->{"_".$fieldname} = isset($_rs[0][$fieldname]) ? $_rs[0][$fieldname] : null;
                if ($fieldname == "invoice_type") {
                    error_log("Invoice Type fetched: " . ($_rs[0][$fieldname] ?? 'Not set'));
                }
                if ($fieldname == "customer") {
                    error_log("Customer value fetched: " . ($_rs[0][$fieldname] ?? 'Not set'));
                }
            }
            $_mdl->_transactionmode = $_REQUEST["transactionmode"];
        }
    } else {
        error_log("No record found for rent_invoice_id: " . $_REQUEST["rent_invoice_id"]);
    }
}
}


$_bll=new bll_rentinvoicemaster();

/*** FOR DETAIL ***/
$_blldetail=new bll_rentinvoicedetail();
/*** /FOR DETAIL ***/
if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
// Add this to the actions section
if(isset($_REQUEST["action"])) {
    $action=$_REQUEST["action"];
    if($action == "get_company_year") {
        $companyYearId = $_REQUEST["company_year_id"];
        try {
            $stmt = $_dbh->prepare("SELECT start_date, end_date FROM tbl_company_year_master WHERE company_year_id = ?");
            $stmt->execute([$companyYearId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row) {
                echo json_encode([
                    'success' => true,
                    'start_date' => $row['start_date'],
                    'end_date' => $row['end_date']
                ]);
            } else {
                echo json_encode(['success' => false]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    $_bll->$action();
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
    if(is_array($_bll->_mdl->generator_fields_names) && !empty($_bll->_mdl->generator_fields_names)){
        foreach($_bll->_mdl->generator_fields_names as $i=>$fieldname)
        {
            if(isset($_REQUEST[$fieldname])) {
                // Special handling for lot_no field which comes as array
                if($fieldname == "lot_no" && is_array($_REQUEST[$fieldname])) {
                    $field = implode(",", $_REQUEST[$fieldname]); // Convert array to comma-separated string
                } else {
                    $field = is_array($_REQUEST[$fieldname]) ? implode(",", $_REQUEST[$fieldname]) : trim($_REQUEST[$fieldname]);
                }
            } else {
                if($_bll->_mdl->generator_field_data_type[$i]=="int" || $_bll->_mdl->generator_field_data_type[$i]=="bigint" || $_bll->_mdl->generator_field_data_type[$i]=="decimal") {
                    $field = 0;
                } else {
                    $field = null;
                }
            }
            $_bll->_mdl->{"_".$fieldname} = $field;
        }
    }

    // Ensure created_by is set to a valid user ID
    if($tmode == "I") { // Only for insert operations
        $_bll->_mdl->_created_by = isset($_SESSION['sess_user_id']) ? $_SESSION['sess_user_id'] : 1; // Fallback to admin user if session not set
        $_bll->_mdl->_created_date = date('Y-m-d H:i:s');
    }
    
    // Always set modified_by and modified_date for updates
    $_bll->_mdl->_modified_by = isset($_SESSION['sess_user_id']) ? $_SESSION['sess_user_id'] : 1;
    $_bll->_mdl->_modified_date = date('Y-m-d H:i:s');

    if(isset($_REQUEST["transactionmode"])) {
        $tmode = $_REQUEST["transactionmode"];
    } else {
        $tmode = "I";
    }
    $_bll->_mdl->_transactionmode = $tmode;
        $_bll->_mdl->_created_by = $_SESSION['sess_user_id'];
    $_bll->_mdl->_modified_by = $_SESSION['sess_user_id'];
        $_bll->_mdl->_company_year_id = $_SESSION['sess_company_year_id'];
    $_bll->_mdl->_company_id = $_SESSION['sess_company_id'];
    /*** FOR DETAIL ***/
    $_bll->_mdl->_array_itemdetail = array();
    $_bll->_mdl->_array_itemdelete = array();
    if(isset($_REQUEST["detail_records"])) {
        $detail_records = json_decode($_REQUEST["detail_records"], true);
        if(!empty($detail_records)) {
            $arrayobject = new ArrayObject($detail_records);
            $_bll->_mdl->_array_itemdetail = $arrayobject;
        }
    }
    if(isset($_REQUEST["deleted_records"])) {
        $deleted_records = json_decode($_REQUEST["deleted_records"], true);
        if(!empty($deleted_records)) {
            $deleteobject = new ArrayObject($deleted_records);
            $_bll->_mdl->_array_itemdelete = $deleteobject;
        }
    }
    /*** \FOR DETAIL ***/
    
    $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
