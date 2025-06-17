<?php
    include("classes/cls_rent_invoice_master.php");
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
global $_dbh;
$next_invoice_sequence = 1;
$invoice_no_formatted = '';
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
                SELECT MAX(rent_invoice_sequence) AS max_seq
                FROM tbl_rent_invoice_master 
                WHERE invoice_date BETWEEN ? AND ?
            ");
            $stmt2->execute([$startDate, $endDate]);
            $seqRow = $stmt2->fetch(PDO::FETCH_ASSOC);
            $next_invoice_sequence = (isset($seqRow['max_seq']) && is_numeric($seqRow['max_seq']))
                ? $seqRow['max_seq'] + 1 : 1; 
            $sequence_padded = str_pad($next_invoice_sequence, 4, '0', STR_PAD_LEFT); // Pad sequence with leading zeros
            $invoice_no_formatted = $sequence_padded . '/' . $finYear; // Format invoice_no with financial year
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
      <form id="masterForm" action="classes/cls_rent_invoice_master.php" method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
        <div class="box-body">
          <div class="form-group row gy-2">
            <?php
            global $database_name;
            global $_dbh;
            $hidden_str = "";
            $table_name = "tbl_rent_invoice_master";
            $lbl_array = array();
            $field_array = array();
            $err_array = array();
            $clserr_array = array();
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
            if ($row) {
              $generator_options = json_decode($row["generator_options"]);
              if ($generator_options) {
                $table_layout = $generator_options->table_layout ?? "vertical";
                $fields_names = $generator_options->field_name ?? [];
                $fields_types = $generator_options->field_type ?? [];
                $field_scale = $generator_options->field_scale ?? [];
                $dropdown_table = $generator_options->dropdown_table ?? [];
                $label_column = $generator_options->label_column ?? [];
                $value_column = $generator_options->value_column ?? [];
                $where_condition = $generator_options->where_condition ?? [];
                $fields_labels = $generator_options->field_label ?? [];
                $field_display = $generator_options->field_display ?? [];
                $field_required = $generator_options->field_required ?? [];
                $allow_zero = $generator_options->allow_zero ?? [];
                $allow_minus = $generator_options->allow_minus ?? [];
                $chk_duplicate = $generator_options->chk_duplicate ?? [];
                $field_data_type = $generator_options->field_data_type ?? [];
                $field_is_disabled = $generator_options->field_is_disabled ?? [];
                $after_detail = $generator_options->after_detail ?? [];

                $old_table_layout = $table_layout;
                if ($table_layout == "horizontal") {
                  $label_layout_classes = "col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                  $field_layout_classes = "col-8 col-sm-4 col-md-3 col-lg-2";
                } else {
                  $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                  $field_layout_classes = "col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                }
                $side_by_side_started = false;

                // Define the custom layout fields
                $row1_fields = ['basic_amount', 'net_amount'];
                $row2_fields = ['tax_amount', 'sgst', 'cgst', 'igst'];
                $row3_fields = ['unloading_exp', 'loading_exp', 'other_expense3', 'sp_note'];

                // Store fields for custom layout
                $custom_fields = [];
                // Track when to show Generate Invoice button
                $show_generate_btn = false;

                if (is_array($fields_names) && !empty($fields_names)) {
                  for ($i = 0; $i < count($fields_names); $i++) {
                    $fieldname = $fields_names[$i];
                    $value = "";
                    // Standard rendering
                    if ($fieldname == "invoice_type") {
                      echo '<div class="row align-items-center mb-3 mt-3">';
                        echo '<label class="' . $label_layout_classes . '">Invoice Type</label>';
                        echo '<div class="col-12 col-sm-9 col-md-7 col-lg-6">';
                        echo '<div class="d-flex flex-nowrap gap-3 align-items-center">';
                        echo '<div class="form-check form-check-inline m-0">';
                        echo '<input class="form-check-input" type="radio" name="invoice_type" value="1" id="invoice_type1" checked>'; // <-- checked added here
                        echo '<label class="form-check-label" for="invoice_type1">Regular</label>';
                        echo '</div>';
                        echo '<div class="form-check form-check-inline m-0">';
                        echo '<input class="form-check-input" type="radio" name="invoice_type" value="2" id="invoice_type2">';
                        echo '<label class="form-check-label" for="invoice_type2">Tax Invoice</label>';
                        echo '</div>';
                        echo '<div class="form-check form-check-inline m-0">';
                        echo '<input class="form-check-input" type="radio" name="invoice_type" value="3" id="invoice_type3">';
                        echo '<label class="form-check-label" for="invoice_type3">Bill of Supply</label>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                      continue;
                    }

                    // Layout logic
                    $table_layout = $old_table_layout;
                    $required = "";
                    $checked = "";
                    $field_str = "";
                    $lbl_str = "";
                    $required_str = "";
                    $min_str = "";
                    $step_str = "";
                    $error_container = "";
                    $duplicate_str = "";
                    $cls_field_name = "_" . $fields_names[$i];
                    $is_disabled = 0;
                    $disabled_str = "";

                    if (!empty($field_required) && in_array($fields_names[$i], $field_required)) {
                      $required = 1;
                    }
                    if (!empty($field_is_disabled) && in_array($fields_names[$i], $field_is_disabled)) {
                      $is_disabled = 1;
                    }
                    if (!empty($chk_duplicate) && in_array($fields_names[$i], $chk_duplicate)) {
                      $error_container = '<div class="invalid-feedback"></div>';
                      $duplicate_str = "duplicate";
                    }
                    $custom_col_class = "";
                    if (!empty($fields_labels[$i])) {
                      $lbl_str = '<label for="' . $fields_names[$i] . '" class="' . $label_layout_classes . '">' . $fields_labels[$i];
                      if ($table_layout == "vertical") {
                        $field_layout_classes = "col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                      }
                    } else {
                      if ($table_layout == "vertical") {
                        $field_layout_classes = "col-12";
                      }
                    }
                    if ($required) {
                      $required_str = "required";
                      $error_container = '<div class="invalid-feedback"></div>';
                      $lbl_str .= "*";
                    }
                    if ($is_disabled) {
                      $disabled_str = "disabled";
                    }
                    $lbl_str .= "</label>";
                    $fieldtype = $fields_types[$i];
                    $chk_str = "";
                   if ($fieldname == "rent_invoice_sequence") {
                    echo '<div class="row mb-3 align-items-center">';
                    echo $lbl_str;
                    echo '<div class="col-6 col-sm-2 col-md-1 col-lg-1">';
                    echo '<input type="number" id="' . $fieldname . '" name="' . $fieldname . '" class="form-control" placeholder="Enter Rent Invoice Sequence" value="' . htmlspecialchars($next_invoice_sequence) . '" required>'; 
                    echo $error_container;
                    echo '</div>';
                    $side_by_side_started = true;
                    continue;
                }

                if ($fieldname == "invoice_no" && $side_by_side_started) {
                    echo $lbl_str;
                    echo '<div class="col-6 col-sm-2 col-md-1 col-lg-1">';
                    echo '<input type="text" id="' . $fieldname . '" name="invoice_no_display" class="form-control" placeholder="Invoice No" value="' . htmlspecialchars($invoice_no_formatted) . '" readonly disabled>'; 
                    echo '<input type="hidden" id="invoice_no_hidden" name="' . $fieldname . '" value="' . htmlspecialchars($invoice_no_formatted) . '">';
                    echo $error_container;
                    echo '</div>';
                    echo '</div>';
                    $side_by_side_started = false;
                    continue;
                }

                    switch ($fields_types[$i]) {
                      case "text":
                      case "email":
                      case "file":
                      case "date":
                      case "datetime-local":
                      case "radio":
                      case "checkbox":
                      case "number":
                      case "select":
                        $value = "";
                        $field_str = "";
                        $cls = "";
                        $flag = 0;
                        $table = explode("_", $fieldname);
                        $field_name = $table[0] . "_name";
                        $fields = $fieldname . ", " . $table[0] . "_name";
                        $tablename = "tbl_" . $table[0] . "_master";
                        $selected_val = "";
                        $where_condition_val = !empty($where_condition[$i]) ? $where_condition[$i] : null;

                        if ($fields_types[$i] == "checkbox" || $fields_types[$i] == "radio") {
                          $cls .= $required_str;
                          if (!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                            $flag = 1;
                            $field_str .= getChecboxRadios(
                              $dropdown_table[$i],
                              $value_column[$i],
                              $label_column[$i],
                              $where_condition_val,
                              $fieldname,
                              $selected_val,
                              $cls,
                              $required_str,
                              $fields_types[$i]
                            ) . $error_container;
                          } else {
                            if ($transactionmode == "U" && $selected_val == 1) {
                              $chk_str = "checked='checked'";
                            }
                            $value = "1";
                            $field_str .= addHidden($fieldname, 0);
                          }
                        } else {
                          $cls .= "form-control $required_str $duplicate_str";
                          $chk_str = "";
                            if (($fields_names[$i] == "rent_invoice_sequence" || $fields_names[$i] == "invoice_no") && $transactionmode != "U") {
                                                if ($fields_names[$i] == "rent_invoice_sequence") {
                                                    $value = $next_invoice_sequence;
                                                } else {
                                                    $value = $invoice_no_formatted;
                                                }
                                                $readonly_str = "readonly";
                                            } else {
                                                $value = isset($_bll->_mdl) ? $_bll->_mdl->$cls_field_name : "";
                                            }
                        }

                        if (!empty($value) && in_array($fields_types[$i], ["date", "datetime-local", "datetime", "timestamp"])) {
                          $value = date("Y-m-d", strtotime($value));
                        }
                        if ($fields_types[$i] == "number") {
                          $step = "";
                          if (!empty($field_scale[$i]) && $field_scale[$i] > 0) {
                            for ($k = 1; $k < $field_scale[$i]; $k++) {
                              $step .= 0;
                            }
                            $step = "0." . $step . "1";
                          } else {
                            $step = 1;
                          }
                          $step_str = 'step="' . $step . '"';
                          $min = 1;
                          if (!empty($allow_zero) && in_array($fieldname, $allow_zero)) $min = 0;
                          if (!empty($allow_minus) && in_array($fieldname, $allow_minus)) $min = "";
                          $min_str = 'min="' . $min . '"';
                          $field_str .= addNumber($fieldname, $value, $required_str, $disabled_str, $cls, $duplicate_str, $min_str, $step_str) . $error_container;
                        } else if ($fields_types[$i] == "select") {
                          $cls = "form-select $required_str $duplicate_str";
                          if (!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                            $field_str .= getDropdown($dropdown_table[$i], $value_column[$i], $label_column[$i], $where_condition_val, $fieldname, $selected_val, $cls, $required_str) . $error_container;
                          }
                        } else {
                          if ($flag == 0) {
                            $field_str .= addInput($fields_types[$i], $fieldname, $value, $required_str, $disabled_str, $cls, $duplicate_str, $chk_str) . $error_container;
                          }
                        }
                        break;

                      case "hidden":
                        $lbl_str = "";
                        if (in_array($field_data_type[$i], ["int", "bigint", "tinyint", "decimal"]))
                          $hiddenvalue = 0;
                        else
                          $hiddenvalue = "";
                        if ($fieldname == "company_id") {
                          $hiddenvalue = COMPANY_ID;
                        } else if ($fieldname == "created_by") {
                          $hiddenvalue = $transactionmode == "U" ? "" : USER_ID;
                        } else if ($fieldname == "created_date") {
                          $hiddenvalue = $transactionmode == "U" ? "" : date("Y-m-d H:i:s");
                        } else if ($fieldname == "modified_by") {
                          $hiddenvalue = USER_ID;
                        } else if ($fieldname == "modified_date") {
                          $hiddenvalue = date("Y-m-d H:i:s");
                        } else {
                          if ($transactionmode == "U") {
                            $hiddenvalue = "";
                          }
                        }
                        $hidden_str .= addHidden($fieldname, $hiddenvalue);
                        break;

                      case "textarea":
                        $value = "";
                        $field_str .= addTextArea($fieldname, $value, $required_str, $disabled_str, $cls, $duplicate_str) . $error_container;
                        break;

                      default:
                        break;
                    }
                    $cls_err = "";
                    $lbl_err = "";

                    // Store fields for custom layout
                    if (in_array($fieldname, array_merge($row1_fields, $row2_fields, $row3_fields))) {
                      $custom_fields[$fieldname] = [
                        'label' => $lbl_str,
                        'field' => $field_str,
                        'cls_err' => $cls_err,
                        'lbl_err' => $lbl_err
                      ];
                      continue;
                    }

                    // Output standard fields
                    if (empty($after_detail) || (!empty($after_detail) && !in_array($fields_names[$i], $after_detail))) {
                      if ($table_layout == "vertical" && $fields_types[$i] != "hidden") {
                        ?>
                        <div class="row mb-3 align-items-center">
                        <?php
                      }
                      echo $lbl_str;
                      if ($field_str) {
                        $extra_margin_class = ($fields_names[$i] == 'rent_invoice_date') ? ' mt-3' : '';
                        ?>
                        <div class="<?php echo $field_layout_classes . " " . $cls_err . $extra_margin_class; ?>">
                          <?php
                          echo $field_str;
                          echo $lbl_err;
                          ?>
                        </div>
                        <?php
                      }
                      if ($table_layout == "vertical" && $fields_types[$i] != "hidden") {
                        ?>
                        </div>
                        <?php
                      }
                    } else {
                      $lbl_array[] = $lbl_str;
                      $field_array[] = $field_str;
                      $err_array[] = $lbl_err;
                      $clserr_array[] = $cls_err;
                    }

                    // Show generate button immediately after the specified fields
                    if (
                      $fieldname == "lot_no" // Change to your actual field key if different
                    ) {
                      $show_generate_btn = true;
                    }
                    if ($show_generate_btn) {
                      ?>
                      <div class="my-3" id="generate-btn-wrap">
                        <button type="button" class="btn btn-primary mt-3 mb-3" name="generate" id="generate">Generate Invoice</button>
                      <!-- Placeholder for the dynamically generated grid -->
                      <div id="generatedInvoiceGrid" class="mt-4" style="display:none;">
                        <table id="searchGeneratedDetail" class="table table-bordered table-striped" style="width:100%; font-size:14px;">
                          <thead>
                            <tr>
                              <th>In. No.</th>
                              <th>In. Date</th>
                              <th>Lot No.</th>
                              <th>Item</th>
                              <th>Marko</th>
                              <th>Qty.</th>
                              <th>Unit</th>
                              <th>Weight (Kg.)</th>
                              <th>Storage Duration</th>
                              <th>Rent</th>
                              <th>Per</th>
                              <th>Out. Date</th>
                              <th>Charges From</th>
                              <th>Charges To</th>
                              <th>Act. Month</th>
                              <th>Act. Day</th>
                              <th>Invoice Month</th>
                              <th>Invoice Day</th>
                              <th>Amount</th>
                              <th>Invoice For</th>
                            </tr>
                          </thead>
                          <tbody id="generatedInvoiceTableBody">
                            <tr>
                              <td colspan="21" style="text-align:center;">No records available.</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                        <div id="generated-invoice-details" style="display:none;"></div>
                        <div class="box-detail" id="manual-invoice-details" style="display:none;">
                          <?php
                          $_blldetail = new bll_rentinvoicedetail();
                          $detailHtml = $_blldetail->pageSearch();
                          echo $detailHtml ? $detailHtml : '';
                          ?>
                          <button type="button" name="detailBtn" id="detailBtn" class="btn btn-primary add"
                              data-bs-toggle="modal" data-bs-target="#modalDialog" onclick="openModal()">
                              Add Detail Record
                          </button>
                        </div>
                      </div>
                      <?php
                      $show_generate_btn = false;
                    }
                  }
                }
              }
            }
            ?>
            <!-- Custom Layout for Specified Fields -->
            <div class="row mb-3 align-items-center">
              <div class="col-12">
                <!-- Row 1: Basic Amount | Net Amount -->
                <div class="row mb-3 align-items-center">
                  <?php
                  foreach ($row1_fields as $field) {
                    if (isset($custom_fields[$field])) {
                      echo $custom_fields[$field]['label'];
                      ?>
                      <div class="<?php echo $field_layout_classes . ' ' . $custom_fields[$field]['cls_err']; ?>">
                        <?php
                        echo $custom_fields[$field]['field'];
                        echo $custom_fields[$field]['lbl_err'];
                        ?>
                      </div>
                      <?php
                    }
                  }
                  ?>
                </div>
                <!-- Row 2: Tax Amount | Sgst | Cgst | Igst -->
                <div class="row mb-3 align-items-center">
                  <?php
                  foreach ($row2_fields as $field) {
                    if (isset($custom_fields[$field])) {
                      echo $custom_fields[$field]['label'];
                      ?>
                      <div class="<?php echo $field_layout_classes . ' ' . $custom_fields[$field]['cls_err']; ?>">
                        <?php
                        echo $custom_fields[$field]['field'];
                        echo $custom_fields[$field]['lbl_err'];
                        ?>
                      </div>
                      <?php
                    }
                  }
                  ?>
                </div>
                <!-- Row 3: Unloading Exp | Loading Exp | Other Expense3 | Sp Note -->
                <div class="row mb-3 align-items-center">
                  <?php
                  foreach ($row3_fields as $field) {
                    if (isset($custom_fields[$field])) {
                      echo $custom_fields[$field]['label'];
                      ?>
                      <div class="<?php echo $field_layout_classes . ' ' . $custom_fields[$field]['cls_err']; ?>">
                        <?php
                        echo $custom_fields[$field]['field'];
                        echo $custom_fields[$field]['lbl_err'];
                        ?>
                      </div>
                      <?php
                    }
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body detail table content -->
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
        </div>
        <!-- .box-footer -->
        <div class="box-footer">
          <input type="hidden" id="transactionmode" name="transactionmode" value="<?php if ($transactionmode == "U") echo "U"; else echo "I"; ?>">
          <input type="hidden" id="detail_records" name="detail_records" />
          <input type="hidden" id="deleted_records" name="deleted_records" />
          <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
          <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value="Save">
          <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_rent_invoice_master.php'">
          <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
            <input type="hidden" id="invoice_no_hidden" name="invoice_no" value="<?php echo $invoice_no_formatted; ?>">
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
            $table_name_detail="tbl_rent_invoice_detail";
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
                                        $field_str=""; $cls="";$flag=0;
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
                                        if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                            $cls.=$display_str." ".$required_str;
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $flag=1;
                                                $field_str.=getChecboxRadios($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str, $fields_types[$i]).$error_container;
                                            } else {
                                                if(isset(${"val_$fields_names[$i]"}) &&  ${"val_$fields_names[$i]"}==1) {
                                                    $chk_str="checked='checked'";
                                                }
                                                $value="1";
                                                $field_str.=addHidden($fields_names[$i],0);
                                                }
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
                                             $field_str.=addNumber($fields_names[$i],$value,$required_str,$disabled_str,$cls,$duplicate_str,$min_str,$step_str).$error_container;
                                         }
                                         else if($fields_types[$i]=="select") {
                                            $cls="form-select ".$required_str." ".$duplicate_str." ".$display_str;
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $field_str.=getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val,$cls,$required_str);
                                                $field_str.=$error_container;
                                            }
                                        } else {
                                                if($flag==0) {
                                                    $field_str.=addInput($fields_types[$i],$fields_names[$i],$value,$required_str,$disabled_str,$cls,$duplicate_str,$chk_str).$error_container;
                                                }
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
                                             if($fields_names[$i]!="rent_invoice_id") {
                                                $hidden_str.=addHidden($fields_names[$i],$hiddenvalue);
                                                }                                       
                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset(${"val_$fields_names[$i]"}))
                                             $value=${"val_$fields_names[$i]"};
                                        $field_str.=addTextArea($fields_names[$i],$value,$required_str,$disabled_str,$cls,$duplicate_str).$error_container;
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
    const financialYear = "<?php echo $finYear; ?>";
    const rentInvoiceSequenceInput = document.getElementById("rent_invoice_sequence");
    const invoiceNoInput = document.getElementById("invoice_no");
    if (rentInvoiceSequenceInput && invoiceNoInput) {
        rentInvoiceSequenceInput.addEventListener("input", function () {
            const sequence = parseInt(this.value) || 1;
            const paddedSequence = sequence.toString().padStart(4, '0');
            invoiceNoInput.value = `${paddedSequence}/${financialYear}`;
        });
    }
    const invoiceNoHidden = document.getElementById("invoice_no_hidden");
    if (invoiceNoInput && invoiceNoHidden) {
        invoiceNoInput.addEventListener("input", function () {
            invoiceNoHidden.value = invoiceNoInput.value;
        });
    }
});
</script>
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
       let id_column="<?php echo "rent_invoice_id" ?>";
       let id_value=document.getElementById(id_column).value;
       $.ajax({
            url: "<?php echo "classes/cls_rent_invoice_master.php"; ?>",
            type: "POST",
            data: { column_name: input.name, column_value:column_value, id_name:id_column,id_value:id_value,table_name:"<?php echo "tbl_rent_invoice_master"; ?>",action:"checkDuplicate"},
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
    /*duplicateInputs.forEach((input) => {
        input.addEventListener("blur", function () {
          checkDuplicate(input);
        });
      });*/

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
  function toggleManualInvoiceDetails() {
    var selected = $('#invoice_for').val();
    if (selected === '6') { 
      $('#manual-invoice-details').show();
      $('#generate-btn-wrap').show(); 
      $('#generate').prop('disabled', true);
    } else {
      $('#manual-invoice-details').hide();
      $('#generate-btn-wrap').show();
      $('#generate').prop('disabled', false); 
    }
  }
     $('#invoice_for').val('1'); 
  toggleManualInvoiceDetails();
  $('#invoice_for').on('change', toggleManualInvoiceDetails);
});
</script>
<script>
document.getElementById("generate").addEventListener("click", function () {
    const gridContainer = document.getElementById("generatedInvoiceGrid");
    const tableBody = document.getElementById("generatedInvoiceTableBody");
    var lot_no = document.getElementById('lot_no') ? document.getElementById('lot_no').value : '';
    var customer = document.getElementById('customer') ? document.getElementById('customer').value : '';
     const invoiceFor = document.getElementById("invoice_for").value;
    if (!customer) {
    Swal.fire({
        icon: "warning",
        title: "Missing Input",
        text: "Please provide at least a Customer to generate the invoice.",
    });
    return;
}
    $.ajax({
        url: "classes/cls_rent_invoice_detail.php",
        type: "POST",
        data: {
            action: "generate_details",
            lot_no: lot_no,
            customer: customer,
            invoice_for: invoiceFor
        },
        dataType: "json",
        success: function (invoiceData) {
            tableBody.innerHTML = "";

            if (!invoiceData || invoiceData.length === 0) {
                const row = document.createElement("tr");
                row.innerHTML = '<td colspan="21" style="text-align:center;">No records available.</td>';
                tableBody.appendChild(row);
            } else {
                invoiceData.forEach((data) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                    <td>${data.in_no ?? ''}</td>
                    <td>${data.in_date ?? ''}</td>
                    <td>${data.lot_no ?? ''}</td>
                    <td>${data.item ?? ''}</td>
                    <td>${data.marko ?? ''}</td>
                    <td>${data.qty ?? ''}</td>
                    <td>${data.unit ?? ''}</td>
                    <td>${data.weight ?? ''}</td>
                    <td>${data.storage_duration ?? ''}</td>
                    <td>${data.rent_per_storage_duration ?? ''}</td>
                    <td>${data.rent_per ?? ''}</td>
                    <td>${data.out_date ?? ''}</td>
                    <td>${data.charges_from ?? ''}</td>
                    <td>${data.charges_to ?? ''}</td>
                    <td>${data.act_month ?? ''}</td>
                    <td>${data.act_day ?? ''}</td>
                    <td>${data.invoice_month ?? ''}</td>
                    <td>${data.invoice_day ?? ''}</td>
                    <td>${data.amount ?? ''}</td>
                    <td>${data.invoice_for ?? ''}</td>
                `;
                    tableBody.appendChild(row);
                });
            }
            gridContainer.style.display = "block";
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status
            });
            tableBody.innerHTML = '<tr><td colspan="21" style="text-align:center;">Error loading data: ' + (xhr.responseText || error) + '</td></tr>';
            gridContainer.style.display = "block";
        }
    });
});
</script>