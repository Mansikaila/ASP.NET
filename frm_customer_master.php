<?php
    include("classes/cls_customer_master.php");
    include("classes/cls_city_form_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
 $form = new FormGenerator("tbl_city_master"); 
    $transactionmode="";
$form = new FormGenerator("tbl_city_master", $_bll->_mdl ?? null);
    $state_name="";
    $country_name="";
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    if( $transactionmode=="U")       
    {    
        $_bll->fillModel();
        /*Hetasvi - country fetch*/
    try {
    $p_field = 'country_id';
    $p_field_val = $_bll->_mdl->_country_id;
    $columns = 'country_name';
    $tableName = 'tbl_country_master';
    $stmt = $_dbh->prepare("CALL csms1_getval(?, ?, ?, ?)");
    $stmt->bindParam(1, $p_field);       
    $stmt->bindParam(2, $p_field_val);    
    $stmt->bindParam(3, $columns);        
    $stmt->bindParam(4, $tableName);     
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $country_name = $row['country_name'] ?? "";
    $stmt->closeCursor(); 

    $p_field = 'state_id';
    $p_field_val = $_bll->_mdl->_state_id;
    $columns = 'state_name';
    $tableName = 'tbl_state_master';
    $stmt1 = $_dbh->prepare("CALL csms1_getval(?, ?, ?, ?)");
    $stmt1->bindParam(1, $p_field);       
    $stmt1->bindParam(2, $p_field_val);    
    $stmt1->bindParam(3, $columns);        
    $stmt1->bindParam(4, $tableName); 
    $stmt1->execute();
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    $state_name = $row['state_name'] ?? "";
    $stmt1->closeCursor();

} 
    catch (PDOException $e)
    {
    echo json_encode(['error' => $e->getMessage()]);
    }     
/*Hetasvi - country fetch*/

    $label="Update";
    } else {
        $label="Add";
    }?>
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
        <ol class="breadcrumb">
          <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="srh_customer_master.php"><i class="fa fa-dashboard"></i> Customer Master</a></li>
          <li class="active"><?php echo $label; ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_customer_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="box-body">
                <div class="form-group row gy-2">
    <?php
            global $database_name;
            global $_dbh;
            $hidden_str="";
            $table_name="tbl_customer_master";
            $lbl_array=array();
            $field_array=array();
            $err_array=array();
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
             if($row) {
                    $generator_options=json_decode($row["generator_options"]);
                    if($generator_options) {
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
                        
                        if(is_array($fields_names) && !empty($fields_names)) {
                            for($i=0;$i<count($fields_names);$i++) {
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
                                $lbl_str='<label for="'.$fields_names[$i].'" class="col-4 col-sm-2 col-md-1 col-lg-1 control-label">'.$fields_labels[$i].'';
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
                                     /*Mansi - Validation Email,GSTIn , PAN*/
                                    case "text":
                                        $value = "";
                                        $field_str = "";
                                        $cls = "form-control ".$required_str." ".$duplicate_str;
                                        if (isset($_bll->_mdl)) {
                                            $value = $_bll->_mdl->$cls_field_name; 
                                        }
                                        if ($fields_names[$i] == "gstin") {
                                                $cls .= " gstin-field";
                                                $error_container = '<div class="invalid-feedback gstin-error">Please enter a valid GSTIN 15 characters</div>';
                                            }
                                        if ($fields_names[$i] == "pan") {
                                                $cls .= " pan-field";
                                                $error_container = '<div class="invalid-feedback pan-error">Please enter a valid PAN (10 characters).</div>';
                                            }
                                        if ($fields_names[$i] == "email_id") {
                                                $cls .= " email-field";
                                                $error_container = '<div class="invalid-feedback email-error">Please enter a valid email address.</div>';
                                            }
                                        if ($fields_names[$i] == "phone") {
                                                $cls .= " mobile-field";
                                                $error_container = '<div class="invalid-feedback mobile-error">Please enter a valid 10-digit mobile number.</div>';
                                            }

                                        $field_str .= '<input type="text" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" placeholder="'.ucwords(str_replace("_", " ", $fields_names[$i])).'" value="'.$value.'" '.$disabled_str.' '.$required_str.' />
                                        '.$error_container;
                                        break;
                                        /*Mansi - Validation Email,GSTIn , PAN*/
                                    case "email":
                                    case "file":
                                    case "date":
                                    case "datetime-local":
                                    case "radio":
                                    case "checkbox":
                                    case "number":
                                    case "select":
                                        $value="";$field_str="";$cls="";
                                        if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                            $cls.=$required_str;
                                             if($transactionmode=="U" && $_bll->_mdl->$cls_field_name==1) {
                                                $chk_str="checked='checked'";
                                             }
                                            $value="1";
                                            $field_str.='<input type="hidden" name="'.$fields_names[$i].'" value="0" />';
                                        } else {
                                            $cls.="form-control ".$required_str." ".$duplicate_str;
                                            $chk_str="";
                                             if(isset($_bll->_mdl)) {
                                                    $value=$_bll->_mdl->$cls_field_name; 
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
                                            if (strpos(strtolower($fields_names[$i]), 'city') !== false) {
                                            $field_str .= '
                                                <div>
                                                    <div style="display: flex; align-items: flex-start; gap: 5px;">
                                                        <div style="flex: 1;">
                                                            ' . $dropdown_html . '
                                                            ' . $error_container . '
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm btn_plus_icon" onclick="openCityModal()" style="padding: 9px 8px;  margin-top: 0;">
                                                            <i class="fa fa-plus" style="cursor: pointer;"></i>
                                                        </button>
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
                                            /*By Hetasvi*/
                                            if($fields_names[$i]=="state_id") {
                                                $lbl_str='<label for="state_name" class="col-4 col-sm-2 col-md-1 col-lg-1 control-label">State</label>';
                                                $field_str .= '<input type="text" class="form-control" id="state_name" name="state_name" value="'.$state_name.'" disabled />';
                                            }
                                                if($fields_names[$i]=="country_id") {
                                                $lbl_str='<label for="country_name" class="col-4 col-sm-2 col-md-1 col-lg-1 control-label">Country</label>';
                                                $field_str .= '<input type="text" class="form-control" id="country_name" name="country_name" value="'.$country_name.'" disabled />';
                                            }
                                            /*By Hetasvi*/
                                        }
                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset($_bll->_mdl)){
                                             $value=$_bll->_mdl->$cls_field_name;
                                            }
                                        $field_str.='<textarea id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" class="'.$cls.'" '.$disabled_str.' placeholder="'.ucwords(str_replace("_"," ",$fields_names[$i])).'"  '.$required_str.' >'.$value.'</textarea>
                                        '.$error_container;
                                        break;
                                    default:
                                        break;
                                } //switch ends
                                 $cls_err="";
                                    $lbl_err="";
                                   
                                if(empty($after_detail) || (!empty($after_detail) && !in_array($fields_names[$i],$after_detail))) {
                                    echo $lbl_str;
                                    if($field_str) {
                                    ?>
                                    <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $cls_err; ?>"  >
                                    <?php
                                            echo $field_str;
                                            echo $lbl_err;
                                    ?>
                                    </div>
                        <?php
                                    }
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
            <!-- detail table content-->
                <div class="box-body">
                    <div class="box-detail">
                        <?php
                            if(isset($_blldetail))
                                $_blldetail->pageSearch(); 
                        ?>
                        <button type="button" name="detailBtn" id="detailBtn" class="btn btn-primary add" data-bs-toggle="modal" data-bs-target="#modalDialog"  onclick="openModal()">Add Detail Record</button>
                </div>
              </div>
              <!-- /.box-body detail table content -->
<?php
    if(!empty($field_array)) {
?>
     <!-- remaining main table content-->
    <div class="box-body">
        <div class="form-group row gy-2">
    <?php
        for($j=0;$j<count($field_array);$j++) {
            echo $lbl_array[$j];
            if($field_array[$j]) {
            ?>
            <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $clserr_array[$j]; ?>"  >
            <?php
                    echo $field_array[$j];
                    echo $err_array[$j];
            ?>
            </div>
    <?php
            }
        } // after detail for loop ends
    ?>
    </div>
</div>
<?php
    } // empty detail array if ends
?>
<!-- .box-footer -->
              <div class="box-footer">
               <?php echo  $hidden_str; ?>
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="modified_by" name="modified_by" value="<?php echo USER_ID; ?>">
                <input type="hidden" id="modified_date" name="modified_date" value="<?php echo date("Y-m-d H:i:s"); ?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                                        <input type="hidden" id="deleted_records" name="deleted_records" />
                    <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_customer_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();" >
                <input type="button" class="btn btn-dark" id="btn_cancel" name="btn_cancel" value="Cancel"  onclick="window.location=window.history.back();">
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    
     <!-- Modal -->
    <div class="detail-modal">
        <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
          <div class="modal-dialog  modal-dialog-scrollable modal-xl">
            <div class="modal-content">
            <form id="popupForm"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
              <div class="modal-header">
                  <h4 class="modal-title" id="modalToggleLabel">Add Customer Contact Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="box-body container-fluid">
                    <div class="form-group row" >
    <?php
            $hidden_str="";
            $table_name_detail="tbl_contact_person_detail";
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name_detail);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
             if($row) {
                    $generator_options=json_decode($row["generator_options"]);
                    if($generator_options) {
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
                        if(is_array($fields_names) && !empty($fields_names)) {
                            for($i=0;$i<count($fields_names);$i++) {
                                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$is_disabled=0;$disabled_str="";$duplicate_str="";
                                $display_str="";
                                $cls_field_name="_".$fields_names[$i];
                                 
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
                                if(!empty($field_display) && in_array($fields_names[$i],$field_display)) {
                                    $display_str="display";
                                }
                                $lbl_str='<label for="'.$fields_names[$i].'" class="col-sm-4 control-label">'.$fields_labels[$i].'';
                                if($required) {
                                    $required_str="required";
                                    $lbl_str.="*";
                                    $error_container='<div class="invalid-feedback"></div>';
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
                                        $value="";
                                        $field_str=""; $cls="";
                                        if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                            $cls.=$display_str." ".$required_str;
                                             if(isset(${"val_$fields_names[$i]"}) &&  ${"val_$fields_names[$i]"}==1) {
                                                $chk_str="checked='checked'";
                                             }
                                            $value="1";
                                             $field_str.='<input type="hidden" name="'.$fields_names[$i].'" value="0" />';
                                        } else {
                                            $cls.="form-control ".$required_str." ".$duplicate_str." ".$display_str;
                                            $chk_str="";
                                             if(isset(${"val_$fields_names[$i]"}))  {
                                                $value=$fields_names[$i];
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
                                         if($fields_types[$i]=="select") {
                                            $cls="form-select ".$required_str." ".$duplicate_str." ".$display_str;
                                            $table=explode("_",$fields_names[$i]);
                                            $field_name=$table[0]."_name";
                                            $fields=$fields_names[$i].", ".$table[0]."_name";
                                            $tablename="tbl_".$table[0]."_master";
                                            $selected_val="";
                                            if(isset(${"val_$fields_names[$i]"})) {
                                                $selected_val=${"val_$fields_names[$i]"};
                                            }
                                            if(!empty($where_condition[$i]))
                                                $where_condition_val=$where_condition[$i];
                                            else {
                                                $where_condition_val=null;
                                            }
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i]))
                                                $field_str.=getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val,$cls,$required_str);
                                                $field_str.=$error_container;
                                                } else {
                                            $field_str.='<input type="'.$fields_types[$i].'" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" placeholder="'.ucwords(str_replace("_"," ",$fields_names[$i])).'" value= "'.$value.'"  '.$required_str.' '.$min_str.'  '.$step_str.' '.$chk_str.' '.$disabled_str.' />
                                            '.$error_container;
                                        }
                                        break;
                                    case "hidden":
                                        $lbl_str="";
                                        if($field_data_type[$i]=="int" || $field_data_type[$i]=="bigint"  || $field_data_type[$i]=="tinyint" || $field_data_type[$i]=="decimal")
                                            $hiddenvalue=0;
                                        else
                                            $hiddenvalue="";
                                       
                                            if(isset(${"val_$fields_names[$i]"})) {
                                                $hiddenvalue=${"val_$fields_names[$i]"};
                                            }
                                             if($fields_names[$i]!="customer_id") {
                                                $hidden_str.='
                                                <input type="'.$fields_types[$i].'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" value= "'.$hiddenvalue.'" class="exclude-field"  />';
                                                }

                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset(${"val_$fields_names[$i]"}))
                                             $value=${"val_$fields_names[$i]"};
                                        $field_str.='<textarea id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" class="'.$cls.'" placeholder="'.ucwords(str_replace("_"," ",$fields_names[$i])).'" '.$required_str.' '.$disabled_str.'>'.$value.'</textarea>
                                        '.$error_container;
                                        break;
                                    default:
                                        break;
                                } //switch ends
                                 if($field_str) {
                            ?>
                                <div class="col-sm-6 row gy-1">
                                  <?php echo $lbl_str; ?>
                                  <div class="col-sm-8">
                                    <?php echo $field_str; ?>
                                  </div>
                                </div>
                        <?php
                        }
                            } //for loop ends
                        } // field_types if ends
                    }
             } 
            ?> 
                    </div>
              </div>
              </div>
              <div class="modal-footer">
                
                <?php echo $hidden_str; ?>
                <input class="btn btn-success" type="submit" id="detailbtn_add" name="detailbtn_add" value= "Save">
                <input class="btn btn-dark" type="button" id="detailbtn_cancel" name="detailbtn_add" value= "Cancel" data-bs-dismiss="modal">
              </div>
                </form>
            </div> <!-- /.modal-content -->
          </div>  <!-- /.modal-dialog -->
        </div> <!-- /.modal -->
    </div>
    <!-- /Modal -->
    <div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
    <div class="modal-dialog customer-model-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-customer-title" id="cityModalLabel" >Add New City</h5>
            </div>
            <div class="modal-body" >
