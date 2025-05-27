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
        $stmt = $_dbh->prepare("SELECT contact_person_id, contact_person_name FROM tbl_contact_person_detail WHERE customer_id = ? ORDER BY contact_person_name");
        $stmt->execute([$customer_id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = htmlspecialchars($row['contact_person_id']);
            $name = htmlspecialchars($row['contact_person_name']);
            $options .= "<option value=\"$id\">$name</option>";
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
                        
                        if(is_array($fields_names) && !empty($fields_names)) {
                            
                            for($i=0;$i<count($fields_names);$i++) {
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
                                            $selected_val="";
                                            if($_bll->_mdl->$cls_field_name) {
                                                $selected_val=$_bll->_mdl->$cls_field_name;
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
                                                $field_str.=getChecboxRadios($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str, $fields_types[$i]).$error_container;
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
                                            $selected_val = isset($_bll->_mdl->$cls_field_name) ? $_bll->_mdl->$cls_field_name : "";
                                            $where_condition_val = !empty($where_condition[$i]) ? $where_condition[$i] : null;
                                            if (!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $dropdown_html = getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],
                                                    $where_condition_val,$fields_names[$i],$selected_val,$cls, $required_str);
                                            if (strpos(strtolower($fields_names[$i]), 'customer') !== false) {
                                            $field_str .= '
                                                <div>
                                               
                                                    <div style="display: flex; align-items: flex-start; gap: 5px;">
                                                    
                                                        <div style="flex: 1;">
                                                            ' . $dropdown_html . '
                                                            ' . $error_container . '
                                                             <div id="customer_error" class="invalid-feedback" style="display:none;">Please select customer</div>
                                                        </div>
                                                       <button type="button" class="btn btn-info" id="btn_inward">Select Inward</button>
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
                 </div><!-- /.row -->
              </div>
              <!-- /.box-body -->
                    <div class="box-body">
    <div class="box-detail">
        <table id="outwardDetail" class="table table-bordered table-striped table-sm align-middle" style="width:100%;">
            <thead class="table-light boxheader">
                <tr>
                    <th>Inward No.</th>
                    <th>Lot No.</th>
                    <th>Inward Date</th>
                    <th>Item</th>
                    <th>variety</th>
                    <th>Stock Qty.</th>
                    <th>Out Qty.</th>
                    <th>Unit</th>
                    <th>Out. Wt. (Kg.)</th>
                    <th>Loading Charges</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody id="outwardTableBody">
                <?php if (!empty($outward_data)): ?>
                    <?php foreach ($outward_data as $row): ?>
                       <tr>
                            <td data-label="Inward No."><?php echo htmlspecialchars($row['inward_no']) ?></td>
                            <td data-label="Lot No."><?php echo htmlspecialchars($row['lot_no']) ?></td>
                            <td data-label="Inward Date"><?php echo htmlspecialchars($row['inward_date']) ?></td>
                            <td data-label="Item"><?php echo htmlspecialchars($row['item']) ?></td>
                            <td data-label="Marko"><?php echo htmlspecialchars($row['variety']) ?></td>
                            <td data-label="Stock Qty."><?php echo $row['stock_qty'] ?></td>
                            <td data-label="Out Qty."><?php echo $row['out_qty'] ?></td>
                            <td data-label="Unit"><?php echo htmlspecialchars($row['unit']) ?></td>
                            <td data-label="Out. Wt. (Kg.)"><?php echo $row['out_wt'] ?></td>
                            <td data-label="Loading Charges"><?php echo $row['loading_charges'] ?></td>
                            <td data-label="Location"><?php echo htmlspecialchars($row['location']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="norecords" class="norecords">
                        <td colspan="12" class="text-center">No records available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
                
<div class="modal fade" id="pendingInwardModal" tabindex="-1" aria-labelledby="pendingInwardLabel" aria-hidden="true">
    <div class="modal-dialog  modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingInwardLabel">Pending Inward</h5>
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
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td data-label='Inward No.'>" . htmlspecialchars($row['inward_no'] ?? 'N/A') . "</td>";
            echo "<td data-label='Lot No.'>" . htmlspecialchars($row['lot_no'] ?? 'N/A') . "</td>";
            echo "<td data-label='Inward Date'>" . (!empty($row['inward_date']) ? date("d-m-Y", strtotime($row['inward_date'])) : 'N/A') . "</td>";
            echo "<td data-label='Broker'>" . htmlspecialchars($row['broker'] ?? 'N/A') . "</td>";
            echo "<td data-label='Item'>" . htmlspecialchars($row['item'] ?? 'N/A') . "</td>";
            echo "<td data-label='Marko'>" . htmlspecialchars($row['variety'] ?? 'N/A') . "</td>";
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
             <div class="modal-footer">
                <button type="button" id="saveSelectedInward" class="btn btn-success">Ok</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- content -->
    </div>
    
    <!-- container -->
  </div>
  <!-- content-wrapper -->
  <?php
    include("include/footer.php");
?>
</div>
<!-- wrapper -->
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
 document.getElementById("btn_add").addEventListener("click", function (event) {
    //event.preventDefault();
    const form = document.getElementById("masterForm"); // Store form reference
    let i=0;
    let firstelement;
     duplicateInputs.forEach((input) => {
          checkDuplicate(input);
      });
    if (!form.checkValidity()) {
        //event.stopPropagation();
        form.querySelectorAll(":invalid").forEach(function (input) {
            if(i==0) {
                firstelement=input;
            }
          input.classList.add("is-invalid");
          input.nextElementSibling.textContent = input.validationMessage; 
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
              //await customAlert(message);
              result=await Swal.fire(title, message, icon);
                if (result.isConfirmed) {
                $("#masterForm").submit();
                }
                
            })();
        }
    },200);
} );
});
</script>
<script>
$(document).ready(function () {
    function validateOutwardDate() {
        var outwardDate = $('#outward_date').val();
        var errorContainer = $('#outward_date_error');
        if (outwardDate === '') {
            showError('Date is required');
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
        return true;

        function showError(message) {
            errorContainer.text(message);
            $('#outward_date').addClass('is-invalid');
        }
    }
    if ($('#outward_date').val() === '') {
        var today = new Date();
        var formattedToday = today.toISOString().split('T')[0];
        $('#outward_date').val(formattedToday);
    }
    $('#outward_date').on('blur', function () {
        validateOutwardDate();
    });
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
document.getElementById('btn_inward').addEventListener('click', function () {
    var customerSelect = document.getElementById('customer');
    var errorDiv = document.getElementById('customer_error');
    if (customerSelect && customerSelect.value === '') {
        errorDiv.style.display = 'block';
        customerSelect.classList.add('is-invalid');
    } else {
        errorDiv.style.display = 'none';
        customerSelect.classList.remove('is-invalid');
        var myModal = new bootstrap.Modal(document.getElementById('pendingInwardModal'));
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
const outQtyTracker = {}; 
const selectedInwards = {};

// Helper to get a unique row key (even if inward_no is same)
function getRowKey(row) {
    const inwardNo = row.querySelectorAll('td')[1].innerText.trim();
    const lotNo = row.querySelectorAll('td')[2].innerText.trim();
    const rowIndex = Array.from(row.parentNode.children).indexOf(row);
    return inwardNo + '_' + lotNo + '_' + rowIndex;
}

function fetchPendingInwards(customer = '') {
    const pendingInwardTableBody = document.getElementById('pendingInwardTableBody');
    pendingInwardTableBody.innerHTML = `<tr><td colspan="16" class="text-center">Loading...</td></tr>`;
    fetch('pending_inward.php' + (customer ? '?customer=' + encodeURIComponent(customer) : ''))
        .then(response => response.json())
        .then(data => {
            pendingInwardTableBody.innerHTML = "";
            if (Array.isArray(data) && data.length > 0) {
                data.forEach((row, idx) => {
                    const rowKey = row.inward_no + '_' + (row.lot_no || '') + '_' + idx;
                    const trackerObj = outQtyTracker[rowKey] || {};
                    const lastOutQty = trackerObj.out_qty !== undefined ? trackerObj.out_qty : (row.out_qty || 0);
                    const lastOutWt  = trackerObj.out_wt  !== undefined ? trackerObj.out_wt  : (row.out_wt ? (parseFloat(row.out_wt) / 1000).toFixed(3) : '0.000');
                    const isChecked = selectedInwards[rowKey] ? 'checked' : '';
                    pendingInwardTableBody.innerHTML += `
                        <tr data-row-key="${rowKey}">
                            <td data-label="Select"><input type="checkbox" class="inwardCheckbox" value="${row.inward_no || ''}" data-row-key="${rowKey}" ${isChecked}></td>
                            <td data-label="Inward No.">${row.inward_no || ''}</td>
                            <td data-label="Lot No.">${row.lot_no || ''}</td>
                            <td data-label="Inward Date">${row.inward_date ? new Date(row.inward_date).toLocaleDateString("en-GB") : ''}</td>
                            <td data-label="Broker">${row.broker || ''}</td>
                            <td data-label="Item">${row.item || ''}</td>
                            <td data-label="Marko">${row.variety || ''}</td>
                            <td data-label="Inward Qty">${row.inward_qty || ''}</td>
                            <td data-label="Unit">${row.packing_unit || ''}</td>
                            <td data-label="Inward Wt">${row.inward_wt || ''}</td>
                            <td data-label="Stock Qty">${row.stock_qty || ''}</td>
                            <td data-label="Stock Wt">${row.stock_wt || ''}</td>
                            <td contenteditable="true" class="editable out-qty" data-field="out_qty" data-original="${lastOutQty}"
                                data-inward-wt="${row.inward_wt || 0}" data-inward-qty="${row.inward_qty || 0}"
                                data-label="Out Qty">${lastOutQty}</td>
                            <td class="out-wt" data-label="Out. Wt. (Kg.)">${lastOutWt}</td>
                            <td contenteditable="true" class="editable loading-charge" data-original="${row.loading_charge || ''}"
                                data-label="Loading Charges">${row.loading_charge || ''}</td>
                            <td data-label="Location">${row.location || ''}</td>
                        </tr>
                    `;
                });
            } else if (data.message) {
                pendingInwardTableBody.innerHTML = `<tr><td colspan="16" class="text-center">${data.message}</td></tr>`;
            } else if (data.error) {
                pendingInwardTableBody.innerHTML = `<tr><td colspan="16" class="text-center text-danger">${data.error}</td></tr>`;
            } else {
                pendingInwardTableBody.innerHTML = `<tr><td colspan="16" class="text-center">No inwards found.</td></tr>`;
            }
        })
        .catch(error => {
            console.error("Error fetching inwards:", error);
            pendingInwardTableBody.innerHTML = `<tr><td colspan="16" class="text-center text-danger">Failed to load data.</td></tr>`;
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const customerSelect = document.getElementById('customer');
    if (customerSelect) {
        customerSelect.addEventListener('change', function () {
            fetchPendingInwards(this.value);
        });
        fetchPendingInwards(customerSelect.value);
    } else {
        fetchPendingInwards('');
    }

    document.getElementById('saveSelectedInward').addEventListener('click', function() {
        const tableBody = document.getElementById('pendingInwardTableBody');
        const rows = tableBody.querySelectorAll('tr');
        let anySelected = false;
        let emptyOutQty = false;
        let firstEmptyCell = null;
        rows.forEach(function(row) {
            const checkbox = row.querySelector('.inwardCheckbox');
            if (checkbox && checkbox.checked) {
                anySelected = true;
                const cells = row.querySelectorAll('td');
                let outQty = cells[12].innerText.trim();
                if (outQty === "" || isNaN(outQty) || parseFloat(outQty) <= 0) {
                    emptyOutQty = true;
                    if (!firstEmptyCell) firstEmptyCell = cells[12];
                }
            }
        });
        if (emptyOutQty) {
            showCustomMessagePopup("Enter Outward Qty", firstEmptyCell);
            return;
        }
        if (!anySelected) {
            var pendingModal = bootstrap.Modal.getInstance(document.getElementById('pendingInwardModal'));
            if (pendingModal) pendingModal.hide();
            return;
        }
        rows.forEach(function(row) {
            const checkbox = row.querySelector('.inwardCheckbox');
            if (checkbox && checkbox.checked) {
                const cells = row.querySelectorAll('td');
                const rowKey = getRowKey(row);
                let inward_no = cells[1].innerText.trim();
                let out_qty = cells[12].innerText.trim();
                let out_wt  = cells[13].innerText.trim();
                outQtyTracker[rowKey] = {
                    out_qty: out_qty,
                    out_wt: out_wt
                };

                let rowData = {
                    inward_no: cells[1].innerHTML,
                    lot_no: cells[2].innerHTML,
                    inward_date: cells[3].innerHTML,
                    item: cells[5].innerHTML,
                    marko: cells[6].innerHTML,
                    stock_qty: cells[10].innerHTML,
                    out_qty: cells[12].innerText,
                    unit: cells[8].innerHTML,
                    out_wt: cells[13].innerHTML,
                    loading_charge: cells[14].innerText,
                    location: cells[15].innerHTML
                };
                const outwardTableBody = document.getElementById('outwardTableBody');
                let found = false;
                outwardTableBody.querySelectorAll('tr').forEach(function(outwardRow) {
                    const outCells = outwardRow.querySelectorAll('td');
                    if (outCells.length > 0 && outCells[0].innerText.trim() === inward_no) {
                        outCells[5].innerHTML = rowData.stock_qty;
                        outCells[6].innerText = rowData.out_qty;
                        outCells[8].innerHTML = rowData.out_wt;
                        outCells[9].innerText = rowData.loading_charge;
                        found = true;
                    }
                });
                if(!found){
                    let newRow = '<tr>';
                    newRow += `<td data-label="Inward No.">${rowData.inward_no}</td>`;
                    newRow += `<td data-label="Lot No.">${rowData.lot_no}</td>`;
                    newRow += `<td data-label="Inward Date">${rowData.inward_date}</td>`;
                    newRow += `<td data-label="Item">${rowData.item}</td>`;
                    newRow += `<td data-label="Marko">${rowData.marko}</td>`;
                    newRow += `<td data-label="Stock Qty.">${rowData.stock_qty}</td>`;
                    newRow += `<td data-label="Out Qty">${rowData.out_qty}</td>`;
                    newRow += `<td data-label="Unit">${rowData.unit}</td>`;
                    newRow += `<td data-label="Out. Wt. (Kg.)">${rowData.out_wt}</td>`;
                    newRow += `<td data-label="Loading Charges">${rowData.loading_charge}</td>`;
                    newRow += `<td data-label="Location">${rowData.location}</td>`;
                    newRow += '</tr>';
                    outwardTableBody.insertAdjacentHTML('beforeend', newRow);
                }
                selectedInwards[rowKey] = rowData;
            }
        });
        if (anySelected) {
            document.getElementById('customer').disabled = true;
            const noRecords = document.getElementById('norecords');
            if (noRecords) noRecords.style.display = 'none';
        }
        var pendingModal = bootstrap.Modal.getInstance(document.getElementById('pendingInwardModal'));
        if (pendingModal) pendingModal.hide();
    });

    document.getElementById('pendingInwardModal').addEventListener('show.bs.modal', function () {
        const checkboxes = document.querySelectorAll('.inwardCheckbox');
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            const rowKey = row.getAttribute('data-row-key');
            if(selectedInwards[rowKey]) cb.checked = true;
            else cb.checked = false;
        });
    });
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('inwardCheckbox')) {
        const row = e.target.closest('tr');
        const rowKey = row.getAttribute('data-row-key');
        if (e.target.checked) {
            const trackerObj = outQtyTracker[rowKey];
            if (trackerObj) {
                row.querySelector('.out-qty').innerText = trackerObj.out_qty;
                row.querySelector('.out-wt').innerText  = trackerObj.out_wt;
            }
        }
    }
});
document.addEventListener('focusin', function(e) {
    if (e.target.classList.contains('out-qty') || e.target.classList.contains('loading-charge')) {
        const row = e.target.closest('tr');
        const checkbox = row.querySelector('.inwardCheckbox');
        if (!checkbox || !checkbox.checked) {
            e.target.blur();
        }
    }
    if (e.target.classList.contains('out-wt')) {
        const row = e.target.closest('tr');
        const outQtyCell = row.querySelector('.out-qty');
        let outQty = parseFloat(outQtyCell.innerText.trim());
        if (isNaN(outQty)) outQty = 0;
        let inwardWt = parseFloat(outQtyCell.getAttribute('data-inward-wt'));
        let inwardQty = parseFloat(outQtyCell.getAttribute('data-inward-qty'));
        let outWtKg = 0;
        if (!isNaN(inwardWt) && !isNaN(inwardQty) && inwardQty > 0) {
            outWtKg = (inwardWt / inwardQty) * outQty / 1000;
        }
        e.target.innerText = outWtKg.toFixed(3);
    }
});
document.addEventListener('blur', function(e) {
    if (e.target.classList.contains('out-qty')) {
        const row = e.target.closest('tr');
        const checkbox = row.querySelector('.inwardCheckbox');
        const rowKey = row.getAttribute('data-row-key');
        if (checkbox && checkbox.checked) {
            let outQty = parseFloat(e.target.innerText.trim());
            if (isNaN(outQty)) outQty = 0;
            const stockQtyCell = row.querySelectorAll('td')[10];
            let stockQty = parseFloat(stockQtyCell.innerText.trim());
            if (isNaN(stockQty)) stockQty = 0;
            let inwardWt = parseFloat(e.target.getAttribute('data-inward-wt'));
            let inwardQty = parseFloat(e.target.getAttribute('data-inward-qty'));
            let outWtKg = 0;
            if (!isNaN(inwardWt) && !isNaN(inwardQty) && inwardQty > 0) {
                outWtKg = (inwardWt / inwardQty) * outQty / 1000;
            }
            if (outQty > stockQty) {
                e.target.innerText = 0;
                outWtKg = 0;
                showStockNotAvailablePopup(e.target);
            }
            const outWtCell = row.querySelectorAll('td')[13];
            if (outWtCell) {
                outWtCell.innerText = outWtKg.toFixed(3);
            }
            outQtyTracker[rowKey] = {
                out_qty: outQty,
                out_wt: outWtKg.toFixed(3)
            };
        } else {
            e.target.innerText = e.target.getAttribute('data-original');
            const outWtCell = e.target.closest('tr').querySelectorAll('td')[13];
            if (outWtCell) outWtCell.innerText = "0.000";
        }
    }
    if (e.target.classList.contains('loading-charge')) {
        const row = e.target.closest('tr');
        const checkbox = row.querySelector('.inwardCheckbox');
        if (!checkbox || !checkbox.checked) {
            e.target.innerText = e.target.getAttribute('data-original');
        }
    }
}, true);

function showStockNotAvailablePopup(cellToRefocus) {
    showCustomPopup("Stock not available", cellToRefocus, 'customStockPopup');
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

// ---- MAIN LOGIC: Update grid on main Save (btn_add) ----
document.getElementById('btn_add').addEventListener('click', function () {
    const tableBody = document.getElementById('pendingInwardTableBody');
    const rows = Array.from(tableBody.querySelectorAll('tr'));  
    rows.forEach(function(row) {
        const checkbox = row.querySelector('.inwardCheckbox');
        if (checkbox && checkbox.checked) {
            const cells = row.querySelectorAll('td');
            const inwardQtyCell = cells[7];  // Inward Qty
            const stockQtyCell  = cells[10]; // Stock Qty
            const outQtyCell    = cells[12]; // Out Qty

            let inwardQty = parseFloat(inwardQtyCell.textContent.trim());
            let stockQty  = parseFloat(stockQtyCell.textContent.trim());
            let outQty    = parseFloat(outQtyCell.textContent.trim());

            if (isNaN(inwardQty)) inwardQty = 0;
            if (isNaN(stockQty))  stockQty  = 0;
            if (isNaN(outQty))    outQty    = 0;

            let newInwardQty = inwardQty - outQty;
            let newStockQty  = stockQty  - outQty;

            inwardQtyCell.textContent = newInwardQty < 0 ? 0 : newInwardQty;
            stockQtyCell.textContent  = newStockQty  < 0 ? 0 : newStockQty;
            if (newInwardQty <= 0 || newStockQty <= 0) {
                row.remove();
            }
        }
    });
    if (tableBody.querySelectorAll('tr').length === 0) {
        tableBody.innerHTML = `<tr><td colspan="16" class="text-center">No inwards found.</td></tr>`;
    }
    document.getElementById('customer').disabled = false;
});
</script>
<!--All calculation-->
<script>
document.addEventListener('DOMContentLoaded', function () {
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
            let outWt  = tds[13] ? parseFloat(tds[13].innerText || tds[13].textContent) : 0;
            let loadingCharge = tds[14] ? parseFloat(tds[14].innerText || tds[14].textContent) : 0;
            if (!isNaN(outQty)) totalQty += outQty;
            if (!isNaN(outWt))  totalWt  += outWt;
            if (!isNaN(outQty) && !isNaN(loadingCharge)) {
                totalLoadingExpense += outQty * loadingCharge;
            }
        });
        totalQtyInput.value = totalQty ? totalQty : "";
        totalWeightInput.value = totalWt ? totalWt.toFixed(3) : "";
        if (totalLoadingExpenseInput) {
            totalLoadingExpenseInput.value = totalLoadingExpense ? totalLoadingExpense.toFixed(2) : "";
        }
    }
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('inwardCheckbox')) {
            updateTotalsFromPendingGrid();
        }
    });
    document.addEventListener('blur', function(e) {
        if (e.target.classList.contains('out-qty') || e.target.classList.contains('out-wt')) {
            updateTotalsFromPendingGrid();
        }
    }, true);
    const saveBtn = document.getElementById('saveSelectedInward');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            setTimeout(updateTotalsFromPendingGrid, 100);
        });
    }
    updateTotalsFromPendingGrid();
    
    const customerSelect = document.getElementById('customer');
    const deliveryToInput = document.getElementById('delivery_to');
    if (!customerSelect || !deliveryToInput) return;
    function updateDeliveryTo() {
        const selected = customerSelect.options[customerSelect.selectedIndex];
        deliveryToInput.value = selected ? selected.text : '';
    }
    customerSelect.addEventListener('change', updateDeliveryTo);
    updateDeliveryTo();
    });
</script>
<!--Done-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const customerSelect = document.getElementById('customer'); // your customer dropdown
    const orderBySelect = document.getElementById('outward_order_by'); // your contact person dropdown
    function loadContactPersons(customerId) {
        orderBySelect.innerHTML = '<option value="">Loading...</option>';
        if (!customerId) {
            orderBySelect.innerHTML = '<option value="">Select Contact Person</option>';
            return;
        }
        fetch('frm_outward_master.php?get_contact_persons=1&customer_id=' + encodeURIComponent(customerId))
            .then(response => response.text())
            .then(html => {
                orderBySelect.innerHTML = html;
            });
    }
    if (customerSelect && orderBySelect) {
        customerSelect.addEventListener('change', function () {
            loadContactPersons(this.value);
        });
        if (customerSelect.value) {
            loadContactPersons(customerSelect.value);
        }
    }
});
</script>
<?php
    include("include/footer_close.php");
?>