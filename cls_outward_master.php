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
        $where_condition=" t.company_id=".COMPANY_ID;
        $sql="CAll ".$database_name."_search_detail('t.outward_sequence, t.outward_date, t4.customer_name as val4, t.total_qty, t.total_wt, t.gross_wt, t.tare_wt, t.outward_id','tbl_outward_master t INNER JOIN tbl_customer_master t4 ON t.customer=t4.customer_id','{$where_condition}')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> Outward No <br><input type=\"text\" data-index=\"1\" placeholder=\"Search Outward No\" /></th> 
                         <th> Outward Date <br><input type=\"text\" data-index=\"3\" placeholder=\"Search Outward Date\" /></th> 
                         <th> Customer <br><input type=\"text\" data-index=\"4\" placeholder=\"Search Customer\" /></th> 
                         <th> Total Qty <br><input type=\"text\" data-index=\"5\" placeholder=\"Search Total Qty\" /></th> 
                         <th> Total Wt <br><input type=\"text\" data-index=\"6\" placeholder=\"Search Total Wt\" /></th> 
                         <th> Gross Wt <br><input type=\"text\" data-index=\"7\" placeholder=\"Search Gross Wt\" /></th> 
                         <th> Tare Wt <br><input type=\"text\" data-index=\"8\" placeholder=\"Search Tare Wt\" /></th> 
                         </tr>
        </thead>
        <tbody>";
         $_grid="";
         $j=0;
        foreach($_dbh-> query($sql) as $_rs)
        {
            $j++;
        
        $_grid.="<tr>
        <td> 
            <form  method=\"post\" action=\"frm_outward_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"outward_id\" value=\"".$_rs["outward_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_outward_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"outward_id\" value=\"".$_rs["outward_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $fieldvalue=$_rs["outward_sequence"];
                            $_grid.= "<td> ".$fieldvalue." </td>"; 
                             if(!empty($_rs["outward_date"])) {
                             $fieldvalue=date("d/m/Y",strtotime($_rs["outward_date"]));
                             $fieldvalue.="<br><small> ".date("h:i:s a",strtotime($_rs["outward_date"]))."</small>";
                             }
                            $_grid.= "<td> ".$fieldvalue." </td>"; 
                       
                            $fieldvalue=$_rs["val4"];
                            $_grid.= "<td> ".$fieldvalue." </td>"; 
                       
                            $fieldvalue=$_rs["total_qty"];
                            $_grid.= "<td> ".$fieldvalue." </td>"; 
                       
                            $fieldvalue=$_rs["total_wt"];
                            $_grid.= "<td> ".$fieldvalue." </td>"; 
                       
                            $fieldvalue=$_rs["gross_wt"];
                            $_grid.= "<td> ".$fieldvalue." </td>"; 
                       
                            $fieldvalue=$_rs["tare_wt"];
                            $_grid.= "<td> ".$fieldvalue." </td>"; 
                       
                            $_grid.= "</tr>\n";
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"25\">No records available.</td>";
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="</tr>";
            }
        $_grid.="</tbody>
        </table> ";
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
