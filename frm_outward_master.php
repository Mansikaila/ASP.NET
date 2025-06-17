<?php
    include("classes/cls_outward_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="";
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    if( $transactionmode=="U")       
    {    
        $_bll->fillModel();
        $label="Update";
    } else {
        $label="Add";
    }
if (isset($_GET['get_contact_persons']) && isset($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);
    $options = '<option value="">Select</option>';
    if ($customer_id > 0) {
        try {
            $stmt = $_dbh->prepare("SELECT contact_person_id, contact_person_name FROM tbl_contact_person_detail WHERE customer_id = ? ORDER BY contact_person_name");
            $stmt->execute([$customer_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $id = htmlspecialchars($row['contact_person_id']);
                    $name = htmlspecialchars($row['contact_person_name']);
                    $options .= "<option value=\"$id\">$name</option>";
                }
            } else {
                $options = '<option value="">No contact persons found</option>';
            }
        } catch (PDOException $e) {
            $options = '<option value="">Error loading contact persons</option>';
            error_log("Database error: " . $e->getMessage());
        }
    }
    echo $options;
    exit;
}

global $_dbh;
$next_outward_sequence = 1;
$outward_no_formatted = '';
$finYear = '';
try {
    $companyYearId = $_SESSION['sess_company_year_id'] ?? null;
    if ($companyYearId) {
        $stmt = $_dbh->prepare("
            SELECT 
                CONCAT(LPAD(YEAR(start_date) % 100, 2, '0'), '-', LPAD(YEAR(end_date) % 100, 2, '0')) AS short_range,
                start_date, end_date
            FROM tbl_company_year_master 
            WHERE company_year_id = ?
        ");
        $stmt->execute([$companyYearId]);
        $yearRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($yearRow) {
            $finYear = $yearRow['short_range'];
            $startDate = $yearRow['start_date'];
            $endDate = $yearRow['end_date'];
            $stmt2 = $_dbh->prepare("
                SELECT MAX(outward_sequence) AS max_seq
                FROM tbl_outward_master 
                WHERE outward_date BETWEEN ? AND ?
            ");
            $stmt2->execute([$startDate, $endDate]);
            $seqRow = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($seqRow && is_numeric($seqRow['max_seq'])) {
                $next_outward_sequence = $seqRow['max_seq'] + 1;
            }
            $sequence_padded = str_pad($next_outward_sequence, 4, '0', STR_PAD_LEFT);
            $outward_no_formatted = $sequence_padded . '/' . $finYear;
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?php echo $label; ?> Data
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_outward_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="box-body">
                <div class="form-group row gy-2">                
    <?php
            global $database_name;
            global $_dbh;
            $hidden_str="";
            $table_name="tbl_outward_master";
            $lbl_array=array();
            $field_array=array();
            $err_array=array();
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
                    $detail_table_code = '
                    <!-- detail table content-->
                        <div class="box-detail">';
                    $_blldetail = new bll_outwarddetail();
                    $detailHtml = $_blldetail->pageSearch();
                    if ($detailHtml) {
                        $detail_table_code .= $detailHtml;
                    }
                    $detail_table_code .= '
                        </div>
                    <!-- /.box-body detail table content -->';
             if($row) {
                    $generator_options=json_decode($row["generator_options"]);
                    //print_r($generator_options);
                    if($generator_options) {
                        $table_layout=$generator_options->table_layout;
                        $fields_names=$generator_options->field_name;
                        $fields_types=$generator_options->field_type;
                        $field_scale=$generator_options->field_scale;
                        $dropdown_table=$generator_options->dropdown_table;
                        $label_column=$generator_options->label_column;
                        $value_column=$generator_options->value_column;
                        $where_condition=$generator_options->where_condition;
                        $fields_labels=$generator_options->field_label;
                        $field_display=$generator_options->field_display;
                        $field_required=$generator_options->field_required;
                        $allow_zero=$generator_options->allow_zero;
                        $allow_minus=$generator_options->allow_minus;
                        $chk_duplicate=$generator_options->chk_duplicate;
                        $field_data_type=$generator_options->field_data_type;
                        $field_is_disabled=$generator_options->is_disabled;
                        $after_detail=$generator_options->after_detail;

                        $old_table_layout=$table_layout;
                        if($table_layout=="horizontal") {
                            $label_layout_classes="col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                            $field_layout_classes="col-8 col-sm-4 col-md-3 col-lg-2";
                        } else {
                            $label_layout_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                            $field_layout_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                        }
                        $total_qty_index = array_search('total_qty', $fields_names);
                        if(is_array($fields_names) && !empty($fields_names)) {
                            
                            for($i=0;$i<count($fields_names);$i++) {
                                if ($i == $total_qty_index) {
                                    echo $detail_table_code;
                                }
                                if($fields_names[$i] == "outward_sequence" || $fields_names[$i] == "outward_no") {
                                    $table_layout="horizontal";
                                } else{
                                    $table_layout=$old_table_layout;
                                } 
                               $table_layout = ($fields_names[$i] == "outward_sequence" || $fields_names[$i] == "outward_no") ? "horizontal" : $old_table_layout;
                                if ($fields_names[$i] == "outward_sequence" || $fields_names[$i] == "outward_no") {
                                    if ($fields_names[$i] == "outward_sequence") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-1 mt-3";
                                    }
                                    else if ($fields_names[$i] == "outward_no") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-4 col-md-2 col-lg-2 col-xxl-1 mt-3";
                                    }
                                } else {
                                    if ($table_layout == "horizontal") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-8 col-md-9 col-lg-10";
                                    } else {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                                    }
                                }
                                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$duplicate_str="";
                                 $cls_field_name="_".$fields_names[$i];$is_disabled=0;$disabled_str="";
                                 
                                if(!empty($field_required) && in_array($fields_names[$i],$field_required)) {
                                    $required=1;
                                }
                                if(!empty($field_is_disabled) && in_array($fields_names[$i],$field_is_disabled)) {
                                    $is_disabled=1;
                                }
                                if(!empty($chk_duplicate) && in_array($fields_names[$i],$chk_duplicate)) {
                                    $error_container='<div class="invalid-feedback"></div>';
                                    $duplicate_str="duplicate";
                                }
                                   $custom_col_class = "";
                                if($fields_labels[$i]) {
                                    $lbl_str='<label for="'.$fields_names[$i].'" class="'.$label_layout_classes.'">'.$fields_labels[$i].'';
                                     if($table_layout=="vertical") {
                                        $field_layout_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
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
                                switch($fields_types[$i]) {
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
                                         $table=explode("_",$fields_names[$i]);
                                            $field_name=$table[0]."_name";
                                            $fields=$fields_names[$i].", ".$table[0]."_name";
                                            $tablename="tbl_".$table[0]."_master";
                                            $selected="";
                                            if($_bll->_mdl->$cls_field_name) {
                                                $selected=$_bll->_mdl->$cls_field_name;
                                            }
                                            if(!empty($where_condition[$i]))
                                                $where_condition_val=$where_condition[$i];
                                            else {
                                                $where_condition_val=null;
                                            }
                                            if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                             $cls.=$required_str;
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $flag=1;
                                                $field_str.=getChecboxRadios($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected, $cls, $required_str, $fields_types[$i]).$error_container;
                                            }
                                            else{
                                                    if($transactionmode=="U" && $_bll->_mdl->$cls_field_name==1) {
                                                        $chk_str="checked='checked'";
                                                    }
                                                    $value="1";
                                                    $field_str.='<input type="hidden" name="'.$fields_names[$i].'" value="0" />';
                                            }
                                        } else {
                                            $cls.="form-control ".$required_str." ".$duplicate_str;
                                            $chk_str="";
                                              if (($fields_names[$i] == "outward_sequence" || $fields_names[$i] == "outward_no") && $transactionmode != "U") {
                                                if ($fields_names[$i] == "outward_sequence") {
                                                    $value = $next_outward_sequence;
                                                } else {
                                                    $value = $outward_no_formatted;
                                                }
                                                $readonly_str = "readonly";
                                            } else {
                                                $value = isset($_bll->_mdl) ? $_bll->_mdl->$cls_field_name : "";
                                            }
                                        }
                                         if($fields_types[$i]=="number") {
                                            $step="";
                                            if(!empty($field_scale[$i]) && $field_scale[$i]>0) {
                                                for($k=1;$k<$field_scale[$i];$k++) {
                                                    $step.=0;
                                                }
                                                $step="0.".$step."1";
                                            } else {
                                                $step=1;
                                            }
                                            $step_str='step="'.$step.'"';
                                             $min=1; 
                                             if(!empty($allow_zero) && in_array($fields_names[$i],$allow_zero)) 
                                                 $min=0;
                                             if(!empty($allow_minus) && in_array($fields_names[$i],$allow_minus)) 
                                                $min="";

                                             $min_str='min="'.$min.'"';
                                         }
                                         if(!empty($value) && ($fields_types[$i]=="date" || $fields_types[$i]=="datetime-local" || $fields_types[$i]=="datetime" || $fields_types[$i]=="timestamp")) {
                                                $value=date("Y-m-d",strtotime($value));
                                         }
                                         if ($fields_names[$i] == 'outward_date' && empty($value)) {
                                        $value = '';  
                                            }
                                        
                                        if($fields_names[$i] == 'outward_date') {
                                            $error_container='<div id="outward_date_error" class="invalid-feedback"></div>';

                                        }
                                         if ($fields_types[$i] == "select") {
                                            $cls = "form-select " . $required_str . " " . $duplicate_str;
                                            $table = explode("_", $fields_names[$i]);
                                            $field_name = $table[0] . "_name";
                                            $fields = $fields_names[$i] . ", " . $field_name;
                                            $tablename = "tbl_" . $table[0] . "_master";
                                            $selected = $_bll->_mdl->$cls_field_name;
                                            $where_condition_val = !empty($where_condition[$i]) ? $where_condition[$i] : null;
                                            if (!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $dropdown_html = getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected,$cls, $required_str);
                                           if (strpos(strtolower($fields_names[$i]), 'customer') !== false) {

                                            $dropdown_html = str_replace(
                                                '<select',
                                                '<select style="width:120px; min-width:0; display:inline-block;"',
                                                $dropdown_html
                                            );
                                            $field_str .= '
                                                <div>
                                                    <div style="display: flex; align-items: flex-start; gap: 3px;">
                                                        <div style="flex: 1; max-width: 200px;"> 
                                                            ' . $dropdown_html . '
                                                            ' . $error_container . '
                                                            <div id="customer_error" class="invalid-feedback" style="display:none;">Please select customer</div>
                                                        </div>
                                                        <button type="button" class="btn btn-info" id="btn_inward" style="white-space:nowrap;">Select Inward</button>
                                                    </div>  
                                                </div>';
                                        } else {
                                            $field_str .= $dropdown_html . $error_container;
                                        }
                                        }
                                        }
                                        else {
                                            $field_str.='<input type="'.$fields_types[$i].'" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" placeholder="'.ucwords(str_replace("_"," ",$fields_names[$i])).'" value= "'.$value.'"  '.$min_str.' '.$step_str.' '.$chk_str.'  '.$disabled_str.' '.$required_str.' />
                                            '.$error_container;
                                        }
                                        break;
                                    case "hidden":
                                        $lbl_str="";
                                        if($field_data_type[$i]=="int" || $field_data_type[$i]=="bigint"  || $field_data_type[$i]=="tinyint" || $field_data_type[$i]=="decimal")
                                            $hiddenvalue=0;
                                        else
                                            $hiddenvalue="";
                                        if($fields_names[$i]!="modified_by" && $fields_names[$i]!="modified_date") {
                                            if($fields_names[$i]=="company_id") {
                                                $hiddenvalue=COMPANY_ID;
                                            }
                                            if($fields_names[$i]=="company_year_id") {
                                                $hiddenvalue=COMPANY_YEAR_ID;
                                            }
                                            else if($fields_names[$i]=="created_by") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=USER_ID;
                                                }
                                            } else if($fields_names[$i]=="created_date") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=date("Y-m-d H:i:s");
                                                }
                                            } else {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } 
                                            }
                                            $hidden_str.='
                                            <input type="'.$fields_types[$i].'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" value= "'.$hiddenvalue.'"  />';
                                        }
                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset($_bll->_mdl)){
                                             $value=$_bll->_mdl->$cls_field_name;
                                            }
                                        $field_str.='<textarea id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" class="'.$cls.'" '.$disabled_str.' placeholder="Enter '.ucwords(str_replace("_"," ",$fields_names[$i])).'"  '.$required_str.' >'.$value.'</textarea>
                                        '.$error_container;
                                        break;
                                     default:
                                        $field_str .= '<input type="'.$fields_types[$i].'" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" value="'.$value.'" '.$readonly_str.' />';

                                        if ($is_disabled) {
                                            $field_str .= '<input type="hidden" name="'.$fields_names[$i].'" value="'.$value.'" />';
                                        }
                                        $field_str .= $error_container;
                                        break;
                                } //switch ends
                                 $cls_err="";
                                    $lbl_err="";
                                   
                                if(empty($after_detail) || (!empty($after_detail) && !in_array($fields_names[$i],$after_detail))) {
                                    if($table_layout=="vertical" && $fields_types[$i]!="hidden") {
                                ?>
                                <div class="row mb-3 align-items-center">
                                <?php
                                    }
                                    echo $lbl_str;
                                    if($field_str) {
                                        $extra_margin_class = ($fields_names[$i] == 'outward_date') ? ' mt-3' : '';
                                    ?>
                                    <div class="<?php echo $field_layout_classes." ".$cls_err.$extra_margin_class; ?>">
                                    <?php
                                        echo $field_str;
                                        echo $lbl_err;
                                    ?>
                                    </div>
                                <?php
                                    }
                                if($table_layout=="vertical" && $fields_types[$i]!="hidden") {
                                ?>
                                </div>
                                <?php
                                    } // verticle condition ends
                                } else {
                                    $lbl_array[]=$lbl_str;
                                    $field_array[]=$field_str;
                                    $err_array[]=$lbl_err;
                                    $clserr_array[]=$cls_err;
                                }
                            } //for loop ends
                        } // field_types if ends
                    }
             } 
            ?>
                 </div>
              </div>