<div id="cityFormContent" style="padding-left:30px;">
    <form id="modalCityForm" method="post" action="classes/cls_city_master.php">
        <?php
        $formContent = $form->getForm();
        echo !empty(trim($formContent)) ? $formContent : "<div class='alert alert-warning'>No fields available.</div>";
        ?>
         <input type="hidden" id="city_id" name="city_id" value="<?php echo isset($form->_mdl->city_id) ? $form->_mdl->city_id : ''; ?>">
     <input type="hidden" name="transactionmode" value="I">
        <input type="hidden" id="country_id" name="country_id" value="1">
        <input type="hidden" id="submit_type" name="submit_type" value="ajax">
        <input type="hidden" name="masterHidden" value="save">
        <input type="hidden" id="modified_date" name="modified_date" value="<?php echo date('Y-m-d H:i:s') ?>">
        <input type="hidden" id="created_date" name="created_date" value="<?php echo date('Y-m-d H:i:s') ?>">
         <input type="hidden" id="company_id" name="company_id" value="<?php echo COMPANY_ID; ?>">
    </form>
</div>
            </div>
            <div class="modal-footer justify-content-end">
                   <input type="hidden" name="masterHidden" id="masterHidden" value="save">
               <button type="button" class="btn btn-primary" id="btn_add_modal">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <?php
    include("include/footer.php");
