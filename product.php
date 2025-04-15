<?php
include '../includes/header.php';
include '../includes/connection.php';
?>
<?php


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Handle the case when the user is not logged in
    echo "User is not logged in!";
    exit;  // Exit the script if user is not logged in
}
?>

<style>
        
.img-prod img {
    height: 200px; / Set the desired height /
    width: 100%;   / Make it fit the container width /
    object-fit: cover; / Ensures the image covers the area while maintaining aspect ratio /
    border-radius: 5px; / Optional: Adds rounded corners /
}

</style>
<section class="ftco-section bg-light">
	<div id="cart-notification" class="position- top-0 end-0 p-3" style="z-index: 5;"></div>

	<div class="container">
		<div class="row">
			<?php
			$sql = "SELECT brand.name as bname, product.id, product.name, product.price, product.pro_img, 
						   IFNULL(ROUND(AVG(ratings.rating), 1), 0) as avg_rating
					FROM brand 
					JOIN product ON brand.id = product.brand_id
					LEFT JOIN ratings ON product.id = ratings.product_id
					GROUP BY product.id";

			$result = $con->query($sql);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$productId = $row['id'];
					$avgRating = round($row['avg_rating']);
					echo '<div class="col-sm-12 col-md-6 col-lg-3 ftco-animate d-flex">';
					echo '  <div class="product d-flex flex-column">';
					echo '      <a href="/theshoesbox/pages/product-single.php?pro_id=' . $productId . '" class="img-prod"><img class="img-fluid" src="/theshoesbox/admin/assets/images/product/' . $row['pro_img'] . '" alt="Product Image">';
					echo '          <div class="overlay"></div>';
					echo '      </a>';
					echo '      <div class="text py-3 pb-4 px-3">';
					echo '          <div class="d-flex">';
					echo '              <div class="cat"><span>' . $row["bname"] . '</span></div>';
					echo '              <div class="rating" data-productid="' . $productId . '">';
					
					// Render stars dynamically based on average rating
					for ($i = 1; $i <= 5; $i++) {
						if ($i <= $avgRating) {
							echo '<span class="ion-ios-star star" data-rating="' . $i . '"></span>';
						} else {
							echo '<span class="ion-ios-star-outline star" data-rating="' . $i . '"></span>';
						}
					}
					
					echo '              </div>';
					echo '          </div>';
					echo '          <h3><a href="/theshoesbox/pages/product-single.php?pro_id=' . $productId . '">' . $row['name'] . '</a></h3>';
					echo '          <div class="pricing">';
					echo '              <p class="price"><span>' . $row['price'] . '</span></p>';
					echo '          </div>';
					echo '          <p class="bottom-area d-flex px-3">';
					echo '              <a href="#" class="add-to-cart text-center py-2 mr-1" data-productId="' . $productId . '" data-userId="' . $user_id . '"><span>Add to cart <i class="ion-ios-add ml-1"></i></span></a>';
					echo '              <a href="/theshoesbox/pages/address-details.php?pro_id=' . $productId . '" class="buy-now text-center py-2">Buy now<span><i class="ion-ios-cart ml-1"></i></span></a>';
					echo '          </p>';
					echo '      </div>';
					echo '  </div>';
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
include '../includes/footer.php';
?>

<script>
	// Handle Star Click for Ratings
	$(document).on('click', '.star', function() {
		const productId = $(this).closest('.rating').data('productid');
		const rating = $(this).data('rating');
		const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;

		if (!userId) {
			swal({
				title: "Oops!",
				text: "You need to log in to rate a product.",
				icon: "error"
			});
			return;
		}

		$.ajax({
			type: "POST",
			url: "/theshoesbox/processes/rating-process.php",
			data: { product_id: productId, rating: rating, user_id: userId },
			success: function(response) {
				if (response === "Rating submitted successfully.") {
					swal({
						title: "Thank You!",
						text: "Your rating has been recorded.",
						icon: "success"
					});
				} else {
					swal("Oops!", response, "error");
				}
			},
			error: function() {
				swal("Error!", "Unable to submit rating. Please try again.", "error");
			}
		});
	});
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
