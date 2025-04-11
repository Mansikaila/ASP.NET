-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 11:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `csms1`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `bank_master_fillmodel` (IN `p_bank_id` INT)   BEGIN
SELECT * 
       FROM `tbl_bank_master` 
        WHERE bank_id= p_bank_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `bank_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `bank_master_transaction` (INOUT `p_bank_id` INT, IN `p_bank_name` VARCHAR(100), IN `p_branch_name` VARCHAR(100), IN `p_account_no` VARCHAR(100), IN `p_ifs_code` VARCHAR(100), IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_bank_id = (SELECT COALESCE(MAX(bank_id),0) + 1 FROM tbl_bank_master);

                    insert into tbl_bank_master
                    (
                        bank_id,
                        bank_name,
                        branch_name,
                        account_no,
                        ifs_code,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_bank_id,
                        p_bank_name,
                        p_branch_name,
                        p_account_no,
                        p_ifs_code,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_bank_master
                
                SET
                        bank_name= p_bank_name,
                        branch_name= p_branch_name,
                        account_no= p_account_no,
                        ifs_code= p_ifs_code,
                        status= p_status,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE bank_id= p_bank_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_bank_master WHERE bank_id= p_bank_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `chamber_master_fillmodel` (IN `p_chamber_id` INT)   BEGIN
SELECT * 
       FROM `tbl_chamber_master` 
        WHERE chamber_id= p_chamber_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `chamber_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `chamber_master_transaction` (INOUT `p_chamber_id` INT, IN `p_chamber_name` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_chamber_id = (SELECT COALESCE(MAX(chamber_id),0) + 1 FROM tbl_chamber_master);

                    insert into tbl_chamber_master
                    (
                        chamber_id,
                        chamber_name,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_chamber_id,
                        p_chamber_name,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_chamber_master
                
                SET
                        chamber_name= p_chamber_name,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE chamber_id= p_chamber_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_chamber_master WHERE chamber_id= p_chamber_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `city_master_fillmodel` (IN `p_city_id` INT)   BEGIN
SELECT * 
       FROM `tbl_city_master` 
        WHERE city_id= p_city_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `city_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `city_master_transaction` (INOUT `p_city_id` INT, IN `p_city_name` VARCHAR(100), IN `p_state_id` INT, IN `p_country_id` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_city_id = (SELECT COALESCE(MAX(city_id),0) + 1 FROM tbl_city_master);

                    insert into tbl_city_master
                    (
                        city_id,
                        city_name,
                        state_id,
                        country_id,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_city_id,
                        p_city_name,
                        p_state_id,
                        p_country_id,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_city_master
                
                SET
                        city_name= p_city_name,
                        state_id= p_state_id,
                        country_id= p_country_id,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE city_id= p_city_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_city_master WHERE city_id= p_city_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_master_fillmodel` (IN `p_company_id` INT)   BEGIN
SELECT * 
       FROM `tbl_company_master` 
        WHERE company_id= p_company_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_master_transaction` (INOUT `p_company_id` INT, IN `p_company_name` VARCHAR(500), IN `p_company_code` VARCHAR(100), IN `p_company_logo` TEXT, IN `p_address` TEXT, IN `p_city` VARCHAR(50), IN `p_pincode` VARCHAR(50), IN `p_state` VARCHAR(50), IN `p_phone` VARCHAR(50), IN `p_email` VARCHAR(100), IN `p_web_address` VARCHAR(100), IN `p_gstin` VARCHAR(50), IN `p_bank_id` INT, IN `p_jurisdiction` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_company_id = (SELECT COALESCE(MAX(company_id),0) + 1 FROM tbl_company_master);

                    insert into tbl_company_master
                    (
                        company_id,
                        company_name,
                        company_code,
                        company_logo,
                        address,
                        city,
                        pincode,
                        state,
                        phone,
                        email,
                        web_address,
                        gstin,
                        bank_id,
                        jurisdiction,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_company_id,
                        p_company_name,
                        p_company_code,
                        p_company_logo,
                        p_address,
                        p_city,
                        p_pincode,
                        p_state,
                        p_phone,
                        p_email,
                        p_web_address,
                        p_gstin,
                        p_bank_id,
                        p_jurisdiction,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_company_master
                
                SET
                        company_name= p_company_name,
                        company_code= p_company_code,
                        company_logo= p_company_logo,
                        address= p_address,
                        city= p_city,
                        pincode= p_pincode,
                        state= p_state,
                        phone= p_phone,
                        email= p_email,
                        web_address= p_web_address,
                        gstin= p_gstin,
                        bank_id= p_bank_id,
                        jurisdiction= p_jurisdiction,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE company_id= p_company_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_company_master WHERE company_id= p_company_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_year_master_fillmodel` (IN `p_company_year_id` INT)   BEGIN
SELECT * 
       FROM `tbl_company_year_master` 
        WHERE company_year_id= p_company_year_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_year_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `company_year_master_transaction` (INOUT `p_company_year_id` INT, IN `p_company_type` VARCHAR(100), IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_company_year_id = (SELECT COALESCE(MAX(company_year_id),0) + 1 FROM tbl_company_year_master);

                    insert into tbl_company_year_master
                    (
                        company_year_id,
                        company_type,
                        start_date,
                        end_date,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_company_year_id,
                        p_company_type,
                        p_start_date,
                        p_end_date,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_company_year_master
                
                SET
                        company_type= p_company_type,
                        start_date= p_start_date,
                        end_date= p_end_date,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE company_year_id= p_company_year_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_company_year_master WHERE company_year_id= p_company_year_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `contact_person_detail_fillmodel` (IN `p_contact_person_id` INT)   BEGIN
SELECT * 
       FROM `tbl_contact_person_detail` 
        WHERE contact_person_id= p_contact_person_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `contact_person_detail_transaction` (INOUT `p_contact_person_id` INT, IN `p_customer_id` INT, IN `p_contact_person_name` VARCHAR(100), IN `p_mobile` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_contact_preference` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_contact_person_id = (SELECT COALESCE(MAX(contact_person_id),0) + 1 FROM tbl_contact_person_detail);

                    insert into tbl_contact_person_detail
                    (
                        contact_person_id,
                            customer_id,
                            contact_person_name,
                            mobile,
                            email,
                            contact_preference
                    )
                    values
                    ( 
                        p_contact_person_id,
                            p_customer_id,
                            p_contact_person_name,
                            p_mobile,
                            p_email,
                            p_contact_preference
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_contact_person_detail
                
                SET
                        customer_id= p_customer_id,
                        contact_person_name= p_contact_person_name,
                        mobile= p_mobile,
                        email= p_email,
                        contact_preference= p_contact_preference

                WHERE contact_person_id= p_contact_person_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_contact_person_detail WHERE contact_person_id= p_contact_person_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `country_master_fillmodel` (IN `p_country_id` INT)   BEGIN
SELECT * 
       FROM `tbl_country_master` 
        WHERE country_id= p_country_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `country_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `country_master_transaction` (INOUT `p_country_id` INT, IN `p_country_name` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_country_id = (SELECT COALESCE(MAX(country_id),0) + 1 FROM tbl_country_master);

                    insert into tbl_country_master
                    (
                        country_id,
                        country_name,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_country_id,
                        p_country_name,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_country_master
                
                SET
                        country_name= p_country_name,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE country_id= p_country_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_country_master WHERE country_id= p_country_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_check_duplicate` (IN `p_column_name` VARCHAR(50), IN `p_column_value` VARCHAR(50), IN `p_id_name` VARCHAR(50), IN `p_id_value` INT, IN `p_table_name` VARCHAR(50), OUT `is_duplicate` BOOLEAN)   BEGIN
    DECLARE duplicate_count INT;

    -- Build the SQL query dynamically
    SET @query = CONCAT('SELECT COUNT(*) INTO @duplicate_count FROM ', p_table_name,
                        ' WHERE ', p_column_name, ' = "', p_column_value, '" ',
                        ' AND (', p_id_value, ' IS NULL OR ', p_id_name, ' <> ', p_id_value, ')');

    -- Prepare and execute the query
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- Set output variable
    IF @duplicate_count > 0 THEN
        SET is_duplicate = TRUE;
    ELSE
        SET is_duplicate = FALSE;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_getval` (IN `p_field` VARCHAR(10), IN `p_field_val` VARCHAR(10), IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
      SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName, " WHERE ", p_field, "='", p_field_val, "'");
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_search_detail` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255), IN `whereColumn` TEXT)   BEGIN

SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName, " WHERE 1=1");


IF whereColumn IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND ', whereColumn);
    END IF;
   

PREPARE stmt FROM @sql;

EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `csms1_search_new` (IN `columns` TEXT, IN `tables` TEXT, IN `joinTypes` TEXT, IN `joinConditions` TEXT)   BEGIN
    DECLARE tableList TEXT;
    DECLARE joinTypeList TEXT;
    DECLARE conditionList TEXT;
    DECLARE tableArray TEXT;
    DECLARE joinArray TEXT;
    DECLARE conditionArray TEXT;
    DECLARE numJoins INT;
    DECLARE i INT DEFAULT 1;
    DECLARE resultQuery TEXT;

    -- Split tables, join types, and join conditions
    SET tableArray = tables;
    SET joinArray = joinTypes;
    SET conditionArray = joinConditions;
    
    -- Get the first table
    SET tableList = SUBSTRING_INDEX(tableArray, ',', 1);
    SET tableArray = SUBSTRING(tableArray FROM LOCATE(',', tableArray) + 1);

    -- Initialize the final query
    SET resultQuery = CONCAT("SELECT ", columns, " FROM ", tableList);

    -- Count the number of joins (tables - 1)
    SET numJoins = (LENGTH(tables) - LENGTH(REPLACE(tables, ',', '')));

    -- Loop through the remaining tables and build JOIN statements
    WHILE i <= numJoins DO
        -- Get next join type, table, and condition
        SET joinTypeList = SUBSTRING_INDEX(joinArray, ',', 1);
        SET joinArray = IF(LOCATE(',', joinArray) > 0, SUBSTRING(joinArray FROM LOCATE(',', joinArray) + 1), '');

        SET tableList = SUBSTRING_INDEX(tableArray, ',', 1);
        SET tableArray = IF(LOCATE(',', tableArray) > 0, SUBSTRING(tableArray FROM LOCATE(',', tableArray) + 1), '');

        SET conditionList = SUBSTRING_INDEX(conditionArray, ',', 1);
        SET conditionArray = IF(LOCATE(',', conditionArray) > 0, SUBSTRING(conditionArray FROM LOCATE(',', conditionArray) + 1), '');

        -- Append the JOIN clause
        SET resultQuery = CONCAT(resultQuery, ' ', joinTypeList, ' JOIN ', tableList, ' ON ', conditionList);

        SET i = i + 1;
    END WHILE;

    -- Prepare and execute the query
    SET @sql = resultQuery;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `currency_master_fillmodel` (IN `p_currency_id` INT)   BEGIN
SELECT * 
       FROM `tbl_currency_master` 
        WHERE currency_id= p_currency_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `currency_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `currency_master_transaction` (INOUT `p_currency_id` INT, IN `p_currency_symbol` VARCHAR(100), IN `p_currency_name` VARCHAR(100), IN `p_currency_in_paise` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_currency_id = (SELECT COALESCE(MAX(currency_id),0) + 1 FROM tbl_currency_master);

                    insert into tbl_currency_master
                    (
                        currency_id,
                        currency_symbol,
                        currency_name,
                        currency_in_paise,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_currency_id,
                        p_currency_symbol,
                        p_currency_name,
                        p_currency_in_paise,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_currency_master
                
                SET
                        currency_symbol= p_currency_symbol,
                        currency_name= p_currency_name,
                        currency_in_paise= p_currency_in_paise,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE currency_id= p_currency_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_currency_master WHERE currency_id= p_currency_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_account_group_master_fillmodel` (IN `p_customer_account_group_id` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_account_group_master` 
        WHERE customer_account_group_id= p_customer_account_group_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_account_group_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_account_group_master_transaction` (INOUT `p_customer_account_group_id` INT, IN `p_customer_account_group_name` VARCHAR(100), IN `p_under_group` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_account_group_id = (SELECT COALESCE(MAX(customer_account_group_id),0) + 1 FROM tbl_customer_account_group_master);

                    insert into tbl_customer_account_group_master
                    (
                        customer_account_group_id,
                        customer_account_group_name,
                        under_group,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_customer_account_group_id,
                        p_customer_account_group_name,
                        p_under_group,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_account_group_master
                
                SET
                        customer_account_group_name= p_customer_account_group_name,
                        under_group= p_under_group,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE customer_account_group_id= p_customer_account_group_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_account_group_master WHERE customer_account_group_id= p_customer_account_group_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_item_preservation_price_list_master_fillmodel` (IN `p_customer_item_preservation_price_list` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_item_preservation_price_list_master` 
        WHERE customer_item_preservation_price_list= p_customer_item_preservation_price_list;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_item_preservation_price_list_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_item_preservation_price_list_master_transaction` (INOUT `p_customer_item_preservation_price_list` INT, IN `p_customer_id` INT, IN `p_item_id` INT, IN `p_rent_kg_per_month` DECIMAL, IN `p_rent_per_kg` DECIMAL, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_item_preservation_price_list = (SELECT COALESCE(MAX(customer_item_preservation_price_list),0) + 1 FROM tbl_customer_item_preservation_price_list_master);

                    insert into tbl_customer_item_preservation_price_list_master
                    (
                        customer_item_preservation_price_list,
                        customer_id,
                        item_id,
                        rent_kg_per_month,
                        rent_per_kg,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_customer_item_preservation_price_list,
                        p_customer_id,
                        p_item_id,
                        p_rent_kg_per_month,
                        p_rent_per_kg,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_item_preservation_price_list_master
                
                SET
                        customer_id= p_customer_id,
                        item_id= p_item_id,
                        rent_kg_per_month= p_rent_kg_per_month,
                        rent_per_kg= p_rent_per_kg,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE customer_item_preservation_price_list= p_customer_item_preservation_price_list;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_item_preservation_price_list_master WHERE customer_item_preservation_price_list= p_customer_item_preservation_price_list;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_master_fillmodel` (IN `p_customer_id` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_master` 
        WHERE customer_id= p_customer_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_master_transaction` (INOUT `p_customer_id` INT, IN `p_customer` VARCHAR(100), IN `p_customer_name` VARCHAR(100), IN `p_customer_type` INT, IN `p_account_group_id` INT, IN `p_address` TEXT, IN `p_city_id` INT, IN `p_pincode` VARCHAR(100), IN `p_state_id` INT, IN `p_country_id` INT, IN `p_phone` VARCHAR(100), IN `p_email_id` VARCHAR(100), IN `p_web_address` VARCHAR(100), IN `p_gstin` VARCHAR(100), IN `p_pan` VARCHAR(100), IN `p_aadhar_no` VARCHAR(100), IN `p_mandli_license_no` VARCHAR(100), IN `p_fssai_license_no` VARCHAR(100), IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_id = (SELECT COALESCE(MAX(customer_id),0) + 1 FROM tbl_customer_master);

                    insert into tbl_customer_master
                    (
                        customer_id,
                        customer,
                        customer_name,
                        customer_type,
                        account_group_id,
                        address,
                        city_id,
                        pincode,
                        state_id,
                        country_id,
                        phone,
                        email_id,
                        web_address,
                        gstin,
                        pan,
                        aadhar_no,
                        mandli_license_no,
                        fssai_license_no,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_customer_id,
                        p_customer,
                        p_customer_name,
                        p_customer_type,
                        p_account_group_id,
                        p_address,
                        p_city_id,
                        p_pincode,
                        p_state_id,
                        p_country_id,
                        p_phone,
                        p_email_id,
                        p_web_address,
                        p_gstin,
                        p_pan,
                        p_aadhar_no,
                        p_mandli_license_no,
                        p_fssai_license_no,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_master
                
                SET
                        customer= p_customer,
                        customer_name= p_customer_name,
                        customer_type= p_customer_type,
                        account_group_id= p_account_group_id,
                        address= p_address,
                        city_id= p_city_id,
                        pincode= p_pincode,
                        state_id= p_state_id,
                        country_id= p_country_id,
                        phone= p_phone,
                        email_id= p_email_id,
                        web_address= p_web_address,
                        gstin= p_gstin,
                        pan= p_pan,
                        aadhar_no= p_aadhar_no,
                        mandli_license_no= p_mandli_license_no,
                        fssai_license_no= p_fssai_license_no,
                        status= p_status,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE customer_id= p_customer_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_master WHERE customer_id= p_customer_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_item_preservation_price_list_master_fillmodel` (IN `p_customer_wise_item_preservation_price_list_id` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_wise_item_preservation_price_list_master` 
        WHERE customer_wise_item_preservation_price_list_id= p_customer_wise_item_preservation_price_list_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_item_preservation_price_list_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_item_preservation_price_list_master_transaction` (INOUT `p_customer_wise_item_preservation_price_list_id` INT, IN `p_customer_id` INT, IN `p_item_id` INT, IN `p_rent_kg_per_month` DECIMAL, IN `p_rent_per_kg` DECIMAL, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_wise_item_preservation_price_list_id = (SELECT COALESCE(MAX(customer_wise_item_preservation_price_list_id),0) + 1 FROM tbl_customer_wise_item_preservation_price_list_master);

                    insert into tbl_customer_wise_item_preservation_price_list_master
                    (
                        customer_wise_item_preservation_price_list_id,
                        customer_id,
                        item_id,
                        rent_kg_per_month,
                        rent_per_kg,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_customer_wise_item_preservation_price_list_id,
                        p_customer_id,
                        p_item_id,
                        p_rent_kg_per_month,
                        p_rent_per_kg,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_wise_item_preservation_price_list_master
                
                SET
                        customer_id= p_customer_id,
                        item_id= p_item_id,
                        rent_kg_per_month= p_rent_kg_per_month,
                        rent_per_kg= p_rent_per_kg,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE customer_wise_item_preservation_price_list_id= p_customer_wise_item_preservation_price_list_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_wise_item_preservation_price_list_master WHERE customer_wise_item_preservation_price_list_id= p_customer_wise_item_preservation_price_list_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_tem_preservation_price_list_master_fillmodel` (IN `p_customer_wise_item_preservation_price_list` INT)   BEGIN
SELECT * 
       FROM `tbl_customer_wise_tem_preservation_price_list_master` 
        WHERE customer_wise_item_preservation_price_list= p_customer_wise_item_preservation_price_list;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_tem_preservation_price_list_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `customer_wise_tem_preservation_price_list_master_transaction` (INOUT `p_customer_wise_item_preservation_price_list` INT, IN `p_customer_id` INT, IN `p_item_id` INT, IN `p_rent_kg_per_month` DECIMAL, IN `p_rent_per_kg` DECIMAL, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_customer_wise_item_preservation_price_list = (SELECT COALESCE(MAX(customer_wise_item_preservation_price_list),0) + 1 FROM tbl_customer_wise_tem_preservation_price_list_master);

                    insert into tbl_customer_wise_tem_preservation_price_list_master
                    (
                        customer_wise_item_preservation_price_list,
                        customer_id,
                        item_id,
                        rent_kg_per_month,
                        rent_per_kg,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_customer_wise_item_preservation_price_list,
                        p_customer_id,
                        p_item_id,
                        p_rent_kg_per_month,
                        p_rent_per_kg,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_customer_wise_tem_preservation_price_list_master
                
                SET
                        customer_id= p_customer_id,
                        item_id= p_item_id,
                        rent_kg_per_month= p_rent_kg_per_month,
                        rent_per_kg= p_rent_per_kg,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE customer_wise_item_preservation_price_list= p_customer_wise_item_preservation_price_list;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_customer_wise_tem_preservation_price_list_master WHERE customer_wise_item_preservation_price_list= p_customer_wise_item_preservation_price_list;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `floor_master_fillmodel` (IN `p_floor_id` INT)   BEGIN
SELECT * 
       FROM `tbl_floor_master` 
        WHERE floor_id= p_floor_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `floor_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `floor_master_transaction` (INOUT `p_floor_id` INT, IN `p_floor_name` VARCHAR(100), IN `p_chamber_id` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_floor_id = (SELECT COALESCE(MAX(floor_id),0) + 1 FROM tbl_floor_master);

                    insert into tbl_floor_master
                    (
                        floor_id,
                        floor_name,
                        chamber_id,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_floor_id,
                        p_floor_name,
                        p_chamber_id,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_floor_master
                
                SET
                        floor_name= p_floor_name,
                        chamber_id= p_chamber_id,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE floor_id= p_floor_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_floor_master WHERE floor_id= p_floor_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `gst_tax_detail_fillmodel` (IN `p_gst_tax_id` INT)   BEGIN
SELECT * 
       FROM `tbl_gst_tax_detail` 
        WHERE gst_tax_id= p_gst_tax_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `gst_tax_detail_transaction` (INOUT `p_gst_tax_id` INT, IN `p_hsn_code_id` INT, IN `p_tax_type` VARCHAR(100), IN `p_tax` DECIMAL, IN `p_effective_date` DATE, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_gst_tax_id = (SELECT COALESCE(MAX(gst_tax_id),0) + 1 FROM tbl_gst_tax_detail);

                    insert into tbl_gst_tax_detail
                    (
                        gst_tax_id,
                            hsn_code_id,
                            tax_type,
                            tax,
                            effective_date
                    )
                    values
                    ( 
                        p_gst_tax_id,
                            p_hsn_code_id,
                            p_tax_type,
                            p_tax,
                            p_effective_date
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_gst_tax_detail
                
                SET
                        hsn_code_id= p_hsn_code_id,
                        tax_type= p_tax_type,
                        tax= p_tax,
                        effective_date= p_effective_date

                WHERE gst_tax_id= p_gst_tax_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_gst_tax_detail WHERE gst_tax_id= p_gst_tax_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_code_master_fillmodel` (IN `p_hsn_code_id` INT)   BEGIN
SELECT * 
       FROM `tbl_hsn_code_master` 
        WHERE hsn_code_id= p_hsn_code_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_code_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_code_master_transaction` (INOUT `p_hsn_code_id` INT, IN `p_hsn_code_name` VARCHAR(100), IN `p_description` VARCHAR(500), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_hsn_code_id = (SELECT COALESCE(MAX(hsn_code_id),0) + 1 FROM tbl_hsn_code_master);

                    insert into tbl_hsn_code_master
                    (
                        hsn_code_id,
                        hsn_code_name,
                        description,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_hsn_code_id,
                        p_hsn_code_name,
                        p_description,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_hsn_code_master
                
                SET
                        hsn_code_name= p_hsn_code_name,
                        description= p_description,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE hsn_code_id= p_hsn_code_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_hsn_code_master WHERE hsn_code_id= p_hsn_code_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_master_fillmodel` (IN `p_hsn_id` INT)   BEGIN
SELECT * 
       FROM `tbl_hsn_master` 
        WHERE hsn_id= p_hsn_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `hsn_master_transaction` (INOUT `p_hsn_id` INT, IN `p_hsn_code` VARCHAR(100), IN `p_description` VARCHAR(500), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_hsn_id = (SELECT COALESCE(MAX(hsn_id),0) + 1 FROM tbl_hsn_master);

                    insert into tbl_hsn_master
                    (
                        hsn_id,
                        hsn_code,
                        description,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_hsn_id,
                        p_hsn_code,
                        p_description,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_hsn_master
                
                SET
                        hsn_code= p_hsn_code,
                        description= p_description,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE hsn_id= p_hsn_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_hsn_master WHERE hsn_id= p_hsn_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_master_fillmodel` (IN `p_item_id` INT)   BEGIN
SELECT * 
       FROM `tbl_item_master` 
        WHERE item_id= p_item_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_master_transaction` (INOUT `p_item_id` INT, IN `p_item_name` VARCHAR(100), IN `p_item_gst` INT, IN `p_market_rate` DECIMAL, IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_item_id = (SELECT COALESCE(MAX(item_id),0) + 1 FROM tbl_item_master);

                    insert into tbl_item_master
                    (
                        item_id,
                        item_name,
                        item_gst,
                        market_rate,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_item_id,
                        p_item_name,
                        p_item_gst,
                        p_market_rate,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_item_master
                
                SET
                        item_name= p_item_name,
                        item_gst= p_item_gst,
                        market_rate= p_market_rate,
                        status= p_status,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE item_id= p_item_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_item_master WHERE item_id= p_item_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_preservation_price_list_master_fillmodel` (IN `p_item_preservation_price_list_id` INT)   BEGIN
SELECT * 
       FROM `tbl_item_preservation_price_list_master` 
        WHERE item_preservation_price_list_id= p_item_preservation_price_list_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_preservation_price_list_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `item_preservation_price_list_master_transaction` (INOUT `p_item_preservation_price_list_id` INT, IN `p_item_id` INT, IN `p_rent_kg_per_month` DECIMAL, IN `p_rent_per_kg` DECIMAL, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_item_preservation_price_list_id = (SELECT COALESCE(MAX(item_preservation_price_list_id),0) + 1 FROM tbl_item_preservation_price_list_master);

                    insert into tbl_item_preservation_price_list_master
                    (
                        item_preservation_price_list_id,
                        item_id,
                        rent_kg_per_month,
                        rent_per_kg,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_item_preservation_price_list_id,
                        p_item_id,
                        p_rent_kg_per_month,
                        p_rent_per_kg,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_item_preservation_price_list_master
                
                SET
                        item_id= p_item_id,
                        rent_kg_per_month= p_rent_kg_per_month,
                        rent_per_kg= p_rent_per_kg,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE item_preservation_price_list_id= p_item_preservation_price_list_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_item_preservation_price_list_master WHERE item_preservation_price_list_id= p_item_preservation_price_list_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_master_fillmodel` (IN `p_menu_id` INT)   BEGIN
SELECT * 
       FROM `tbl_menu_master` 
        WHERE menu_id= p_menu_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_master_transaction` (INOUT `p_menu_id` INT, IN `p_module_id` INT, IN `p_menu_name` VARCHAR(100), IN `p_menu_text` VARCHAR(100), IN `p_menu_url` VARCHAR(255), IN `p_tab_index` INT, IN `p_is_display` BIT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_menu_id = (SELECT COALESCE(MAX(menu_id),0) + 1 FROM tbl_menu_master);

                    insert into tbl_menu_master
                    (
                        menu_id,
                        module_id,
                        menu_name,
                        menu_text,
                        menu_url,
                        tab_index,
                        is_display
                    )
                    values
                    ( 
                        p_menu_id,
                        p_module_id,
                        p_menu_name,
                        p_menu_text,
                        p_menu_url,
                        p_tab_index,
                        p_is_display
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_menu_master
                
                SET
                        module_id= p_module_id,
                        menu_name= p_menu_name,
                        menu_text= p_menu_text,
                        menu_url= p_menu_url,
                        tab_index= p_tab_index,
                        is_display= p_is_display

                WHERE menu_id= p_menu_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_menu_master WHERE menu_id= p_menu_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_right_master_fillmodel` (IN `p_menu_right_id` INT)   BEGIN
SELECT * 
       FROM `tbl_menu_right_master` 
        WHERE menu_right_id= p_menu_right_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_right_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_right_master_transaction` (INOUT `p_menu_right_id` INT, IN `p_menu_id` INT, IN `p_right_name` CHAR(1), IN `p_right_text` VARCHAR(10), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_menu_right_id = (SELECT COALESCE(MAX(menu_right_id),0) + 1 FROM tbl_menu_right_master);

                    insert into tbl_menu_right_master
                    (
                        menu_right_id,
                        menu_id,
                        right_name,
                        right_text
                    )
                    values
                    ( 
                        p_menu_right_id,
                        p_menu_id,
                        p_right_name,
                        p_right_text
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_menu_right_master
                
                SET
                        menu_id= p_menu_id,
                        right_name= p_right_name,
                        right_text= p_right_text

                WHERE menu_right_id= p_menu_right_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_menu_right_master WHERE menu_right_id= p_menu_right_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `menu_search` ()   BEGIN
SELECT 
    m.module_text, 
    mm.menu_text,   
    mm.menu_url AS menu_link,
    mm.menu_group
FROM tbl_menu_master mm
INNER JOIN tbl_module_master m ON mm.module_id = m.module_id
WHERE mm.is_display = 1
ORDER BY m.tab_index, mm.tab_index, mm.menu_group, mm.menu_text;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `module_master_fillmodel` (IN `p_module_id` INT)   BEGIN
SELECT * 
       FROM `tbl_module_master` 
        WHERE module_id= p_module_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `module_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `module_master_transaction` (INOUT `p_module_id` INT, IN `p_module_name` VARCHAR(100), IN `p_module_text` VARCHAR(100), IN `p_tab_index` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_module_id = (SELECT COALESCE(MAX(module_id),0) + 1 FROM tbl_module_master);

                    insert into tbl_module_master
                    (
                        module_id,
                        module_name,
                        module_text,
                        tab_index
                    )
                    values
                    ( 
                        p_module_id,
                        p_module_name,
                        p_module_text,
                        p_tab_index
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_module_master
                
                SET
                        module_name= p_module_name,
                        module_text= p_module_text,
                        tab_index= p_tab_index

                WHERE module_id= p_module_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_module_master WHERE module_id= p_module_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `packing_unit_master_fillmodel` (IN `p_packing_unit_id` INT)   BEGIN
SELECT * 
       FROM `tbl_packing_unit_master` 
        WHERE packing_unit_id= p_packing_unit_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `packing_unit_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `packing_unit_master_transaction` (INOUT `p_packing_unit_id` INT, IN `p_packing_unit_name` VARCHAR(100), IN `p_conversion_factor` DECIMAL, IN `p_unloading_charge` DECIMAL, IN `p_loading_charge` DECIMAL, IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_packing_unit_id = (SELECT COALESCE(MAX(packing_unit_id),0) + 1 FROM tbl_packing_unit_master);

                    insert into tbl_packing_unit_master
                    (
                        packing_unit_id,
                        packing_unit_name,
                        conversion_factor,
                        unloading_charge,
                        loading_charge,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_packing_unit_id,
                        p_packing_unit_name,
                        p_conversion_factor,
                        p_unloading_charge,
                        p_loading_charge,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_packing_unit_master
                
                SET
                        packing_unit_name= p_packing_unit_name,
                        conversion_factor= p_conversion_factor,
                        unloading_charge= p_unloading_charge,
                        loading_charge= p_loading_charge,
                        status= p_status,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE packing_unit_id= p_packing_unit_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_packing_unit_master WHERE packing_unit_id= p_packing_unit_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `state_master_fillmodel` (IN `p_state_id` INT)   BEGIN
SELECT * 
       FROM `tbl_state_master` 
        WHERE state_id= p_state_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `state_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `state_master_transaction` (INOUT `p_state_id` INT, IN `p_state_name` VARCHAR(100), IN `p_country_id` INT, IN `p_gst_code` VARCHAR(100), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_state_id = (SELECT COALESCE(MAX(state_id),0) + 1 FROM tbl_state_master);

                    insert into tbl_state_master
                    (
                        state_id,
                        state_name,
                        country_id,
                        gst_code,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_state_id,
                        p_state_name,
                        p_country_id,
                        p_gst_code,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_state_master
                
                SET
                        state_name= p_state_name,
                        country_id= p_country_id,
                        gst_code= p_gst_code,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE state_id= p_state_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_state_master WHERE state_id= p_state_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_login` (IN `p_login_id` VARCHAR(100), IN `p_login_pass` VARCHAR(100))   BEGIN
    SELECT user_id, person_name 
    FROM tbl_user_master 
    WHERE login_id = p_login_id 
    AND login_pass = p_login_pass 
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_master_fillmodel` (IN `p_user_id` INT)   BEGIN
SELECT * 
       FROM `tbl_user_master` 
        WHERE user_id= p_user_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_master_transaction` (INOUT `p_user_id` INT, IN `p_login_id` VARCHAR(100), IN `p_login_pass` VARCHAR(100), IN `p_person_name` VARCHAR(100), IN `p_status` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_user_id = (SELECT COALESCE(MAX(user_id),0) + 1 FROM tbl_user_master);

                    insert into tbl_user_master
                    (
                        user_id,
                        login_id,
                        login_pass,
                        person_name,
                        status,
                        created_date,
                        created_by,
                        modified_date,
                        modified_by
                    )
                    values
                    ( 
                        p_user_id,
                        p_login_id,
                        p_login_pass,
                        p_person_name,
                        p_status,
                        p_created_date,
                        p_created_by,
                        p_modified_date,
                        p_modified_by
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_user_master
                
                SET
                        login_id= p_login_id,
                        login_pass= p_login_pass,
                        person_name= p_person_name,
                        status= p_status,
                        created_date= p_created_date,
                        created_by= p_created_by,
                        modified_date= p_modified_date,
                        modified_by= p_modified_by

                WHERE user_id= p_user_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_user_master WHERE user_id= p_user_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_right_master_fillmodel` (IN `p_user_right_id` INT)   BEGIN
SELECT * 
       FROM `tbl_user_right_master` 
        WHERE user_right_id= p_user_right_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_right_master_search` (IN `columns` VARCHAR(255), IN `tableName` VARCHAR(255))   BEGIN
                SET @sql = CONCAT("SELECT ", columns, " FROM ", tableName);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `user_right_master_transaction` (INOUT `p_user_right_id` INT, IN `p_user_id` INT, IN `p_menu_right_id` INT, IN `p_is_right` BIT, IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_user_right_id = (SELECT COALESCE(MAX(user_right_id),0) + 1 FROM tbl_user_right_master);

                    insert into tbl_user_right_master
                    (
                        user_right_id,
                        user_id,
                        menu_right_id,
                        is_right
                    )
                    values
                    ( 
                        p_user_right_id,
                        p_user_id,
                        p_menu_right_id,
                        p_is_right
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE tbl_user_right_master
                
                SET
                        user_id= p_user_id,
                        menu_right_id= p_menu_right_id,
                        is_right= p_is_right

                WHERE user_right_id= p_user_right_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  tbl_user_right_master WHERE user_right_id= p_user_right_id;
            
            END IF;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `view_gsttaxdetail_tax_type_fillmodel` (IN `p_id` INT)   BEGIN
SELECT * 
       FROM `view_gsttaxdetail_tax_type` 
        WHERE id= p_id;
        END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `view_gsttaxdetail_tax_type_transaction` (INOUT `p_id` INT, IN `p_value` VARCHAR(4), IN `TransactionMode` CHAR(1))   BEGIN
    
            IF TransactionMode = 'I' THEN
            
                    SET p_id = (SELECT COALESCE(MAX(id),0) + 1 FROM view_gsttaxdetail_tax_type);

                    insert into view_gsttaxdetail_tax_type
                    (
                        id,
                            value
                    )
                    values
                    ( 
                        p_id,
                            p_value
                    );
                
            ELSEIF TransactionMode = 'U' THEN
            
                UPDATE view_gsttaxdetail_tax_type
                
                SET
                        value= p_value

                WHERE id= p_id;
  
            ELSEIF TransactionMode = 'D' THEN
        
                DELETE FROM  view_gsttaxdetail_tax_type WHERE id= p_id;
            
            END IF;
        END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bank_master`
--

CREATE TABLE `tbl_bank_master` (
  `bank_id` int(11) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `account_no` varchar(100) DEFAULT NULL,
  `ifs_code` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_bank_master`
--

INSERT INTO `tbl_bank_master` (`bank_id`, `bank_name`, `branch_name`, `account_no`, `ifs_code`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'HDFCRb', 'Rajkot', '789545623154', '45D8741J42', NULL, '2025-04-05 12:23:41', 1, '2025-04-08 11:32:05', 1),
(2, 'HDFC', 'Andheri West', '00012366', 'XXXXX1234', 1, '2025-04-08 15:29:16', 1, '2025-04-08 15:29:16', 1),
(3, 'Bank Of Baroda', 'Rajkot', '00012377', 'XXXXX1234', 2, '2025-04-08 15:29:34', 1, '2025-04-08 15:31:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_chamber_master`
--

CREATE TABLE `tbl_chamber_master` (
  `chamber_id` int(11) NOT NULL,
  `chamber_name` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_chamber_master`
--

INSERT INTO `tbl_chamber_master` (`chamber_id`, `chamber_name`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'IcyFortress', '2025-04-05 11:10:23', 1, '2025-04-05 11:10:23', 1),
(2, 'Medical', '2025-04-08 10:49:51', 1, '2025-04-08 10:49:51', 1),
(3, 'Medical2', '2025-04-08 15:34:29', 1, '2025-04-08 15:34:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_city_master`
--

CREATE TABLE `tbl_city_master` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(100) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_city_master`
--

INSERT INTO `tbl_city_master` (`city_id`, `city_name`, `state_id`, `country_id`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'ahemdabad', 1, NULL, '2025-04-08 15:36:18', 1, '2025-04-08 15:36:18', 1),
(2, 'Dang', 2, NULL, '2025-04-10 15:10:17', 1, '2025-04-10 15:10:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_company_master`
--

CREATE TABLE `tbl_company_master` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(500) DEFAULT NULL,
  `company_code` varchar(100) DEFAULT NULL,
  `company_logo` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `pincode` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `web_address` varchar(100) DEFAULT NULL,
  `gstin` varchar(50) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `jurisdiction` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_company_master`
--

INSERT INTO `tbl_company_master` (`company_id`, `company_name`, `company_code`, `company_logo`, `address`, `city`, `pincode`, `state`, `phone`, `email`, `web_address`, `gstin`, `bank_id`, `jurisdiction`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, NULL, NULL, NULL, 'Main Street', 'Ahmedabad', '360002', 'Gujarat', '1234567890', 'example@email.com', 'www.example.com', '22ABCDE1234F1Z5', 1, 'Gujarat', '2025-04-07 11:26:59', 1, '2025-04-07 11:26:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_company_year_master`
--

CREATE TABLE `tbl_company_year_master` (
  `company_year_id` int(11) NOT NULL,
  `company_type` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_company_year_master`
--

INSERT INTO `tbl_company_year_master` (`company_year_id`, `company_type`, `start_date`, `end_date`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(4, '1', '2025-04-01', '2025-04-02', '2025-04-11 11:13:33', 1, '2025-04-11 11:13:33', 1),
(6, '1', '2025-04-01', '2025-04-26', '2025-04-11 11:14:54', 1, '2025-04-11 11:14:54', 1),
(7, '2', '2025-04-04', '2025-04-02', '2025-04-11 14:26:22', 1, '2025-04-11 14:26:43', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_contact_person_detail`
--

CREATE TABLE `tbl_contact_person_detail` (
  `contact_person_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `mobile` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_preference` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_country_master`
--

CREATE TABLE `tbl_country_master` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_country_master`
--

INSERT INTO `tbl_country_master` (`country_id`, `country_name`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'India', '2025-04-05 11:20:32', 1, '2025-04-05 11:20:53', 1),
(3, 'Francee', '2025-04-10 15:07:37', 1, '2025-04-10 15:07:37', 1),
(4, 'India', '2025-04-11 12:48:12', 1, '2025-04-11 12:48:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_currency_master`
--

CREATE TABLE `tbl_currency_master` (
  `currency_id` int(11) NOT NULL,
  `currency_symbol` varchar(100) DEFAULT NULL,
  `currency_name` varchar(100) DEFAULT NULL,
  `currency_in_paise` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_currency_master`
--

INSERT INTO `tbl_currency_master` (`currency_id`, `currency_symbol`, `currency_name`, `currency_in_paise`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, '₹', 'Indian Rupee', '1 Rupee = 100 Paise', '2025-04-05 15:04:46', 1, '2025-04-05 15:04:46', 1),
(2, '$', 'US Dollar', 'No Paise', '2025-04-05 15:16:27', 1, '2025-04-05 15:16:27', 1),
(3, '€', 'Euro', '€No Paise', '2025-04-05 15:17:32', 1, '2025-04-08 15:42:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_account_group_master`
--

CREATE TABLE `tbl_customer_account_group_master` (
  `customer_account_group_id` int(11) NOT NULL,
  `customer_account_group_name` varchar(100) DEFAULT NULL,
  `under_group` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_account_group_master`
--

INSERT INTO `tbl_customer_account_group_master` (`customer_account_group_id`, `customer_account_group_name`, `under_group`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, '1000 (Assets)', 'G:\\xampp\\htdocs\\csms1\\classes/../uploads/customer_account_group_master/purchase_order_summary__1_.cs', '2025-04-05 15:54:52', 1, '2025-04-05 15:54:52', 1),
(2, '10022(Assets)', '/uploads//customer_account_group_master/purchase_order_summary__1_.csv', '2025-04-08 15:43:55', 1, '2025-04-08 15:43:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_master`
--

CREATE TABLE `tbl_customer_master` (
  `customer_id` int(11) NOT NULL,
  `customer` varchar(100) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_type` int(11) DEFAULT NULL,
  `account_group_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `pincode` varchar(100) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email_id` varchar(100) DEFAULT NULL,
  `web_address` varchar(100) DEFAULT NULL,
  `gstin` varchar(100) DEFAULT NULL,
  `pan` varchar(100) DEFAULT NULL,
  `aadhar_no` varchar(100) DEFAULT NULL,
  `mandli_license_no` varchar(100) DEFAULT NULL,
  `fssai_license_no` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_wise_item_preservation_price_list_master`
--

CREATE TABLE `tbl_customer_wise_item_preservation_price_list_master` (
  `customer_wise_item_preservation_price_list_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `rent_kg_per_month` decimal(18,2) DEFAULT NULL,
  `rent_per_kg` decimal(18,3) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_wise_item_preservation_price_list_master`
--

INSERT INTO `tbl_customer_wise_item_preservation_price_list_master` (`customer_wise_item_preservation_price_list_id`, `customer_id`, `item_id`, `rent_kg_per_month`, `rent_per_kg`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, NULL, 1, 1.00, 12.000, '2025-04-08 14:31:17', 1, '2025-04-08 14:31:17', 1),
(2, NULL, 2, 800.00, 800.000, '2025-04-08 15:58:39', 1, '2025-04-08 15:58:39', 1),
(3, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-10 17:48:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_floor_master`
--

CREATE TABLE `tbl_floor_master` (
  `floor_id` int(11) NOT NULL,
  `floor_name` varchar(100) DEFAULT NULL,
  `chamber_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_floor_master`
--

INSERT INTO `tbl_floor_master` (`floor_id`, `floor_name`, `chamber_id`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, '1', 1, '2025-04-05 11:13:53', 1, '2025-04-05 11:13:53', 1),
(2, '2', 2, '2025-04-08 11:02:58', 1, '2025-04-08 11:02:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_generator_master`
--

CREATE TABLE `tbl_generator_master` (
  `generator_id` int(11) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `generator_options` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_generator_master`
--

INSERT INTO `tbl_generator_master` (`generator_id`, `table_name`, `generator_options`) VALUES
(20, 'tbl_issue_slip_master', '{\"field_name\":[\"issue_id\",\"issue_no\",\"issue_date\",\"special_note\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"number\",\"date\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Issue Id\",\"Issue No\",\"Issue Date\",\"Special Note\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"issue_no\",\"issue_date\",\"created_date\",\"modified_date\"],\"field_required\":[\"issue_no\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"issue_no\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"date\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(21, 'tbl_issue_slip_detail', '{\"field_name\":[\"issue_slip_detail_id\",\"issue_id\",\"item_id\",\"current_stock\",\"issue_qty\",\"unit_id\",\"item_type\",\"remark\"],\"field_type\":[\"hidden\",\"hidden\",\"select\",\"number\",\"number\",\"number\",\"select\",\"text\"],\"field_scale\":[\"0\",\"0\",\"0\",\"0\",\"0\",\"0\",\"\",\"\"],\"dropdown_table\":[\"\",\"\",\"tbl_item_master\",\"\",\"\",\"\",\"item_type_view\",\"\"],\"value_column\":[\"\",\"\",\"item_id\",\"\",\"\",\"\",\"item_type\",\"\"],\"label_column\":[\"\",\"\",\"item_name\",\"\",\"\",\"\",\"item_type\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Issue Slip Detail Id\",\"Issue Id\",\"Item Name\",\"Current Stock\",\"Issue Qty\",\"Unit Id\",\"Item Type\",\"Remark\"],\"field_display\":[\"item_id\",\"current_stock\",\"issue_qty\",\"unit_id\",\"item_type\",\"remark\"],\"field_required\":[\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"int\",\"int\",\"int\",\"varchar\",\"varchar\"]}'),
(23, 'tbl_purchase_order_master', '{\"field_name\":[\"purchase_order_id\",\"purchase_order_no\",\"date\",\"customer_id\",\"ref_no\",\"ref_date\",\"total_quantity\",\"total_amount\",\"special_note\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"number\",\"date\",\"select\",\"number\",\"date\",\"number\",\"number\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"0\",\"\",\"\",\"0\",\"0\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"tbl_customer_master\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"customer_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"customer_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Purchase Order Id\",\"Purchase Order No\",\"Date\",\"Customer Name\",\"Ref No\",\"Ref Date\",\"Total Quantity\",\"Total Amount\",\"Special Note\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"purchase_order_no\",\"date\",\"customer_id\",\"ref_no\",\"ref_date\",\"created_date\",\"modified_date\"],\"field_required\":[\"purchase_order_no\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"purchase_order_no\"],\"is_disabled\":[],\"after_detail\":[\"total_quantity\",\"total_amount\",\"special_note\"],\"field_data_type\":[\"int\",\"int\",\"date\",\"int\",\"varchar\",\"date\",\"int\",\"int\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(24, 'tbl_purchase_order_detail', '{\"field_name\":[\"purchase_order_detail_id\",\"purchase_order_id\",\"item_id\",\"unit\",\"po_qty\",\"rate\",\"amount\",\"remark\",\"pending_qty\"],\"field_type\":[\"hidden\",\"hidden\",\"select\",\"number\",\"number\",\"number\",\"number\",\"text\",\"number\"],\"field_scale\":[\"0\",\"0\",\"0\",\"0\",\"3\",\"2\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_item_master\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"item_id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"item_name\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Purchase Order Detail Id\",\"Purchase Order Id\",\"Item Id\",\"Unit\",\"Po Qty\",\"Rate\",\"Amount\",\"Remark\",\"Pending Qty\"],\"field_display\":[\"item_id\",\"unit\",\"po_qty\",\"rate\",\"amount\",\"remark\",\"pending_qty\"],\"field_required\":[\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"int\",\"decimal\",\"decimal\",\"int\",\"varchar\",\"int\"]}'),
(25, 'tbl_module_master', '{\"field_name\":[\"module_id\",\"module_name\",\"module_text\",\"tab_index\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\"],\"field_scale\":[\"0\",\"\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\"],\"field_label\":[\"Module Id\",\"Module Name\",\"Module Text\",\"Tab Index\"],\"field_display\":[\"module_name\",\"module_text\",\"tab_index\"],\"field_required\":[\"module_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"int\"]}'),
(26, 'tbl_menu_master', '{\"field_name\":[\"menu_id\",\"module_id\",\"menu_name\",\"menu_text\",\"menu_url\",\"tab_index\",\"is_display\"],\"field_type\":[\"hidden\",\"select\",\"text\",\"text\",\"hidden\",\"checkbox\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"\",\"0\",\"\"],\"dropdown_table\":[\"\",\"tbl_module_master\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"module_id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"module_name\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Menu Id\",\"Module Name\",\"Menu Name\",\"Menu Text\",\"Menu Url\",\"Is Display\",\"Is Display\"],\"field_display\":[\"module_id\",\"menu_name\",\"menu_text\",\"menu_url\",\"tab_index\",\"is_display\"],\"field_required\":[\"menu_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"menu_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"bit\"]}'),
(27, 'tbl_user_master', '{\"field_name\":[\"user_id\",\"login_id\",\"login_pass\",\"person_name\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"User Id\",\"User Name\",\"Password\",\"Person Name\",\"Want to Enable\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"login_id\",\"login_pass\",\"person_name\"],\"field_required\":[\"login_id\",\"login_pass\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"login_id\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(28, 'tbl_chamber_master', '{\"field_name\":[\"chamber_id\",\"chamber_name\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Chamber Id\",\"Chamber Name\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"chamber_name\"],\"field_required\":[\"chamber_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"chamber_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(29, 'tbl_floor_master', '{\"field_name\":[\"floor_id\",\"floor_name\",\"chamber_id\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_chamber_master\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"chamber_id\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"chamber_name\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Floor Id\",\"Floor Name\",\"Chamber Name\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"floor_name\",\"chamber_id\"],\"field_required\":[\"floor_name\",\"chamber_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(30, 'tbl_country_master', '{\"field_name\":[\"country_id\",\"country_name\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Country Id\",\"Country Name\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"country_name\",\"modified_date\",\"modified_by\"],\"field_required\":[\"country_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"country_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(31, 'tbl_state_master', '{\"field_name\":[\"state_id\",\"state_name\",\"country_id\",\"gst_code\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"select\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_country_master\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"country_id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"country_name\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"State Id\",\"State Name\",\"Country Name\",\"Gst Code\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"state_name\",\"country_id\",\"gst_code\",\"modified_date\",\"modified_by\"],\"field_required\":[\"state_name\",\"country_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"state_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(34, 'tbl_bank_master', '{\"field_name\":[\"bank_id\",\"bank_name\",\"branch_name\",\"account_no\",\"ifs_code\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Bank Id\",\"Bank Name\",\"Branch Name\",\"Account No\",\"Ifs Code\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"bank_name\",\"branch_name\",\"account_no\",\"ifs_code\",\"status\",\"modified_date\",\"modified_by\"],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(35, 'tbl_currency_master', '{\"field_name\":[\"currency_id\",\"currency_symbol\",\"currency_name\",\"currency_in_paise\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Currency Id\",\"Currency Symbol\",\"Currency Name\",\"Currency In Paise\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"currency_symbol\",\"currency_name\",\"currency_in_paise\",\"modified_date\",\"modified_by\"],\"field_required\":[\"currency_symbol\",\"currency_name\",\"currency_in_paise\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"currency_symbol\",\"currency_name\",\"currency_in_paise\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(36, 'tbl_item_master', '{\"field_name\":[\"item_id\",\"item_name\",\"item_gst\",\"market_rate\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"select\",\"number\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"2\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"view_item_master_item_gst\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"id\",\"\",\"id\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"value\",\"\",\"value\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Item Id\",\"Item \",\"Item GST\",\"Market Rate\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"item_name\",\"market_rate\",\"status\"],\"field_required\":[\"item_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"item_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"decimal\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(37, 'tbl_customer_account_group_master', '{\"field_name\":[\"customer_account_group_id\",\"customer_account_group_name\",\"under_group\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"file\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Account Group Id\",\"Account Group\",\"Under Group\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"customer_account_group_name\",\"under_group\",\"modified_date\",\"modified_by\"],\"field_required\":[\"customer_account_group_name\",\"under_group\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"customer_account_group_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(38, 'tbl_company_year_master', '{\"field_name\":[\"company_year_id\",\"company_type\",\"start_date\",\"end_date\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"select\",\"date\",\"date\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"view_company_year_master_company_type\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"value\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Company Id\",\"Company Type\",\"Start Date\",\"End Date\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"company_type\",\"start_date\",\"end_date\",\"modified_date\",\"modified_by\"],\"field_required\":[\"company_type\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"date\",\"date\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(40, 'tbl_gst_tax_detail', '{\"field_name\":[\"gst_tax_id\",\"hsn_code_id\",\"tax_type\",\"tax\",\"effective_date\"],\"field_type\":[\"hidden\",\"hidden\",\"select\",\"number\",\"date\"],\"field_scale\":[\"0\",\"0\",\"\",\"2\",\"\"],\"dropdown_table\":[\"\",\"\",\"view_gst_tax_detail_tax_type\",\"\",\"\"],\"value_column\":[\"\",\"\",\"id\",\"\",\"\"],\"label_column\":[\"\",\"\",\"value\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Tax Id\",\"Hsn Code\",\"Tax Type\",\"Tax\",\"Effective Date\"],\"field_display\":[\"tax_type\",\"tax\",\"effective_date\"],\"field_required\":[\"tax_type\",\"tax\",\"effective_date\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"decimal\",\"date\"]}'),
(45, 'tbl_hsn_master', '{\"field_name\":[\"hsn_id\",\"hsn_code\",\"description\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Hsn Id\",\"Hsn Code\",\"Description\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"hsn_code\",\"description\",\"modified_date\",\"modified_by\"],\"field_required\":[\"hsn_code\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"hsn_code\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(47, 'view_gsttaxdetail_tax_type', '{\"field_name\":[\"id\",\"value\"],\"field_type\":[\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\"],\"dropdown_table\":[\"\",\"view_gsttaxdetail_tax_type\"],\"value_column\":[\"\",\"value\"],\"label_column\":[\"\",\"value\"],\"where_condition\":[\"\",\"\"],\"field_label\":[\"Id\",\"Value\"],\"field_display\":[],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"varchar\"]}'),
(48, 'tbl_company_master', '{\"field_name\":[\"company_id\",\"company_name\",\"company_code\",\"company_logo\",\"address\",\"city\",\"pincode\",\"state\",\"phone\",\"email\",\"web_address\",\"gstin\",\"bank_id\",\"jurisdiction\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"file\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"select\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"0\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"tbl_bank_master\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"bank_id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"bank_name\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Company Id\",\"Company Name\",\"Company Code\",\"Company Logo\",\"Address\",\"City\",\"Pincode\",\"State\",\"Phone\",\"Email\",\"Web Address\",\"Gstin\",\"Bank Id\",\"Jurisdiction\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"company_name\",\"company_code\",\"company_logo\",\"address\",\"city\",\"pincode\",\"state\",\"phone\",\"email\",\"web_address\",\"gstin\",\"bank_id\",\"jurisdiction\",\"modified_date\",\"modified_by\"],\"field_required\":[\"company_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"text\",\"text\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(53, 'tbl_packing_unit_master', '{\"field_name\":[\"packing_unit_id\",\"packing_unit_name\",\"conversion_factor\",\"unloading_charge\",\"loading_charge\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"number\",\"number\",\"number\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"3\",\"2\",\"2\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"value\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Packing Unit Id\",\" Unit \",\"Conversion Factor\",\"Unloading Charge\",\"Loading Charge\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"packing_unit_name\",\"conversion_factor\",\"status\"],\"field_required\":[\"packing_unit_name\",\"conversion_factor\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"packing_unit_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"decimal\",\"decimal\",\"decimal\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(54, 'tbl_menu_right_master', '{\"field_name\":[\"menu_right_id\",\"menu_id\",\"right_name\",\"right_text\"],\"field_type\":[\"hidden\",\"select\",\"text\",\"text\"],\"field_scale\":[\"0\",\"0\",\"\",\"\"],\"dropdown_table\":[\"\",\"tbl_menu_master\",\"\",\"\"],\"value_column\":[\"\",\"menu_id\",\"\",\"\"],\"label_column\":[\"\",\"menu_text\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\"],\"field_label\":[\"Menu Right Id\",\"Menu Id\",\"Right Name\",\"Right Text\"],\"field_display\":[\"menu_id\",\"right_name\",\"right_text\"],\"field_required\":[\"menu_id\",\"right_name\",\"right_text\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"char\",\"varchar\"]}'),
(55, 'tbl_user_right_master', '{\"field_name\":[\"user_right_id\",\"user_id\",\"menu_right_id\",\"is_right\"],\"field_type\":[\"hidden\",\"select\",\"select\",\"checkbox\"],\"field_scale\":[\"0\",\"0\",\"0\",\"\"],\"dropdown_table\":[\"\",\"tbl_user_master\",\"tbl_menu_right_master\",\"\"],\"value_column\":[\"\",\"user_id\",\"menu_right_id\",\"\"],\"label_column\":[\"\",\"login_id\",\"menu_right_id\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\"],\"field_label\":[\"User Right Id\",\"User Id\",\"Menu Right Id\",\"Is Right\"],\"field_display\":[\"user_id\",\"menu_right_id\",\"is_right\"],\"field_required\":[\"user_id\",\"menu_right_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"bit\"]}'),
(56, 'tbl_item_preservation_price_list_master', '{\"field_name\":[\"item_preservation_price_list_id\",\"item_id\",\"rent_kg_per_month\",\"rent_per_kg\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"select\",\"number\",\"number\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"2\",\"3\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"tbl_item_master\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"item_id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"item_name\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Item Preservation Price List Id\",\"Item Name\",\"Rent \\/ Kg. \\/ Month\",\"Rent \\/ Kg.\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"item_id\",\"rent_kg_per_month\",\"rent_per_kg\",\"modified_date\",\"modified_by\"],\"field_required\":[\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"decimal\",\"decimal\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(58, 'tbl_hsn_code_master', '{\"field_name\":[\"hsn_code_id\",\"hsn_code_name\",\"description\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Hsn Code Id\",\"Hsn Code\",\"Description\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"hsn_code_name\",\"description\"],\"field_required\":[\"hsn_code_name\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"hsn_code_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(77, 'tbl_contact_person_detail', '{\"field_name\":[\"contact_person_id\",\"customer_id\",\"contact_person_name\",\"mobile\",\"email\",\"contact_preference\"],\"field_type\":[\"hidden\",\"hidden\",\"text\",\"number\",\"email\",\"select\"],\"field_scale\":[\"0\",\"0\",\"\",\"\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"\",\"\",\"view_contact_person_detail_contect_preference\"],\"value_column\":[\"\",\"\",\"\",\"\",\"\",\"id\"],\"label_column\":[\"\",\"\",\"\",\"\",\"\",\"value\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Contact Person Id\",\"Customer Id\",\"Contact Person Name\",\"Mobile\",\"Email\",\"Contact Preference\"],\"field_display\":[\"contact_person_name\",\"mobile\",\"email\"],\"field_required\":[],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"field_data_type\":[\"int\",\"int\",\"varchar\",\"varchar\",\"varchar\",\"int\"]}'),
(85, 'tbl_customer_master', '{\"field_name\":[\"customer_id\",\"customer\",\"customer_name\",\"customer_type\",\"account_group_id\",\"address\",\"city_id\",\"pincode\",\"state_id\",\"country_id\",\"phone\",\"email_id\",\"web_address\",\"gstin\",\"pan\",\"aadhar_no\",\"mandli_license_no\",\"fssai_license_no\",\"status\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"text\",\"select\",\"select\",\"text\",\"select\",\"text\",\"text\",\"number\",\"email\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"text\",\"select\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"\",\"0\",\"0\",\"\",\"0\",\"\",\"0\",\"0\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"\",\"view_customer_master_customer_type\",\"tbl_customer_account_group_master\",\"\",\"tbl_city_master\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"view_status_type\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"\",\"id\",\"customer_account_group_id\",\"\",\"city_id\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"id\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"\",\"value\",\"customer_account_group_name\",\"\",\"city_name\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"value\",\"value\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Customer Id\",\"Customer\",\"Customer Name\",\"Customer Type\",\"Account Group Name\",\"Address\",\"City Name\",\"Pincode\",\"State Name\",\"Country Name\",\"Phone\",\"Email Id\",\"Web Address\",\"Gstin\",\"Pan\",\"Aadhar No\",\"Mandli License No\",\"Fssai License No\",\"Status\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"customer\",\"customer_name\",\"customer_type\",\"account_group_id\",\"city_id\",\"state_id\",\"country_id\",\"phone\",\"email_id\",\"gstin\",\"pan\",\"aadhar_no\",\"status\"],\"field_required\":[\"customer\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"customer\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"varchar\",\"int\",\"int\",\"text\",\"int\",\"varchar\",\"int\",\"int\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"varchar\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(87, 'tbl_customer_wise_item_preservation_price_list_master', '{\"field_name\":[\"customer_wise_item_preservation_price_list_id\",\"customer_id\",\"item_id\",\"rent_kg_per_month\",\"rent_per_kg\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"select\",\"select\",\"number\",\"number\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"0\",\"0\",\"2\",\"3\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"tbl_customer_master\",\"tbl_item_master\",\"\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"customer_id\",\"item_id\",\"\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"customer_name\",\"item_name\",\"\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"Customer Wise Item Preservation Price List Id\",\"Customer Name\",\"Item Name\",\"Rent Kg Per Month\",\"Rent Per Kg\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"customer_id\",\"item_id\",\"rent_kg_per_month\",\"rent_per_kg\",\"modified_date\",\"modified_by\"],\"field_required\":[\"customer_id\",\"item_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"int\",\"int\",\"decimal\",\"decimal\",\"datetime\",\"int\",\"datetime\",\"int\"]}'),
(88, 'tbl_city_master', '{\"field_name\":[\"city_id\",\"city_name\",\"state_id\",\"country_id\",\"created_date\",\"created_by\",\"modified_date\",\"modified_by\"],\"field_type\":[\"hidden\",\"text\",\"select\",\"text\",\"hidden\",\"hidden\",\"hidden\",\"hidden\"],\"field_scale\":[\"0\",\"\",\"0\",\"0\",\"\",\"0\",\"\",\"0\"],\"dropdown_table\":[\"\",\"\",\"tbl_state_master\",\"\",\"\",\"\",\"\",\"\"],\"value_column\":[\"\",\"\",\"state_id\",\"\",\"\",\"\",\"\",\"\"],\"label_column\":[\"\",\"\",\"state_name\",\"\",\"\",\"\",\"\",\"\"],\"where_condition\":[\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"],\"field_label\":[\"City Id\",\"City Name\",\"State Name\",\"Country Name\",\"Created Date\",\"Created By\",\"Modified Date\",\"Modified By\"],\"field_display\":[\"city_name\",\"state_id\",\"country_id\"],\"field_required\":[\"city_name\",\"state_id\"],\"allow_zero\":[],\"allow_minus\":[],\"chk_duplicate\":[\"city_name\"],\"is_disabled\":[],\"after_detail\":[],\"field_data_type\":[\"int\",\"varchar\",\"int\",\"int\",\"datetime\",\"int\",\"datetime\",\"int\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gst_tax_detail`
--

CREATE TABLE `tbl_gst_tax_detail` (
  `gst_tax_id` int(11) NOT NULL,
  `hsn_code_id` int(11) NOT NULL,
  `tax_type` varchar(100) DEFAULT NULL,
  `tax` decimal(18,2) DEFAULT NULL,
  `effective_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_gst_tax_detail`
--

INSERT INTO `tbl_gst_tax_detail` (`gst_tax_id`, `hsn_code_id`, `tax_type`, `tax`, `effective_date`) VALUES
(1, 1, NULL, 12.00, '2025-04-05'),
(2, 1, NULL, 2132.00, '2025-04-07'),
(3, 1, NULL, 778.00, '2025-04-24'),
(4, 2, NULL, 13434.00, '2025-04-12'),
(5, 2, NULL, 13.00, '2025-04-11'),
(6, 2, NULL, 13434.00, '2025-04-05'),
(7, 2, NULL, 12.00, '2025-04-06'),
(8, 2, NULL, 13434.00, '2025-04-11'),
(9, 1, NULL, 1234567890.00, '2025-04-19'),
(10, 1, '2', 1234567890.00, '2025-04-06'),
(11, 3, '2', 12.00, '2025-04-01'),
(12, 4, NULL, 90.00, '2025-04-06'),
(13, 4, '3', 9.00, '2025-04-06');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hsn_code_master`
--

CREATE TABLE `tbl_hsn_code_master` (
  `hsn_code_id` int(11) NOT NULL,
  `hsn_code_name` varchar(100) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_hsn_code_master`
--

INSERT INTO `tbl_hsn_code_master` (`hsn_code_id`, `hsn_code_name`, `description`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, '1', 'Bags', '2025-04-08 17:36:36', 1, '2025-04-11 11:00:51', 1),
(2, '2', 'Boxx', '2025-04-10 12:44:34', 1, '2025-04-11 11:00:44', 1),
(3, '1', 'Bags', '2025-04-11 14:39:27', 1, '2025-04-11 14:39:27', 1),
(4, '5', 'watch', '2025-04-11 14:41:37', 1, '2025-04-11 14:42:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_master`
--

CREATE TABLE `tbl_item_master` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `item_gst` int(11) DEFAULT NULL,
  `market_rate` decimal(18,2) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_master`
--

INSERT INTO `tbl_item_master` (`item_id`, `item_name`, `item_gst`, `market_rate`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'pen', 0, 10.00, 1, '2025-04-05 15:37:23', 1, '2025-04-08 14:18:50', 1),
(2, 'bottle', 0, 30.00, 1, '2025-04-08 12:29:10', 1, '2025-04-08 14:19:32', 1),
(3, 'box', 0, 10.00, 2, '2025-04-08 12:35:03', 1, '2025-04-10 11:53:01', 1),
(4, 'laptop', 0, 10.00, NULL, '2025-04-08 14:19:45', 1, '2025-04-08 14:19:45', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_preservation_price_list_master`
--

CREATE TABLE `tbl_item_preservation_price_list_master` (
  `item_preservation_price_list_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `rent_kg_per_month` decimal(18,2) DEFAULT NULL,
  `rent_per_kg` decimal(18,3) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_preservation_price_list_master`
--

INSERT INTO `tbl_item_preservation_price_list_master` (`item_preservation_price_list_id`, `item_id`, `rent_kg_per_month`, `rent_per_kg`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 1, 12.00, 12.000, '2025-04-08 12:24:19', 1, '2025-04-08 12:24:19', 1),
(2, 2, 200.00, 150.000, '2025-04-08 12:30:11', 1, '2025-04-08 12:30:11', 1),
(3, 4, 12.00, 12.000, '2025-04-10 15:31:04', 1, '2025-04-10 15:31:04', 1),
(4, NULL, 12.00, 12.000, '2025-04-11 11:54:58', 1, '2025-04-11 11:54:58', 1),
(5, NULL, 33.00, 56.000, '2025-04-11 11:55:10', 1, '2025-04-11 11:55:10', 1),
(6, NULL, NULL, NULL, '2025-04-11 12:50:01', 1, '2025-04-11 12:50:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu_master`
--

CREATE TABLE `tbl_menu_master` (
  `menu_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `menu_name` varchar(100) DEFAULT NULL,
  `menu_text` varchar(100) DEFAULT NULL,
  `menu_url` varchar(255) DEFAULT NULL,
  `menu_group` int(11) DEFAULT NULL,
  `tab_index` int(11) DEFAULT NULL,
  `is_display` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_menu_master`
--

INSERT INTO `tbl_menu_master` (`menu_id`, `module_id`, `menu_name`, `menu_text`, `menu_url`, `menu_group`, `tab_index`, `is_display`) VALUES
(1, 1, 'country', 'Country', 'srh_country_master.php', 4, 1, b'1'),
(2, 1, 'state', 'State', 'srh_state_master.php', 4, 2, b'1'),
(3, 1, 'city', 'City', 'srh_city_master.php', 4, 3, b'1'),
(4, 1, 'currency', 'Currency', 'srh_currency_master.php', 8, 4, b'1'),
(5, 1, 'bank', 'Bank', 'srh_bank_master.php', 8, 5, b'1'),
(6, 1, 'customer_account_group', 'Customer Account Group', 'srh_customer_account_group_master.php', 6, 6, b'1'),
(7, 1, 'customer', 'Customer', 'srh_customer_master.php', 6, 7, b'1'),
(8, 1, 'packing_unit', 'Packing Unit', 'srh_packing_unit_master.php', 7, 8, b'1'),
(9, 1, 'item', 'Item', 'srh_item_master.php', 7, 9, b'1'),
(10, 1, 'chamber', 'Chamber', 'srh_chamber_master.php', 5, 10, b'1'),
(11, 1, 'floor', 'Floor', 'srh_floor_master.php', 5, 11, b'1'),
(12, 1, 'item_preservation_price_list', 'Item Preservation Price List', 'srh_item_preservation_price_list_master.php', 7, 12, b'1'),
(13, 1, 'customer_wise_item_preservation_price_list', 'Customer wise Item Preservation Price List', 'srh_customer_wise_item_preservation_price_list_master.php', 7, 13, b'1'),
(14, 1, 'hsn_code', 'HSN Code', 'srh_hsn_code_master.php', 8, 14, b'1'),
(15, 2, 'inward', 'Inward', 'srh_inward_master.php', 14, 15, b'1'),
(16, 2, 'outward', 'Outward', 'srh_outward_master.php', 14, 16, b'1'),
(17, 2, 'invoice', 'Invoice', 'srh_invoice_master.php', 14, 17, b'1'),
(18, 3, 'generate_rent_invoice', 'Generate Rent Invoice', 'srh_generate_rent_invoice_master.php', 15, 18, b'1'),
(19, 3, 'multi_invoice_print', 'Multi Invoice Print', 'srh_multi_invoice_print_master.php', 15, 19, b'1'),
(20, 3, 'inward_lock/unlock', 'Inward Lock / Unlock', 'srh_inward_lock/unlock_master.php', 15, 20, b'1'),
(21, 3, 'change_location', 'Change Location', 'srh_change_location_master.php', 15, 21, b'1'),
(22, 4, 'inward_summary', 'Inward Summary', 'srh_inward_summary_master.php', 9, 22, b'1'),
(23, 4, 'outward_summary', 'Outward Summary', 'srh_outward_summary_master.php', 9, 23, b'1'),
(24, 4, 'invoice_summary', 'Invoice Summary', 'srh_invoice_summary_master.php', 10, 24, b'1'),
(25, 4, 'invoice_gst_summary', 'Invoice GST Summary', 'srh_invoice_gst_summary_master.php', 10, 25, b'1'),
(26, 4, 'inward_outward_summary', 'Inward Outward Summary', 'srh_inward_outward_summary_master.php', 9, 26, b'1'),
(27, 4, 'rent_valuation', 'Rent Valuation', 'srh_rent_valuation_master.php', 13, 27, b'1'),
(28, 4, 'party_wise_inward_balance', 'Party wise Inward Balance', 'srh_party_wise_inward_balance_master.php', 9, 28, b'1'),
(29, 4, 'item_stock', 'Item Stock', 'srh_item_stock_master.php', 11, 29, b'1'),
(30, 4, 'item_stock_statement', 'Item Stock Statement', 'srh_item_stock_statement_master.php', 11, 30, b'1'),
(31, 4, 'lot_statement', 'Lot Statement', 'srh_lot_statement_master.php', 12, 31, b'1'),
(32, 4, 'lot_transfer_history', 'Lot Transfer History', 'srh_lot_transfer_history_master.php', 12, 32, b'1'),
(33, 4, 'location_detail_view', 'Location Detail View', 'srh_location_detail_view_master.php', 13, 33, b'1'),
(34, 4, 'item_preservation_charges_list', 'Item Preservation Charges List', 'srh_item_preservation_charges_list_master.php', 11, 34, b'1'),
(35, 4, 'yearly_stock_report', 'Yearly Stock Report', 'srh_yearly_stock_report_master.php', 11, 35, b'1'),
(36, 4, 'location_change_history', 'Location Change History', 'srh_location_change_history_master.php', 13, 36, b'1'),
(37, 5, 'receipt', 'Receipt', 'srh_receipt_master.php', 16, 37, b'1'),
(38, 5, 'payment', 'Payment', 'srh_payment_master.php', 16, 38, b'1'),
(39, 5, 'contra', 'Contra', 'srh_contra_master.php', 17, 39, b'1'),
(40, 5, 'journal', 'Journal', 'srh_journal_master.php', 17, 40, b'1'),
(41, 5, 'day_book', 'Day Book', 'srh_day_book_master.php', 18, 41, b'1'),
(42, 5, 'account_ledger', 'Account Ledger', 'srh_account_ledger_master.php', 18, 42, b'1'),
(43, 5, 'net_payable_outstanding', 'Net Payable Outstanding', 'srh_net_payable_outstanding_master.php', 19, 43, b'1'),
(44, 5, 'net_receivable_outstanding', 'Net Receivable Outstanding', 'srh_net_receivable_outstanding_master.php', 19, 44, b'1'),
(45, 6, 'user', 'User', 'srh_user_master.php', 1, 45, b'1'),
(46, 6, 'user_right', 'User Right Access', 'srh_user_right_master.php', 1, 46, b'1'),
(47, 6, 'company_year', 'Company Year', 'srh_company_year_master.php', 2, 47, b'1'),
(48, 6, 'company', 'Company', 'srh_company_master.php', 2, 48, b'1'),
(49, 6, 'module', 'Module', 'srh_module_master.php', 3, 49, b'1'),
(51, 6, 'menu', 'Menu', 'srh_menu_master.php', 3, 50, b'1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu_right_master`
--

CREATE TABLE `tbl_menu_right_master` (
  `menu_right_id` int(11) NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `right_name` char(1) DEFAULT NULL,
  `right_text` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_menu_right_master`
--

INSERT INTO `tbl_menu_right_master` (`menu_right_id`, `menu_id`, `right_name`, `right_text`) VALUES
(1, 1, 'D', 'Delete'),
(2, 4, 'E', 'Edit'),
(3, 1, 'V', 'View'),
(4, 4, 'A', 'add');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_module_master`
--

CREATE TABLE `tbl_module_master` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(100) DEFAULT NULL,
  `module_text` varchar(100) DEFAULT NULL,
  `tab_index` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_module_master`
--

INSERT INTO `tbl_module_master` (`module_id`, `module_name`, `module_text`, `tab_index`) VALUES
(1, 'master', 'Master', 1),
(2, 'transaction', 'Transaction', 2),
(3, 'utilities', 'Utilities', 3),
(4, 'report', 'Report', 4),
(5, 'accounting', 'Accounting', 5),
(6, 'admin', 'Admin', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_packing_unit_master`
--

CREATE TABLE `tbl_packing_unit_master` (
  `packing_unit_id` int(11) NOT NULL,
  `packing_unit_name` varchar(100) DEFAULT NULL,
  `conversion_factor` decimal(18,3) DEFAULT NULL,
  `unloading_charge` decimal(18,2) DEFAULT NULL,
  `loading_charge` decimal(18,2) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_packing_unit_master`
--

INSERT INTO `tbl_packing_unit_master` (`packing_unit_id`, `packing_unit_name`, `conversion_factor`, `unloading_charge`, `loading_charge`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'Box', 1.000, 12.00, 2.00, 1, '2025-04-08 16:29:17', 1, '2025-04-08 16:29:17', 1),
(2, 'bag', 3.000, 6.00, 7.00, 2, '2025-04-08 16:29:27', 1, '2025-04-08 16:29:27', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_state_master`
--

CREATE TABLE `tbl_state_master` (
  `state_id` int(11) NOT NULL,
  `state_name` varchar(100) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `gst_code` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_state_master`
--

INSERT INTO `tbl_state_master` (`state_id`, `state_name`, `country_id`, `gst_code`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'Gujarat', 1, '12', '2025-04-05 11:22:10', 1, '2025-04-11 11:10:56', 1),
(2, 'goa', 3, '2', '2025-04-10 15:08:30', 1, '2025-04-11 14:35:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_master`
--

CREATE TABLE `tbl_user_master` (
  `user_id` int(11) NOT NULL,
  `login_id` varchar(100) DEFAULT NULL,
  `login_pass` varchar(100) DEFAULT NULL,
  `person_name` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_master`
--

INSERT INTO `tbl_user_master` (`user_id`, `login_id`, `login_pass`, `person_name`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'admin', 'admin', 'bhumita radadiya', 0, '2025-04-03 14:17:09', 1, '2025-04-03 14:17:09', 1),
(2, 'nidhi', 'nidhi', 'Admin', 1, '2025-04-05 10:49:40', 1, '2025-04-05 11:19:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_right_master`
--

CREATE TABLE `tbl_user_right_master` (
  `user_right_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `menu_right_id` int(11) DEFAULT NULL,
  `is_right` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_right_master`
--

INSERT INTO `tbl_user_right_master` (`user_right_id`, `user_id`, `menu_right_id`, `is_right`) VALUES
(1, 1, NULL, NULL),
(2, 1, NULL, NULL),
(3, 2, NULL, NULL),
(4, 1, NULL, NULL),
(5, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_company_year_master_company_type`
-- (See below for the actual view)
--
CREATE TABLE `view_company_year_master_company_type` (
`id` int(1)
,`value` varchar(7)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_contact_person_detail_contect_preference`
-- (See below for the actual view)
--
CREATE TABLE `view_contact_person_detail_contect_preference` (
`id` int(1)
,`value` varchar(8)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_customer_master_customer_type`
-- (See below for the actual view)
--
CREATE TABLE `view_customer_master_customer_type` (
`id` int(1)
,`value` varchar(8)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_gst_tax_detail_tax_type`
-- (See below for the actual view)
--
CREATE TABLE `view_gst_tax_detail_tax_type` (
`id` int(1)
,`value` varchar(4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_item_master_item_gst`
-- (See below for the actual view)
--
CREATE TABLE `view_item_master_item_gst` (
`id` int(1)
,`value` varchar(18)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_status_type`
-- (See below for the actual view)
--
CREATE TABLE `view_status_type` (
`id` int(1)
,`value` varchar(8)
);

-- --------------------------------------------------------

--
-- Structure for view `view_company_year_master_company_type`
--
DROP TABLE IF EXISTS `view_company_year_master_company_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_company_year_master_company_type`  AS SELECT 1 AS `id`, 'Forward' AS `value`union all select 2 AS `id`,'Reverse' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_contact_person_detail_contect_preference`
--
DROP TABLE IF EXISTS `view_contact_person_detail_contect_preference`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_contact_person_detail_contect_preference`  AS SELECT 1 AS `id`, 'WhatsApp' AS `value`union all select 2 AS `id`,'Email' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_customer_master_customer_type`
--
DROP TABLE IF EXISTS `view_customer_master_customer_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_customer_master_customer_type`  AS SELECT 1 AS `id`, 'Customer' AS `value`union all select 2 AS `id`,'Broker' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_gst_tax_detail_tax_type`
--
DROP TABLE IF EXISTS `view_gst_tax_detail_tax_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_gst_tax_detail_tax_type`  AS SELECT 1 AS `id`, 'SGST' AS `value`union all select 2 AS `id`,'CGST' AS `value` union all select 3 AS `id`,'IGST' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_item_master_item_gst`
--
DROP TABLE IF EXISTS `view_item_master_item_gst`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_item_master_item_gst`  AS SELECT 1 AS `id`, 'GST Applicable' AS `value`union all select 2 AS `id`,'GST Exempted' AS `value` union all select 3 AS `id`,'GST Not Applicable' AS `value`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_status_type`
--
DROP TABLE IF EXISTS `view_status_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_status_type`  AS SELECT 1 AS `id`, 'Active' AS `value`union all select 2 AS `id`,'Deactive' AS `value`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_bank_master`
--
ALTER TABLE `tbl_bank_master`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `bank_master_created_by` (`created_by`),
  ADD KEY `bank_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  ADD PRIMARY KEY (`chamber_id`),
  ADD KEY `chamber_master_created_by` (`created_by`),
  ADD KEY `chamber_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `city_master_country_id` (`country_id`),
  ADD KEY `city_master_state_id` (`state_id`),
  ADD KEY `city_master_created_by` (`created_by`),
  ADD KEY `city_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_company_master`
--
ALTER TABLE `tbl_company_master`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `company_master_bank_id` (`bank_id`),
  ADD KEY `company_master_created_by` (`created_by`),
  ADD KEY `company_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_company_year_master`
--
ALTER TABLE `tbl_company_year_master`
  ADD PRIMARY KEY (`company_year_id`),
  ADD KEY `company_year_master_created_by` (`created_by`),
  ADD KEY `company_year_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_contact_person_detail`
--
ALTER TABLE `tbl_contact_person_detail`
  ADD PRIMARY KEY (`contact_person_id`),
  ADD KEY `contact_person_detail_customer_id` (`customer_id`);

--
-- Indexes for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  ADD PRIMARY KEY (`country_id`),
  ADD KEY `country_master_created_by` (`created_by`),
  ADD KEY `country_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_currency_master`
--
ALTER TABLE `tbl_currency_master`
  ADD PRIMARY KEY (`currency_id`),
  ADD KEY `currency_master_created_by` (`created_by`),
  ADD KEY `currency_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_customer_account_group_master`
--
ALTER TABLE `tbl_customer_account_group_master`
  ADD PRIMARY KEY (`customer_account_group_id`),
  ADD KEY `customer_account_group_master_created_by` (`created_by`),
  ADD KEY `customer_account_group_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `customer_master_country_id` (`country_id`),
  ADD KEY `customer_master_city_id` (`city_id`),
  ADD KEY `customer_master_customer_account_group_id` (`account_group_id`),
  ADD KEY `customer_master_created_by` (`created_by`),
  ADD KEY `customer_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_customer_wise_item_preservation_price_list_master`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_master`
  ADD PRIMARY KEY (`customer_wise_item_preservation_price_list_id`),
  ADD KEY `customer_wise_item_preservation_price_list_master_customer_id` (`customer_id`),
  ADD KEY `customer_wise_item_preservation_price_list_master_item_id` (`item_id`),
  ADD KEY `customer_wise_item_preservation_price_list_master_created_by` (`created_by`),
  ADD KEY `customer_wise_item_preservation_price_list_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  ADD PRIMARY KEY (`floor_id`),
  ADD KEY `floor_master_chamber_id` (`chamber_id`),
  ADD KEY `floor_master_created_by` (`created_by`),
  ADD KEY `floor_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_generator_master`
--
ALTER TABLE `tbl_generator_master`
  ADD PRIMARY KEY (`generator_id`);

--
-- Indexes for table `tbl_gst_tax_detail`
--
ALTER TABLE `tbl_gst_tax_detail`
  ADD PRIMARY KEY (`gst_tax_id`),
  ADD KEY `hsn_id` (`hsn_code_id`);

--
-- Indexes for table `tbl_hsn_code_master`
--
ALTER TABLE `tbl_hsn_code_master`
  ADD PRIMARY KEY (`hsn_code_id`),
  ADD KEY `hsn_code_master_created_by` (`created_by`),
  ADD KEY `hsn_code_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `item_master_created_by` (`created_by`),
  ADD KEY `item_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_item_preservation_price_list_master`
--
ALTER TABLE `tbl_item_preservation_price_list_master`
  ADD PRIMARY KEY (`item_preservation_price_list_id`),
  ADD KEY `item_preservation_price_list_master_item_id` (`item_id`),
  ADD KEY `item_preservation_price_list_master_created_by` (`created_by`),
  ADD KEY `item_preservation_price_list_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_menu_master`
--
ALTER TABLE `tbl_menu_master`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `menu_master_module_id` (`module_id`);

--
-- Indexes for table `tbl_menu_right_master`
--
ALTER TABLE `tbl_menu_right_master`
  ADD PRIMARY KEY (`menu_right_id`);

--
-- Indexes for table `tbl_module_master`
--
ALTER TABLE `tbl_module_master`
  ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `tbl_packing_unit_master`
--
ALTER TABLE `tbl_packing_unit_master`
  ADD PRIMARY KEY (`packing_unit_id`),
  ADD KEY `packing_unit_master_created_by` (`created_by`),
  ADD KEY `packing_unit_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  ADD PRIMARY KEY (`state_id`),
  ADD KEY `state_master_country_id` (`country_id`),
  ADD KEY `state_master_created_by` (`created_by`),
  ADD KEY `state_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_master_created_by` (`created_by`),
  ADD KEY `user_master_modified_by` (`modified_by`);

--
-- Indexes for table `tbl_user_right_master`
--
ALTER TABLE `tbl_user_right_master`
  ADD PRIMARY KEY (`user_right_id`),
  ADD KEY `menu` (`menu_right_id`),
  ADD KEY `user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_bank_master`
--
ALTER TABLE `tbl_bank_master`
  MODIFY `bank_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  MODIFY `chamber_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_company_master`
--
ALTER TABLE `tbl_company_master`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_company_year_master`
--
ALTER TABLE `tbl_company_year_master`
  MODIFY `company_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_contact_person_detail`
--
ALTER TABLE `tbl_contact_person_detail`
  MODIFY `contact_person_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_currency_master`
--
ALTER TABLE `tbl_currency_master`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_customer_account_group_master`
--
ALTER TABLE `tbl_customer_account_group_master`
  MODIFY `customer_account_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_customer_wise_item_preservation_price_list_master`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_master`
  MODIFY `customer_wise_item_preservation_price_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  MODIFY `floor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_generator_master`
--
ALTER TABLE `tbl_generator_master`
  MODIFY `generator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `tbl_gst_tax_detail`
--
ALTER TABLE `tbl_gst_tax_detail`
  MODIFY `gst_tax_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_hsn_code_master`
--
ALTER TABLE `tbl_hsn_code_master`
  MODIFY `hsn_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_item_preservation_price_list_master`
--
ALTER TABLE `tbl_item_preservation_price_list_master`
  MODIFY `item_preservation_price_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_menu_master`
--
ALTER TABLE `tbl_menu_master`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `tbl_module_master`
--
ALTER TABLE `tbl_module_master`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_packing_unit_master`
--
ALTER TABLE `tbl_packing_unit_master`
  MODIFY `packing_unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_bank_master`
--
ALTER TABLE `tbl_bank_master`
  ADD CONSTRAINT `bank_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `bank_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  ADD CONSTRAINT `chamber_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `chamber_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  ADD CONSTRAINT `city_master_country_id` FOREIGN KEY (`country_id`) REFERENCES `tbl_country_master` (`country_id`),
  ADD CONSTRAINT `city_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `city_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `city_master_state_id` FOREIGN KEY (`state_id`) REFERENCES `tbl_state_master` (`state_id`);

--
-- Constraints for table `tbl_company_master`
--
ALTER TABLE `tbl_company_master`
  ADD CONSTRAINT `company_master_bank_id` FOREIGN KEY (`bank_id`) REFERENCES `tbl_bank_master` (`bank_id`),
  ADD CONSTRAINT `company_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `company_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_company_year_master`
--
ALTER TABLE `tbl_company_year_master`
  ADD CONSTRAINT `company_year_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `company_year_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_contact_person_detail`
--
ALTER TABLE `tbl_contact_person_detail`
  ADD CONSTRAINT `contact_person_detail_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `tbl_customer_master` (`customer_id`);

--
-- Constraints for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  ADD CONSTRAINT `country_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `country_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_currency_master`
--
ALTER TABLE `tbl_currency_master`
  ADD CONSTRAINT `currency_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `currency_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_customer_account_group_master`
--
ALTER TABLE `tbl_customer_account_group_master`
  ADD CONSTRAINT `customer_account_group_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_account_group_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  ADD CONSTRAINT `customer_master_city_id` FOREIGN KEY (`city_id`) REFERENCES `tbl_city_master` (`city_id`),
  ADD CONSTRAINT `customer_master_country_id` FOREIGN KEY (`country_id`) REFERENCES `tbl_country_master` (`country_id`),
  ADD CONSTRAINT `customer_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_master_customer_account_group_id` FOREIGN KEY (`account_group_id`) REFERENCES `tbl_customer_account_group_master` (`customer_account_group_id`),
  ADD CONSTRAINT `customer_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_master_state_id` FOREIGN KEY (`state_id`) REFERENCES `tbl_state_master` (`state_id`);

--
-- Constraints for table `tbl_customer_wise_item_preservation_price_list_master`
--
ALTER TABLE `tbl_customer_wise_item_preservation_price_list_master`
  ADD CONSTRAINT `customer_wise_item_preservation_price_list_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `customer_wise_item_preservation_price_list_master_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `tbl_customer_master` (`customer_id`),
  ADD CONSTRAINT `customer_wise_item_preservation_price_list_master_item_id` FOREIGN KEY (`item_id`) REFERENCES `tbl_item_master` (`item_id`),
  ADD CONSTRAINT `customer_wise_item_preservation_price_list_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  ADD CONSTRAINT `floor_master_chamber_id` FOREIGN KEY (`chamber_id`) REFERENCES `tbl_chamber_master` (`chamber_id`),
  ADD CONSTRAINT `floor_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `floor_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_gst_tax_detail`
--
ALTER TABLE `tbl_gst_tax_detail`
  ADD CONSTRAINT `gst_tax_detail_hsn_code_id` FOREIGN KEY (`hsn_code_id`) REFERENCES `tbl_hsn_code_master` (`hsn_code_id`);

--
-- Constraints for table `tbl_hsn_code_master`
--
ALTER TABLE `tbl_hsn_code_master`
  ADD CONSTRAINT `hsn_code_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `hsn_code_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  ADD CONSTRAINT `item_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `item_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_item_preservation_price_list_master`
--
ALTER TABLE `tbl_item_preservation_price_list_master`
  ADD CONSTRAINT `item_preservation_price_list_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `item_preservation_price_list_master_item_id` FOREIGN KEY (`item_id`) REFERENCES `tbl_item_master` (`item_id`),
  ADD CONSTRAINT `item_preservation_price_list_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_menu_master`
--
ALTER TABLE `tbl_menu_master`
  ADD CONSTRAINT `menu_master_module_id` FOREIGN KEY (`module_id`) REFERENCES `tbl_module_master` (`module_id`);

--
-- Constraints for table `tbl_packing_unit_master`
--
ALTER TABLE `tbl_packing_unit_master`
  ADD CONSTRAINT `packing_unit_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `packing_unit_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  ADD CONSTRAINT `state_master_country_id` FOREIGN KEY (`country_id`) REFERENCES `tbl_country_master` (`country_id`),
  ADD CONSTRAINT `state_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `state_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  ADD CONSTRAINT `user_master_created_by` FOREIGN KEY (`created_by`) REFERENCES `tbl_user_master` (`user_id`),
  ADD CONSTRAINT `user_master_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `tbl_user_master` (`user_id`);

--
-- Constraints for table `tbl_user_right_master`
--
ALTER TABLE `tbl_user_right_master`
  ADD CONSTRAINT `menu` FOREIGN KEY (`menu_right_id`) REFERENCES `tbl_menu_right_master` (`menu_right_id`),
  ADD CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `tbl_user_master` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