?>
</div>
<!-- ./wrapper -->

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
       let id_column="<?php echo "customer_id" ?>";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_customer_master.php"; ?>",
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:"<?php echo "tbl_customer_master"; ?>",type:"ajax"},
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
        }); // ajax completed
    }
    duplicateInputs.forEach((input) => {
        input.addEventListener("blur", function () {
          checkDuplicate(input);
        });
      });
/*Mansi - detail validation mobile */
const detailForm = document.getElementById("popupForm");
if (detailForm) {
    const mobileFieldDetail = detailForm.querySelector('.mobile-field');
    const mobileErrorDetail = detailForm.querySelector('.mobile-error');

    if (mobileFieldDetail) {
        mobileFieldDetail.addEventListener('keydown', function (e) {
            if (e.key === 'Tab') {
                const val = mobileFieldDetail.value.trim();
                const mobilePattern = /^[6-9]\d{9}$/; // India mobile validation
                if (val !== '' && !mobilePattern.test(val)) {
                    e.preventDefault();
                    mobileFieldDetail.classList.add('is-invalid');
                    mobileErrorDetail.style.display = 'block';
                    mobileFieldDetail.focus();
                } else {
                    mobileFieldDetail.classList.remove('is-invalid');
                    mobileErrorDetail.style.display = 'none';
                }
            }
        });
    }
}
    /*Mansi- gstin,PAN, Email validation*/
        // Validation logic for GSTIN, PAN, Email, and Mobile fields
