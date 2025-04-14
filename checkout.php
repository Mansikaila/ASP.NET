<?php
include '../includes/header.php';
if (!isset($_SESSION['username'])) {
    echo "<script>window.location.href='/theshoesbox/pages/login.php';</script>";
}
include '../includes/connection.php';

$user_id = $_SESSION['user_id'];
$pro_id = $_GET['pro_id'];

// Fetch Address
$sql = "SELECT * FROM addressdetails WHERE user_id=$user_id AND pro_id=$pro_id";
$data = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($data);
$address_id = $row['id'];

// Fetch Product Details
$sql = "SELECT brand.name AS bname, product.id, product.name, product.price, product.pro_img 
        FROM brand, product 
        WHERE brand.id = product.brand_id AND product.id = '$pro_id'";
$product_data = mysqli_query($con, $sql);
$product = mysqli_fetch_assoc($product_data);
$price = $product['price'];
?>

<section class="ftco-section contact-section bg-light">
    <div class="container">
        <h3 class="mb-4 billing-heading">Order</h3>
        <div class="row">
            <!-- Product Details -->
            <div class="col-md-6 mb-5">
                <div class="card shadow">
                    <div class="bg-white p-5 contact-form">
                        <h3 class="billing-heading mb-4 text-center">Product Details</h3>
                        <div class="row">
                            <div class="col-lg-4 mb-3 ftco-animate">
                                <a href="/theshoesbox/assets/images/product-1.png" class="image-popup">
                                    <img src="/theshoesbox/admin/assets/images/product/<?php echo $product['pro_img'] ?>" 
                                         height="200px" width="200px" class="img-fluid">
                                </a>
                            </div>
                            <div class="col-lg-7 product-details pl-md-3 ftco-animate">
                                <h3><?php echo $product['name'] ?></h3>
                                <p><strong>Brand:</strong> <?php echo $product['bname'] ?></p>
                                <p class="price"><span>₹ <?php echo $price ?></span></p>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <span>Product Size</span>
                                        <div class="form-group d-flex">
                                            <div class="select-wrap">
                                                <select name="size" id="size" class="form-control">
                                                    <?php for ($i = 4; $i <= 10; $i++) : ?>
                                                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="col-md-6 mb-5">
                <div class="card shadow">
                    <div class="info bg-white p-4">
                        <div class="cart-detail cart-total bg-light p-3 p-md-4">
                            <h3 class="billing-heading mb-4 text-center">Total Payment</h3>
                            <p class="d-flex">
                                <span>Price</span>
                                <span class="price" id="price">₹ <?php echo $price ?></span>
                            </p>
                            <div class="d-flex">
                                <span>Quantity</span>
                                <div class="quantity">
                                    <div class="input-group mb-3">
                                        <button class="quantity-minus px-2 border-0" type="button">-</button>
                                        <input type="number" name="quantity" id="quantity" 
                                               class="quantity form-control input-number" 
                                               value="1" min="1" max="100" oninput="updateCartTotal();">
                                        <button class="quantity-plus px-2 border-0" type="button">+</button>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <p class="d-flex">
                                <span>Total Price</span>
                                <span class="total" id="totalprice">₹ <?php echo $price ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="col-md-5 mb-5">
                <div class="card shadow">
                    <div class="info bg-white p-4 text-center">
                        <div class="cart-detail bg-light p-3 p-md-4">
                            <h3 class="billing-heading mb-4">Payment Method</h3>
                            <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
                                <input type="hidden" name="business" mailto:value="sb-23lya34085897@business.example.com">
                                <input type="hidden" name="cmd" value="_xclick">
                                <input type="hidden" name="item_name" value="<?php echo $product['name']; ?>">
                                <input type="hidden" name="amount" id="paypal-amount" value="<?php echo $price; ?>">
                                <input type="hidden" name="currency_code" value="USD">
                                <input type="hidden" name="return" value="http://localhost/theshoesbox/pages/success.php?status=success">
                                <input type="hidden" name="cancel_return" value="http://localhost/theshoesbox/pages/cancel.php?status=cancel">
                                <input type="image" name="submit" border="0" src="../assets/images/pp.png" width="180">
                            </form>
                            <p>
                                <!-- <button class="btn btn-primary py-3 px-5 mt-3" id="place-order">Place an Order</button> -->
                                <button class="btn btn-secondary py-3 px-5 mt-3" id="cod-order">Cash on Delivery</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
function updateCartTotal() {
    const price = parseFloat(document.getElementById('price').innerText.replace('₹', '').trim());
    const quantity = parseInt(document.getElementById('quantity').value, 10) || 1;
    const total = price * quantity;
    document.getElementById('totalprice').innerText = '₹ ' + total.toFixed(2);
    document.getElementById('paypal-amount').value = total.toFixed(2); // Update PayPal amount
}

document.querySelector('.quantity-minus').addEventListener('click', function () {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value, 10) || 1;
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
        updateCartTotal();
    }
});

document.querySelector('.quantity-plus').addEventListener('click', function () {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value, 10) || 1;
    if (currentValue < 100) {
        quantityInput.value = currentValue + 1;
        updateCartTotal();
    }
});

// Handle Cash on Delivery
document.getElementById('cod-order').addEventListener('click', function () {
    const size = document.getElementById('size').value;
    const quantity = document.getElementById('quantity').value;
    const totalprice = parseFloat(document.getElementById('totalprice').innerText.replace('₹', '').trim());

    // Send the order data to the server using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cod_order.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert('Order placed successfully!');
            window.location.href = '/theshoesbox/pages/order_success.php';
        }
    };
    xhr.send('user_id=<?php echo $user_id; ?>&product_id=<?php echo $pro_id; ?>&address_id=<?php echo $address_id; ?>&rate=<?php echo $price; ?>&pro_size=' + size + '&quantity=' + quantity + '&totalprice=' + totalprice + '&status=Confirmed');
});
</script>