<?php  
include_once(__DIR__ . "/../config/connection.php");
include("cls_outward_detail.php"); 
        class mdl_outwardmaster 
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
        global $tbl_outward_master;
        $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
        $select->bindParam(1,  $tbl_outward_master);
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

class bll_outwardmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_outwardmaster(); 
        $this->_dal =new dal_outwardmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       /** FOR DETAIL **/
               
        $_bllitem= new bll_outwarddetail();
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
                        $_bllitem->_mdl->outward_id = $this->_mdl->_outward_id;
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
                        $_bllitem->_mdl->outward_id = $this->_mdl->_outward_id;
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
            header("Location:../srh_outward_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_outward_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_outward_master.php");
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
    $where_condition = "t.company_id = " . COMPANY_ID;
    $select_columns = "t.outward_no,t.outward_date,t4.customer_name AS customer_name,t5.city_name AS city_name,t9.marko,t8.inward_no,t8.inward_date,t9.lot_no,t1.out_qty,t12.packing_unit_name AS packing_unit,t1.out_wt,t1.loading_charges,t.outward_order_by,t.delivery_to,t.vehicle_no,t.driver_name,t.transporter,
    t10.customer_name AS broker_name,t11.item_name AS item,t.outward_id";
   $from_clause = "
    tbl_outward_master t
    INNER JOIN tbl_customer_master t4 ON t.customer = t4.customer_id
    LEFT JOIN tbl_city_master t5 ON t4.city_id = t5.city_id
    INNER JOIN tbl_outward_detail t1 ON t.outward_id = t1.outward_id
    LEFT JOIN tbl_inward_detail t9 ON t1.inward_detail_id = t9.inward_detail_id
    LEFT JOIN tbl_inward_master t8 ON t9.inward_id = t8.inward_id
    LEFT JOIN tbl_customer_master t10 ON t8.broker = t10.customer_id
    LEFT JOIN tbl_item_master t11 ON t9.item = t11.item_id
    LEFT JOIN tbl_packing_unit_master t12 ON t9.packing_unit = t12.packing_unit_id
";
    
    $select_columns = trim(preg_replace('/\s+/', ' ', $select_columns));
    $from_clause = trim(preg_replace('/\s+/', ' ', $from_clause));
    $where_condition = trim(preg_replace('/\s+/', ' ', $where_condition));

   $sql = "CALL " . $database_name . "_search_detail('" . $select_columns . "', '" . $from_clause . "', '" . $where_condition . "')";
    $_grid = "";
    echo '
    <table id="searchMaster" class="ui celled table display">
        <thead>
            <tr>
                <th>Action</th>
                <th>Outward No<br><input type="text" data-index="1" placeholder="Search Outward No" /></th>
                <th>Outward Date<br><input type="text" data-index="2" placeholder="Search Date" /></th>
                <th>Customer<br><input type="text" data-index="3" placeholder="Search Customer" /></th>
                <th>City<br><input type="text" data-index="17" placeholder="Search City" /></th>
                <th>Broker<br><input type="text" data-index="18" placeholder="Search Broker" /></th>
                <th>Item<br><input type="text" data-index="19" placeholder="Search Item" /></th>
                <th>marko<br><input type="text" data-index="14" placeholder="Search Marko" /></th>
                <th>Inward No<br><input type="text" data-index="12" placeholder="Search Inward No" /></th>
                <th>Inward Date<br><input type="text" data-index="13" placeholder="Search Inward Date" /></th>
                <th>Lot No<br><input type="text" data-index="15" placeholder="Search Lot No" /></th>
                <th>Out Qty<br><input type="text" data-index="9" placeholder="Search Out Qty" /></th>
                <th>Packing Unit<br><input type="text" data-index="16" placeholder="Search Unit" /></th>
                <th>Out Wt<br><input type="text" data-index="10" placeholder="Search Out Wt" /></th>
                <th>Loading Charges<br><input type="text" data-index="11" placeholder="Search Charges" /></th>
                <th>Outward Order By<br><input type="text" data-index="4" placeholder="Search Order By" /></th>
                <th>Del. To<br><input type="text" data-index="5" placeholder="Search Delivery To" /></th>
                <th>Vehicle No<br><input type="text" data-index="6" placeholder="Search Vehicle No" /></th>
                <th>Driver Name<br><input type="text" data-index="7" placeholder="Search Driver Name" /></th>
                <th>Transporter<br><input type="text" data-index="8" placeholder="Search Transporter" /></th>
            </tr>
        </thead>
        <tbody>';

    $j = 0;

    foreach ($_dbh->query($sql) as $_rs) {
        $j++;

        $_grid .= "<tr>
            <td>
                <form method='post' action='frm_outward_master.php' style='display:inline; margin-right:5px;'>
                    <i class='fa fa-edit update' style='cursor: pointer;'></i>
                    <input type='hidden' name='outward_id' value='{$_rs["outward_id"]}' />
                    <input type='hidden' name='transactionmode' value='U' />
                </form>
                <form method='post' action='classes/cls_outward_master.php' style='display:inline;'>
                    <i class='fa fa-trash delete' style='cursor: pointer;'></i>
                    <input type='hidden' name='outward_id' value='{$_rs["outward_id"]}' />
                    <input type='hidden' name='transactionmode' value='D' />
                </form>
            </td>
            <td>{$_rs["outward_no"]}</td>
            <td>" . (isset($_rs["outward_date"]) ? date("d/m/Y", strtotime($_rs["outward_date"])) : "") . "</td>
            <td>{$_rs["customer_name"]}</td>
            <td>{$_rs["city_name"]}</td>
            <td>{$_rs["broker_name"]}</td>
            <td>{$_rs["item"]}</td>
            <td>{$_rs["marko"]}</td>
            <td>{$_rs["inward_no"]}</td>
            <td>" . (isset($_rs["inward_date"]) ? date("d/m/Y", strtotime($_rs["inward_date"])) : "") . "</td>
            <td>{$_rs["lot_no"]}</td>
            <td>{$_rs["out_qty"]}</td>
            <td>{$_rs["packing_unit"]}</td>
            <td>{$_rs["out_wt"]}</td>
            <td>{$_rs["loading_charges"]}</td>
            <td>{$_rs["outward_order_by"]}</td>
            <td>{$_rs["delivery_to"]}</td>
            <td>{$_rs["vehicle_no"]}</td>
            <td>{$_rs["driver_name"]}</td>
            <td>{$_rs["transporter"]}</td>
        </tr>";
    }

    if ($j == 0) {
        $_grid .= "<tr><td colspan='16'>No records available.</td></tr>";
    }

    $_grid .= "</tbody></table>";
    echo $_grid;
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
            echo 0;
            exit;
        }
        echo 0;
        exit;
    }
}
 class dal_outwardmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;       
        try {
            $_dbh->exec("set @p0 = ".$_mdl->_outward_id);
            $_pre=$_dbh->prepare("CALL outward_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
            
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
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                $insertedId = $result->fetchColumn();
                $_mdl->_outward_id=$insertedId;
            }
            /*** /FOR DETAIL ***/
    
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL outward_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["outward_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {
            if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                foreach($_mdl->generator_fields_names as $i=>$fieldname)
                {
                    $_mdl->{"_".$fieldname}=$_rs[0][$fieldname];
                }
                $_mdl->_transactionmode =$_REQUEST["transactionmode"];
            }
        }
    }
}
$_bll=new bll_outwardmaster();