function validateField(field, pattern, errorMessage, errorElement) {
    const value = field.value.trim();
    if (value !== "" && !pattern.test(value)) {
        field.classList.add("is-invalid");
        errorElement.textContent = errorMessage;
        errorElement.style.display = "block";
        return false;
    } else {
        field.classList.remove("is-invalid");
        errorElement.textContent = "";
        errorElement.style.display = "none";
        return true;
    }
}

// Validate all fields (on submit)
function validateAllFields() {
    let isValid = true;

    // Duplicate checks (if you have this logic)
    duplicateInputs.forEach((input) => {
        checkDuplicate(input);
        if (input.classList.contains("is-invalid")) {
            isValid = false;
        }
    });

    // GSTIN validation
    const gstinField = document.querySelector(".gstin-field");
    const gstinError = document.querySelector(".gstin-error");
    if (gstinField && gstinError) {
        isValid =
            validateField(
                gstinField,
                /^[a-zA-Z0-9]{15}$/,
                "Please enter a valid GSTIN (15 characters).",
                gstinError
            ) && isValid;
    }

    // PAN validation (exactly 10 digits)
    const panField = document.querySelector(".pan-field");
    const panError = document.querySelector(".pan-error");
    if (panField && panError) {
        isValid =
            validateField(
                panField,
                /^\d{10}$/,
                "Please enter a valid PAN (10 digits).",
                panError
            ) && isValid;
    }

    // Email validation
    const emailField = document.querySelector(".email-field");
    const emailError = document.querySelector(".email-error");
    if (emailField && emailError) {
        isValid =
            validateField(
                emailField,
                /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
                "Please enter a valid email address.",
                emailError
            ) && isValid;
    }

    // Mobile validation
    const mobileField = document.querySelector(".mobile-field");
    const mobileError = document.querySelector(".mobile-error");
    if (mobileField && mobileError) {
        isValid =
            validateField(
                mobileField,
                /^[6-9]\d{9}$/, // 10-digit mobile starting with 6-9
                "Please enter a valid 10-digit mobile number.",
                mobileError
            ) && isValid;
    }

    return isValid;
}


