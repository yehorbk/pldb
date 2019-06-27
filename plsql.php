<?php
    // Global variables
    
    $GLOBALS['currentDataBase'];
    $GLOBALS['fileDataBase'];
    $GLOBALS['fileSQLQuery'];

    ////////////////////////////////////////////////// METHODS //////////////////////////////////////////////////

    // Method for sending queries
    function sendQuery($request) {
        $res = "Bad query";
        if (isset($request)) {
            $SQLCommands = array('CREATE DATABASE', 'USE', 'CREATE TABLE', 'INSERT INTO', 'SHOW DATABASES', 'SHOW TABLES', 'DROP DATABASE', 'DROP TABLE', 'SELECT');
            $res = $request;
            foreach ($SQLCommands as $item) {
                if(strpos($request, $item) > -1)
                {
                    callMethod($request, $item, strpos($request, $item));
                }
            }
        }
        return $res;
    }

    // Method for calling SQL commands
    function callMethod($request, $command, $commandPosition)
    {
        switch ($command) {
            case 'CREATE DATABASE':
                CreateDataBase($request);
                break;
            case 'CREATE TABLE':
                CreateTable($request, $commandPosition);
                break;
            case 'USE':
                UseDataBase($request, $commandPosition);
                break;
            case 'INSERT INTO':
                InsertIntoTable($request, $commandPosition);
                break;
            case 'SELECT':
                SelectDataFromTable($request, $commandPosition);
                break;
            default:
                # code...
                break;
        }
    }

    // Create database method
    function CreateDataBase($request) {
        // Получить строку комманды через substr
        $firstEnter = strpos($request, '`');
        $lastEnter = strrpos($request, '`');
        trim($request);
        
        $GLOBALS['currentDataBase'] = substr($request, $firstEnter + 1, $lastEnter - $firstEnter - 1);
        $GLOBALS['fileDataBase'] = $GLOBALS['currentDataBase'].".plsql";
        $GLOBALS['fileSQLQuery'] = $GLOBALS['currentDataBase'].".sql";
       
        if (!file_exists($GLOBALS['fileDataBase'])) {
            $fp = fopen($GLOBALS['fileDataBase'], "w");
            fwrite($fp, "EmptyFile");
            fclose($fp);
        }
        if (!file_exists($GLOBALS['fileSQLQuery'])) {
            $fp = fopen($GLOBALS['fileSQLQuery'], "w");
            fwrite($fp, $res);
            fclose($fp);
        }
    }

    // Select (USE) database method
    function UseDataBase($request, $commandPosition) {
        $request = substr($request, $commandPosition);
        $request = substr($request, 0, strpos($request, ';') + 1);
        $firstEnter = strpos($request, '`');
        $lastEnter = strrpos($request, '`');
        trim($request);
        $GLOBALS['currentDataBase'] = substr($request, $firstEnter + 1, $lastEnter - $firstEnter - 1);
        $GLOBALS['fileDataBase'] = $GLOBALS['currentDataBase'].".plsql";
        $GLOBALS['fileSQLQuery'] = $GLOBALS['currentDataBase'].".sql";
        
    }

    // Create Table method
    function CreateTable($request, $commandPosition) {
        
        $request = substr($request, $commandPosition);
        $request = substr($request, 0, strpos($request, ';') + 1);
        

        $tableName = substr($request, strpos($request, '`') + 1);
        $tableName = substr($tableName, 0, strpos($tableName, '`'));
        
        if(file_exists($GLOBALS['fileSQLQuery'])) {
            $fp = fopen($GLOBALS['fileSQLQuery'], "a");
            fwrite($fp, $request);
            fclose($fp);
        }
        if (file_exists($GLOBALS['fileDataBase'])) {
            $request = substr($request, strpos($request, '('), strpos($request, ';'));
            
            $tableRows = array();
            $counter = 0;
            while(strpos($request, '`') > 0){
                $request = substr($request, strpos($request, '`') + 1);
                
                $rowName = substr($request, 0, strpos($request, '`'));
                $rowType = substr($request, strpos($request, $rowName) + strlen($rowName) + 1);
                $rowType = trim($rowType);
                $rowType = substr($rowType, 0, strpos($rowType, ' '));
                if(strpos($rowType, ',') > 0) $rowType = substr($rowType, 0, strpos($rowType, ','));
                
                $tableRows[$rowName] = $rowType;

                $request = substr($request, strpos($request, '`') + 1);
                $counter++;
            }
            $fp = fopen($GLOBALS['fileDataBase'], "a");
            
            fwrite($fp, "\n".$tableName." (");
            foreach($tableRows as $key => $value) 
            { 
                fwrite($fp, $key.": ".$value);
                if($tableRows[$key] != end($tableRows)) fwrite($fp, ", ");
                echo end($tableRows);
            } 

            fwrite($fp, ") {Data_".$tableName."};");
            fclose($fp);
        }
    }

    function InsertIntoTable($request, $commandPosition) {
       
        $request = substr($request, $commandPosition);
        $request = substr($request, 0, strpos($request, ';') + 1);
        
        if(file_exists($GLOBALS['fileSQLQuery'])) {
            $fp = fopen($GLOBALS['fileSQLQuery'], "a");
            fwrite($fp, $request);
            fclose($fp);
            
        }
        if (file_exists($GLOBALS['fileDataBase'])) {
            //echo $request;
            
            $tableName = substr($request, strpos($request, '`') + 1);
            $tableName = substr($tableName, 0, strpos($tableName, '`'));
            $data = substr($request, strpos($request, "VALUES"));
            $data = substr($data, strpos($data, '('));
            $data = str_replace('(', '{', $data);
            $data = str_replace(')', '}', $data);
            $data = str_replace(';', ', ', $data);
            // Дописать получение всей строки таблицы и дозапись данных.
           
            $DataBaseContent = file_get_contents($GLOBALS['fileDataBase']);
            $DataBaseContent = str_replace('Data_'.$tableName, $data.'Data_'.$tableName, $DataBaseContent);
            $fp = fopen($GLOBALS['fileDataBase'], "w");
            fwrite($fp, $DataBaseContent);
            fclose($fp);
            //echo $fp;
            
        }
    }

    function SelectDataFromTable($request, $commandPosition) {
        $request = substr($request, $commandPosition);
        $request = substr($request, 0, strpos($request, ';') + 1);
        
        if(file_exists($GLOBALS['fileSQLQuery'])) {
            $fp = fopen($GLOBALS['fileSQLQuery'], "a");
            fwrite($fp, $request);
            fclose($fp);
            
        }

        if (file_exists($GLOBALS['fileDataBase'])) {
            
            $tableName = substr($request, strpos($request, '`') + 1);
            $tableName = substr($tableName, 0, strpos($tableName, '`'));
            $DataBaseContent = file_get_contents($GLOBALS['fileDataBase']);
            $Content = substr($DataBaseContent, strpos($DataBaseContent, $tableName));
            $Content = substr($Content, 0, strpos($Content, ';'));
            $Content = substr($Content, strpos($Content, '{'));
            $Content = str_replace(', Data_'.$tableName, '', $Content);
            echo $Content;
            
        }
    }
    
    
