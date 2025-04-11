<?php  
include_once(__DIR__ . "/../config/connection.php");

class mdl_userrightmaster {                        
    public $_user_right_id;          
    public $_user_id;          
    public $_menu_right_id;          
    public $_is_right;          
    public $_transactionmode;
}

class bll_userrightmaster {
    public $_mdl;
    public $_dal;

    public function __construct() {
        $this->_mdl = new mdl_userrightmaster();
        $this->_dal = new dal_userrightmaster();
    }

    public function dbTransaction() {
        $this->_dal->dbTransaction($this->_mdl);

        if ($this->_mdl->_transactionmode == "D") {
            header("Location: ../srh_user_right_master.php");
        } elseif ($this->_mdl->_transactionmode == "U") {
            header("Location: ../srh_user_right_master.php");
        } elseif ($this->_mdl->_transactionmode == "I") {
            header("Location: ../srh_user_right_master.php"); 
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
        
        $sql = "CALL csms1_search('t.user_id, t.menu_right_id, t.menu_right_id, t1.login_id, t.is_right, t.user_id, t1.login_id, t.is_right, t2.menu_right_id, t.user_right_id','tbl_user_right_master t INNER JOIN tbl_user_master t1 ON t.user_id=t1.user_id INNER JOIN tbl_menu_right_master t2 ON t.menu_right_id=t2.menu_right_id')";

        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> User Id <br><input type=\"text\" data-index=\"1\" placeholder=\"Search User Id\" /></th> 
                         <th> Menu Right Id <br><input type=\"text\" data-index=\"2\" placeholder=\"Search Menu Right Id\" /></th> 
                         <th> Is Right <br><input type=\"text\" data-index=\"3\" placeholder=\"Search Is Right\" /></th> 
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
            <form  method=\"post\" action=\"srh _user_right_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"user_right_id\" value=\"".$_rs["user_right_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_user_right_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"user_right_id\" value=\"".$_rs["user_right_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $fieldvalue=$_rs["login_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["menu_right_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["is_right"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"3\">No records available.</td>";
                $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="<td style=\"display:none\">&nbsp;</td>";
                         $_grid.="</tr>";
            }
        $_grid.="</tbody>
        </table> ";
        echo $_grid; 
    }
}


class dal_userrightmaster {
    public function dbTransaction($_mdl) {
        global $_dbh;
//        print_r($_mdl);
//        exit;
        $_dbh->exec("set @p0 = " . $_mdl->_user_right_id);
        $_pre = $_dbh->prepare("CALL user_right_master_transaction (@p0,?,?,?,?) ");
        $_pre->bindParam(1, $_mdl->_user_id);
        $_pre->bindParam(2, $_mdl->_menu_right_id);
        $_pre->bindParam(3, $_mdl->_is_right);
        $_pre->bindParam(4, $_mdl->_transactionmode);
        $_pre->execute();
        
           if($_mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $_dbh->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $_mdl->_user_right_id=$insertedId;
            }
    }

    public function fillModel($_mdl) {
        global $_dbh;
        $_pre = $_dbh->prepare("CALL user_right_master_fillmodel (?) ");
        $_pre->bindParam(1, $_REQUEST["user_right_id"]);
        $_pre->execute();
        $_rs = $_pre->fetchAll(); 
         
        if (!empty($_rs)) {
            $_mdl->_user_right_id = $_rs[0]["user_right_id"];
            $_mdl->_user_id = $_rs[0]["user_id"];
            $_mdl->_menu_right_id = $_rs[0]["menu_right_id"];
            $_mdl->_is_right = $_rs[0]["is_right"];
            $_mdl->_transactionmode = $_REQUEST["transactionmode"];
        }
    }
}

$_bll = new bll_userrightmaster();

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
            
            if(isset($_REQUEST["user_right_id"]) && !empty($_REQUEST["user_right_id"]))
                $field=trim($_REQUEST["user_right_id"]);
            else {
                    $field=0;
           }
            $_bll->_mdl->_user_right_id=$field;

            
            if(isset($_REQUEST["user_id"]) && !empty($_REQUEST["user_id"]))
                $field=trim($_REQUEST["user_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_user_id=$field;

            
            if(isset($_REQUEST["menu_right_id"]) && !empty($_REQUEST["menu_right_id"]))
                $field=trim($_REQUEST["menu_right_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_menu_right_id=$field;

            
            if(isset($_REQUEST["is_right"]) && !empty($_REQUEST["is_right"]))
                $field=trim($_REQUEST["is_right"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_is_right=$field;

            if(isset($_REQUEST["transactionmode"]))
                $tmode=$_REQUEST["transactionmode"];
            else
                $tmode="I";
            $_bll->_mdl->_transactionmode =$tmode;
        $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}

?>