document.querySelectorAll("input").forEach((input) => {
    input.addEventListener("keydown", function (e) {
        if (e.key === "Tab") {
            const clsList = input.classList;
            const value = input.value.trim();
            let isValid = true;

            if (clsList.contains("gstin-field")) {
                isValid = /^[a-zA-Z0-9]{15}$/.test(value);
            } else if (clsList.contains("pan-field")) {
                isValid = /^\d{10}$/.test(value);
            } else if (clsList.contains("email-field")) {
                isValid =  /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
            } else if (clsList.contains("mobile-field")) {
                isValid = /^[6-9]\d{9}$/.test(value);
            }

            if (!isValid) {
                e.preventDefault(); // Stop tabbing to next field
                input.classList.add("is-invalid");
                input.focus();
            } else {
                input.classList.remove("is-invalid");
            }
        }
    });
});

    /*done*/
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
                const inputFields = form.elements[key]; // May return NodeList if multiple inputs exist

                if (!inputFields) continue; // Skip if field not found

                if (inputFields.length) {
                    // If multiple inputs exist (radio, checkbox, hidden with same name)
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

        // Ensure focus on the first visible field
        setTimeout(() => {
            const firstInput = form.querySelector("input:not([type=hidden]), input:not(.btn-close), select, textarea");
            if (firstInput) firstInput.focus();
        }, 10);
    }
    /*fetchCountryAndState by Hetasvi*/
    function fetchCountryAndState(cityId) {
    $.ajax({
        url: "classes/cls_customer_master.php",
        type: "POST",
        data: { action: "fetchCountryAndState", city_id: cityId },
        success: function (response) {
            try {
                const data = JSON.parse(response);
                if (data && data.country_id && data.state_id) {
                    $('#country_id').val(data.country_id);
                    $('#country_name').val(data.country_name);
                    $('#state_id').val(data.state_id);
                    $('#state_name').val(data.state_name);
                    // $('#state_name_hidden').val(data.state_name); // optional hidden
                    // $('#country_name_hidden').val(data.country_name); // optional hidden
                } else {
                    console.error("Country or state data missing in response.");
                    $('#country_id, #state_id').val('');
                    $('#country_name, #state_name').val('');
                }
            } catch (e) {
                console.error("Invalid JSON:", response);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    }); 
}
const cityField = document.getElementById("city_id");
if (cityField) {
    cityField.addEventListener("change", function () {
        fetchCountryAndState(this.value);
    });
}
   /*end fetchCountryAndState by Hetasvi*/  
    function saveData() {
    
        const formData = new FormData(form);
        const newEntry = {};
        const allEntries= {};

         // Convert form data to object (excluding hidden fields)
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
            //jsonData[editIndex] = allEntries;
            updateTableRow(editIndex, newEntry);
            modal.hide();
            Swal.fire({
                icon: "success",
                title: "Updated Successfully",
                text: "The record has been updated successfully!",
                showConfirmButton: true,
                showClass: {
                    popup: "" // Disable the popup animation
                },
                hideClass: {
                    popup: "" // Disable the popup hide animation
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
                    popup: "" // Disable the popup animation
                },
                hideClass: {
                    popup: "" // Disable the popup hide animation
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

        // Add a static entry
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
        addActions(row,index,id);       

        Object.keys(rowData).forEach(col => {
            if (!getHiddenFields().includes(col) && getDisplayFields().includes(col))  {
                const cell = document.createElement("td");
                cell.textContent = rowData[col] || "";
                cell.setAttribute("data-label", col);
                row.appendChild(cell);
            }
        });

        tableBody.appendChild(row);
    }

function updateTableRow(index, rowData) {
        const row = tableBody.children[index];
        var id=0;
      if(detailIdLabel!=""){
            id=rowData[detailIdLabel];
        } 
        row.innerHTML = "";
        addActions(row,index,id);

        Object.keys(rowData).forEach(col => {
            const cell = document.createElement("td");
            cell.setAttribute("data-label", col);
            cell.textContent = rowData[col] || "";
            row.appendChild(cell);
        });
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
        document.getElementById("detailBtn").focus();
    }
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("edit-btn")) {
            event.preventDefault(); // Stops the required field validation trigger
            const index = event.target.getAttribute("data-index");
            openModal(index);
        }
    });
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-btn")) {
            event.preventDefault(); // Stops the required field validation trigger
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
            // Remove the item from the jsonData array
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
                // If there are rows left, re-populate the table
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
    // Expose functions globally
    window.openModal = openModal;
    window.saveData = saveData;
   
 document.getElementById("btn_add").addEventListener("click", function (event) {
    event.preventDefault();
      // Validate all fields
        const isValid = validateAllFields();

        // If invalid, stop submission and show error alert
        if (!isValid) {
            Swal.fire({
                icon: "error",
                title: "Validation Errors",
                text: "Please correct the highlighted errors before submitting the form.",
                confirmButtonText: "OK"
            });
            return;
        }
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
        const jsonDataString = JSON.stringify(jsonData);
            document.getElementById("detail_records").value = jsonDataString;

            const deletedDataString = JSON.stringify(deleteData);
            document.getElementById("deleted_records").value = deletedDataString;
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
            /*const result=await Swal.fire(title, message, icon);
            if (result.isConfirmed) {
              form.removeEventListener("submit", arguments.callee);
              document.getElementById("btn_add").click();
            }*/
             
             //form.removeEventListener("submit", arguments.callee);
             // document.getElementById("btn_add").click();
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
        window.openCityModal = function () {
            $("#cityModal").modal("show");
        };
        $(".btn-secondary").click(function () {
            $("#cityModal").modal("hide");
        });
     $("#btn_add_modal").click(function () {
    const form = $("#modalCityForm")[0];

    if (form.checkValidity()) {
        const formData = $("#modalCityForm").serialize();
        const city_name = $("#modalCityForm #city_name").val();
        $.ajax({
            url: "classes/cls_city_master.php",
            method: "POST",
            data: formData,
            success: function (response) {
                console.log("Response from server:", response);
                if (response > 0) {
                    city_id = response;
                }

                if (city_id && city_name) {
                    alert("City saved successfully!");

                    $("select[name*='city']").each(function () {
                        const $dropdown = $(this);
                        if (!$dropdown.find('option[value="' + city_id + '"]').length) {
                            $dropdown.append(
                                $("<option>", {
                                    value: city_id,
                                    text: city_name,
                                    selected: true
                                })
                            );
                        } else {
                            $dropdown.val(city_id);
                        }

                        $dropdown.trigger("change");
                    });

                    // Fetch State and Country for the newly added city
                    fetchCountryAndState(city_id);

                    $("#cityModal").modal("hide");
                    form.reset();
                } else {
                    alert("Unexpected response: " + response);
                }
            },
            error: function (xhr, status, error) {
                alert("Error saving city: " + error);
            }
        });
    } else {
        form.reportValidity();
    }
});
    });
</script>
<!--
    <script>
    class FieldValidator {
    constructor(selector, regex, errorMsg) {
        this.field = document.querySelector(selector);
        this.regex = regex;
        this.errorMsg = errorMsg;
        this.init();
    }

    init() {
        if (!this.field) return;

        const errorDiv = document.createElement("div");
        errorDiv.className = "invalid-feedback";
        errorDiv.innerText = this.errorMsg;
        this.field.insertAdjacentElement("afterend", errorDiv);

        this.field.addEventListener("input", () => this.validate());
    }

    validate() {
        const value = this.field.value.trim();
        const isValid = this.regex.test(value);

        if (!isValid) {
            this.field.classList.add("is-invalid");
            this.field.classList.remove("is-valid");
            this.field.nextElementSibling.style.display = "block";
        } else {
            this.field.classList.remove("is-invalid");
            this.field.classList.add("is-valid");
            this.field.nextElementSibling.style.display = "none";
        }

        return isValid;
    }
}
    </script>
    
    
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const mobileValidator = new FieldValidator(
        ".mobile-field", 
        /^[6-9]\d{9}$/,   
        "Please enter a valid 10-digit mobile number."
    );

    document.querySelector('form').addEventListener("submit", function (e) {
        if (!mobileValidator.validate()) {
            e.preventDefault(); // Block form submit
        }
    });
});
</script>
-->

<?php
    include("include/footer_close.php");
?>