<?php
    if(!empty($field_array)) {
?>
    <div class="box-body">
    <div class="form-group row gy-2">
    <?php
        for ($j = 0; $j < count($field_array); $j++) {
            echo $lbl_array[$j];
            if ($field_array[$j]) {
    ?>
            <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $clserr_array[$j]; ?>">
                <?php
                    echo $field_array[$j];
                    echo $err_array[$j];
                ?>
            </div>
    <?php
            }
        }
    ?>
    </div>  
</div>
<?php
    }
?>
<!-- .box-footer -->
              <div class="box-footer">
               <?php echo  $hidden_str; ?>
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="modified_by" name="modified_by" value="<?php echo USER_ID; ?>">
                <input type="hidden" id="modified_date" name="modified_date" value="<?php echo date("Y-m-d H:i:s"); ?>">
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_outward_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
                  <input type="hidden" id="outward_no_hidden" name="outward_no" value="<?php echo $outward_no_formatted; ?>">
                  <input type="hidden" name="selected_inwards_json" id="selected_inwards_json" value=''>
              </div>
              <!-- /.box-footer -->
        </form>
          </div>
          </div>
      </section>
    </div>
  </div>
     <!-- Modal -->
<div class="detail-modal">
  <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <form id="popupForm" method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
          <div class="modal-body">
            <div class="box-body container-fluid">
              <div id="pendingInwardSection">
                <h5 class="modal-title" id="pendingInwardLabel">Pending Inward</h5>
                <table class="table table-bordered table-striped table-sm align-middle" style="width:100%;">
                  <thead class="table-light boxheader">
                    <tr>
                      <th>Select</th>
                      <th>Inward No.</th>
                      <th>Lot No.</th>
                      <th>Inward Date</th>
                      <th>Broker</th>
                      <th>Item</th>
                      <th>marko</th>
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
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td data-label='Inward No.'>" . htmlspecialchars($row['inward_no'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Lot No.'>" . htmlspecialchars($row['lot_no'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Inward Date'>" . (!empty($row['inward_date']) ? date("d-m-Y", strtotime($row['inward_date'])) : 'N/A') . "</td>";
                        echo "<td data-label='Broker'>" . htmlspecialchars($row['broker'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Item'>" . htmlspecialchars($row['item'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Marko'>" . htmlspecialchars($row['marko'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Inward Qty'>" . htmlspecialchars($row['inward_qty'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Unit'>" . htmlspecialchars($row['packing_unit'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Inward Wt'>" . htmlspecialchars($row['inward_wt'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Stock Qty'>" . htmlspecialchars($row['stock_qty'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Stock Wt'>" . htmlspecialchars($row['stock_wt'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Out Qty'>" . htmlspecialchars($row['out_qty'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Out Wt'>" . htmlspecialchars($row['out_wt'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Loading Charges'>" . htmlspecialchars($row['loading_charge'] ?? 'N/A') . "</td>";
                        echo "<td data-label='Location'>" . htmlspecialchars($row['location'] ?? 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
            <div class="modal-footer">
                  <button type="button" id="saveSelectedInward" class="btn btn-success">Ok</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
    include("include/footer.php");
?>
<?php
    include("include/footer_includes.php");
?>
<script>
document.addEventListener("DOMContentLoaded", function () {    
    let jsonData = [];
    let editIndex = -1;
    let deleteData = [];
    let detailIdLabel="";
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    function checkDuplicate(input) {
       let column_value = input.value.trim();
       if (column_value == "") return;
       let id_column="<?php echo "outward_id" ?>";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_outward_master.php"; ?>",
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:"<?php echo "tbl_outward_master"; ?>",action:"checkDuplicate"},
            success: function(response) {
                //let input=document.getElementById("party_sequence");
                if (response == 1) {
                    input.classList.add("is-invalid");
                    input.focus();
                    let message="";
                    if(input.validationMessage)
                        message=input.validationMessage;
                    else
                        message="Duplicate Value";
                    if(input.nextElementSibling) 
                      input.nextElementSibling.textContent = message;
                      return false;
                } else {
                   input.classList.remove("is-invalid");
                    if(input.nextElementSibling) 
                        input.nextElementSibling.textContent = "";
                }
            },
            error: function() {
                console.log("Error");
            }
        }); 
    }
        const tableHead = document.getElementById("tableHead");
        const tableBody = document.getElementById("tableBody");
        const form = document.getElementById("popupForm");
        const modalDialog = document.getElementById("modalDialog");
        const modal = new bootstrap.Modal(modalDialog);
    
        document.querySelectorAll("#searchDetail tbody tr").forEach(row => {
            let rowData = {};
            if(!row.classList.contains("norecords")) {
                rowData[row.dataset.label]=row.dataset.id;
                detailIdLabel=row.dataset.label;
                editIndex++;
                row.querySelectorAll("td[data-label]").forEach(td => {
                    if(!td.classList.contains("actions")){
                        rowData[td.dataset.label] = td.innerText;
                    }
                });
                rowData["detailtransactionmode"]="U";
                jsonData[editIndex]=rowData;
            }
        });
    modalDialog.addEventListener("hidden.bs.modal", function () {
     clearForm(form);
     setFocustAfterClose();
    });
    function openModal(index = -1) {
  
        if (index >= 0) {
            editIndex = index;
            const data = jsonData[index];

            for (let key in data) {
                const inputFields = form.elements[key];

                if (!inputFields) continue; 

                if (inputFields.length) {
                    inputFields.forEach(inputField => {
                        if (inputField.type === "checkbox" || inputField.type === "radio") {
                             if (inputField.value === data[key]) {
                                 inputField.checked = true;
                                jQuery("#"+key).attr( "checked", "checked" );
                            } else {
                                $("#"+key).removeAttr("checked");
                            }
                        }
                        else if (inputField.type !== "hidden") {
                            inputField.value = data[key]; // Avoid setting hidden field values
                        }
                    });
                } else {
                        inputFields.value = data[key]; // Avoid hidden fields
                }
            }
        } else {
            editIndex = -1;
            clearForm(form);
        }
        modal.show();
        setTimeout(() => {
            const firstInput = form.querySelector("input:not([type=hidden]), input:not(.btn-close), select, textarea");
            if (firstInput) firstInput.focus();
        }, 10);
    }
    function saveData() {
        const formData = new FormData(form);
        const newEntry = {};
        const allEntries= {};
          for (const [key, value] of formData.entries()) {
            if (!getHiddenFields().includes(key) && getDisplayFields().includes(key)) {
                newEntry[key] = value;
            } 
            if (editIndex >= 0) {
                if(jsonData[editIndex].hasOwnProperty(key)) {
                    jsonData[editIndex][key] = value;
                } 
            }
            allEntries[key]=value;
          }
        
        if($("#norecords").length>0) {
            $("#norecords").remove();
        }
        
        if (editIndex >= 0) {
            updateTableRow(editIndex, newEntry);
            modal.hide();
            Swal.fire({
                icon: "success",
                title: "Updated Successfully",
                text: "The record has been updated successfully!",
                showConfirmButton: true,
                showClass: {
                    popup: ""
                },
                hideClass: {
                    popup: ""
                }
            }).then((result) => {
                 setFocustAfterClose();
            });
        } else {
            allEntries["detailtransactionmode"]="I";
            jsonData.push(allEntries);
            appendTableRow(newEntry, jsonData.length - 1);
            modal.hide();
            Swal.fire({
                icon: "success",
                title: "Added Successfully",
                text: "The record has been added successfully!",
                showConfirmButton: true,
                showClass: {
                    popup: "" 
                },
                hideClass: {
                    popup: ""
                }
            }).then((result) => {
                  if (result.isConfirmed) {
                    modal.show();
                    setTimeout(() => {
                        const firstInput = form.querySelector("input:not([type=hidden]), input:not(.btn-close)");
                        if (firstInput) firstInput.focus();
                    }, 100);
                  }
            });
        }
        clearForm(form);
    }
    function getHiddenFields() {
      
        let hiddenFields = Array.from(form.elements)
            .filter(input => input.type === "hidden" && input.classList.contains("exclude-field"))
            .map(input => input.name);

        hiddenFields.push("detailtransactionmode");

        return hiddenFields;
    }
    function getDisplayFields() {
        let displayFields=[];
        let formElements = Array.from(form.elements);
        formElements.forEach(input => {
            if (input.length) { // Handle RadioNodeList
                for (let element of input) {
                    if (element.classList && element.classList.contains("display")) {
                        displayFields.push(input.name);
                        break;
                    }
                }
            } else if (input.classList && input.classList.contains("display")) { 
                displayFields.push(input.name);
            }
        });
      return displayFields;
  }
   function appendTableRow(rowData, index) {
    const row = document.createElement("tr");
    var id=0;
    if(detailIdLabel!=""){
        id=rowData[detailIdLabel];
    } 
    row.setAttribute("data-id", id);

    Object.keys(rowData).forEach(col => {
        if (col === 'detailtransactionmode') return;
        const cell = document.createElement("td");
        cell.textContent = rowData[col] || "";
        cell.setAttribute("data-label", col);
        row.appendChild(cell);
    });

    addActions(row, index, id);
    tableBody.appendChild(row);
}

function updateTableRow(index, rowData) {
    const row = tableBody.children[index];
    var id=0;
    if(detailIdLabel!=""){
        id=rowData[detailIdLabel];
    } 
    row.innerHTML = "";
    Object.keys(rowData).forEach(col => {
        if (col === 'detailtransactionmode') return;
        const cell = document.createElement("td");
        cell.setAttribute("data-label", col);
        cell.textContent = rowData[col] || "";
        row.appendChild(cell);
    });
    addActions(row, index, id);
}
    function addActions(row,index,id) {
        const actionCell = document.createElement("td");
        actionCell.classList.add("actions");
        const editButton = document.createElement("button");
        editButton.textContent = "Edit";
        editButton.classList.add("btn", "btn-info", "btn-sm","me-2", "edit-btn");
        editButton.setAttribute("data-index", index);
        editButton.setAttribute("data-id", id);

        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.classList.add("btn", "btn-danger", "btn-sm","delete-btn");
        deleteButton.setAttribute("data-index", index);
        deleteButton.setAttribute("data-id", id);
        
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);
        row.appendChild(actionCell);
    }
    function setFocustAfterClose() {
    var detailBtn = document.getElementById("detailBtn");
    if (detailBtn) {
        detailBtn.focus();
        }
    }
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("edit-btn")) {
            event.preventDefault();
            const index = event.target.getAttribute("data-index");
            openModal(index);
        }
    });
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-btn")) {
            event.preventDefault();
            const index = event.target.getAttribute("data-index");
            const id = event.target.getAttribute("data-id");
            deleteRow(index,id);
        }
    });
    function deleteRow(index,id) {
        Swal.fire({
          title: "Are you sure you want to delete this record?",
          text: "You won't be able to revert it!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
            if(id>0) {
                jsonData[index]["detailtransactionmode"]="D";
                deleteData.push(jsonData[index]);
            }
            jsonData.splice(index, 1);
            tableBody.innerHTML = "";
            const numberOfColumns = document.querySelector("table th") ? document.querySelector("table th").parentElement.children.length : 0;
            // Check if there are any rows left
            if (jsonData.length === 0) {
                // If no rows, add a row saying "No records"
                const noRecordsRow = document.createElement("tr");
                for(var i=1; i< numberOfColumns; i++) {
                    const noRecordsCell = document.createElement("td");
                    if(i==1) {
                        noRecordsCell.colSpan = numberOfColumns;
                        noRecordsCell.textContent = "No records available";
                    }
                    noRecordsRow.appendChild(noRecordsCell);
                }
                noRecordsRow.setAttribute("id","norecords");
                noRecordsRow.classList.add("norecords"); 
                tableBody.appendChild(noRecordsRow);
            } else {
                jsonData.forEach((data, idx) => appendTableRow(data, idx));
            }
          }
        });
    }
    $("#popupForm" ).on( "submit", function( event ) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
            let i=0;
            let firstelement;
            this.querySelectorAll(":invalid").forEach(function (input) {
              if(i==0) {
                firstelement=input;
              }
              input.classList.add("is-invalid");
              input.nextElementSibling.textContent = input.validationMessage; 
              i++;
            });
            if(firstelement) firstelement.focus(); 
            return false;
          } 
        saveData();
    } );
    window.openModal = openModal;
    window.saveData = saveData;
   
 document.getElementById("btn_add").addEventListener("click", function (event) {
    const form = document.getElementById("masterForm");
    let i=0;
    let firstelement;
     duplicateInputs.forEach((input) => {
          checkDuplicate(input);
      });
    if (!form.checkValidity()) {
        form.querySelectorAll(":invalid").forEach(function (input) {
    if(i==0) {
        firstelement=input;
    }
    input.classList.add("is-invalid");
        if (input.nextElementSibling) {
            input.nextElementSibling.textContent = input.validationMessage;
        }
        i++;
    });
         if(firstelement) firstelement.focus(); 
         return false;
    } else {
        form.querySelectorAll(".is-invalid").forEach(function (input) {
          input.classList.remove("is-invalid");
          input.nextElementSibling.textContent = "";
        });
    }
    setTimeout(function(){
        const invalidInputs = document.querySelectorAll(".is-invalid");
        if(invalidInputs.length>0)
        {} else{
        const jsonDataString = JSON.stringify(jsonData);
        const deletedDataString = JSON.stringify(deleteData);
            
        const detailRecordsInput = document.getElementById("detail_records");
        if (detailRecordsInput) detailRecordsInput.value = jsonDataString;
        const deletedRecordsInput = document.getElementById("deleted_records");
        if (deletedRecordsInput) deletedRecordsInput.value = deletedDataString;
            let transactionMode = document.getElementById("transactionmode").value;
            let message = "";
            let title = "";
            let icon = "success";

            if (transactionMode === "U") {
                message = "Record updated successfully!";
                title = "Update Successful!";
            } else {
                message = "Record added successfully!";
                title = "Save Successful!";
            }
             (async function() {
              result=await Swal.fire(title, message, icon);
                if (result.isConfirmed) {
                $("#masterForm").submit();
                }
                
            })();
        }
    },200);
      document.getElementById('customer').disabled = false;
} );
    function appendTableRow(data, idx) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <input type="hidden" name="inward_detail_id[]" value="${data.inward_detail_id || 0}">
        <td>${data.inward_no}</td>
        <td>${data.lot_no}</td>
        <td>${data.inward_date}</td>
        <td>${data.item}</td>
        <td>${data.marko}</td>
        <td>${data.stock_qty}</td>
        <td>${data.out_qty}</td>
        <td>${data.unit}</td>
        <td>${data.out_wt}</td>
        <td>${data.loading_charges}</td>
        <td>${data.location}</td>
    `;
    tableBody.appendChild(tr);
}
document.getElementById('saveSelectedInward').addEventListener('click', function() {
    const tbody = document.getElementById('pendingInwardTableBody');
    const checkedRows = tbody.querySelectorAll('input[type="checkbox"]:checked');
    let foundError = false;
    let firstErrorCell = null;
    checkedRows.forEach(cb => {
        const tr = cb.closest('tr');
        const outQtyCell = tr.querySelector('.out-qty-cell');
        const stockQtyCell = tr.querySelector('[data-label="Stock Qty"]');
        let outQty = parseFloat(outQtyCell?.textContent || "0");
        let stockQty = parseFloat(stockQtyCell?.textContent || "0");

        if (!outQty || outQty <= 0) {
            if (!foundError) {
                showCustomMessagePopup("Please enter Outward Qty", outQtyCell);
                foundError = true;
                firstErrorCell = outQtyCell;
            }
            return;
        }
        if (outQty > stockQty) {
            if (!foundError) {
                showStockNotAvailablePopup(outQtyCell);
                foundError = true;
                firstErrorCell = outQtyCell;
            }
            return;
        }
    });
    if (foundError) return;
    if (checkedRows.length === 0) {
        bootstrap.Modal.getInstance(document.getElementById('modalDialog')).hide();
        return;
    }
    let jsonData = [];
    checkedRows.forEach(cb => {
        const tr = cb.closest('tr');
        const record = {
            inward_detail_id: tr.getAttribute('data-inward-detail-id') ?? 0,
            inward_no: tr.querySelector('[data-label="Inward No."]')?.textContent.trim() || "",
            lot_no: tr.querySelector('[data-label="Lot No."]')?.textContent.trim() || "",
            inward_date: tr.querySelector('[data-label="Inward Date"]')?.textContent.trim() || "",
            item: tr.querySelector('[data-label="Item"]')?.textContent.trim() || "",
            marko: (tr.querySelector('[data-label="Marko"]') || tr.querySelector('[data-label="marko"]'))?.textContent.trim() || "",
            stock_qty: tr.querySelector('[data-label="Stock Qty"]')?.textContent.trim() || "",
            out_qty: tr.querySelector('.out-qty-cell')?.textContent.trim() || "0",
            unit: tr.querySelector('[data-label="Unit"]')?.textContent.trim() || "",
            out_wt: tr.querySelector('.out-wt-cell')?.textContent.trim() || "0",
            loading_charges: tr.querySelector('.loading-charge-cell')?.textContent.trim() || "0",
            location: tr.querySelector('[data-label="Location"]')?.textContent.trim() || "",
            detailtransactionmode: 'I'
        };
        jsonData.push(record);
    });
    tableBody.innerHTML = "";
    jsonData.forEach((data, idx) => appendTableRow(data, idx));

    const detailsInput = document.getElementById('selected_inwards_json');
    if(detailsInput) {
        detailsInput.value = JSON.stringify(jsonData);
    }
    bootstrap.Modal.getInstance(document.getElementById('modalDialog')).hide();
    
    document.getElementById('customer').disabled = true;

    function showStockNotAvailablePopup(cellToRefocus) {
        showCustomPopup("Stock Qty not available", cellToRefocus, 'customStockPopup');
    }
    function showCustomMessagePopup(msg, cellToRefocus) {
        showCustomPopup(msg, cellToRefocus, 'customOutwardQtyPopup');
    }
    function showCustomPopup(message, cell, popupId) {
        if (document.getElementById(popupId)) return;
        const overlay = Object.assign(document.createElement('div'), {
            id: popupId,
            style: `
                position:fixed;top:0;left:0;width:100vw;height:100vh;
                background:rgba(0,0,0,0.25);display:flex;align-items:center;justify-content:center;z-index:9999;
            `.replace(/\s+/g, '')
        });
        const popup = document.createElement('div');
        popup.style.cssText = `
            background:#fff;padding:2rem 2.5rem 1.5rem 2.5rem;
            border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,0.15);text-align:center;
        `;
        popup.innerHTML = `
            <div style="font-size:1.3rem;color:red;margin-bottom:1rem;">${message}</div>
            <button id="${popupId}CloseBtn" style="padding:.5rem 2rem;font-size:1.1rem;background:#0d6efd;color:#fff;border:none;border-radius:5px;cursor:pointer;">OK</button>
        `;
        overlay.appendChild(popup);
        document.body.appendChild(overlay);
        document.getElementById(`${popupId}CloseBtn`).onclick = function() {
            document.body.removeChild(overlay);
            if (cell) {
                cell.focus();
                const range = document.createRange();
                range.selectNodeContents(cell);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
        };
    }
});
    //All calculation
    var grossInput = document.getElementById('gross_wt');
    var tareInput = document.getElementById('tare_wt');
    var netInput = document.getElementById('net_wt');
    var netSpan = document.getElementById('display_net_wt');
    function updateNetWt() {
        var gross = parseFloat(grossInput && grossInput.value) || 0;
        var tare = parseFloat(tareInput && tareInput.value) || 0;
        var net = gross - tare;
        if(net < 0) net = 0;
        if(netInput) netInput.value = net.toFixed(3);
        if(netSpan) netSpan.textContent = net.toFixed(3) + " Kg";
    }
    if (grossInput && tareInput && netInput) {
        grossInput.addEventListener('input', updateNetWt);
        tareInput.addEventListener('input', updateNetWt);
        updateNetWt();
        netInput.readOnly = true;
    }
    const totalQtyInput = document.getElementById("total_qty");
    const totalWeightInput = document.getElementById("total_wt");
    const totalLoadingExpenseInput = document.getElementById("loading_expense");
    let initialTotalsSet = false; 
    function updateTotalsFromPendingGrid() {
        let totalQty = 0;
        let totalWt = 0;
        let totalLoadingExpense = 0;
        const rows = document.querySelectorAll('#pendingInwardTableBody tr');
        rows.forEach(row => {
            const checkbox = row.querySelector('.inwardCheckbox');
            if (checkbox && !checkbox.checked) return;
            const tds = row.querySelectorAll('td');
            let outQty = tds[12] ? parseFloat(tds[12].innerText || tds[12].textContent) : 0;
            let outWt = tds[13] ? parseFloat(tds[13].innerText || tds[13].textContent) : 0;
            let loadingCharge = tds[14] ? parseFloat(tds[14].innerText || tds[14].textContent) : 0;

            if (!isNaN(outQty)) totalQty += outQty;
            if (!isNaN(outWt)) totalWt += outWt;
            if (!isNaN(outQty) && !isNaN(loadingCharge)) {
                totalLoadingExpense += outQty * loadingCharge;
            }
        });
        if (initialTotalsSet) {
            totalQtyInput.value = totalQty ? totalQty : "";
            totalWeightInput.value = totalWt ? totalWt.toFixed(3) : "";
            if (totalLoadingExpenseInput) {
                totalLoadingExpenseInput.value = totalLoadingExpense ? totalLoadingExpense.toFixed(2) : "";
            }
        }
    }
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('inwardCheckbox')) {
            initialTotalsSet = true;
            updateTotalsFromPendingGrid();
        }
    });

    document.addEventListener('blur', function (e) {
        if (e.target.classList.contains('out-qty') || e.target.classList.contains('out-wt')) {
            initialTotalsSet = true;
            updateTotalsFromPendingGrid();
        }
    }, true);

    const saveBtn = document.getElementById('saveSelectedInward');
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            setTimeout(() => {
                initialTotalsSet = true;
                updateTotalsFromPendingGrid();
            }, 100);
        });
    }

    // ---- Delivery To Auto-fill Logic ----
    const customerSelect = document.getElementById('customer');
    const deliveryToInput = document.getElementById('delivery_to');
    const transactionmode = "<?php echo $transactionmode; ?>";
    function updateDeliveryTo() {
        const selected = customerSelect.options[customerSelect.selectedIndex];
        deliveryToInput.value = selected ? selected.text : '';
    }
    if (customerSelect && deliveryToInput) {
        customerSelect.addEventListener('change', updateDeliveryTo);
        if (transactionmode !== "U") {
            updateDeliveryTo();
        }
    }
    //End calculation
});
</script>
<script>
let editedInwardData = {};
function saveEditedInwardRow(tr) {
    const inwardId = tr.getAttribute('data-inward-id');
    const inwardDetailId = tr.getAttribute('data-inward-detail-id');
    const uniqueKey = inwardId + '_' + inwardDetailId;
    const outQty = parseFloat(tr.querySelector('.out-qty-cell')?.textContent) || 0;
    const outWt = parseFloat(tr.querySelector('.out-wt-cell')?.textContent) || 0;
    const loadingCharge = parseFloat(tr.querySelector('.loading-charge-cell')?.textContent) || 0;
    const checked = tr.querySelector('.select-inward-checkbox')?.checked || false;

    editedInwardData[uniqueKey] = {
        out_qty: outQty,
        out_wt: outWt,
        loading_charge: loadingCharge,
        checked: checked
    };
}
document.getElementById('btn_inward').addEventListener('click', function() {
    let customerId = document.getElementById('customer').value;     
        
    fetch('pending_inward.php?customer=' + encodeURIComponent(customerId))
        .then(response => response.json())
        .then(data => {
            let tbody = document.getElementById('pendingInwardTableBody');
            tbody.innerHTML = '';
            data.forEach(row => {
                let tr = document.createElement('tr');   
                tr.setAttribute('data-inward-id', row.inward_id ?? 0);
                tr.setAttribute('data-inward-detail-id', row.inward_detail_id ?? 0);
                /*stock color*/
                if (Number(row.stock_qty) === Number(row.inward_qty)) {
                    tr.classList.add('full-inward-qty');
                }
                
                const uniqueKey = (row.inward_id ?? 0) + '_' + (row.inward_detail_id ?? 0);
                const edited = editedInwardData[uniqueKey];

                const defaultOutQty = row.out_qty ?? 0;
                const defaultOutWt = row.out_wt ?? 0;
                const defaultLoadingCharge = row.loading_charge ?? 0;

                tr.innerHTML = `
                    <td><input type="checkbox" class="select-inward-checkbox" ${edited && edited.checked ? 'checked' : ''}></td>
                    <td data-label="Inward No.">${row.inward_no ?? 'N/A'}</td>
                    <td data-label="Lot No.">${row.lot_no ?? 'N/A'}</td>
                    <td data-label="Inward Date">${row.inward_date ? (new Date(row.inward_date)).toLocaleDateString() : 'N/A'}</td>
                    <td data-label="Broker">${row.broker ?? 'N/A'}</td>
                    <td data-label="Item">${row.item ?? 'N/A'}</td>
                    <td data-label="marko">${row.marko ?? 'N/A'}</td>
                    <td data-label="Inward Qty">${row.inward_qty ?? 'N/A'}</td>
                    <td data-label="Unit">${row.packing_unit ?? 'N/A'}</td>
                    <td data-label="Inward Wt">${row.inward_wt ?? 'N/A'}</td>
                    <td data-label="Stock Qty" class="stock-qty-cell">${row.stock_qty ?? 'N/A'}</td>
                    <td data-label="Stock Wt" class="stock-wt-cell">${row.stock_wt ?? 'N/A'}</td>
                    <td data-label="Out Qty" class="out-qty-cell" contenteditable="${edited && edited.checked ? 'true' : 'false'}">${edited ? edited.out_qty : defaultOutQty}</td>
                    <td data-label="Out Wt" class="out-wt-cell">${edited ? edited.out_wt : defaultOutWt}</td>
                    <td data-label="Loading Charges" class="loading-charge-cell" contenteditable="${edited && edited.checked ? 'true' : 'false'}">${edited ? edited.loading_charge : defaultLoadingCharge}</td>
                    <td data-label="Location">${row.location ?? 'N/A'}</td>
                `;
                tbody.appendChild(tr);
            });
        });
});
document.getElementById('pendingInwardTableBody').addEventListener('change', function(e) {
    if (e.target.classList.contains('select-inward-checkbox')) {
        const tr = e.target.closest('tr');
        const outQtyCell = tr.querySelector('.out-qty-cell');
        const loadingChargeCell = tr.querySelector('.loading-charge-cell');
        if (e.target.checked) {
            outQtyCell.setAttribute('contenteditable', 'true');
            loadingChargeCell.setAttribute('contenteditable', 'true');
            outQtyCell.focus();
            document.getSelection().selectAllChildren(outQtyCell);
        } else {
            outQtyCell.removeAttribute('contenteditable');
            loadingChargeCell.removeAttribute('contenteditable');
        }
        saveEditedInwardRow(tr);
    }
});
document.getElementById('pendingInwardTableBody').addEventListener('input', function(e) {
    const tr = e.target.closest('tr');
    if (e.target.classList.contains('out-qty-cell')) {
        const outQty = parseFloat(e.target.textContent) || 0;
        const stockQty = parseFloat(tr.querySelector('.stock-qty-cell').textContent) || 0;
        const stockWt = parseFloat(tr.querySelector('.stock-wt-cell').textContent) || 0;
        const outWtCell = tr.querySelector('.out-wt-cell');
        const perUnitKg = stockQty > 0 ? (stockWt / stockQty) : 0;
        const outWt = (outQty * perUnitKg).toFixed(3);
        outWtCell.textContent = outWt;
        saveEditedInwardRow(tr);
    }
    else if (e.target.classList.contains('loading-charge-cell')) {
        saveEditedInwardRow(tr);
    }
});
document.getElementById('pendingInwardTableBody').addEventListener('blur', function(e) {
    if (e.target.classList.contains('out-qty-cell') || e.target.classList.contains('loading-charge-cell')) {
        const tr = e.target.closest('tr');
        saveEditedInwardRow(tr);
    }
}, true);
</script>
<script>
document.getElementById('btn_inward').addEventListener('click', function () {
    var customerSelect = document.getElementById('customer');
    var errorDiv = document.getElementById('customer_error');
    if (customerSelect && customerSelect.value === '') {
        errorDiv.style.display = 'block';
        customerSelect.classList.add('is-invalid');
    } else {
        errorDiv.style.display = 'none';
        customerSelect.classList.remove('is-invalid');
        var myModal = new bootstrap.Modal(document.getElementById('modalDialog'));
        myModal.show();
    }
});
</script>
<!--Outward no & outward sequence auto -->
<script>
const financialYear = "<?php echo $finYear; ?>";
document.addEventListener("DOMContentLoaded", function () {
    const outwardSequenceInput = document.getElementById("outward_sequence");
    const outwardNoInput = document.getElementById("outward_no");
    if (outwardSequenceInput && outwardNoInput) {
        outwardSequenceInput.addEventListener("input", function () {
            const sequence = this.value.padStart(4, '0');
            outwardNoInput.value = sequence + '/' + financialYear;
        });
    }
});
const outwardNoInput = document.getElementById("outward_no");
const outwardNoHidden = document.getElementById("outward_no_hidden");
if (outwardNoInput && outwardNoHidden) {
    outwardNoInput.addEventListener("input", function () {
        outwardNoHidden.value = outwardNoInput.value;
    });
}
</script>
<!--Done-->
<script>
$(document).ready(function () {
    function setDefaultOutwardDate() {
    var outwardDateInput = $('#outward_date');
    var finYear = "<?php echo $finYear; ?>"; // e.g., "24-25"
    if (outwardDateInput.val() === '') {
        var today = new Date(); // e.g., 2025-06-16
        var currentDay = today.getDate(); // e.g., 16
        var currentMonth = today.getMonth(); // 0-based, e.g., 5 for June

        // Extract start year from finYear (e.g., "24-25" → 2024)
        var startYear = finYear ? parseInt(finYear.split('-')[0], 10) + 2000 : today.getFullYear();

        // Create a date with today's day & month, but startYear as year
        var defaultDate = new Date(startYear, currentMonth, currentDay);

        // Format as YYYY-MM-DD
        var yyyy = defaultDate.getFullYear();
        var mm = String(defaultDate.getMonth() + 1).padStart(2, '0'); // month is 0-based
        var dd = String(defaultDate.getDate()).padStart(2, '0');
        var formattedDate = yyyy + '-' + mm + '-' + dd;

        outwardDateInput.val(formattedDate);
    }
}

    function validateOutwardDate() {
        var outwardDate = $('#outward_date').val();
        var errorContainer = $('#outward_date_error');
        
        if (outwardDate === '') {
            showError('Enter Proper Outward Date');
            return false;
        }
        var outwardDateParts = outwardDate.split('-');
        if (outwardDateParts.length !== 3) {
            showError('Enter Proper Outward Date');
            return false;
        }
        var year = parseInt(outwardDateParts[0], 10);
        var month = parseInt(outwardDateParts[1], 10);
        var day = parseInt(outwardDateParts[2], 10);
        var validDate = new Date(year, month - 1, day);
        if (
            validDate.getFullYear() !== year ||
            validDate.getMonth() !== (month - 1) ||
            validDate.getDate() !== day
        ) {
            showError('Enter Proper Outward Date');
            return false;
        }
        var currentDate = new Date();
        var todayStr = currentDate.toISOString().split('T')[0];
        var selectedDate = new Date(outwardDate);
        var today = new Date(todayStr);
        selectedDate.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);

        if (selectedDate > today) {
            showError('Date Above Current Period');
            return false;
        }
        var currentMonth = today.getMonth();
        var currentYear = today.getFullYear();
        var prevMonth = currentMonth - 1;
        var prevYear = currentYear;
        if (prevMonth < 0) {
            prevMonth = 11;
            prevYear -= 1;
        }
        var selectedMonth = selectedDate.getMonth();
        var selectedYear = selectedDate.getFullYear();
        if (
            selectedYear < prevYear ||
            (selectedYear === prevYear && selectedMonth < prevMonth)
        ) {
            showError('Date Below Current Period');
            return false;
        }
        errorContainer.text(''); // Clear error if valid
        $('#outward_date').removeClass('is-invalid');
        return true;

        function showError(message) {
            errorContainer.text(message);
            $('#outward_date').addClass('is-invalid');
        }
    }

    // Set the default date on page load
    setDefaultOutwardDate();

    // Validate on blur
    $('#outward_date').on('blur', function () {
        validateOutwardDate();
    });

    // Validate before form submission
    $('#btn_add').on('click', function (e) {
        if (!validateOutwardDate()) {
            e.preventDefault();
            $('#outward_date').focus();
            return false;
        }
    });
});
</script>
    
<script>
document.addEventListener("DOMContentLoaded", function () {
    const customerSelect = document.getElementById('customer');
    const orderBySelect = document.getElementById('outward_order_by');
    const selectedValue = orderBySelect.value;
    function loadContactPersons(customerId, selected = '') {
        if (customerId) {
            orderBySelect.innerHTML = '<option value="">Loading...</option>';
        } else {
            orderBySelect.innerHTML = '<option value="">Select</option>';
            return;
        }
        fetch('frm_outward_master.php?get_contact_persons=1&customer_id=' + encodeURIComponent(customerId))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                orderBySelect.innerHTML = html;
                if (selected) {
                    orderBySelect.value = selected;
                    if (!orderBySelect.value) {
                        orderBySelect.value = '';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching contact persons:', error);
                orderBySelect.innerHTML = '<option value="">Error loading contact persons</option>';
            });
    }
    if (customerSelect && orderBySelect) {
        orderBySelect.innerHTML = '<option value="">Select</option>';
        customerSelect.addEventListener('change', function () {
            loadContactPersons(this.value, '');
        });
        if (customerSelect.value) {
            loadContactPersons(customerSelect.value, selectedValue);
        }
    }
});
</script>
<?php
    include("include/footer_close.php");
?>