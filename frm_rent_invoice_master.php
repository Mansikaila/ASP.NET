<?php
    include("classes/cls_rent_invoice_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    $transactionmode="";
    $currentmenu_label=getCurrentMenuLabel();
    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    if( $transactionmode=="U")       
    {
        if (!$canUpdate) {
            $_SESSION["sess_message"]="You don't have permission to update ".$currentmenu_label.".";
            $_SESSION["sess_message_cls"]="danger";
            $_SESSION["sess_message_title"]="Permission Denied";
            $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
            header("Location: ".BASE_URL."srh_country_master.php");
            exit();
        }
        $_bll->fillModel();
        $label="Update";
    } else {
        if (!$canAdd) {
            $_SESSION["sess_message"]="You don't have permission to add ".$currentmenu_label.".";
            $_SESSION["sess_message_cls"]="danger";
            $_SESSION["sess_message_title"]="Permission Denied";
            $_SESSION["sess_message_icon"]="exclamation-triangle-fill";
            header("Location: ".BASE_URL."srh_rent_invoice_master.php");
            exit();
        }
        $label="Add";
    }
// Fetch company state_id based on the logged-in user's company_id-drashti
$company_id = $_SESSION['sess_company_id'] ?? COMPANY_ID;
$company_state_id = '';
try {
    $stmt = $_dbh->prepare("SELECT state FROM tbl_company_master WHERE company_id = ?");
    $stmt->execute([$company_id]);
    $company_row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($company_row && $company_row['state']) {
        $company_state_name = $company_row['state'];

        // Now get state_id from state master
        $stmt2 = $_dbh->prepare("SELECT state_id FROM tbl_state_master WHERE state_name = ?");
        $stmt2->execute([$company_state_name]);
        $state_row = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($state_row) {
            $company_state_id = $state_row['state_id'];
        }
    }
} catch (PDOException $e) {
    echo "Error fetching company state: " . $e->getMessage();
}

if (isset($_POST['ajax_get_lots']) && isset($_POST['customer_id']) && isset($_POST['invoice_type'])) {
    try {
        global $_dbh;
        $customer_id = $_POST['customer_id'];
        $invoice_type = $_POST['invoice_type'];
        $gst_type = null;
        switch ($invoice_type) {
            case '1':
                $gst_type = 3;
                break;
            case '2':
                $gst_type = 1;
                break;
            case '3':
                $gst_type = 2;
                break;
            default:
                throw new Exception("Invalid invoice type");
        }
        ob_clean();
        $stmt = $_dbh->prepare("
            SELECT i.lot_no
            FROM tbl_inward_detail i
            INNER JOIN tbl_inward_master m ON i.inward_id = m.inward_id
            WHERE m.customer = ? AND i.gst_type = ?
            GROUP BY i.lot_no
            ORDER BY i.lot_no
        ");
        $stmt->execute([$customer_id, $gst_type]);
        $lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($lots);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
global $_dbh;
if ($transactionmode == "U") {
    $next_invoice_sequence = $_bll->_mdl->_rent_invoice_sequence;
    $invoice_no_formatted = $_bll->_mdl->_invoice_no;
} else {
    $next_invoice_sequence = 1;
    $invoice_no_formatted = '';
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
 
            $companyYearStartYear = '';
            if ($yearRow) {
                $finYear = $yearRow['short_range'];
                $startDate = $yearRow['start_date'];
                $endDate = $yearRow['end_date'];
                $companyYearStartYear = date('Y', strtotime($startDate));
                $stmt2 = $_dbh->prepare("
                    SELECT MAX(rent_invoice_sequence) AS max_seq
                    FROM tbl_rent_invoice_master 
                    WHERE invoice_date BETWEEN ? AND ?
                ");
                $stmt2->execute([$startDate, $endDate]);
                $seqRow = $stmt2->fetch(PDO::FETCH_ASSOC);
                $next_invoice_sequence = (isset($seqRow['max_seq']) && is_numeric($seqRow['max_seq']))
                    ? $seqRow['max_seq'] + 1 : 1; 
                $sequence_padded = str_pad($next_invoice_sequence, 4, '0', STR_PAD_LEFT);
                $invoice_no_formatted = $sequence_padded . '/' . $finYear; 
            }
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>
<!-- Add this CSS for multiselect, before </body> -->
<style>
.multiselect {
  width: 100%;
}
.selectBox {
  position: relative;
  cursor: pointer;
}
.selectBox select {
  width: 100%;
}
.overSelect {
  position: absolute;
  left: 0; right: 0; top: 0; bottom: 0;
}
#lotNoSelectOptions {
  display: none;
  border: 1px solid #ced4da;
  border-top: none;
  background-color: #fff;
  max-height: 180px;
  overflow-y: auto;
  position: absolute;
  width: 100%;
  z-index: 10;
  box-shadow: 0 4px 8px rgba(0,0,0,0.04);
}
#lotNoSelectOptions label {
  display: block;
  padding: 0.375rem 2.25rem 0.375rem .75rem;
  cursor: pointer;
  margin-bottom: 0;
  font-weight: normal;
  background: none;
  transition: background 0.2s;
}
#lotNoSelectOptions label:hover {
  background-color: #f1f1f1;
}
</style>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
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

                $row1_fields = ['basic_amount', 'net_amount'];
                $row2_fields = ['tax_amount', 'sgst', 'cgst', 'igst'];
                $row3_fields = ['unloading_exp', 'loading_exp', 'other_expense3', 'sp_note'];
                $custom_fields = [];
                $show_generate_btn = false;

                if (is_array($fields_names) && !empty($fields_names)) {
                  for ($i = 0; $i < count($fields_names); $i++) {
                    $fieldname = $fields_names[$i];
                    $value = "";
                    // Standard rendering invoice_type-drashti
                   if ($fieldname == "invoice_type") {
                        echo '<div class="row align-items-center mb-3 mt-3">';
                        echo '<label class="' . $label_layout_classes . '">Invoice Type</label>';
                        echo '<div class="col-12 col-sm-9 col-md-7 col-lg-6">';
                        echo '<div class="d-flex flex-nowrap gap-3 align-items-center">';
                        echo '<div class="form-check form-check-inline m-0">';
                        echo '<input class="form-check-input" type="radio" name="invoice_type" value="1" id="invoice_type1">';
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
                      //Mansi-Lot_no
                     if ($fieldname == "lot_no") {
                        ?>
                        <label for="lot_no" class="col-4 col-sm-2 col-md-1 col-lg-1 form-label">Lot No</label>
                        <div class="col-8 col-sm-4 col-md-3 col-lg-2">
                            <?php if ($transactionmode == "U") { ?>
                                <!-- Display as text input in edit mode with the stored value -->
                                <input type="text" id="lot_no_display" name="lot_no_display" class="form-control" 
                                       value="<?php echo htmlspecialchars($_bll->_mdl->_lot_no ?? ''); ?>" readonly>
                                <input type="hidden" id="lot_no" name="lot_no" value="<?php echo htmlspecialchars($_bll->_mdl->_lot_no ?? ''); ?>">
                            <?php } else { ?>
                                <!-- Display as multiselect in add mode -->
                                <div id="lotNoMultiselect" class="multiselect position-relative">
                                    <div id="lotNoSelectLabel" class="selectBox" tabindex="0">
                                        <select id="lot_no" name="lot_no" class="lot-no form-control form-select required" style="width: 100%;" required>
                                            <option>Select Lot No</option>
                                        </select>
                                        <div class="overSelect"></div>
                                    </div>
                                    <div id="lotNoSelectOptions" class="shadow"></div>
                                </div>
                            <?php } ?>
                        </div>
                        <?php
                        $show_generate_btn = true;
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
                    echo '<input type="number" id="' . $fieldname . '" name="' . $fieldname . '" class="form-control" placeholder="Enter Rent Invoice Sequence" value="' . htmlspecialchars($next_invoice_sequence) . '" ' . ($transactionmode == "U" ? "readonly" : "") . ' required>'; 
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
                        if ($fieldname == "billing_till_date") {
                            $error_container = '<div class="invalid-feedback"></div>';
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
                            $selected_val = ($transactionmode == "U" && $_bll->_mdl->{"_" . $fieldname}) ? $_bll->_mdl->{"_" . $fieldname} : "";
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
                    // Show generate button -drashti
                    if (
                      $fieldname == "lot_no" 
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
                              <th>marko</th>
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
                              <th>Invoice For</th>
                              <th>Invoice Day</th>
                              <th>Amount</th>
                              <th>Status</th>
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
            <!-- Custom Layout for Specified Fields-drashti -->
            <div class="row mb-3 align-items-center">
              <div class="col-12">
                <!-- Row 1: Basic Amount | Net Amount -->
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
                    if (strpos(strtolower($field), 'sgst') !== false || 
                        strpos(strtolower($field), 'cgst') !== false || 
                        strpos(strtolower($field), 'igst') !== false) {
                      $rate_field = $field . '_amt'; // e.g., sgst_amt, cgst_amt, igst_amt
                      $rate_value = '';
                      if ($field == 'sgst') $rate_value = '9%';
                      else if ($field == 'cgst') $rate_value = '12%';
                      else if ($field == 'igst') $rate_value = '18%';
                      ?>
                      <div class="d-flex align-items-center gap-2">
                        <!-- Disabled Percentage Input -->
                        <div class="flex-grow-1">
                          <input type="text" id="<?php echo $field; ?>" name="<?php echo $field; ?>"
                            class="form-control" value="<?php echo $rate_value; ?>" disabled>
                        </div>
                        <span class="fw-bold">%</span>
                        <!-- Disabled Tax Amount Input with NO placeholder -->
                        <div class="flex-grow-1">
                          <input type="text" id="<?php echo $rate_field; ?>" name="<?php echo $rate_field; ?>"
                            class="form-control" disabled>
                        </div>
                      </div>
                      <?php
                    } else {
                      echo $custom_fields[$field]['field'];
                    }
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
                      if ($field === 'other_expense3') {
                        echo $custom_fields[$field]['label'];
                        ?>
                        <div class="<?php echo $field_layout_classes . ' ' . $custom_fields[$field]['cls_err']; ?>" style="display: flex; align-items: center; gap: 16px;">
                          <textarea name="other_expense3" class="form-control" rows="3" cols="20"><?php echo htmlspecialchars($custom_fields[$field]['value'] ?? ''); ?></textarea>
                          <span style="font-size: 22px; line-height: 1; display: flex; align-items: center;">:</span>
                          <textarea name="other_expense3_sign" class="form-control" rows="3" cols="20"></textarea>
                          <?php
                          echo $custom_fields[$field]['lbl_err'];
                          ?>
                        </div>
                        <?php
                      } else {
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
                            <div class="form-group row">
                    <?php
                    if (!isset($validation_errors)) $validation_errors = [];
                    $table_name_detail = "tbl_rent_invoice_detail";
                    $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
                    $select->bindParam(1, $table_name_detail);
                    $select->execute();
                    $row = $select->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $generator_options = json_decode($row["generator_options"]);
                        if ($generator_options) {
                            $fields_names = $generator_options->field_name;
                            $fields_types = $generator_options->field_type;
                            $field_scale = $generator_options->field_scale;
                            $dropdown_table = $generator_options->dropdown_table;
                            $label_column = $generator_options->label_column;
                            $value_column = $generator_options->value_column;
                            $where_condition = $generator_options->where_condition;
                            $fields_labels = $generator_options->field_label;
                            $field_display = $generator_options->field_display;
                            $field_required = $generator_options->field_required;
                            $allow_zero = $generator_options->allow_zero;
                            $allow_minus = $generator_options->allow_minus;
                            $chk_duplicate = $generator_options->chk_duplicate;
                            $field_data_type = $generator_options->field_data_type;
                            $field_is_disabled = $generator_options->is_disabled;

                            if (is_array($fields_names) && !empty($fields_names)) {
                                for ($i = 0; $i < count($fields_names); $i++) {

                                    // Rate / Unit and manual_rent_per (side by side)
                                    if ($fields_names[$i] == 'rate_per_unit') {
                                        $rate_required = (!empty($field_required) && in_array('rate_per_unit', $field_required)) ? 'required' : '';
                                        $rate_disabled = (!empty($field_is_disabled) && in_array('rate_per_unit', $field_is_disabled)) ? 'disabled' : '';
                                        $rate_duplicate = (!empty($chk_duplicate) && in_array('rate_per_unit', $chk_duplicate)) ? 'duplicate' : '';
                                        $rate_lbl = 'Rate / Unit' . ($rate_required ? '*' : '');

                                        $rate_is_invalid = isset($validation_errors['rate_per_unit']) ? 'is-invalid' : '';
                                        $rate_error_text = isset($validation_errors['rate_per_unit']) ? $validation_errors['rate_per_unit'] : "";

                                        $rate_min = (!empty($allow_zero) && in_array('rate_per_unit', $allow_zero)) ? 0 : 1;
                                        $rate_min_str = 'min="' . $rate_min . '"';
                                        $rate_step = 1;
                                        if (!empty($field_scale[$i]) && $field_scale[$i] > 0) {
                                            $step = '';
                                            for ($k = 1; $k < $field_scale[$i]; $k++) $step .= '0';
                                            $rate_step = '0.' . $step . '1';
                                        }
                                        $rate_step_str = 'step="' . $rate_step . '"';
                                        $rate_value = isset(${"val_rate_per_unit"}) ? ${"val_rate_per_unit"} : '';

                                        // Find manual_rent_per index
                                        $manual_idx = array_search('manual_rent_per', $fields_names);
                                        $manual_required = ($manual_idx !== false && !empty($field_required) && in_array('manual_rent_per', $field_required)) ? 'required' : '';
                                        $manual_is_invalid = ($manual_idx !== false && isset($validation_errors['manual_rent_per'])) ? 'is-invalid' : '';
                                        $manual_error_text = ($manual_idx !== false && isset($validation_errors['manual_rent_per'])) ? $validation_errors['manual_rent_per'] : "";
                                        $manual_cls = 'form-select '.$manual_required.' '.$manual_is_invalid;
                                        $manual_val = ($manual_idx !== false && isset(${"val_manual_rent_per"})) ? ${"val_manual_rent_per"} : '';
                                        $manual_dropdown = '';
                                        if ($manual_idx !== false) {
                                            $manual_dropdown = getDropdown(
                                                $dropdown_table[$manual_idx],
                                                $value_column[$manual_idx],
                                                $label_column[$manual_idx],
                                                !empty($where_condition[$manual_idx]) ? $where_condition[$manual_idx] : null,
                                                'manual_rent_per',
                                                $manual_val,
                                                $manual_cls,
                                                $manual_required
                                            );
                                            $manual_dropdown = preg_replace('/<div class="invalid-feedback">.*?<\/div>/', '', $manual_dropdown);
                                            $manual_dropdown = preg_replace('/<select/', '<select style="max-width:160px;width:160px;"', $manual_dropdown, 1);
                                        }
                                    ?>
                            <div class="col-sm-6 row gy-1 align-items-center">
                                <?php
                                //drashti
                                $lbl_str = '<label for="rate_per_unit" class="col-sm-4 control-label">Rate / Unit';
                                if ($rate_required && 'rate_per_unit' !== 'manual_rent_per') {
                                    $lbl_str .= "*";
                                }
                                $lbl_str .= '</label>';
                                echo $lbl_str;
                                //done
                                ?>
                                <div class="col-sm-8 d-flex" style="gap:8px;">
                                    <div class="d-flex flex-column" style="min-width: 170px;width:170px;">
                                        <input type="number"
                                            name="rate_per_unit"
                                            id="rate_per_unit"
                                            value="<?php echo htmlspecialchars($rate_value); ?>"
                                            class="form-control <?php echo $rate_is_invalid; ?>"
                                            <?php echo $rate_required; ?>
                                            <?php echo $rate_disabled; ?>
                                            <?php echo $rate_min_str; ?>
                                            <?php echo $rate_step_str; ?>
                                            style="max-width: 170px;width:170px;"
                                            autocomplete="off"
                                            placeholder="Enter Rate"
                                        />
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($rate_error_text); ?></div>
                                    </div>
                                    <?php if ($manual_idx !== false): ?>
                                    <div class="d-flex flex-column" style="min-width: 160px;width:160px;">
                                        <?php echo $manual_dropdown; ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($manual_error_text); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                                    <?php
                                        if ($manual_idx > $i) $i = $manual_idx;
                                        continue;
                                    }
                                    // Regular rendering for all other fields
                                    $required = (!empty($field_required) && in_array($fields_names[$i], $field_required)) ? 1 : 0;
                                    $is_disabled = (!empty($field_is_disabled) && in_array($fields_names[$i], $field_is_disabled)) ? 1 : 0;
                                    $duplicate_str = (!empty($chk_duplicate) && in_array($fields_names[$i], $chk_duplicate)) ? "duplicate" : "";
                                    $display_str = (!empty($field_display) && in_array($fields_names[$i], $field_display)) ? "display" : "";
                                    $required_str = $required ? "required" : "";
                                    $disabled_str = $is_disabled ? "disabled" : "";

                                    //drashti
                                    $lbl_str = '<label for="' . $fields_names[$i] . '" class="col-sm-4 control-label">' . $fields_labels[$i];
                                    if ($required) {
                                        $required_str = "required";
                                        // Only append the asterisk (*) to the label if the field is NOT "manual_rent_per"
                                        if ($fields_names[$i] !== 'manual_rent_per') {
                                            $lbl_str .= "*";
                                        }
                                        $error_container = '<div class="invalid-feedback"></div>';
                                    }
                                    if ($is_disabled) {
                                        $disabled_str = "disabled";
                                    }
                                    $lbl_str .= '</label>';
                                    //done
                                    $field_str = "";
                                    $value = isset(${"val_" . $fields_names[$i]}) ? ${"val_" . $fields_names[$i]} : "";
                                    $is_invalid = isset($validation_errors[$fields_names[$i]]) ? 'is-invalid' : '';
                                    $error_text = isset($validation_errors[$fields_names[$i]]) ? $validation_errors[$fields_names[$i]] : "";
                                    if ($fields_types[$i] == "select") $error_text = isset($validation_errors[$fields_names[$i]]) ? $validation_errors[$fields_names[$i]] : "";
                                    $error_container = '<div class="invalid-feedback">' . htmlspecialchars($error_text) . '</div>';
                                    $placeholder = '';
                                    if ($fields_types[$i] != "select" && $fields_types[$i] != "hidden" && $fields_types[$i] != "radio" && $fields_types[$i] != "checkbox") {
                                        if (strpos(strtolower($fields_names[$i]), 'qty') !== false || 
                                            strpos(strtolower($fields_names[$i]), 'quantity') !== false) {
                                            $placeholder = 'placeholder="Enter Qty"';
                                        } else {
                                            $field_name = str_replace('_', ' ', $fields_names[$i]);
                                            $field_name = ucfirst($field_name); 
                                            $placeholder = 'placeholder="Enter ' . $field_name . '"';
                                        }
                                    }

                                    switch ($fields_types[$i]) {
                                        case "text":
                                        case "email":
                                        case "file":
                                        case "date":
                                        case "datetime-local":
                                        case "number":
                                            $cls = "form-control $is_invalid $required_str $duplicate_str $display_str";
                                            $step_str = "";
                                            $min_str = "";
                                            if ($fields_types[$i] == "number") {
                                                $step = 1;
                                                if (!empty($field_scale[$i]) && $field_scale[$i] > 0) {
                                                    $s = '';
                                                    for ($k = 1; $k < $field_scale[$i]; $k++) $s .= '0';
                                                    $step = '0.' . $s . '1';
                                                }
                                                $step_str = 'step="' . $step . '"';
                                                $min = 1;
                                                if (!empty($allow_zero) && in_array($fields_names[$i], $allow_zero)) $min = 0;
                                                if (!empty($allow_minus) && in_array($fields_names[$i], $allow_minus)) $min = "";
                                                $min_str = 'min="' . $min . '"';
                                            }
                                            $field_str .= '<input type="' . $fields_types[$i] . '" name="' . $fields_names[$i] . '" id="' . $fields_names[$i] . '" value="' . htmlspecialchars($value) . '" class="' . $cls . '" ' . $required_str . ' ' . $disabled_str . ' ' . $min_str . ' ' . $step_str . ' ' . $placeholder . ' autocomplete="off" />' . $error_container;
                                            break;

                                        case "select":
                                            $cls = "form-select $is_invalid $required_str $duplicate_str $display_str";
                                            $where_condition_val = (!empty($where_condition[$i])) ? $where_condition[$i] : null;
                                            $selected_val = $value;
                                            $dropdown_html = getDropdown(
                                                $dropdown_table[$i],
                                                $value_column[$i],
                                                $label_column[$i],
                                                $where_condition_val,
                                                $fields_names[$i],
                                                $selected_val,
                                                $cls,
                                                $required_str
                                            );
                                            // Remove any invalid-feedback from getDropdown, we'll add below
                                            $dropdown_html = preg_replace('/<div class="invalid-feedback">.*?<\/div>/', '', $dropdown_html);
                                            $field_str .= $dropdown_html . $error_container;
                                            break;

                                        case "radio":
                                        case "checkbox":
                                            break;
                                        case "hidden":
                                            $hiddenvalue = ($field_data_type[$i] == "int" || $field_data_type[$i] == "bigint" || $field_data_type[$i] == "tinyint" || $field_data_type[$i] == "decimal") ? 0 : "";
                                            $hiddenvalue = isset(${"val_" . $fields_names[$i]}) ? ${"val_" . $fields_names[$i]} : $hiddenvalue;
                                            if ($fields_names[$i] != "rent_invoice_id") {
                                                $hidden_str .= addHidden($fields_names[$i], $hiddenvalue);
                                            }
                                            $lbl_str = "";
                                            break;
                                        case "textarea":
                                            $cls = "form-control $is_invalid $required_str $duplicate_str $display_str";
                                            $field_str .= '<textarea name="' . $fields_names[$i] . '" id="' . $fields_names[$i] . '" class="' . $cls . '" ' . $required_str . ' ' . $disabled_str . ' ' . $placeholder . '>' . htmlspecialchars($value) . '</textarea>' . $error_container;
                                            break;

                                        default:
                                            break;
                                    }

                                    if ($field_str) {
                                    ?>
                                        <div class="col-sm-6 row gy-1">
                                            <?php echo $lbl_str; ?>
                                            <div class="col-sm-8">
                                                <?php echo $field_str; ?>
                                            </div>
                                        </div>
                                    <?php
                                                    }
                                                }
                                            }
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
<!-- DRASHTI-invoice type(Regular, Tax Invoice, or Bill of Supply)--> 
<script>
    const companyStateId = '<?php echo htmlspecialchars($company_state_id, ENT_QUOTES); ?>';
</script>   
<script>
$(document).ready(function () {
    const taxInputs = ['sgst', 'cgst', 'igst'];
    const taxContainer = $('.tax-fields-container');
    const transactionMode = $('#transactionmode').val();
    if (transactionMode === 'U' && $('#invoice_for').val() === '5') {
        $('#manual-invoice-details').show();
        $('#generate-btn-wrap').show();
        $('#generate').prop('disabled', true);
    }
    function hideFields(fields) {
        fields.forEach(function (name) {
            $('label[for="' + name + '"]').parent().hide(); 
            $('[name="' + name + '"]').parent().hide();
        });
        taxContainer.addClass('hidden-fields'); 
    }
    function showFields(fields) {
        fields.forEach(function (name) {
            $('label[for="' + name + '"]').parent().show();
            $('[name="' + name + '"]').parent().show();
        });
        taxContainer.removeClass('hidden-fields'); 
    }
    function disableFields(fields) {
        fields.forEach(function (name) {
            $('[name="' + name + '"]').prop('disabled', true)
                .closest('.tax-field').addClass('disabled-tax');
        });
    }
    function enableFields(fields) {
        fields.forEach(function (name) {
            $('[name="' + name + '"]').prop('disabled', false)
                .closest('.tax-field').removeClass('disabled-tax');
        });
    }
    $('#customer').on('change', function() {
        const customerId = this.value;
        if (!customerId) {
            $('input[name="tax_amount"]').prop('checked', false);
            return;
        }
        $('input[name="tax_amount"]').prop('disabled', true);
        $.ajax({
            url: 'classes/cls_rent_invoice_master.php',
            type: 'POST',
            data: { action: 'get_customer_state', customer_id: customerId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.state_id) {
                    if (String(companyStateId) === String(response.state_id)) {
                        $('input[name="tax_amount"][value="2"]').prop('checked', true); // SGST+CGST
                    } else {
                        $('input[name="tax_amount"][value="3"]').prop('checked', true); // IGST
                    }
                } else {
                    $('input[name="tax_amount"][value="2"]').prop('checked', true);
                }
            },
            error: function() {
                $('input[name="tax_amount"][value="2"]').prop('checked', true);
            },
            complete: function() {
                // Only enable tax_amount if NOT Bill of Supply (invoice_type != 3)
                const selectedType = $('input[name="invoice_type"]:checked').val();
                if (selectedType !== "3") {
                    $('input[name="tax_amount"]').prop('disabled', false);
                } else {
                    $('input[name="tax_amount"]').prop('disabled', true);
                }
            }
        });
    });
    $('#hsn_code').on('change', function () {
        var hsnCodeId = $(this).val();
        if (!hsnCodeId) {
            $('#sgst').val('');
            $('#cgst').val('');
            $('#igst').val('');
            return;
        }
        $.ajax({
            url: 'classes/cls_rent_invoice_master.php',
            type: 'POST',
            data: { action: 'get_hsn_tax_rates', hsn_code_id: hsnCodeId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#sgst').val(response.sgst || '');
                    $('#cgst').val(response.cgst || '');
                    $('#igst').val(response.igst || '');
                } else {
                    $('#sgst').val('');
                    $('#cgst').val('');
                    $('#igst').val('');
                    Swal.fire({ icon: 'error', title: 'Error', text: response.error || 'Failed to fetch tax rates' });
                }
            },
            error: function() {
                $('#sgst').val('');
                $('#cgst').val('');
                $('#igst').val('');
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch tax rates' });
            }
        });
    });
    if ($('#hsn_code').val()) {
        $('#hsn_code').trigger('change');
    }
    if ($('#customer').val()) {
        $('#customer').trigger('change');
    }
    function handleInvoiceTypeChange() {
        const selectedType = $('input[name="invoice_type"]:checked').val();
        const customerId = $('#customer').val();
        if (selectedType === "1") {
            hideFields(['tax_amount', ...taxInputs]);
            $('input[name="tax_amount"]').prop('checked', false).prop('disabled', false);
            enableFields(taxInputs);
            $('select[name="hsn_code"]').prop('disabled', true);
            $('#sgst').val('');
            $('#cgst').val('');
            $('#igst').val('');
        } else if (selectedType === "2") { 
            showFields(['tax_amount', ...taxInputs]);
            $('input[name="tax_amount"]').prop('disabled', false);
            disableFields(taxInputs);
            $('select[name="hsn_code"]').prop('disabled', false);
            if ($('#hsn_code').val()) {
                $('#hsn_code').trigger('change');
            }
        } else if (selectedType === "3") {
            showFields(['tax_amount', ...taxInputs]);
            $('input[name="tax_amount"]').prop('disabled', true);
            disableFields(taxInputs);
            $('select[name="hsn_code"]').prop('disabled', false);
            if ($('#hsn_code').val()) {
                $('#hsn_code').trigger('change');
            }
        }
    }
    $('input[name="invoice_type"][value="2"]').prop('checked', true).focus();
    handleInvoiceTypeChange();
    $('input[name="invoice_type"]').change(handleInvoiceTypeChange);
});
</script>
<!--DONE-->
<script>
document.addEventListener("DOMContentLoaded", function () {    
    let jsonData = [];
    let editIndex = -1;
    let deleteData = [];
    let detailIdLabel="";
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    //HETASVI-HETANSHREE Invoice Date,Billing Date
    const invoiceDateInput = document.getElementById("invoice_date");
    const billingTillDateInput = document.getElementById("billing_till_date");
    const companyYearSelect = document.getElementById("company_year_id");
    let companyYearStart, companyYearEnd;
    const currentDate = new Date();
    function formatDate(dateObj) {
        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(dateObj.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
    function setDefaultInvoiceDatesForYear(fyStart, fyEnd) {
        const today = new Date();
        let defaultDate;
        if (today >= fyStart && today <= fyEnd) {
            defaultDate = today;
        } else {
            defaultDate = fyStart;
        }
        const formattedDate = formatDate(defaultDate);
        if (invoiceDateInput) {
            invoiceDateInput.value = formattedDate;
            invoiceDateInput.min = formatDate(fyStart);
            invoiceDateInput.max = formatDate(fyEnd);
        }
        if (billingTillDateInput) {
            billingTillDateInput.value = formattedDate;
            billingTillDateInput.min = formatDate(fyStart);
            billingTillDateInput.max = formatDate(fyEnd);
        }
    }
    if (companyYearSelect) {
        companyYearSelect.addEventListener("change", function () {
            const newYearId = this.value;
            if (!newYearId) return;
            $.ajax({
                url: "classes/cls_rent_invoice_master.php",
                type: "POST",
                data: { action: "get_company_year", company_year_id: newYearId },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        companyYearStart = new Date(response.start_date);
                        companyYearEnd = new Date(response.end_date);
                        setDefaultInvoiceDatesForYear(companyYearStart, companyYearEnd);
                    }
                }
            });
        });
    }
    (function() {
        const companyYearStartYear = "<?php echo isset($companyYearStartYear) ? $companyYearStartYear : ''; ?>";
        if ((invoiceDateInput || billingTillDateInput) && companyYearStartYear) {
            const today = new Date();
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const day = today.getDate().toString().padStart(2, '0');
            const formattedDate = companyYearStartYear + '-' + month + '-' + day;
            if (invoiceDateInput) invoiceDateInput.value = formattedDate;
            if (billingTillDateInput) billingTillDateInput.value = formattedDate;
        }
    })();
    if (invoiceDateInput && billingTillDateInput) {
        invoiceDateInput.addEventListener("blur", function() {
            if (invoiceDateInput.value) {
                billingTillDateInput.value = invoiceDateInput.value;
                validateBillingTillDate();
            }
        });
        invoiceDateInput.addEventListener("change", function() {
            validateBillingTillDate();
        });
        billingTillDateInput.addEventListener("change", function() {
            validateBillingTillDate();
        });
        billingTillDateInput.addEventListener("blur", function() {
            validateBillingTillDate();
        });
    }
    function validateInvoiceDate(dateString) {
        if (!dateString) return true;
        const invoiceDate = new Date(dateString);
        invoiceDate.setHours(0, 0, 0, 0);
        if (companyYearStart && companyYearEnd) {
            const startDate = new Date(companyYearStart);
            startDate.setHours(0, 0, 0, 0);

            const endDate = new Date(companyYearEnd);
            endDate.setHours(0, 0, 0, 0);

            if (invoiceDate < startDate) {
                showDateError("invoice_date", "Date is below current period");
                return false;
            }
            if (invoiceDate > endDate) {
                showDateError("invoice_date", "Date is above current period");
                return false;
            }
        } else {
            const currentFYStart = new Date(2025, 3, 1);
            const currentFYEnd = new Date(2026, 2, 31); 
            if (invoiceDate < currentFYStart) {
                showDateError("invoice_date", "Date is below current period");
                return false;
            }
            if (invoiceDate > currentFYEnd) {
                showDateError("invoice_date", "Date is above current period");
                return false;
            }
        }
        clearDateError("invoice_date");
        return true;
    }
    function validateBillingTillDate() {
        const invoiceDateInput = document.getElementById("invoice_date");
        const billingTillDateInput = document.getElementById("billing_till_date");
        if (!invoiceDateInput || !billingTillDateInput) return true;
        const invoiceDateStr = invoiceDateInput.value;
        const billingDateStr = billingTillDateInput.value;
        if (invoiceDateStr && billingDateStr) {
            const invoiceDate = new Date(invoiceDateStr);
            const billingDate = new Date(billingDateStr);

            if (billingDate > invoiceDate) {
                showDateError("billing_till_date", "Billing date can not be greater than invoice date.");
                return false;
            }
        }
        clearDateError("billing_till_date");
        return true;
    }
    function showDateError(fieldId, message) {
        const input = document.getElementById(fieldId);
        if (input) {
            input.classList.add("is-invalid");
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains("invalid-feedback")) {
                feedback.textContent = message;
            }
        }
    }
    function clearDateError(fieldId) {
        const input = document.getElementById(fieldId);
        if (input) {
            input.classList.remove("is-invalid");
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains("invalid-feedback")) {
                feedback.textContent = "";
            }
        }
    }
    if (companyYearSelect && companyYearSelect.value) {
        companyYearSelect.dispatchEvent(new Event('change'));
    }
    //Done
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
    function checkDuplicate(input, callback) {
    const column_value = input.value.trim();
    if (!column_value) {
        input.classList.remove("is-invalid");
        const errorContainer = input.nextElementSibling;
        if (errorContainer && errorContainer.classList.contains("invalid-feedback")) {
            errorContainer.textContent = "";
        }
        if (typeof callback === "function") callback(false);
        return;
    }
    const id_column = "<?php echo 'rent_invoice_id'; ?>";
    const id_value = document.getElementById(id_column)?.value || "0";
    $.ajax({
        url: "<?php echo 'classes/cls_rent_invoice_master.php'; ?>",
        type: "POST",
        data: {
            column_name: input.name,
            column_value: column_value,
            id_name: id_column,
            id_value: id_value,
            table_name: "<?php echo 'tbl_rent_invoice_master'; ?>",
            action: "checkDuplicate"
        },
        success: function(response) {
            const result = parseInt(response, 10);
            const errorContainer = input.nextElementSibling;
            const isDuplicate = result === 1;
            if (isDuplicate) {
                input.classList.add("is-invalid");
                if (errorContainer && errorContainer.classList.contains("invalid-feedback")) {
                    errorContainer.textContent = "Duplicate Value";
                }
            } else {
                input.classList.remove("is-invalid");
                if (errorContainer && errorContainer.classList.contains("invalid-feedback")) {
                    errorContainer.textContent = "";
                }
            }
            if (typeof callback === "function") callback(isDuplicate);
        },
        error: function(xhr, status, error) {
            input.classList.remove("is-invalid");
            const errorContainer = input.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains("invalid-feedback")) {
                errorContainer.textContent = "";
            }
            if (typeof callback === "function") callback(false);
        }
    });
}
    //MANSI- INVOICE NO AUTO
   const financialYear = "<?php echo isset($finYear) ? $finYear : ''; ?>";
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
    //DONE
         const tableHead = document.getElementById("tableHead");
        const tableBody = document.getElementById("tableBody");
        const form = document.getElementById("popupForm");
        const modalDialog = document.getElementById("modalDialog");
        const modal = new bootstrap.Modal(modalDialog);
    
   document.querySelectorAll("#searchDetail tbody tr").forEach(row => {
    let rowData = {};
    if (!row.classList.contains("norecords")) {
        rowData[row.dataset.label] = row.dataset.id;
        detailIdLabel = row.dataset.label;
        editIndex++;
        row.querySelectorAll("td[data-label]").forEach(td => {
            if (!td.classList.contains("actions")) {
                rowData[td.dataset.label] = td.innerText.trim();
            }
        });
        rowData["detailtransactionmode"] = "U";
        jsonData[editIndex] = rowData;
        console.log("Row data:", rowData);
    }
});
 console.log("jsonData:", jsonData);
    
    modalDialog.addEventListener("hidden.bs.modal", function () {
     clearForm(form);
     setFocustAfterClose();
    });
    
function openModal(index = -1) {
    const form = document.getElementById("popupForm");
    if (index >= 0 && jsonData[index]) {
        console.log("Editing data:", jsonData[index]);
        
        editIndex = index;
        const data = jsonData[index];
        clearForm(form);
          if (data.rent_invoice_detail_id) {
            form.elements['rent_invoice_detail_id'].value = data.rent_invoice_detail_id;
        }
        if (data.rent_invoice_id) {
            form.elements['rent_invoice_id'].value = data.rent_invoice_id;
        }
        console.log("rate_per_unit value:", data['rate_per_unit'], 
                   "Form field:", form.elements['rate_per_unit']);
        
        // First handle the invoice_for dropdown specifically
        if (data.invoice_for && form.elements['invoice_for']) {
            const invoiceForSelect = form.elements['invoice_for'];
            let optionFound = false;
            for (let i = 0; i < invoiceForSelect.options.length; i++) {
                if (invoiceForSelect.options[i].value == data.invoice_for) {
                    invoiceForSelect.selectedIndex = i;
                    optionFound = true;
                    break;
                }
            }
            if (!optionFound && invoiceForSelect.options.length > 0) {
                invoiceForSelect.selectedIndex = 0;
            }
        }
        
        // Then handle all other fields
        for (let key in data) {
            // Skip invoice_for since we already handled it
            if (key === 'invoice_for') continue;
            
            const inputField = form.elements[key];
            if (!inputField) {
                console.log("No form field for:", key);
                continue;
            }
            if (inputField.type === "checkbox" || inputField.type === "radio") {
                inputField.checked = (inputField.value == data[key]);
            } 
            else if (inputField.tagName === "SELECT") {
                let optionFound = false;
                for (let i = 0; i < inputField.options.length; i++) {
                    if (inputField.options[i].value == data[key]) {
                        inputField.selectedIndex = i;
                        optionFound = true;
                        break;
                    }
                }
                if (!optionFound && inputField.options.length > 0) {
                    inputField.selectedIndex = 0;
                }
            }
            else if (inputField.type !== "hidden") {
                inputField.value = data[key] || "";
            }
            if (inputField.type === "number" && data[key] === 0) {
                inputField.value = "0";
            }
        }
    } else {
        editIndex = -1;
        clearForm(form);
        // Set default value for new records
        if (form.elements['invoice_for']) {
            form.elements['invoice_for'].value = '1'; // Regular(out+stock)
        }
    }
    
    modal.show();
    // Ensure UI updates based on invoice_for value
    toggleManualInvoiceDetails();
    
    setTimeout(() => {
        const firstInput = form.querySelector("input:not([type=hidden]), select, textarea");
        if (firstInput) firstInput.focus();
    }, 10);
}
   function saveData() {
       console.log("jsonData before submit:", jsonData);
    const formData = new FormData(form);
    const newEntry = {};
    const allEntries = {};
       
         const rentInvoiceDetailId = form.elements['rent_invoice_detail_id'].value || "0";
    const rentInvoiceId = form.elements['rent_invoice_id'].value || "";

    for (const [key, value] of formData.entries()) {
        if (["rate_per_unit", "qty", "amount"].includes(key)) {
            newEntry[key] = parseFloat(value) || 0;
            allEntries[key] = parseFloat(value) || 0;
        } else {
            newEntry[key] = value;
            allEntries[key] = value;
        }
                newEntry['rent_invoice_detail_id'] = rentInvoiceDetailId;
        newEntry['rent_invoice_id'] = rentInvoiceId;
        allEntries['rent_invoice_detail_id'] = rentInvoiceDetailId;
        allEntries['rent_invoice_id'] = rentInvoiceId;
        if (editIndex >= 0) {
            if (jsonData[editIndex].hasOwnProperty(key)) {
                if (["rate_per_unit", "qty", "amount"].includes(key)) {
                    jsonData[editIndex][key] = parseFloat(value) || 0;
                } else {
                    jsonData[editIndex][key] = value;
                }
            }
        }
    }

    if ($("#norecords").length > 0) {
        $("#norecords").remove();
    }

    if (editIndex >= 0) {
        jsonData[editIndex]["detailtransactionmode"] = "U";
        updateTableRow(editIndex, newEntry);
        modal.hide();
        Swal.fire({
            icon: "success",
            title: "Updated Successfully",
            text: "The record has been updated successfully!",
            showConfirmButton: true,
            showClass: { popup: "" },
            hideClass: { popup: "" }
        }).then((result) => {
            setFocustAfterClose();
        });
    } else {
        allEntries["detailtransactionmode"] = "I";
        jsonData.push(allEntries);
        appendTableRow(newEntry, jsonData.length - 1);
        modal.hide();
        Swal.fire({
            icon: "success",
            title: "Added Successfully",
            text: "The record has been added successfully!",
            showConfirmButton: true,
            showClass: { popup: "" },
            hideClass: { popup: "" }
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
    updateBasicAmountFromManualGrid();
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
    // Validate required data
    if (!rowData.rate_per_unit && rowData.rate_per_unit !== 0) {
        console.warn("Missing rate_per_unit in rowData:", rowData);
    }
    
    const row = document.createElement("tr");
    const id = rowData["rent_invoice_detail_id"] || 0;
    row.setAttribute("data-id", id);
    
    // Store ALL data as data attributes
    Object.keys(rowData).forEach(key => {
        row.dataset[key] = rowData[key];
    });
    
    // Add action buttons
    const actionCell = document.createElement("td");
    actionCell.classList.add("actions");
    
    const editButton = document.createElement("button");
    editButton.textContent = "Edit";
    editButton.classList.add("btn", "btn-info", "btn-sm", "me-2", "edit-btn");
    editButton.setAttribute("data-index", index);
    editButton.setAttribute("data-id", id);
    
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Delete";
    deleteButton.classList.add("btn", "btn-danger", "btn-sm", "delete-btn");
    deleteButton.setAttribute("data-index", index);
    deleteButton.setAttribute("data-id", id);
    
    actionCell.appendChild(editButton);
    actionCell.appendChild(deleteButton);
    row.appendChild(actionCell);
    
    // Add visible data cells
    const displayFields = ["description", "qty", "unit", "rate_per_unit", "amount", "remark"];
    displayFields.forEach(field => {
        const cell = document.createElement("td");
        cell.setAttribute("data-label", field);
        
        // Special formatting for numeric fields
        if (field === "rate_per_unit" || field === "amount") {
            cell.textContent = parseFloat(rowData[field] || 0).toFixed(2);
        } else {
            cell.textContent = rowData[field] || "";
        }
        
        row.appendChild(cell);
    });
    
    tableBody.appendChild(row);
}
 
function updateTableRow(index, rowData) {
    const row = tableBody.children[index];
    const id = rowData["rent_invoice_detail_id"] || 0;
    row.innerHTML = "";
 
    // Add action buttons first (to match appendTableRow)
    const actionCell = document.createElement("td");
    actionCell.classList.add("actions");
 
    const editButton = document.createElement("button");
    editButton.textContent = "Edit";
    editButton.classList.add("btn", "btn-info", "btn-sm", "me-2", "edit-btn");
    editButton.setAttribute("data-index", index);
    editButton.setAttribute("data-id", id);
 
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Delete";
    deleteButton.classList.add("btn", "btn-danger", "btn-sm", "delete-btn");
    deleteButton.setAttribute("data-index", index);
    deleteButton.setAttribute("data-id", id);
 
    actionCell.appendChild(editButton);
    actionCell.appendChild(deleteButton);
    row.appendChild(actionCell);
 
    // Add visible data cells, same as appendTableRow
    const displayFields = ["description", "qty", "unit", "rate_per_unit", "amount", "remark"];
    displayFields.forEach(field => {
        const cell = document.createElement("td");
        cell.setAttribute("data-label", field);
 
        if (field === "rate_per_unit" || field === "amount") {
            cell.textContent = parseFloat(rowData[field] || 0).toFixed(2);
        } else {
            cell.textContent = rowData[field] || "";
        }
 
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
    //  MANSI- invoice type auto selected HSN
        function updateHSNCodeByInvoiceType() {
        let selectedType = document.querySelector('input[name="invoice_type"]:checked');
        let hsnDropdown = document.getElementById("hsn_code");
        if (selectedType && hsnDropdown) {
            if (selectedType.value == '2') {
                if (hsnDropdown.options.length > 1) hsnDropdown.selectedIndex = 1;
            } else if (selectedType.value == '3') {
                if (hsnDropdown.options.length > 2) hsnDropdown.selectedIndex = 2;
            } else {
                hsnDropdown.selectedIndex = 0;
            }
        var event = new Event('change', { bubbles: true });
        hsnDropdown.dispatchEvent(event);
        }
    }
    document.querySelectorAll('input[name="invoice_type"]').forEach(function(radio) {
        radio.addEventListener('change', updateHSNCodeByInvoiceType);
    });
    setTimeout(updateHSNCodeByInvoiceType, 0);
    //DONE
    
   // MANSI- On customer or invoice type change, fetch lots for that customer
        var customerInput = document.getElementById('customer');
        var invoiceTypeInputs = document.querySelectorAll('input[name="invoice_type"]');
        function fetchLots() {
            var customerId = customerInput ? customerInput.value : '';
            var invoiceType = document.querySelector('input[name="invoice_type"]:checked') ? 
                              document.querySelector('input[name="invoice_type"]:checked').value : '';
            if (!customerId || !invoiceType) {
                setLotNoOptions([]);
                return;
            }
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ 
                    ajax_get_lots: 1, 
                    customer_id: customerId,
                    invoice_type: invoiceType 
                })
            })
            .then(response => response.json())
            .then(response => {
                if (response.error) {
                    setLotNoOptions([], true);
                } else {
                    setLotNoOptions(response);
                }
            })
            .catch(() => setLotNoOptions([], true));
        }
        if (customerInput) {
            customerInput.addEventListener('change', fetchLots);
            if (customerInput.value) {
                fetchLots();
            } else {
                setLotNoOptions([]);
            }
        }
        if (invoiceTypeInputs) {
            invoiceTypeInputs.forEach(input => {
                input.addEventListener('change', fetchLots);
            });
        }
      initLotNoMultiselect();
      function setLotNoOptions(lots, error = false) {
      const optionsDiv = document.getElementById('lotNoSelectOptions');
      if (!optionsDiv) return;
      if (error) {
        optionsDiv.innerHTML = '<div class="p-2 text-danger">Error loading lots</div>';
        return;
      }
      if (!lots || lots.length === 0) {
        optionsDiv.innerHTML = '<div class="p-2 text-muted">No lots found</div>';
        return;
      }
      let allCheckbox = `
        <label for="lot_no_all">
          <input type="checkbox" id="lot_no_all" onchange="toggleAllLotNoCheckboxes(this)" checked />
          All
        </label>
      `;
      let lotsCheckboxes = lots.map((lot, idx) =>
        `<label for="lot_no_${idx}">
          <input type="checkbox" id="lot_no_${idx}" value="${lot.lot_no}" onchange="lotNoCheckboxStatusChange()" name="lot_no[]" checked/>
          ${lot.lot_no}
        </label>`
      ).join('');
      optionsDiv.innerHTML = allCheckbox + lotsCheckboxes;
      lotNoCheckboxStatusChange();
    }
    function toggleAllLotNoCheckboxes(allCheckbox) {
      const optionsDiv = document.getElementById('lotNoSelectOptions');
      if (!optionsDiv) return;
      const checkboxes = optionsDiv.querySelectorAll('input[type="checkbox"][name="lot_no[]"]');
      checkboxes.forEach(cb => {
        cb.checked = allCheckbox.checked;
      });
      lotNoCheckboxStatusChange();
    }
    function initLotNoMultiselect() {
      lotNoCheckboxStatusChange();
      const labelDiv = document.getElementById('lotNoSelectLabel');
      if (labelDiv) {
        labelDiv.addEventListener('click', function(e) {
          e.stopPropagation();
          toggleLotNoCheckboxArea();
        });
        labelDiv.addEventListener('keydown', function(e) {
          if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            toggleLotNoCheckboxArea();
          }
        });
      }
      document.addEventListener("click", function(evt) {
        var flyout = document.getElementById('lotNoMultiselect');
        var target = evt.target;
        do {
          if (target == flyout) return;
          target = target.parentNode;
        } while (target);
        toggleLotNoCheckboxArea(true);
      });
    }

    function lotNoCheckboxStatusChange() {
      var multiselect = document.getElementById("lotNoSelectLabel");
      if (!multiselect) return;
      var option = multiselect.getElementsByTagName('option')[0];
      var optionsDiv = document.getElementById("lotNoSelectOptions");
      if (!optionsDiv) return;
      var allCheckbox = document.getElementById("lot_no_all");
      var checkboxes = optionsDiv.querySelectorAll('input[type=checkbox][name="lot_no[]"]');
      var checked = Array.from(checkboxes).filter(cb => cb.checked);
      var values = checked.map(cb => cb.value);
      if (allCheckbox) {
        allCheckbox.checked = (checked.length === checkboxes.length);
      }
      if (checked.length === checkboxes.length && checkboxes.length > 0) {
        option.innerText = "All";
      } else if (values.length > 0) {
        option.innerText = values.join(', ');
      } else {
        option.innerText = "Select Lot No";
      }
    }
    function toggleLotNoCheckboxArea(onlyHide = false) {
      var checkboxes = document.getElementById("lotNoSelectOptions");
      if (!checkboxes) return;
      if (onlyHide) {
        checkboxes.style.display = "none";
        return;
      }
      checkboxes.style.display = (checkboxes.style.display !== "block") ? "block" : "none";
    }
    //DONE
    //MANUAL MODEL
    function toggleManualInvoiceDetails() {
    var selected = $('#invoice_for').val();
    if (selected === '5') { 
      $('#manual-invoice-details').show();
      $('#generate-btn-wrap').show(); 
      $('#generate').prop('disabled', true);
    } else {
      $('#manual-invoice-details').hide();
      $('#generate-btn-wrap').show();
      $('#generate').prop('disabled', false); 
    }
  }
    const transactionMode = document.getElementById('transactionmode') ? document.getElementById('transactionmode').value : "";

     if (transactionMode === "I") { // Only ADD mode
    $('#invoice_for').val('1'); 
    toggleManualInvoiceDetails();
    $('#invoice_for').on('change', toggleManualInvoiceDetails);
}
    //DONE
    //DRASHTI- detail calculation
    const qtyInput = document.querySelector('input[name="qty"]') || document.getElementById('qty');
    const unitInput = document.querySelector('input[name="unit"]');
    const weightInput = document.querySelector('input[name="weight"]') || document.getElementById('weight');
    const rateInput = document.getElementById('rate_per_unit');
    const amountInput = document.getElementById('amount');
    const manualRentPerSelect = document.querySelector('select[name="manual_rent_per"]');
    function calculateWeight() {
        if (qtyInput && unitInput && weightInput) {
            const qty = parseFloat(qtyInput.value) || 0;
            const unit = parseFloat(unitInput.value) || 0;
            const weight = qty * unit;
            weightInput.value = isNaN(weight) ? '' : weight;
        }
    }
    function calculateAmount() {
        if (qtyInput && weightInput && rateInput && amountInput && manualRentPerSelect) {
            const qty = parseFloat(qtyInput.value) || 0;
            const weight = parseFloat(weightInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const selectedValue = manualRentPerSelect.value;
            let multiplier;
            if (selectedValue === '2') {
                multiplier = weight;
            } else {
                multiplier = qty;
            }
            const amount = multiplier * rate;
            amountInput.value = amount.toFixed(2);
        }
    }
    if (qtyInput) {
        qtyInput.addEventListener('input', function() {
            calculateWeight();
            calculateAmount();
        });
    }
    if (unitInput) {
        unitInput.addEventListener('input', function() {
            calculateWeight();
            calculateAmount();
        });
    }
    if (weightInput) {
        weightInput.addEventListener('input', calculateAmount);
    }
    if (rateInput) {
        rateInput.addEventListener('input', calculateAmount);
    }
    if (manualRentPerSelect) {
        manualRentPerSelect.addEventListener('change', calculateAmount);
    }
    calculateWeight();
    calculateAmount();
    //DONE
function deleteRow(index, id) {
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
            const numberOfColumns = document.querySelector("table th") 
                ? document.querySelector("table th").parentElement.children.length 
                : 0;
            if (jsonData.length === 0) {
                const noRecordsRow = document.createElement("tr");
                const noRecordsCell = document.createElement("td");
                noRecordsCell.colSpan = numberOfColumns;
                noRecordsCell.textContent = "No records available";
                noRecordsRow.appendChild(noRecordsCell);
                noRecordsRow.setAttribute("id","norecords");
                noRecordsRow.classList.add("norecords"); 
                tableBody.appendChild(noRecordsRow);
            } else {
                jsonData.forEach((data, idx) => appendTableRow(data, idx));
            }
        }
    });
    updateBasicAmountFromManualGrid();
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
            event.preventDefault();
            const form = document.getElementById("masterForm");
            let i = 0;
            let firstelement;
            
            // Validate duplicates
            duplicateInputs.forEach((input) => {
                checkDuplicate(input);
            });

            // Form validation
            if (!form.checkValidity()) {
                form.querySelectorAll(":invalid").forEach(function (input) {
                    if (i == 0) {
                        firstelement = input;
                    }
                    input.classList.add("is-invalid");
                    input.nextElementSibling.textContent = input.validationMessage; 
                    i++;
                });
                if (firstelement) firstelement.focus(); 
                return false;
            } else {
                form.querySelectorAll(".is-invalid").forEach(function (input) {
                    input.classList.remove("is-invalid");
                    input.nextElementSibling.textContent = "";
                });
            }

            setTimeout(function() {
                const invalidInputs = document.querySelectorAll(".is-invalid");
                if (invalidInputs.length > 0) {
                    return;
                }

                // Get all selected lot numbers
                const lotNoCheckboxes = document.querySelectorAll('input[name="lot_no[]"]:checked');
                const selectedLotNos = Array.from(lotNoCheckboxes).map(cb => cb.value);

                // Process detail records
                let allDetailRecords = [];
                
                // 1. Handle manual entries
                if (Array.isArray(jsonData)) {
                    jsonData.forEach(record => {
                        // If record doesn't have a lot_no or has placeholder, duplicate for each selected lot
                        if (!record.lot_no || record.lot_no === 'Select Lot No') {
                            selectedLotNos.forEach(lot => {
                                let newRecord = {...record};
                                newRecord.lot_no = lot;
                                allDetailRecords.push(newRecord);
                            });
                        } else {
                            allDetailRecords.push(record);
                        }
                    });
                }

                // 2. Handle generated entries
                if (Array.isArray(generatedDetailsData) && generatedDetailsData.length > 0) {
                    generatedDetailsData.forEach(genRec => {
                        // Only include if the lot was selected
                        if (selectedLotNos.includes(genRec.lot_no)) {
                            // Check for duplicates based on lot_no and item
                            let exists = allDetailRecords.some(manRec => 
                                genRec.lot_no == manRec.lot_no && genRec.item == manRec.item
                            );
                            if (!exists) {
                                allDetailRecords.push(genRec);
                            }
                        }
                    });
                }

                // Prepare data for submission
          document.getElementById("detail_records").value = JSON.stringify(jsonData);
          document.getElementById("deleted_records").value = JSON.stringify(deleteData);

                // Show confirmation and submit
                let transactionMode = document.getElementById("transactionmode").value;
                let message = transactionMode === "U" 
                    ? "Record updated successfully!" 
                    : "Record added successfully!";
                let title = transactionMode === "U" 
                    ? "Update Successful!" 
                    : "Save Successful!";

                Swal.fire({
                    title: title,
                    text: message,
                    icon: "success",
                    showCancelButton: false,
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }, 200);
        });
});
</script>    
<script>
let generatedDetailsData = []; 
document.getElementById("generate").addEventListener("click", function () {
    const gridContainer = document.getElementById("generatedInvoiceGrid");
    const tableBody = document.getElementById("generatedInvoiceTableBody");
    const customer = document.getElementById('customer') ? document.getElementById('customer').value : '';
    const invoiceFor = document.getElementById("invoice_for").value;
    const invoiceType = document.querySelector('input[name="invoice_type"]:checked') ? document.querySelector('input[name="invoice_type"]:checked').value : '';
    const lotNoCheckboxes = document.querySelectorAll('input[name="lot_no[]"]:checked');
    const lotNos = Array.from(lotNoCheckboxes).map(cb => cb.value);
 
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
            lot_no: lotNos,
            customer: customer,
            status: invoiceFor,
            invoice_type: invoiceType
        },
        dataType: "json",
        success: function (invoiceData) {
            tableBody.innerHTML = "";
            generatedDetailsData = [];
            let totalAmount = 0;
 
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
                        <td>${data.invoice_for ?? ''}</td>
                        <td>${data.invoice_day ?? ''}</td>
                        <td>${data.amount ?? ''}</td>
                        <td>${data.status ?? ''}</td>
                    `;
                    tableBody.appendChild(row);
 
                    // Remove commas from amount and parse as float
                    if (data.amount) {
                        let amt = parseFloat(data.amount.replace(/,/g, ''));
                        if (!isNaN(amt)) {
                            totalAmount += amt;
                        }
                    }
 
                    generatedDetailsData.push({
                        inward_no: data.in_no ?? '',
                        inward_date: data.inward_date_db ?? '',
                        lot_no: data.lot_no ?? '',
                        item: data.item_id ?? '',
                        marko: data.marko ?? '',
                        invoice_qty: data.qty ?? '',
                        unit_name: data.unit_id ?? '',
                        wt_per_kg: data.weight ?? '',
                        storage_duration: data.storage_duration_id ?? '',
                        rent_per_storage_duration: data.rent_per_storage_duration ?? '',
                        rent_per: data.rent_per_id ?? '',
                        outward_date: data.outward_date_db ?? '',
                        charges_from: data.charges_from_db ?? '',
                        charges_to: data.charges_to_db ?? '',
                        actual_month: data.act_month ?? '',
                        actual_day: data.act_day ?? '',
                        invoice_for: data.invoice_for ?? '',
                        invoice_day: data.invoice_day ?? '',
                        invoice_amount: data.amount ?? '',
                        status: data.status ?? '',
                        gst_status: data.gst_status ?? '',
                        detailtransactionmode: 'I'
                    });
                });
            }
            gridContainer.style.display = "block";
            let basicAmountInput = document.getElementById("basic_amount");
            if (basicAmountInput) {
                basicAmountInput.value = totalAmount.toFixed(2);
            }
            recalculateInvoiceAmounts();
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
function sumManualGridAmounts() {
    let total = 0;
    document.querySelectorAll('#searchDetail tbody tr:not(.norecords) td[data-label="amount"]').forEach(function(td) {
        let amt = parseFloat((td.textContent || '').replace(/,/g, ''));
        if (!isNaN(amt)) total += amt;
    });
    return total;
}
function updateBasicAmountFromManualGrid() {
    let total = sumManualGridAmounts();
    let basicAmountInput = document.getElementById("basic_amount");
    if (basicAmountInput) {
        basicAmountInput.value = total.toFixed(2);
    }
    recalculateInvoiceAmounts();
}
 
function getFloat(id) {
    var v = document.getElementById(id);
    return v && v.value ? parseFloat(v.value) || 0 : 0;
}
    function getTextareaFloatByName(name) {
    let el = document.getElementsByName(name)[0];
    if (el && el.value) {
        let val = parseFloat(el.value.trim());
        return isNaN(val) ? 0 : val;
    }
    return 0;
}
 
 
function recalculateInvoiceAmounts() {
    let gridTotal = getFloat("basic_amount");
    let otherExpense = getFloat("other_expense3");
    let otherExpenseSign = getTextareaFloatByName("other_expense3_sign");
 
    let newBasic = gridTotal + otherExpense + otherExpenseSign;
 
    let basicInput = document.getElementById("basic_amount");
    if (basicInput) {
        basicInput.value = newBasic.toFixed(2);
    }
 
    let sgstAmtInput = document.getElementById("sgst_amt");
    let cgstAmtInput = document.getElementById("cgst_amt");
    let igstAmtInput = document.getElementById("igst_amt");
 
    if (sgstAmtInput) sgstAmtInput.value = "0.00";
    if (cgstAmtInput) cgstAmtInput.value = "0.00";
    if (igstAmtInput) igstAmtInput.value = "0.00";
 
    let taxType = "";
    let taxRadio = document.querySelector('input[name="tax_amount"]:checked');
    if (taxRadio) {
        taxType = taxRadio.value;
    }
 
    let sgst = 0, cgst = 0, igst = 0;
    if (taxType == "2" && sgstAmtInput && cgstAmtInput) {
        sgst = newBasic * 0.09;
        cgst = newBasic * 0.09;
        sgstAmtInput.value = sgst.toFixed(2);
        cgstAmtInput.value = cgst.toFixed(2);
    } else if (taxType == "3" && igstAmtInput) {
        igst = newBasic * 0.18;
        igstAmtInput.value = igst.toFixed(2);
    } else if (taxType == "1" && cgstAmtInput) {
        cgst = newBasic * 0.12;
        cgstAmtInput.value = cgst.toFixed(2);
    }
 
    let netAmount = newBasic + sgst + cgst + igst;
    let netAmountInput = document.getElementById("net_amount");
    if (netAmountInput) {
        netAmountInput.value = netAmount.toFixed(2);
    }
}
 
let otherExpenseInput = document.getElementById("other_expense3");
if (otherExpenseInput) {
    otherExpenseInput.addEventListener("input", recalculateInvoiceAmounts);
}
 
let otherExpenseSignInput = document.getElementsByName("other_expense3_sign")[0];
if (otherExpenseSignInput) {
    otherExpenseSignInput.addEventListener("input", recalculateInvoiceAmounts);
}
document.querySelectorAll('input[name="tax_amount"]').forEach(function (radio) {
    radio.addEventListener("change", recalculateInvoiceAmounts);
});
window.addEventListener("DOMContentLoaded", recalculateInvoiceAmounts);
</script>