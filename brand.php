<?php
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/connection.php';
?>

<div class="row page-titles mx-0">
    <h3 class="my-2 ml-1">Brand</h3>
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/theshoesbox/admin/pages/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="/theshoesbox/admin/pages/brand.php">Brand</a></li>
        </ol>
    </div>
</div>
<!-- row -->

<!-- Modal -->
<div class="bootstrap-modal">
    <!-- Update Brand -->
    <div class="modal fade" id="update">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Brand</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
          
<div class="modal-body">

    <div class="form-group d-none">
        <label class="card-title">Selected Id</label>
        <input type="text" class="form-control input-default" id="editid" disabled>
    </div>

    <input type="hidden" name="id" id="hiddenid">

    <!-- Brand Name -->
    <h4 class="card-title">Brand Name</h4>
    <div class="form-group">
        <input type="text" class="form-control input-default" name="name" id="editname">
    </div>
</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="savechange">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Brand -->
    <div class="modal fade" id="add">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Brand</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="card-title">Brand Name</h4>
                    <div class="form-group">
                        <input type="text" class="form-control input-default" placeholder="Enter Brand Name" name="name" id="name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="addbrand" name="addbrand">Upload</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Brand -->
    <div class="modal fade" id="view">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Brand</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong class="text-dark mr-4">
                            <label class="col-lg-4">Brand Id</label>
                        </strong>
                        <label type="label" name="id" id="viewid"></label>
                    </div>
                    <div class="form-group">
                        <strong class="text-dark mr-4">
                            <label class="col-lg-4">Brand Name</label>
                        </strong>
                        <label name="name" id="viewname"></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <button type='button' class='btn btn-success float-right mr-4' data-toggle='modal' data-target='#add'>Add Brand</button>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered zero-configuration">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM brand";
                            $data = mysqli_query($con, $sql);

                            while ($row = mysqli_fetch_assoc($data)) {
                                echo "<tr>
                                    <td>" . $row['id'] . "</td>
                                    <td>" . $row['name'] . "</td>
                                    <td>
                                        <button type='button' class='btn btn-warning edit-button' data-toggle='modal' data-target='#update' data-id='" . $row['id'] . "' data-name='" . $row['name'] . "'>Edit</button>
                                        <button type='button' class='btn btn-primary view-button' data-toggle='modal' data-target='#view' data-id='" . $row['id'] . "' data-name='" . $row['name'] . "'>View</button>
                                        <button type='submit' class='btn btn-danger delete-button' data-id='" . $row['id'] . "'>Delete</button>
                                    </td>
                                    </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    include '../includes/footer.php';
    ?>

    <script>
        
        // Add Brand
        $("#addbrand").on("click", function() {
            var name = $("#name").val();
            $.ajax({
                type: "POST",
                url: "/theshoesbox/admin/processes/brand-process.php",
                data: {
                    action: "insert",
                    name: name,
                },
                success: function(res) {
                    if (res == "Brand Added Successfully") {
                        swal({
                            title: "Success",
                            text: res,
                            type: "success"
                        }, function() {
                            window.location = '/theshoesbox/admin/pages/brand.php';
                        });
                    } else if (res == "Looks like you missed some fields. Please check and try again!") {
                        swal("Oops!!", res, "error");
                    } else if (res == "Brand already taken. Please choose a different Brand.") {
                        swal("Oops!!", res, "error");
                    } else {
                        swal("Oops!!", res, "error");
                    }
                }
            });
        });

        // Edit Brand Model
   $('.edit-button').on("click", function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $('#editid').val(id);        // Optional reference (in d-none)
    $('#hiddenid').val(id);      // Actual value for form submission
    $('#editname').val(name);
});


        // View Brand Model
        $('.view-button').on("click", function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#viewid').html(id);
            $('#viewname').html(name);
        });

        // Delete Brand
        $('.delete-button').on("click", function() {
            var id = $(this).data('id');
            swal({
                    title: "Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function() {
                    $.ajax({
                        type: "POST",
                        url: "/theshoesbox/admin/processes/brand-process.php",
                        data: {
                            action: "delete",
                            id: id,
                        },
                        success: function(res) {
                            if (res == "Brand Delete Successfully") {
                                swal({
                                    title: "Success",
                                    text: res,
                                    type: "success"
                                }, function() {
                                    window.location = '/theshoesbox/admin/pages/brand.php';
                                });
                            } else {
                                swal("Oops!!", res, "error");
                            }
                        }
                    });
                });
        });

        // Update Brand
        $("#savechange").on("click", function() {
    var id = $("#editid").val();
    var name = $("#editname").val();
    $.ajax({
        type: "POST",
        url: "/theshoesbox/admin/processes/brand-process.php",
        data: {
            action: "update",
            id: id,
            name: name,
        },
        success: function(res) {
            console.log(res);  // Log the response to check what's coming back
            if (res.trim() == "Brand Update Successfully") {
                swal({
                    title: "Success",
                    text: res,
                    icon: "success"
                }).then(function() {
                    window.location = '/theshoesbox/admin/pages/brand.php';
                });
            } else {
                // Handle all other errors
                swal("Oops!!", res, "error");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: ", status, error);  // Log the error if the request fails
        }
    });
});


    $('#add').on('shown.bs.modal', function() {
    $('#name').trigger('focus');  // Focus and select the input
});

// Focus on Brand Name in the Edit Brand modal
$('#update').on('shown.bs.modal', function() {
    $('#editname').trigger('focus');  // Focus and select the input
});
    </script>