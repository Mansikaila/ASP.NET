<?php
include '../includes/connection.php';

$action = $_POST['action'];

// Add Brand
if ($action == "insert") {
    $name = $_POST['name'];

    if ($name == '') {
        echo "Looks like you missed some fields. Please check and try again!";
        return;
    }
    $query = "SELECT * FROM brand WHERE name = '$name'";
    $result = $con->query($query);
    if ($result->num_rows > 0) {
        echo "Brand already taken. Please choose a different Brand.";
        return;
    }

    $sql = "INSERT INTO brand(name) VALUES('$name')";
    if (mysqli_query($con, $sql)) {
        echo "Brand Added Successfully";
        return;
    } else {
        echo "Somthing Went's Wrong. Please Try Again.";
        return;
    }
}

// Update Brand
elseif ($action == "update") {
    $id = $_POST['id'];
    $name = $_POST['name'];

    if ($name == '') {
        echo "Looks like you missed some fields. Please check and try again!";
        return;
    }

    $query = "SELECT * FROM brand WHERE name = '$name'";
    $result = $con->query($query);
    if ($result->num_rows > 0) {
        echo "Brand already taken. Please choose a different Brand.";
        return;
    }

    $sql = "UPDATE brand SET name='$name' WHERE id=$id";
    if (mysqli_query($con, $sql)) {
        echo "Brand Update Successfully";
        return;
    } else {
        echo "Somthing Went's Wrong. Please Try Again.";
        return;
    }
}

// Delete Brand
elseif ($action == "delete") {
    $id = $_POST['id'];

    $sql = "DELETE FROM brand WHERE id=$id";
    if (mysqli_query($con, $sql)) {
        echo "Brand Delete Successfully";
        return;
    } else {
        echo "Somthing Went's Wrong. Please Try Again.";
        return;
    }
}
// Any Else
else {
    echo "Somthing Went's Wrong. Please Reload The Page And Try Again.";
}
mysqli_close($con);
