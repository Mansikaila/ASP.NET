<?php
include("classes/cls_company_year_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");

$transactionmode = $_REQUEST["transactionmode"] ?? "";
$label = ($transactionmode == "U") ? "Update" : "Add";

$query = "
    SELECT company_year_id, CONCAT(YEAR(start_date), '-', YEAR(end_date)) AS year_range FROM tbl_company_year_master";
    $stmt = $_dbh->prepare($query);
    $stmt->execute();
    $yearRanges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['companyYear'])) {
        $selectedYearId = $_POST['companyYear'];
        $stmt = $_dbh->prepare("SELECT CONCAT(YEAR(start_date), '-', YEAR(end_date)) AS year_range FROM tbl_company_year_master WHERE company_year_id = ?");
        $stmt->execute([$selectedYearId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $_SESSION['sess_company_year_id'] = $selectedYearId;
            $_SESSION['sess_selected_year'] = 'FY ' . $result['year_range'];
        }
        header("Location: dashboard.php");
        exit;
    }
    if (empty($_SESSION['sess_company_year_id'])) {
        $today = date('Y-m-d');
        $stmt = $_dbh->prepare("SELECT company_year_id, CONCAT(YEAR(start_date), '-', YEAR(end_date)) AS year_range FROM tbl_company_year_master WHERE ? BETWEEN start_date AND end_date LIMIT 1");
        $stmt->execute([$today]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $_SESSION['sess_company_year_id'] = $row['company_year_id'];
            $_SESSION['sess_selected_year'] = 'FY ' . $row['year_range'];
        }
    }
$currentYearId = $_SESSION['sess_company_year_id'] ?? '';
?>
<body class="hold-transition skin-blue layout-top-nav">
<?php include("include/body_open.php"); ?>
<div class="wrapper">
    <?php include("include/navigation.php"); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <section class="content-header">
                <h1>Switch Year</h1>
            </section>

            <section class="content">
    <div class="col-md-12" style="padding:0;">
        <div class="box box-info">
            <!-- form start -->
            <form action="" method="post" class="form-horizontal needs-validation" novalidate>
                <div class="box-body">
                    <div class="form-group row gy-2">
                        <div class="row mb-3 align-items-center">
                            <label for="companyYear" class="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label">Select Switch Year*</label>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2">
                                 <select name="companyYear" id="companyYear" class="form-select" required>
                                    <option value="">-- Select Year --</option>
                                    <?php foreach ($yearRanges as $yearRange): ?>
                                        <option value="<?php echo $yearRange['company_year_id']; ?>"
                                            <?php echo ($currentYearId == $yearRange['company_year_id']) ? 'selected' : ''; ?>>
                                            <?php echo $yearRange['year_range']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a year</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->

                <!-- .box-footer -->
                <div class="box-footer">
                    <input type="submit" class="btn btn-success" value="Update">
                    <input type="button" class="btn btn-dark" value="Cancel" onclick="window.history.back();">
                    <input type="hidden" id="company_id" name="company_id" value="<?php echo COMPANY_ID; ?>">
                </div>
                <!-- /.box-footer -->
            </form>
            <!-- form end -->
        </div>
    </div>
</section>

        </div>
    </div>
</div>
<?php include("include/footer.php"); ?>
<?php include("include/footer_includes.php"); ?>
<?php include("include/footer_close.php"); ?>
