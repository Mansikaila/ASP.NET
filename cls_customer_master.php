<?php  
include_once(__DIR__ . "/../config/connection.php");
include("cls_contact_person_detail.php"); 
        class mdl_customermaster 
{                        
public $_customer_id;          
    public $_customer;          
    public $_customer_name;          
    public $_customer_type;          
    public $_account_group_id;          
    public $_address;          
    public $_city_id;          
    public $_pincode;          
    public $_state_id;          
    public $_country_id;          
    public $_phone;          
    public $_email_id;          
    public $_web_address;          
    public $_gstin;          
    public $_pan;          
    public $_aadhar_no;          
    public $_mandli_license_no;          
    public $_fssai_license_no;          
    public $_status;          
    public $_created_date;          
    public $_created_by;          
    public $_modified_date;          
    public $_modified_by;          
    public $_company_id;          
    public $_transactionmode;
    
                    /** FOR DETAIL **/
                    public $_array_itemdetail;
                     public $_array_itemdelete;
                    /** \FOR DETAIL **/
                    
}

class bll_customermaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_customermaster(); 
        $this->_dal =new dal_customermaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       /** FOR DETAIL **/
               
                  $_bllitem= new bll_contactpersondetail();
                    if($this->_mdl->_transactionmode!="D")
                    {
                        if(!empty($this->_mdl->_array_itemdetail)) {
                             for($iterator= $this->_mdl->_array_itemdetail->getIterator();$iterator->valid();$iterator->next())
                             {
                                     $detailrow=$iterator->current();
                                    if(is_array($detailrow)) {
                                        foreach($detailrow as $name=>$value) {
                                            $_bllitem->_mdl->{$name}=$value;
                                        }
                                    }
                                    $_bllitem->_mdl->customer_id = $this->_mdl->_customer_id;
                                    $_bllitem->dbTransaction();
                             }
                        }
                         if(!empty($this->_mdl->_array_itemdelete)) {
                            for($iterator= $this->_mdl->_array_itemdelete->getIterator();$iterator->valid();$iterator->next())
                             {
                                     $detailrow=$iterator->current();
                                    if(is_array($detailrow)) {
                                        foreach($detailrow as $name=>$value) {
                                            $_bllitem->_mdl->{$name}=$value;
                                        }
                                    }
                                    $_bllitem->_mdl->customer_id = $this->_mdl->_customer_id;
                                    $_bllitem->dbTransaction();
                             }
                         }
                  }
               /** \FOR DETAIL **/
        
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_customer_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_customer_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_customer_master.php");
       }

    }
 
    public function fillModel()
    {
        global $_dbh;
        $this->_dal->fillModel($this->_mdl);
    
    
    }
     public function pageSearch()
    {
        global $_dbh;

        $sql="CAll csms1_search('t.customer, t.customer, t4.customer_account_group_name, t.customer, t.customer, t4.customer_account_group_name, t6.city_name, t.state_id, t.country_id, t.phone, t.email_id, t.gstin, t.pan, t.aadhar_no, t.customer_id','tbl_customer_master t INNER JOIN tbl_customer_account_group_master t4 ON t.account_group_id=t4.customer_account_group_id INNER JOIN tbl_city_master t6 ON t.city_id=t6.city_id')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> Customer <br><input type=\"text\" data-index=\"1\" placeholder=\"Search Customer\" /></th> 
                         <th> Account Group Name <br><input type=\"text\" data-index=\"4\" placeholder=\"Search Account Group Name\" /></th> 
                         <th> City Name <br><input type=\"text\" data-index=\"6\" placeholder=\"Search City Name\" /></th> 
                         <th> State Name <br><input type=\"text\" data-index=\"8\" placeholder=\"Search State Name\" /></th> 
                         <th> Country Name <br><input type=\"text\" data-index=\"9\" placeholder=\"Search Country Name\" /></th> 
                         <th> Phone <br><input type=\"text\" data-index=\"10\" placeholder=\"Search Phone\" /></th> 
                         <th> Email Id <br><input type=\"text\" data-index=\"11\" placeholder=\"Search Email Id\" /></th> 
                         <th> Gstin <br><input type=\"text\" data-index=\"13\" placeholder=\"Search Gstin\" /></th> 
                         <th> Pan <br><input type=\"text\" data-index=\"14\" placeholder=\"Search Pan\" /></th> 
                         <th> Aadhar No <br><input type=\"text\" data-index=\"15\" placeholder=\"Search Aadhar No\" /></th> 
                         </tr>
        </thead>
        <tbody>";
         $_grid="";
         $j=0;
        foreach($_dbh-> query($sql) as $_rs)
        {
            $j++;
        
        $_grid.="<tr>
        <td> 
            <form  method=\"post\" action=\"frm_customer_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"customer_id\" value=\"".$_rs["customer_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_customer_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"customer_id\" value=\"".$_rs["customer_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $fieldvalue=$_rs["customer"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["customer_account_group_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["city_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["state_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["country_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["phone"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["email_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["gstin"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["pan"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["aadhar_no"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"23\">No records available.</td>";
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="</tr>";
            }
        $_grid.="</tbody>
        </table> ";
        echo $_grid; 
    }
    function checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name) {
        global $_dbh;
        try {
            $sql="CAll csms1_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',@is_duplicate)";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_default = $result->fetchColumn();
            return $is_default;
        }
        catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            return 0;
        }
        return 0;
    }
    public function fetchCountryForPopup() {
        global $_dbh;
        $state_id = $_POST['state_id'];
        $columns = 'c.country_id AS country_id, c.country_name AS country_name';
        $tableName = 'tbl_state_master s JOIN tbl_country_master c ON s.country_id = c.country_id';
        $whereCondition = "s.state_id = :state_id";
        $stmt = $_dbh->prepare("CALL csms1_search_detail(:columns, :tableName, :whereCondition)");
        $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
        $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);
        $whereCondition = "s.state_id = $state_id";
        $stmt->bindParam(':whereCondition', $whereCondition, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row ?: []);
    }
    /*fetchCountryAndState by Hetasvi*/
    public function fetchCountryAndState() {
        global $_dbh;
        if (empty($_POST['city_id'])) {
            echo json_encode(['error' => 'City ID is required']);
            return;
        }
            $city_id = $_POST['city_id'];
            $columns = "c.country_id AS country_id, c.country_name AS country_name, s.state_id AS state_id, s.state_name AS state_name";
            $tableName = "tbl_city_master ct JOIN tbl_state_master s ON ct.state_id = s.state_id JOIN tbl_country_master c ON s.country_id = c.country_id";
            $whereCondition = "ct.city_id = $city_id";
        try {
            $stmt = $_dbh->prepare("CALL csms1_search_detail(:columns, :tableName, :whereCondition)");
            $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
            $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);
            $stmt->bindParam(':whereCondition', $whereCondition, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($row ?: []);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    /*end fetchCountryAndState by Hetasvi*/
}

 class dal_customermaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        $_dbh->exec("set @p0 = ".$_mdl->_customer_id);
        $_pre=$_dbh->prepare("CALL customer_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->_customer);
        $_pre->bindParam(2,$_mdl->_customer_name);
        $_pre->bindParam(3,$_mdl->_customer_type);
        $_pre->bindParam(4,$_mdl->_account_group_id);
        $_pre->bindParam(5,$_mdl->_address);
        $_pre->bindParam(6,$_mdl->_city_id);
        $_pre->bindParam(7,$_mdl->_pincode);
        $_pre->bindParam(8,$_mdl->_state_id);
        $_pre->bindParam(9,$_mdl->_country_id);
        $_pre->bindParam(10,$_mdl->_phone);
        $_pre->bindParam(11,$_mdl->_email_id);
        $_pre->bindParam(12,$_mdl->_web_address);
        $_pre->bindParam(13,$_mdl->_gstin);
        $_pre->bindParam(14,$_mdl->_pan);
        $_pre->bindParam(15,$_mdl->_aadhar_no);
        $_pre->bindParam(16,$_mdl->_mandli_license_no);
        $_pre->bindParam(17,$_mdl->_fssai_license_no);
        $_pre->bindParam(18,$_mdl->_status);
        $_pre->bindParam(19,$_mdl->_created_date);
        $_pre->bindParam(20,$_mdl->_created_by);
        $_pre->bindParam(21,$_mdl->_modified_date);
        $_pre->bindParam(22,$_mdl->_modified_by);
        $_pre->bindParam(23,$_mdl->_company_id);
        $_pre->bindParam(24,$_mdl->_transactionmode);
        $_pre->execute();
        
           /*** FOR DETAIL ***/
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_customer_id=$insertedId;
            }
            /*** /FOR DETAIL ***/
    
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL customer_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["customer_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {

        $_mdl->_customer_id=$_rs[0]["customer_id"];
        $_mdl->_customer=$_rs[0]["customer"];
        $_mdl->_customer_name=$_rs[0]["customer_name"];
        $_mdl->_customer_type=$_rs[0]["customer_type"];
        $_mdl->_account_group_id=$_rs[0]["account_group_id"];
        $_mdl->_address=$_rs[0]["address"];
        $_mdl->_city_id=$_rs[0]["city_id"];
        $_mdl->_pincode=$_rs[0]["pincode"];
        $_mdl->_state_id=$_rs[0]["state_id"];
        $_mdl->_country_id=$_rs[0]["country_id"];
        $_mdl->_phone=$_rs[0]["phone"];
        $_mdl->_email_id=$_rs[0]["email_id"];
        $_mdl->_web_address=$_rs[0]["web_address"];
        $_mdl->_gstin=$_rs[0]["gstin"];
        $_mdl->_pan=$_rs[0]["pan"];
        $_mdl->_aadhar_no=$_rs[0]["aadhar_no"];
        $_mdl->_mandli_license_no=$_rs[0]["mandli_license_no"];
        $_mdl->_fssai_license_no=$_rs[0]["fssai_license_no"];
        $_mdl->_status=$_rs[0]["status"];
        $_mdl->_created_date=$_rs[0]["created_date"];
        $_mdl->_created_by=$_rs[0]["created_by"];
        $_mdl->_modified_date=$_rs[0]["modified_date"];
        $_mdl->_modified_by=$_rs[0]["modified_by"];
        $_mdl->_company_id=$_rs[0]["company_id"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_customermaster();


    /*** FOR DETAIL ***/
    $_blldetail=new bll_contactpersondetail();
    /*** /FOR DETAIL ***/
if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
if(isset($_POST["type"]) && $_POST["type"]=="ajax") {
    $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";
    if(isset($_POST["column_name"]))
        $column_name=$_POST["column_name"];
    if(isset($_POST["column_value"]))
        $column_value=$_POST["column_value"];
    if(isset($_POST["id_name"]))
        $id_name=$_POST["id_name"];
    if(isset($_POST["id_value"]))
        $id_value=$_POST["id_value"];
    if(isset($_POST["table_name"]))
        $table_name=$_POST["table_name"];
    echo $_bll->checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name);
    exit;
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
 
            
            if(isset($_REQUEST["customer_id"]) && !empty($_REQUEST["customer_id"]))
                $field=trim($_REQUEST["customer_id"]);
            else {
                    $field=0;
           }
    $_bll->_mdl->_customer_id=$field;

            
            if(isset($_REQUEST["customer"]) && !empty($_REQUEST["customer"]))
                $field=trim($_REQUEST["customer"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_customer=$field;

            
            if(isset($_REQUEST["customer_name"]) && !empty($_REQUEST["customer_name"]))
                $field=trim($_REQUEST["customer_name"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_customer_name=$field;

            
            if(isset($_REQUEST["customer_type"]) && !empty($_REQUEST["customer_type"]))
                $field=trim($_REQUEST["customer_type"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_customer_type=$field;

            
            if(isset($_REQUEST["account_group_id"]) && !empty($_REQUEST["account_group_id"]))
                $field=trim($_REQUEST["account_group_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_account_group_id=$field;

            
            if(isset($_REQUEST["address"]) && !empty($_REQUEST["address"]))
                $field=trim($_REQUEST["address"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_address=$field;

            
            if(isset($_REQUEST["city_id"]) && !empty($_REQUEST["city_id"]))
                $field=trim($_REQUEST["city_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_city_id=$field;

            
            if(isset($_REQUEST["pincode"]) && !empty($_REQUEST["pincode"]))
                $field=trim($_REQUEST["pincode"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_pincode=$field;

            
            if(isset($_REQUEST["state_id"]) && !empty($_REQUEST["state_id"]))
                $field=trim($_REQUEST["state_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_state_id=$field;

            
            if(isset($_REQUEST["country_id"]) && !empty($_REQUEST["country_id"]))
                $field=trim($_REQUEST["country_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_country_id=$field;

            
            if(isset($_REQUEST["phone"]) && !empty($_REQUEST["phone"]))
                $field=trim($_REQUEST["phone"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_phone=$field;

            
            if(isset($_REQUEST["email_id"]) && !empty($_REQUEST["email_id"]))
                $field=trim($_REQUEST["email_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_email_id=$field;

            
            if(isset($_REQUEST["web_address"]) && !empty($_REQUEST["web_address"]))
                $field=trim($_REQUEST["web_address"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_web_address=$field;

            
            if(isset($_REQUEST["gstin"]) && !empty($_REQUEST["gstin"]))
                $field=trim($_REQUEST["gstin"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_gstin=$field;

            
            if(isset($_REQUEST["pan"]) && !empty($_REQUEST["pan"]))
                $field=trim($_REQUEST["pan"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_pan=$field;

            
            if(isset($_REQUEST["aadhar_no"]) && !empty($_REQUEST["aadhar_no"]))
                $field=trim($_REQUEST["aadhar_no"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_aadhar_no=$field;

            
            if(isset($_REQUEST["mandli_license_no"]) && !empty($_REQUEST["mandli_license_no"]))
                $field=trim($_REQUEST["mandli_license_no"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_mandli_license_no=$field;

            
            if(isset($_REQUEST["fssai_license_no"]) && !empty($_REQUEST["fssai_license_no"]))
                $field=trim($_REQUEST["fssai_license_no"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_fssai_license_no=$field;

            
            if(isset($_REQUEST["status"]) && !empty($_REQUEST["status"]))
                $field=trim($_REQUEST["status"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_status=$field;

            
            if(isset($_REQUEST["created_date"]) && !empty($_REQUEST["created_date"]))
                $field=trim($_REQUEST["created_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_date=$field;

            
            if(isset($_REQUEST["created_by"]) && !empty($_REQUEST["created_by"]))
                $field=trim($_REQUEST["created_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_by=$field;

            
            if(isset($_REQUEST["modified_date"]) && !empty($_REQUEST["modified_date"]))
                $field=trim($_REQUEST["modified_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_date=$field;

            
            if(isset($_REQUEST["modified_by"]) && !empty($_REQUEST["modified_by"]))
                $field=trim($_REQUEST["modified_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_by=$field;

            
            if(isset($_REQUEST["company_id"]) && !empty($_REQUEST["company_id"]))
                $field=trim($_REQUEST["company_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_company_id=$field;

            if(isset($_REQUEST["transactionmode"]))
                $tmode=$_REQUEST["transactionmode"];
            else
                $tmode="I";
            $_bll->_mdl->_transactionmode =$tmode;
         
               /*** FOR DETAIL ***/
                $_bll->_mdl->_array_itemdetail=array();
                $_bll->_mdl->_array_itemdelete=array();
                if(isset($_REQUEST["detail_records"])) {
                  $detail_records=json_decode($_REQUEST["detail_records"],true);
                   if(!empty($detail_records)) {
                        $arrayobject = new ArrayObject($detail_records);
                          $_bll->_mdl->_array_itemdetail=$arrayobject;
                    }
                }
                if(isset($_REQUEST["deleted_records"])) {
                  $deleted_records=json_decode($_REQUEST["deleted_records"],true);
                   if(!empty($deleted_records)) {
                        $deleteobject = new ArrayObject($deleted_records);
                          $_bll->_mdl->_array_itemdelete=$deleteobject;
                    }
                }
                /*** \FOR DETAIL ***/
            $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}?>


    