<?php
include './includes/header.php';
include './includes/connection.php';
?>
<!--
<?php
		$user_id = $_SESSION['user_id'];
?> 
-->
<style>
            
.img-prod img {
    height: 200px; 
    width: 100%;
    object-fit: cover;
    border-radius: 5px;
}
</style>
<section id="home-section" class="hero">
	<div class="home-slider owl-carousel">
		<div class="slider-item js-fullheight">
			<div class="overlay"></div>
			<div class="container-fluid p-0">
				<div class="row d-md-flex no-gutters slider-text align-items-center justify-content-end" data-scrollax-parent="true">
					<img class="one-third order-md-last img-fluid" src="/theshoesbox/assets/images/2.png" alt="">
					<div class="one-forth d-flex align-items-center ftco-animate" data-scrollax=" properties: { translateY: '70%' }">
						<div class="text">
							<span class="subheading">#New Arrival</span>
							<div class="horizontal">
								<h1 class="mb-4 mt-3">Shoes Collection 2023</h1>
								<p class="mb-4">To wear dreams on one"s feet is to begin to give a reality to one's
									dreams.</p>

								<p><a href="/theshoesbox/pages/product.php" class="btn-custom">Discover Now</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="slider-item js-fullheight">
			<div class="overlay"></div>
			<div class="container-fluid p-0">
				<div class="row d-flex no-gutters slider-text align-items-center justify-content-end" data-scrollax-parent="true">
					<img class="one-third order-md-last img-fluid" src="/theshoesbox/assets/images/bg_2.png" alt="">
					<div class="one-forth d-flex align-items-center ftco-animate" data-scrollax=" properties: { translateY: '70%' }">
						<div class="text">
							<span class="subheading">#New Arrival</span>
							<div class="horizontal">
								<h1 class="mb-4 mt-3">New Shoes Collection</h1>
								<p class="mb-4">Shoes transform your body language and attitude. They lift you
									physically and emotionally.</p>

								<p><a href="/theshoesbox/pages/product.php" class="btn-custom">Discover Now</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="ftco-section ftco-no-pt ftco-no-pb">
	<div class="container">
		<div class="row no-gutters ftco-services">
			<div class="col-lg-4 text-center d-flex align-self-stretch ftco-animate">
				<div class="media block-6 services p-4 py-md-5">
					<div class="icon d-flex justify-content-center align-items-center mb-4">
						<span class="flaticon-bag"></span>
					</div>
					<div class="media-body">
						<h3 class="heading">Free Shipping</h3>
						<p>Unleash shopping freedom with our e-commerce website's irresistible offer: Free shipping on every order</p>
					</div>
				</div>
			</div>
			<div class="col-lg-4 text-center d-flex align-self-stretch ftco-animate">
				<div class="media block-6 services p-4 py-md-5">
					<div class="icon d-flex justify-content-center align-items-center mb-4">
						<span class="flaticon-customer-service"></span>
					</div>
					<div class="media-body">
						<h3 class="heading">Support Customer</h3>
						<p>Customer support teams successfully assist customers with questions or problems by 24/7.</p>
					</div>
				</div>
			</div>
			<div class="col-lg-4 text-center d-flex align-self-stretch ftco-animate">
				<div class="media block-6 services p-4 py-md-5">
					<div class="icon d-flex justify-content-center align-items-center mb-4">
						<span class="flaticon-payment-security"></span>
					</div>
					<div class="media-body">
						<h3 class="heading">Secure Payments</h3>
						<p>We provide encryption to keep your customer ’s information secure.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="ftco-section bg-light">
	<div class="container">
		<div class="row justify-content-center mb-3 pb-3">
			<div class="col-md-12 heading-section text-center ftco-animate">
				<h2 class="mb-4">New Shoes Arrival</h2>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php
			$sql = "SELECT brand.name as bname, product.id, product.name, product.price, product.pro_img 
                FROM brand, product 
                WHERE brand.id = product.brand_id 
                ORDER BY product.id DESC 
                LIMIT 8";
            if (isset($_POST['catId'])) {
                $sql = "SELECT brand.name as bname, product.id, product.name, product.price, product.pro_img 
                FROM brand, product 
                WHERE brand.id = product.brand_id AND product.cat_id = " . $_POST['catId'] . " 
                ORDER BY product.id DESC 
                LIMIT 8"; 
            }
			$result = $con->query($sql);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					// Fetch the average rating for the product
					$rating_sql = "SELECT AVG(rating) as avg_rating FROM ratings WHERE product_id = " . $row['id'];
					$rating_result = $con->query($rating_sql);
					$rating_row = $rating_result->fetch_assoc();
					$avg_rating = round($rating_row['avg_rating']); // Round the rating to the nearest integer

					// Display the stars based on the average rating
					echo '<div class="col-sm-12 col-md-6 col-lg-3 ftco-animate d-flex">';
					echo '	<div class="product d-flex flex-column">';
					echo '		<a href="/theshoesbox/pages/product-single.php?pro_id=' . $row['id'] . '" class="img-prod"><img class="img-fluid" src="/theshoesbox/admin/assets/images/product/' . $row['pro_img'] . '" alt="Product Image">';
					echo '			<div class="overlay"></div>';
					echo '		</a>';
					echo '		<div class="text py-3 pb-4 px-3">';
					echo '			<div class="d-flex">';
					echo '				<div class="cat">';
					echo '					<span>' . $row["bname"] . '</span>';
					echo '				</div>';
					echo '				<div class="rating">';
					echo '					<p class="text-right mb-0">';
					
					// Display the stars for the rating
					for ($i = 1; $i <= 5; $i++) {
						if ($i <= $avg_rating) {
							echo '<span class="ion-ios-star"></span>';
						} else {
							echo '<span class="ion-ios-star-outline"></span>';
						}
					}
					
					echo '					</p>';
					echo '				</div>';
					echo '			</div>';
					echo '			<h3><a href="/theshoesbox/pages/product-single.php?product_id=' . $row['id'] . '">' . $row['name'] . '</a></h3>';
					echo '			<div class="pricing">';
					echo '				<p class="price"><span>' . $row['price'] . '</span></p>';
					echo '			</div>';
					echo '			<p class="bottom-area d-flex px-3">';
					echo '				<a href="#" class="add-to-cart text-center py-2 mr-1" data-productId="' . $row['id'] . '" data-userId="' . $user_id . '"><span>Add to cart <i class="ion-ios-add ml-1"></i></span></a>';
					echo '				<a href="/theshoesbox/pages/address-details.php?pro_id=' . $row['id'] . '" class="buy-now text-center py-2">Buy now<span><i class="ion-ios-cart ml-1"></i></span></a>';
					echo '			</p>';
					echo '		</div>';
					echo '	</div>';
					echo '</div>';
				}
			} else {
				echo "Product Not Found";
			}
			?>
		</div>
	</div>
</section>
<?php
include './includes/footer.php';
?>
<script>
	// Add Cart
	$(".add-to-cart").on("click", function() {
		var productId = $(this).data('productid');
		var userId = $(this).data('userid');
		$.ajax({
			type: "POST",
			url: "/theshoesbox/processes/cart-process.php",
			data: {
				action: "insert",
				productId: productId,
				userId: userId,
			},
			success: function(res) {
				if (res == "Product Added Successfully") {
					swal({
						title: "Success",
						text: res,
						type: "success"
					}, function() {
						window.location = '/theshoesbox/pages/cart.php';
					});
				} else if (res == "Product already In Cart. Please choose a different Product.") {
					swal("Oops!!", res, "error");
				} else if (res == "You are not loged in!") {
					swal({
						title: "Oops!!",
						text: res,
						type: "error"
					}, function() {
						window.location = '/theshoesbox/pages/login.php';
					});
				} else {
					swal("Oops!!", res, "error");
				}
			}
		});
	});
</script>