// GARBAGE:
 //echo $request;
 //echo substr($request, 0, strpos($request, '`'))."<br>";
 //echo $request;

 //echo $tableName;
 //echo "</br>";
 //$GLOBALS['fileDataBase'] = $GLOBALS['currentDataBase'].".plsql";
 //$GLOBALS['fileSQLQuery'] = $GLOBALS['currentDataBase'].".sql";
 //echo "Hello";
 //echo $GLOBALS['currentDataBase'];
 //echo "<br>".$request;

 //echo $GLOBALS['currentDataBase'];

 //echo $GLOBALS['fileDataBase'];
 //echo $GLOBALS['fileSQLQuery'];

 //echo $first;
 //echo $last;

 //echo "In IF";
 //echo $item;

 /*fwrite($fp, "\n".$tableName." { \n");
   foreach ($tableRows as $item) {
        fwrite($fp, "\t".$item." { }, \n");
    }
   fwrite($fp, "};\n");*/
  //echo $tableRows[$counter];
 
  /*foreach ($tableRows as $item) {
                fwrite($fp, $item.": datatype");
                if($item != end($tableRows)) fwrite($fp, ", ");
            }*/
 //$tableRows[$counter] = substr($request, 0, strpos($request, '`'));
                //if(strpos($rowType, ',') > 0) $rowType = substr($rowType, 0, strlen($rowType) - 2);
                //$rowType = str_replace(',', '', $rowType);
                //$rowType = str_replace('\n', '1', $rowType);
 //echo $rowType;
?>



