<?php  
include_once(__DIR__ . "/../config/connection.php");
include("cls_outward_detail.php"); 
        class mdl_outwardmaster 
{             
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
        $sql="CAll ".$database_name."_search_detail('t.outward_sequence, t.outward_date, t4.customer_name as val4, t.total_qty, t.total_wt, t.gross_wt, t.tare_wt, t.outward_id','tbl_outward_master t INNER JOIN tbl_customer_master cm INNER JOIN tbl_inward_master im ON cm.customer_id=im.customer t4 ON t.customer=t4.customer_id','{$where_condition}')";
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
            //echo "Error: " . $e->getMessage();
            echo 0;
            exit;
        }
        echo 0;
        exit;
    }
    public function getForm($transactionmode="I",$label_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1", $field_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2") {
        $output=""; $hidden_str="";
         if(isset($this->_mdl->generator_table_layout))
            $table_layout=$this->_mdl->generator_table_layout;
        else
            $table_layout="vertical";
        if(is_array($this->_mdl->generator_fields_names) && !empty($this->_mdl->generator_fields_names)){
            if($table_layout=="horizontal") {
                $label_layout_classes="col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                $field_layout_classes="col-8 col-sm-4 col-md-3 col-lg-2";
            } else {
                $label_layout_classes=$label_classes." col-form-label";
                $field_layout_classes=$field_classes;
            }
            $output.='<div class="box-body">
                <div class="form-group row gy-2">';
            foreach($this->_mdl->generator_fields_names as $i=>$fieldname)
            {
                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$duplicate_str="";$cls_field_name="_".$fieldname;$is_disabled=0;$disabled_str="";

                if(!empty($this->_mdl->generator_field_required) && in_array($fieldname,$this->_mdl->generator_field_required)) {
                    $required=1;
                }
                if(!empty($this->_mdl->generator_field_is_disabled) && in_array($fieldname,$this->_mdl->generator_field_is_disabled)) {
                    $is_disabled=1;
                }
                if(!empty($this->_mdl->generator_chk_duplicate) && in_array($fieldname,$this->_mdl->generator_chk_duplicate)) {
                    $error_container='<div class="invalid-feedback"></div>';
                    $duplicate_str="duplicate";
                }
                if($this->_mdl->generator_fields_labels[$i]) {
                    $lbl_str='<label for="'.$fieldname.'" class="'.$label_layout_classes.'">'.$this->_mdl->generator_fields_labels[$i].'';
                        if($table_layout=="vertical") {
                            $field_layout_classes=$field_classes;
                        } 
                } else {
                    if($table_layout=="vertical") {
                        $field_layout_classes="col-12";
                    } 
                }   
                if($required) {
                    $required_str="required";
                    $error_container='<div class="invalid-feedback"></div>';
                    $lbl_str.="*";
                }
                if($is_disabled) {
                    $disabled_str="disabled";
                }
                $lbl_str.="</label>";
                switch($this->_mdl->generator_fields_types[$i]) {
                    case "text":
                    case "email":
                    case "file":
                    case "date":
                    case "datetime-local":
                    case "radio":
                    case "checkbox":
                    case "number":
                    case "select":
                        $value="";$field_str="";$cls="";$flag=0;
                            $table=explode("_",$fieldname);
                            $field_name=$table[0]."_name";
                            $fields=$fieldname.", ".$table[0]."_name";
                            $tablename="tbl_".$table[0]."_master";
                            $selected_val="";
                            if($this->_mdl->$cls_field_name) {
                                $selected_val=$this->_mdl->$cls_field_name;
                            }
                            if(!empty($this->_mdl->generator_where_condition[$i]))
                                $where_condition_val=$this->_mdl->generator_where_condition[$i];
                            else {
                                $where_condition_val=null;
                            }
                            if($this->_mdl->generator_fields_types[$i]=="checkbox" || $this->_mdl->generator_fields_types[$i]=="radio") {
                                    $cls.=$required_str;
                                    if(!empty($this->_mdl->generator_dropdown_table[$i]) && !empty($this->_mdl->generator_label_column[$i]) && !empty($this->_mdl->generator_value_column[$i])) {
                                        $flag=1;
                                        $field_str.=getChecboxRadios($this->_mdl->generator_dropdown_table[$i],$this->_mdl->generator_value_column[$i],$this->_mdl->generator_label_column[$i],$where_condition_val,$fieldname,$selected_val, $cls, $required_str, $this->_mdl->generator_fields_types[$i]).$error_container;
                                    }
                                    else{
                                            if($transactionmode=="U" && $this->_mdl->$cls_field_name==1) {
                                                $chk_str="checked='checked'";
                                            }
                                            $value="1";
                                            $field_str.=addHidden($fieldname,0);
                                    }
                            } else {
                                $cls.="form-control ".$required_str." ".$duplicate_str;
                                $chk_str="";
                                    if(isset($this->_mdl)) {
                                        $value=$this->_mdl->$cls_field_name; 
                                }
                            }
                            if(!empty($value) && ($this->_mdl->generator_fields_types[$i]=="date" || $this->_mdl->generator_fields_types[$i]=="datetime-local" || $this->_mdl->generator_fields_types[$i]=="datetime" || $this->_mdl->generator_fields_types[$i]=="timestamp")) {
                                $value=date("Y-m-d",strtotime($value));
                            }
                            if($this->_mdl->generator_fields_types[$i]=="number") {
                                $step="";
                                if(!empty($this->_mdl->generator_field_scale[$i]) && $this->_mdl->generator_field_scale[$i]>0) {
                                    for($k=1;$k<$this->_mdl->generator_field_scale[$i];$k++) {
                                        $step.=0;
                                    }
                                    $step="0.".$step."1";
                                } else {
                                    $step=1;
                                }
                                $step_str='step="'.$step.'"';
                                $min=1; 
                                if(!empty($this->_mdl->generator_allow_zero) && in_array($fieldname,$this->_mdl->generator_allow_zero)) 
                                    $min=0;
                                if(!empty($this->_mdl->generator_allow_minus) && in_array($fieldname,$this->_mdl->generator_allow_minus)) 
                                $min="";

                                $min_str='min="'.$min.'"';
                                $field_str.=addNumber($fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,$min_str,$step_str).$error_container;
                            }
                           else if($this->_mdl->generator_fields_types[$i]=="select") {
                        $cls = "form-select " . $required_str . " " . $duplicate_str;

                        if (
                            !empty($this->_mdl->generator_dropdown_table[$i]) && 
                            !empty($this->_mdl->generator_label_column[$i]) && 
                            !empty($this->_mdl->generator_value_column[$i])
                        ) {
                            $dropdown_html = getDropdown(
                                $this->_mdl->generator_dropdown_table[$i],
                                $this->_mdl->generator_value_column[$i],
                                $this->_mdl->generator_label_column[$i],
                                $where_condition_val,
                                $fieldname,
                                $selected_val,
                                $cls,
                                $required_str
                            );
                            if (strpos(strtolower($fieldname), 'customer') !== false) {
                            $field_str .= '
                                <div style="display: flex; align-items: flex-start; gap: 5px;">
                                    <div style="flex: 1;">
                                        ' . $dropdown_html . '
                                        ' . $error_container . '
                                        <div id="customer_error" class="invalid-feedback" style="display:none;">Please select customer</div>
                                    </div>
                                    <button type="button" class="btn btn-info" id="btn_inward"
                                        data-bs-toggle="modal" data-bs-target="#pendingInwardModal">
                                        Select Inward
                                    </button>
                                </div>
                            ';
                            } else {
                                $field_str .= $dropdown_html . $error_container;
                            }
                            }
                            }else {
                                if($flag==0) {
                                    $field_str.=addInput($this->_mdl->generator_fields_types[$i],$fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str,$chk_str).$error_container;
                                }
                            }
                        break;
                    case "hidden":
                        $lbl_str="";
                        if($this->_mdl->generator_field_data_type[$i]=="int" || $this->_mdl->generator_field_data_type[$i]=="bigint"  || $this->_mdl->generator_field_data_type[$i]=="tinyint" || $this->_mdl->generator_field_data_type[$i]=="decimal")
                            $hiddenvalue=0;
                        else
                            $hiddenvalue="";
                        
                        if($fieldname=="company_id") {
                            $hiddenvalue=COMPANY_ID;
                        }
                        else if($fieldname=="created_by") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=USER_ID;
                            }
                        } else if($fieldname=="created_date") {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } else {
                                $hiddenvalue=date("Y-m-d H:i:s");
                            }
                        } else if($fieldname=="modified_by") {
                            $hiddenvalue=USER_ID; 
                        } else if($fieldname=="modified_date") {
                            $hiddenvalue=date("Y-m-d H:i:s");
                        } else {
                            if($transactionmode=="U") {
                                $hiddenvalue=$this->_mdl->$cls_field_name;
                            } 
                        }
                        $hidden_str.=addHidden($fieldname,$hiddenvalue);
                    
                        break;
                    case "textarea":
                        $value="";
                        if(isset($this->_mdl)){
                                $value=$this->_mdl->$cls_field_name;
                            }
                        $field_str.=addTextArea($fieldname,$value,$required_str,$disabled_str,$cls,$duplicate_str).$error_container;
                        break;
                    default:
                        break;
                } //switch ends
                 if(empty($this->_mdl->generator_after_detail) || (!empty($this->_mdl->generator_after_detail) && !in_array($fieldname,$this->_mdl->generator_after_detail))) {
                    if($table_layout=="vertical" && $this->_mdl->generator_fields_types[$i]!="hidden") {
                        $output.='<div class="row mb-3 align-items-center">';
                    }
                    $output.=$lbl_str;
                    if($field_str) {
                    $output.='<div class="'.$field_layout_classes.'">';
                    $output.=$field_str;
                    $output.='</div>';
                    }
                    if($table_layout=="vertical" && $this->_mdl->generator_fields_types[$i]!="hidden") {
                        $output.='</div>';
                    }
                } else {
                    $lbl_array[]=$lbl_str;
                    $field_array[]=$field_str;
                }
            } // foreach ends
            $output.="</div><!-- /.row -->";
            $output.=$hidden_str;
               $output.="</div> <!-- /.box-body -->";
        
               // Detail table content
            $output .= '<!-- detail table content-->
                <div class="box-body">
                    <div class="box-detail">';

            $_blldetail = new bll_outwarddetail();
            $detailHtml = $_blldetail->pageSearch();
            if ($detailHtml) {
                $output .= $detailHtml;
            }

            $output .= '<button type="button" name="detailBtn" id="detailBtn" class="btn btn-primary add" data-bs-toggle="modal" data-bs-target="#modalDialog" onclick="openModal()">Add Detail Record</button>
                    </div>
                </div>
                <!-- /.box-body detail table content -->';
            
            if(!empty($field_array)) {
                $output.='<div class="box-body">
                <div class="form-group row gy-2">';
                 for($j=0;$j<count($field_array);$j++) {
                    if($table_layout=="vertical") {
                        $output.='<div class="row mb-3 align-items-center">';
                    }
                    $output.=$lbl_array[$j];
                    if($field_array[$j]) {
                        $output.='<div class="col-8 col-sm-4 col-md-3 col-lg-2>';
                        $output.=$field_array[$j];
                        $output.='</div>';
                    }
                    if($table_layout=="vertical") {
                        $output.='</div>';
                    }
                 } // for loop ends
                $output .= <<<HTML
<div class="detail-modal">
    <div id="pendingInwardModal" class="modal fade" tabindex="-1" aria-labelledby="pendingInwardLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="pendingInwardLabel">Pending Inward</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <table class="table table-bordered table-striped table-sm align-middle" style="width:100%;">
                        <thead class="table-light boxheader">
                            <tr>
                                <th>Select</th>
                                <th>Inward No.</th>
                                <th>Lot No.</th>
                                <th>Inward Date</th>
                                <th>Broker</th>
                                <th>Item</th>
                                <th>variety</th>
                                <th>Inward Qty</th>
                                <th>Unit</th>
                                <th>Inward Wt</th>
                                <th>Stock Qty</th>
                                <th>Stock Wt.(Kg)</th>
                                <th>Out Qty</th>
                                <th>Out Wt.(Kg)</th>
                                <th>Loading Charges</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody id="pendingInwardTableBody">
                        <?php
                        while (\$row = \$stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr data-inward-id='" . htmlspecialchars(\$row['inward_id'] ?? 0) . "' data-inward-detail-id='" . htmlspecialchars(\$row['inward_detail_id'] ?? 0) . "'>";

                            echo "<td><input type='checkbox' name='select_inward[]' value='".htmlspecialchars(\$row['inward_no'] ?? '')."'></td>";
                            echo "<td data-label='Inward No.'>" . htmlspecialchars(\$row['inward_no'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Lot No.'>" . htmlspecialchars(\$row['lot_no'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Inward Date'>" . (!empty(\$row['inward_date']) ? date("d-m-Y", strtotime(\$row['inward_date'])) : 'N/A') . "</td>";
                            echo "<td data-label='Broker'>" . htmlspecialchars(\$row['broker'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Item'>" . htmlspecialchars(\$row['item'] ?? 'N/A') . "</td>";
                            echo "<td data-label='variety'>" . htmlspecialchars(\$row['variety'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Inward Qty'>" . htmlspecialchars(\$row['inward_qty'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Unit'>" . htmlspecialchars(\$row['packing_unit'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Inward Wt'>" . htmlspecialchars(\$row['inward_wt'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Stock Qty'>" . htmlspecialchars(\$row['stock_qty'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Stock Wt'>" . htmlspecialchars(\$row['stock_wt'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Out Qty'>" . htmlspecialchars(\$row['out_qty'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Out Wt'>" . htmlspecialchars(\$row['out_wt'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Loading Charges'>" . htmlspecialchars(\$row['loading_charge'] ?? 'N/A') . "</td>";
                            echo "<td data-label='Location'>" . htmlspecialchars(\$row['location'] ?? 'N/A') . "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveSelectedInward" class="btn btn-success">Ok</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
HTML;
            }
        } // if ends
        return $output;
    } // function getForm ends
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
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
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
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
 
    if(is_array($_bll->_mdl->generator_fields_names) && !empty($_bll->_mdl->generator_fields_names)){
        foreach($_bll->_mdl->generator_fields_names as $i=>$fieldname)
        {
            if(isset($_REQUEST[$fieldname]) && !empty($_REQUEST[$fieldname]))
                $field=trim($_REQUEST[$fieldname]);
            else {
                if($_bll->_mdl->generator_field_data_type[$i]=="int" || $_bll->_mdl->generator_field_data_type[$i]=="bigint" || $_bll->_mdl->generator_field_data_type[$i]=="decimal")
                    $field=0;
                else
                    $field=null;
            }
            $_bll->_mdl->{"_".$fieldname}=$field;
        }
    }
   
 
if(isset($_REQUEST["transactionmode"]))
    $tmode=$_REQUEST["transactionmode"];
else
    $tmode="I";
$_bll->_mdl->_transactionmode =$tmode;
 
               /*** FOR DETAIL ***/
                $_bll->_mdl->_array_itemdetail=array();
                $_bll->_mdl->_array_itemdelete=array();
                if(isset($_REQUEST["detail_records"])) {
                  $detail_records=json_decode($_REQUEST["detail_records"],true);
                   if(!empty($detail_records)) {
                        $arrayobject = new ArrayObject($detail_records);
                          $_bll->_mdl->_array_itemdetail=$arrayobject;
                    }
                }
                if(isset($_REQUEST["deleted_records"])) {
                  $deleted_records=json_decode($_REQUEST["deleted_records"],true);
                   if(!empty($deleted_records)) {
                        $deleteobject = new ArrayObject($deleted_records);
                          $_bll->_mdl->_array_itemdelete=$deleteobject;
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
