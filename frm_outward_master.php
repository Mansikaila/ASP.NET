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
                <?php
                    echo $_bll->getForm($transactionmode);
                ?>
            <!-- .box-footer -->
              <div class="box-footer">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                                        <input type="hidden" id="deleted_records" name="deleted_records" />
                    <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_outward_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="document.getElementById('masterForm').reset();" >
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
            $table_name_detail="tbl_outward_detail";
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
                                             if($fields_names[$i]!="outward_id") {
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
<!--
                <input class="btn btn-success" type="submit" id="detailbtn_add" name="detailbtn_add" value= "Save">
                <input class="btn btn-dark" type="button" id="detailbtn_cancel" name="detailbtn_add" value= "Cancel" data-bs-dismiss="modal">
-->
              </div>
                </form>
            </div> <!-- /.modal-content -->
          </div>  <!-- /.modal-dialog -->
        </div> <!-- /.modal -->
    </div>
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
        <td>${data.variety}</td>
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

    // 1. Check for validation errors
    checkedRows.forEach(cb => {
        const tr = cb.closest('tr');
        const outQtyCell = tr.querySelector('.out-qty-cell');
        const stockQtyCell = tr.querySelector('[data-label="Stock Qty"]');
        let outQty = parseFloat(outQtyCell?.textContent || "0");
        let stockQty = parseFloat(stockQtyCell?.textContent || "0");

        // Out Qty not filled or zero
        if (!outQty || outQty <= 0) {
            if (!foundError) {
                showCustomMessagePopup("Please enter Outward Qty", outQtyCell);
                foundError = true;
                firstErrorCell = outQtyCell;
            }
            return;
        }
        // Out Qty > Stock Qty
        if (outQty > stockQty) {
            if (!foundError) {
                showStockNotAvailablePopup(outQtyCell);
                foundError = true;
                firstErrorCell = outQtyCell;
            }
            return;
        }
    });

    // If any validation error, do NOT close modal
    if (foundError) return;

    // 2. If nothing checked at all, show error
    if (checkedRows.length === 0) {
        showCustomMessagePopup("Please select at least one record and enter Outward Qty", null);
        return;
    }

    // 3. All validations passed - proceed
    checkedRows.forEach(cb => {
        const tr = cb.closest('tr');
        const record = {
            inward_detail_id: tr.getAttribute('data-inward-detail-id') ?? 0,
            inward_no: tr.querySelector('[data-label="Inward No."]').textContent.trim(),
            lot_no: tr.querySelector('[data-label="Lot No."]').textContent.trim(),
            inward_date: tr.querySelector('[data-label="Inward Date"]').textContent.trim(),
            item: tr.querySelector('[data-label="Item"]').textContent.trim(),
            variety: (tr.querySelector('[data-label="Variety"]') || tr.querySelector('[data-label="variety"]')).textContent.trim(),
            stock_qty: tr.querySelector('[data-label="Stock Qty"]').textContent.trim(),
            out_qty: tr.querySelector('.out-qty-cell')?.textContent.trim() || "0",
            unit: tr.querySelector('[data-label="Unit"]').textContent.trim(),
            out_wt: tr.querySelector('.out-wt-cell')?.textContent.trim() || "0",
            loading_charges: tr.querySelector('.loading-charge-cell')?.textContent.trim() || "0",
            location: tr.querySelector('[data-label="Location"]').textContent.trim(),
            detailtransactionmode: 'I'
        };

        if(!jsonData.some(r => r.inward_detail_id == record.inward_detail_id)) {
            jsonData.push(record);
        }
    });

    // Re-render or append to the main detail grid
    tableBody.innerHTML = "";
    jsonData.forEach((data, idx) => appendTableRow(data, idx));

    // Close the modal
    bootstrap.Modal.getInstance(document.getElementById('pendingInwardModal')).hide();

    // --- Popup helpers ---
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
     document.getElementById('customer').disabled = true;
});
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

                // Key for edited values
                const uniqueKey = (row.inward_id ?? 0) + '_' + (row.inward_detail_id ?? 0);
                const edited = editedInwardData[uniqueKey];

                // Default values from data
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
                    <td data-label="Variety">${row.variety ?? 'N/A'}</td>
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
        } else {
            outQtyCell.removeAttribute('contenteditable');
            loadingChargeCell.removeAttribute('contenteditable');
        }
        // Save checkbox checked state
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
    } else if (e.target.classList.contains('loading-charge-cell')) {
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
<?php
    include("include/footer_close.php");
?>