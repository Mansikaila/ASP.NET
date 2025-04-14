<?php
include '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $address_id = $_POST['address_id'];
    $rate = $_POST['rate'];
    $pro_size = $_POST['pro_size'];
    $quantity = $_POST['quantity'];
    $totalprice = $_POST['totalprice'];
    $status = $_POST['status'];

    // Insert order into the database
    $sql = "INSERT INTO `order` (`user_id`, `product_id`, `address_id`, `rate`, `pro_size`, `quantity`, `totalprice`, `status`) 
            VALUES ('$user_id', '$product_id', '$address_id', '$rate', '$pro_size', '$quantity', '$totalprice', '$status')";

    if (mysqli_query($con, $sql)) {
        echo 'Order placed successfully!';
    } else {
        echo 'Error: ' . mysqli_error($con);
    }
}
?>