/*** FOR DETAIL ***/
$_blldetail=new bll_outwarddetail();
/*** /FOR DETAIL ***/
if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}

if (isset($_POST["masterHidden"]) && ($_POST["masterHidden"] == "save")) {
    // Debug: show incoming data (you may remove after testing)
//     echo "<pre>";
//     print_r($_POST);
//     echo "</pre>";
//     exit;

    // Populate master fields into model as before
    if (is_array($_bll->_mdl->generator_fields_names) && !empty($_bll->_mdl->generator_fields_names)) {
        foreach ($_bll->_mdl->generator_fields_names as $i => $fieldname) {
            if (isset($_REQUEST[$fieldname]) && !empty($_REQUEST[$fieldname]))
                $field = trim($_REQUEST[$fieldname]);
            else {
                if (
                    $_bll->_mdl->generator_field_data_type[$i] == "int" ||
                    $_bll->_mdl->generator_field_data_type[$i] == "bigint" ||
                    $_bll->_mdl->generator_field_data_type[$i] == "decimal"
                )
                    $field = 0;
                else
                    $field = null;
            }
            $_bll->_mdl->{"_" . $fieldname} = $field;
        }
    }

    if (isset($_REQUEST["transactionmode"]))
        $tmode = $_REQUEST["transactionmode"];
    else
        $tmode = "I";
    $_bll->_mdl->_transactionmode = $tmode;

    /*** FOR DETAIL (use new JSON field from modal) ***/
    $_bll->_mdl->_array_itemdetail = array();
    $_bll->_mdl->_array_itemdelete = array();

    // Correction: use 'selected_inwards_json' for details
    if (isset($_REQUEST["selected_inwards_json"])) {
        $detail_records = json_decode($_REQUEST["selected_inwards_json"], true);
        if (!empty($detail_records)) {
            // If you want an ArrayObject (optional, otherwise just assign array)
            $arrayobject = new ArrayObject($detail_records);
            $_bll->_mdl->_array_itemdetail = $arrayobject;
        }
    }

    // If you still want to support deleted rows through JSON (optional)
    if (isset($_REQUEST["deleted_records"])) {
        $deleted_records = json_decode($_REQUEST["deleted_records"], true);
        if (!empty($deleted_records)) {
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
