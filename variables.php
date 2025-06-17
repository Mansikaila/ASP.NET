<?php

    /* DB TABLES */
    $tbl_bank_master="tbl_bank_master";
    $tbl_chamber_master="tbl_chamber_master";
    $tbl_city_master="tbl_city_master";
    $tbl_country_master="tbl_country_master";
    $tbl_state_master="tbl_state_master";
    $tbl_user_master="tbl_user_master";
    $tbl_company_master="tbl_company_master";
    $tbl_company_year_master="tbl_company_year_master";
    $tbl_module_master="tbl_module_master";
    $tbl_menu_master="tbl_menu_master";
    $tbl_currency_master="tbl_currency_master";
    $tbl_item_master="tbl_item_master";
    $tbl_packing_unit_master = "tbl_packing_unit_master";
    $tbl_customer_master="tbl_customer_master";
    $tbl_customer_account_group_master="tbl_customer_account_group_master";
    $tbl_customer_wise_item_preservation_price_list_detail="tbl_customer_wise_item_preservation_price_list_detail";
    $tbl_customer_wise_item_preservation_price_list_master="tbl_customer_wise_item_preservation_price_list_master";
    $tbl_item_preservation_price_list_detail="tbl_item_preservation_price_list_detail";
    $tbl_item_preservation_price_list_master="tbl_item_preservation_price_list_master";
    $tbl_floor_master="tbl_floor_master";  
    $tbl_generator_master="tbl_generator_master";
    $tbl_gst_tax_detail="tbl_gst_tax_detail";
    $tbl_hsn_code_master="tbl_hsn_code_master";
    $tbl_inward_master="tbl_inward_master";
    $tbl_outward_master="tbl_outward_master";
    $tbl_menu_right_master="tbl_menu_right_master";
    $tbl_user_right_master="tbl_user_right_master";
    $tbl_rent_invoice_master="tbl_rent_invoice_master";
    $tbl_rent_invoice_detail="tbl_rent_invoice_detail";
    /* /DB TABLES */

    $menu_permissions=["add"=>"Add", "edit"=>"Edit", "delete"=>"Delete", "view"=>"View","excel"=>"Excel"]; // Used for giving user rights


if (!defined('USER_ID')) {
    if (isset($_SESSION["sess_user_id"])) {
       define('USER_ID', $_SESSION["sess_user_id"]); // User ID
    } else {
        define('USER_ID', 0); // Default user ID
    }
}
if (!defined('PERSON_NAME')) {
    if (isset($_SESSION["sess_person_name"])) {
       define('PERSON_NAME', ucwords($_SESSION["sess_person_name"])); // USER DISPLAY NAME
    } else {
        define('PERSON_NAME', 'Guest'); // Default user display name
    }
}
if (!defined('COMPANY_ID')) {
    if (isset($_SESSION["sess_company_id"])) {
       define('COMPANY_ID', $_SESSION["sess_company_id"]); // COMPANY ID
    } else {
        define('COMPANY_ID', 1); // Default company ID
    }
}
if (!defined('ECNODED_COMPANY_ID')) {
    if (isset($_SESSION["sess_encoded_company_id"])) {
       define('ECNODED_COMPANY_ID', $_SESSION["sess_encoded_company_id"]); // COMPANY ID
    } else {
        define('ECNODED_COMPANY_ID', 'lejRej'); // Default company ID
    }
}
define('ADMIN_USER_ID', 1);
define('ADMIN_COMPANY_ID', 1);
if(COMPANY_ID==ADMIN_COMPANY_ID)
{
    define('COMPANY_QUERY', "");
}
else
{
    define('COMPANY_QUERY', " and company_id = ".COMPANY_ID);
}
if(COMPANY_ID==ADMIN_COMPANY_ID)
{
    define('STATUS_QUERY', "");
}
else
{
    define('STATUS_QUERY', " and `status` = 1");
}
/*Switch year By Mansi*/
if ((COMPANY_ID>0 && USER_ID>0) &&
    (!isset($_SESSION["sess_company_year_id"]) || empty($_SESSION['sess_company_year_id'])) &&
    (!isset($_SESSION["sess_selected_year"]) || empty($_SESSION['sess_selected_year']))
) {
    $currentMonth = date("n");
    $currentYear = date("Y");

    if ($currentMonth >= 4) {
        $startYear = $currentYear;
        $endYear = $currentYear + 1;
    } else {
        $startYear = $currentYear - 1;
        $endYear = $currentYear;
    }
    $columns = "company_year_id";
    $whereClause = "YEAR(start_date) = $startYear AND YEAR(end_date) = $endYear ".COMPANY_QUERY;
    $stmt = $_dbh->prepare("CALL csms_search_detail(:columns, :tableName, :whereClause)");
    $stmt->bindParam(':columns', $columns);
    $stmt->bindParam(':tableName', $tbl_company_year_master);
    $stmt->bindParam(':whereClause', $whereClause);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if (!$row) {
        $startDate = "$startYear-04-01";
        $endDate = "$endYear-03-31";
        $insert = $_dbh->prepare("
            INSERT INTO ".$tbl_company_year_master." (company_id,company_year_type,start_date, end_date,created_by,modified_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([COMPANY_ID, 1, $startDate, $endDate,USER_ID, USER_ID]);
        $companyYearId = $_dbh->lastInsertId();
    } else {
        $companyYearId = $row['company_year_id'];
    }
    $_SESSION['sess_selected_year'] = 'FY ' . $startYear . '-' . $endYear;
    $_SESSION["sess_company_year_id"] = $companyYearId;
}
if (!defined('COMPANY_YEAR_ID')) {
    if (isset($_SESSION["sess_company_year_id"])) {
       define('COMPANY_YEAR_ID', $_SESSION["sess_company_year_id"]); // COMPANY ID
    } else {
        define('COMPANY_YEAR_ID', 1); // Default company ID
    }
}
/*Done*/

if (!defined('ENCODED_BASE_URL')) {
    define('ENCODED_BASE_URL', 'http://CBS5-PC/csms2/'.ECNODED_COMPANY_ID."/"); // Base URL of your application
}
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://CBS5-PC/csms2/'); // Base URL of your application
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/csms2/'); // Base path of your application
}
if (!defined('CSS_PATH')) {
    define('CSS_PATH', BASE_URL . 'dist/css/'); // Path to CSS files
}
if (!defined('JS_PATH')) {
    define('JS_PATH', BASE_URL . 'dist/js/'); // Path to JavaScript files
}
if (!defined('IMAGE_PATH')) {
    define('IMAGE_PATH', BASE_URL . 'images/'); // Path to image files
}
if (!defined('IMAGE_DIR')) {
    define('IMAGE_DIR', '/images/'); // Directory for image uploads
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', BASE_URL.'uploads/'); // URL to file uploads
}
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', BASE_PATH . 'uploads/'); // Path to file uploads
}
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', '/uploads/'); // Directory for file uploads
}
?>