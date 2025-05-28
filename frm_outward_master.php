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
    </script>
<?php
    include("include/footer_close.php");
?>