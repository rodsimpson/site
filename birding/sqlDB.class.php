<?php
/***************************************************************************************
* @desc  Class: sqlDB - a database abstraction layer for use with MySql
*
* @author Roderick Simpson <rfsimpson@gmail.com>
* @version version 1.4 - Copyright 2005 Roderick Simpson
* @package sqlDB
*
* @copyright Copyright &copy; 2006, Roderick Simpson - all rights reserved.
*
* sqlDB usage:
*
* The first thing that needs to be done is to initialize the sqlDB object.  This is done
* as follows:
*
*     //include the sqlDB.php file and initialize the object
*     require_once ROOT_PATH.'includes/db/sqlDB.class.php';
*     $sqlDB = new sqlDB(DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_HOSTNAME, DB_DEBUG_OUTPUT);
*
* The next thing to do is to initialize the log files.  This can be done two ways.  The
* first way will make sure the file exists, but opens it on every execution at least once
* the second way assumes your log file is in place and writeable.
*
*     //Method 1, with file checking:
*     $sqlDB->setErrorLogFile("C:\logs\this_site\error_log.txt");
*     $sqlDB->setQueryLogFile("C:\logs\this_site\sql_log.txt");
*
*     //Method 2, just turn on logging
*     $sqlDB->startErrorLogging();
*     $sqlDB->startQueryLogging();
*
*     //It is also possible to turn off either error or query logging
*     $sqlDB->stopErrorLogging();
*     $sqlDB->stopQueryLogging();
*
* The next step is to set the way the results should be returned.  There are 3 possibile
* return types:
*
*     1. Objected keyed: where each result in the return array is an object.
*     2. Index keyed: where each item returned is the data type as it was stored in
*        the database, but the array is indexed by the column names
*     3. Number keyed: where each item returned is the data type as it was stored in
*        the database, but the array is indexed sequential numbers
*
* To specify the return types use the following function calls (the class will use only one
* type at a time - the most recent call)
*
*     $sqlDB->setObjectKeyed(); // for object keyed
*     $sqlDB->setIndexKeyed(); // for index keyed
*     $sqlDB->setNumberKeyed(); //for numeric keyed
*
* At this point, we are ready to run a query.  This can be done by calling one of three methods.
*
*     1. getRows($query) - This is the primary function for selecting rows from the database.  It
*           will return rows based on the formatting styles listed above
*     2. getRow($query, 0) - This function is similar to getRows, except that it only returns
*           one row.  The row returned is always the first one, unless the optional second
*           argument specifies otherwise.
*     3. execute($query); - This function should be used for all updates, deletes, and inserts.  It
*           will execute the query and return the number of rows affected.
*
*
* Examples:
*     The following are examples of usage:
*
*
*     1. Select all the results from the users table as objects
*
*        $sqlDB->setObjectKeyed();
*        $query = $sqlDB->escapeString("SELECT * FROM users"); // optional
*        $results = $sqlDB->getRows($query);
*        foreach ( $results as $result )
*        {
*           // Access data using object syntax
*           echo $result->id;
*           echo $result->username;
*           echo $result->password;
*        }
*
*     2. Select a single row from the user table
*
*        $sqlDB->setObjectKeyed();
*        $query = $sqlDB->escapeString("SELECT * FROM users where user_id=23"); // optional
*        $result = $sqlDB->getRow($query);
*        // we can now access the objects directly
*        echo $result->id;
*        echo $result->quote;
*        foreach ( $results as $result )
*
*     3. Insert a user into the table
*
*
*/

class sqlDB
{
   // instance vars
   private $dbconnection_handle;          // handle to the database connection
   private $log_queries = false;           // bool to tell if queries are to be logged to a file
   private $query_log_file = '';          // filename + path to log queries to
   private $display_errors = false;       // bool - if we should show errors on the screen or not
   private $log_errors = false;           // bool - if we should try to log errors to a file
   private $trigger_errors = false;       // bool - if we should trigger errors

   // query specific vars
   private $how_called = '';              // stores how the current query is called
   private $returned_rows = 0;            // the number of rows returned
   private $affected_rows = 0;            // keeps track of the number of affected rows
   private $insert_id = null;             // keeps track of the last insert id
   private $bool_query_success = false;   // bool to keep track of if there was an error during the last query
   private $calling_function = '';        // the calling function - used for logging

   private $cached_query = '';            // the last query that was run
   private $result = null;                // stores the last result that was returned
   private $number_keyed_array = false;   // how the results will be returned - number keyed
   private $index_keyed_array = false;    // how the results will be returned - index keyed
   private $object_keyed_array = true;    // how the results will be returned - object keyed
   
   private $has_errors = false;           // bool to mark that an error occured
   private $error_message = null;         // an array to hold any error messages

   /***************************************************************************************
   * @desc Constructor - creates connection, then selects the database
   *
   * @param string $username
   * @param string $password
   * @param string $database_name
   * @param string $host
   * @param bool $debug
   *
   * @return true on success, false on failure
   */
   public function __construct($username, $password, $database_name, $host)
   {                         
      //make the connection to the database
                       
      $this->dbconnection_handle =  @mysql_connect($host, $username, $password);
      if ( !$this->dbconnection_handle )
      {
          $this->setError("sqlDB::Function: __construct - Error making sql connection:".mysql_error());
          return false;
      }
         //if the connection is succesful, select our database
      $this->selectDatabase($database_name);
      return true;      
   }

   function __destruct()
   {
      //after reading the documentation, we have elected not to call mysql_close, as it will be closed when the script is
      //done executing anyway, and the whole point of this class is to open the connection one time in the begining of
      //the page execution, then close it when execution is done.  That way we are not opening and re-opening the
      //connection to the db.
   }

   public function setError( $message )
   {
      $this->has_errors = true;
      $this->error_message[] = $message;      
   }
   
   public function getError()
   {
      return $this->has_errors;      
   }
   
   public function getErrorMessages()
   {
      return $this->error_message;      
   }
   
   
   
   
   /***************************************************************************************
   * @desc selectDatabase - selects the specified database
   *
   * @param string $database_name - the name of the database to connect to
   * @return true on success, the error on failure
   */
   public function selectDatabase($database_name)
   {
      //try to connect to the specified database
      try {
         if ( !@mysql_select_db($database_name, $this->dbconnection_handle) )
         {
            throw new SubsystemException ('Error selecting DB: '.mysql_error(), 0);
         }
      }
      catch( SubsystemException $e ) {
         Errors::saveErrors( $e->getMessage(), $e->getExceptionData());
      }
   }

   /***************************************************************************************
   * @desc escapeString - prepares a query by striping slashes, etc.
   *
   * @param string $string
   * @return the escaped string
   */
   public function escapeString($string)
   {
      return mysql_real_escape_string(stripslashes($string));
   }

   /***************************************************************************************
   * @desc makeStringSafe - modifes a string to make it safe from SQL injection
   *
   * Usage: pass the string to be modified as the first parameter.  Optionally, you can
   *        pass the second parameter to specify what type the string should be.  options
   *        include "text", "numeric", or "currency".  If the parameter is not specified,
   *        the function will trim whitespace and quotes, and remove all dangerous words.
   *
   * @param $input_string - the query string
   * @param $input_type - (optional)  'numeric' to verify if it is a number
   * @return $output_string - the modified "safe" string
   */
   public function makeStringSafe($input_string, $input_type='')
   {
      //make sure input is escaped
      if ( get_magic_quotes_gpc() )
      {
         //strip out the slashes if magic is on
         $input_string = stripslashes($input_string);
      }
      //put the slashes back in for mysql
      $input_string = mysql_real_escape_string($input_string);

      //strip the whitespace
      $output_string = trim($input_string);
      //replace all bad words
      //$bad_characters = array("select ", "drop ", "--", "insert ", "delete ", "explain ");
      //$output_string = str_ireplace($bad_characters, "", $output_string);

      switch ( $input_type )
      {
         case "numeric":
            //first trim any extra whitespace
            $output_string = trim($input_string);
            //test to see if it is numeric, if not just set it to zero
            if (!is_numeric($output)) $output = 0;
            break;
      }
      return $output_string;
   }

   /***************************************************************************************
   * @desc getNumberOfReturnedRows - returns number of returned rows
   *
   * Usage: This function makes data ready to display by pulling any extra slashes out.
   *
   * @param $input_string - the query string
   * @return the cleaned string
   */
   public function makeDataReadyToDisplay($input_string)
   {
       $input_string = stripslashes($input_string);
       return $input_string;
   }

   /***************************************************************************************
   * @desc getNumberOfReturnedRows - returns number of returned rows
   *
   * Usage: This function will return the number of returned rows that resulted from
   *        the last query.  This function does not return the number of affected rows
   *        for an update/insert.
   *
   * @return number of returned rows
   */
   public function getNumberOfReturnedRows()
   {
      return $this->returned_rows;
   }

   /***************************************************************************************
   * @desc getNumberOfAffectedRows - returns the number of affected rows
   *
   * Usage: This function will return the number of affected rows that resulted from
   *        the last insert/update.  This function does not return the number of rows in a
   *        result set for a select.
   *
   * @return number of affected rows
   */
   public function getNumberOfAffectedRows()
   {
      return $this->affected_rows;
   }

   /***************************************************************************************
   * @desc checkForErrors - returns true if an error occured during the last query,
   *        and false if not.
   *
   *
   * Usage: This function should be used after an update, to verify if there was an error
   *
   * @return bool if there was an error
   */
   public function checkQuerySuccess()
   {
      return $this->bool_query_success;
   }

   /***************************************************************************************
   * @desc getNumberOfAffectedRows - returns the number of affected rows
   *
   * Usage: This function will return the number of affected rows that resulted from
   *        the last insert/update.  This function does not return the number of rows in a
   *        result set for a select.
   *
   * @return number of affected rows
   */
   public function getInsertID()
   {
      if ($this->insert_id) { return $this->insert_id; }
      else return false;
   }

   /***************************************************************************************
   * @desc clearCache - clears the cache
   *
   * Usage: A private function to clear out all cached values in the object to prepare
   *        for the next query.
   */
   private function clearCache()
   {
      // clear any cached results
      $this->returned_rows = 0;
      $this->affected_rows = 0;
      $this->cached_query = '';
      $this->result = null;
      $this->cached_results = null;
      $this->bool_query_success = false;
   }

   public function startTransaction()
   {
      $query = "START TRANSACTION";
      $this->execute($query);
   }

   public function commitTransaction()
   {
      $query = "COMMIT";
      return $this->execute($query);
   }

   public function rollbackTransaction()
   {
      $query = "ROLLBACK";
      $this->execute($query);
   }
   /***************************************************************************************
   * @desc execute - the main function for running a query
   *
   * Usage: This function is the one that actually does the work of executing queries.
   *        It can be called directly or via one of the helper functions like getRow()
   *        or getRows().
   */
   public function execute($query)
   {
      $this->returned_rows=0;
      $query = trim($query); // trim reg expressions if needed
      $rows_affected = 0; // the return value - the number of rows affected
      $this->clearCache(); // clear out any current results
      $this->cached_query = $query; // log this query for later use
      if (!$this->how_called)
      {
         $this->how_called = 'execute'; // log the function call

         //log the calling function
         $backtrace = debug_backtrace();
         $this->calling_function = isset($backtrace[1]['function'])?$backtrace[1]['function']:'';
      }
      $start_time = microtime(true);
      $this->bool_query_success = false;

      // execute the query
      $this->result = @mysql_query($query, $this->dbconnection_handle);
      $end_time = microtime(true);
      $query_time = $end_time - $start_time;

      // grab any errors that occured
      $error = mysql_error();
      if ( $error )
      {
         $this->bool_query_success = false;
         $error_number = mysql_errno($this->dbconnection_handle);
         die ('Error executing query in sqlDB: '.$error_number.': '.$error);         
      } else { $this->bool_query_success = true; }
  
     
      $this->logQuery($query, $this->bool_query_success, $query_time);
      $this->how_called = false; // erase the function call log

      // if the query was an insert, delete, update, or replace
      if ( preg_match("/^(insert|delete|update|replace)\s+/i",$query) )
      {
         // figure out and log the number of rows affected
         $this->affected_rows = mysql_affected_rows();

         // log the insert id
         if ( preg_match("/^(insert|replace)\s+/i",$query) )
         {
            $this->insert_id = mysql_insert_id($this->dbconnection_handle);
         }
      }
      else if ( preg_match("/^(select)\s+/i",$query) ) //see if the query was a select
      {
         // cache the results from the query
         while ( $row = @mysql_fetch_object($this->result) )
         {
            $this->cached_results[$this->returned_rows] = $row;
            $this->returned_rows++;
         }
         // then store the info for each column
         $i=0;
         while ( $i < @mysql_num_fields($this->result) )
         {
            $this->column_info[$i] = @mysql_fetch_field($this->result);
            $i++;
         }
         // free the resource
         @mysql_free_result($this->result);
      }
      else //the query was a transaction or commit
      {
      }
      // return whether there was an error or not
      return $this->bool_query_success;
   }

   /***************************************************************************************
   * @desc setNumberKeyed - sets the results to be returned keyed by a number
   *
   * Usage: Call this function to specify that a result set should be returned where the
   *        index for the array is numerical.
   */
   public function setNumberKeyed()
   {
       $this->number_keyed_array = true;
       $this->index_keyed_array = false;
       $this->object_keyed_array = false;
   }
   /***************************************************************************************
   * @desc setIndexKeyed - sets the results to be returned keyed by the column name
   *
   * Usage: Call this function to specify that a result set should be returned where the
   *        index for the array is based on the column name.
   */
   public function setIndexKeyed()
   {
       $this->number_keyed_array = false;
       $this->index_keyed_array = true;
       $this->object_keyed_array = false;
   }
   /***************************************************************************************
   * @desc setNumberKeyed - sets the results to returned where each row is an object
   *
   * Usage: Call this function to specify that a result set should be returned where the
   *        index for the array is an object.
   */
   public function setObjectKeyed()
   {
       $this->number_keyed_array = false;
       $this->index_keyed_array = false;
       $this->object_keyed_array = true;
   }

   /***************************************************************************************
   * @desc getRows - returns all the rows from a query
   *
   * Usage: Returns all the rows in a given query if one is specified, or from the last
   *        query if one is not specified.  Additionally, the function will format the
   *        results based on the keying type (numeric, index, or object), as set by
   *        calling either setNumberKeyed(),  setIndexKeyed(),  or setObjectKeyed().
   */
   public function getRows($query=null)
   {
      $this->how_called = 'getRows';  // log the function call

      //log the calling function
      $backtrace = debug_backtrace();
      $this->calling_function = isset($backtrace[1]['function'])?$backtrace[1]['function']:'';

      if ( $query ) // if a query has been specified, execute
      {
         $result = $this->execute($query);
         if ($result === false) return false; // there was an error
         $this->num_results = $result;
      }
      if ( $this->object_keyed_array )
      {
         return $this->cached_results; //returns each row as an object
      }
      // if they want an index or a number keyed array, make sure there are results
      if ( count($this->cached_results) > 0 )
      {
         $i=0;
         $return_array = array();
         foreach( $this->cached_results as $row )
         {
            $return_array[$i] = get_object_vars($row);
            if ( $this->number_keyed_array )
            {
               $return_array[$i] = array_values($return_array[$i]);
            }
            $i++;
         }
         return $return_array;
      }
      // otherwise, just return null
      return null;
   }

   /***************************************************************************************
   * @desc getRow - returns a single row from a query
   *
   * Usage: Returns the first row generated by the specified query, or the row number as
   *        specified byt the $row_number argument.  If no query is specified, it will use
   *        the results from the last query that was run.  Like getRows(), the function
   *        formats the results based on the keying type (numeric, index, or object), as
   *        set by calling either setNumberKeyed(),  setIndexKeyed(),  or setObjectKeyed().
   */
   public function getRow($query=null,$row_number=0)
   {
      $this->how_called = 'getRow'; // log the function call

      //log the calling function
      $backtrace = debug_backtrace();
      $this->calling_function = isset($backtrace[1]['function'])?$backtrace[1]['function']:'';

      if ( $query ) // if a query has been specified, execute
      {
         $result = $this->execute($query);
         if ($result === false) return false; // there was an error
         $this->num_results = $result;
      }
      if ( $this->object_keyed_array  ) // return an array of objects
      {
         return $this->cached_results[$row_number]?$this->cached_results[$row_number]:null;
      }
      elseif ( $this->number_keyed_array ) //return an array keyed by incremental numbers
      {
         return $this->cached_results[$row_number]?array_values(get_object_vars($this->cached_results[$row_number])):null;
      }
      elseif ( $this->index_keyed_array ) //return an array keyed by column indexes
      {
         return $this->cached_results[$row_number]?get_object_vars($this->cached_results[$row_number]):null;
      }

   }

   /***************************************************************************************
   * @desc logQuery - logs the result of the query to a trace file (error or success)
   *
   */
   private function logQuery($query, $bool_query_success, $query_time)
   {
      if ($this->log_queries  && $query_time > .5)
      {
         $query_time = number_format($query_time, 5);
         $calling_function = str_pad($this->calling_function, 40);
         $calling_function = substr($calling_function, 0, 30);
         //info to write:
         $date = date('Y-m-d h:i:s A');
         $caller = $this->how_called;
         $output = "$query_time\t\t".$calling_function."\t\t$date\t\t$caller\t\t$query";
         $order  = array("\r\n", "\n", "\r");
         $output = str_replace($order, '', $output);
         $output .= " \r\n"; //stick a newline on the end

         if ($bool_query_success) { $log_file = $this->query_log_file.date('Ymd').'_sql_log.txt'; }
         else { $log_file = $this->query_log_file.'error_'.date('Ymd').'_sql_log.txt'; }

         try {
            if ( !($file_handle = @fopen($log_file, 'a')) )
            {
               die ('Function: logQuery - File '.$log_file.' could not be opened for SQL logging.');
            }
            if ( !@fwrite($file_handle, $output) )
            {
              die ('Function: logQuery - File '.$log_file.' could not be written to for SQL logging.');
            }
            //close the handle to the file
            fclose($file_handle);
         } catch( SubsystemException $e ) {
            // the file did not exist, so log the error
            $this->logError($e->getMessage(), $e->getExceptionData());
         }
      }
   }

   /***************************************************************************************
   * @desc startQueryLogging - turns on query logging, does not make sure the file exits
   *
   */
   public function startQueryLogging()
   {
      $this->log_queries = true;
   }
   /***************************************************************************************
   * @desc stopQueryLogging - turns off query logging
   *
   */
   public function stopQueryLogging()
   {
      $this->log_queries = false;
   }

   /***************************************************************************************
   * @desc setQueryLogFile - specifes the query log file to be used
   *
   * Usage: To turn on query logging to a file, pass in the file name as a parameter.
   *           e.g. setLogFile("C:/path_to_my_directory/query.log");
   *        To trun off query logging, just call the function with no arguments
   *           e.g. setLogFile();
   *
   * Note:  This function incurs the overhead of checking to make sure if the log file
   *        exists, and creating it if it does not.  If you know the log file exists
   *        and is writable, you may want to use the startQueryLogging() function
   *        to simply turn on query logging.
   *
   * @param string $query_log_file
   * @return logging status - true on success, false on failure
   */
   public function setQueryLogFile($query_log_file = false)
   {
      // set the return value for the logging status to false by default
      $logging_status = false;
      // and turn off logging by default
      $this->log_queries = false;
      $this->query_log_file = '';

      //next test to see if it should be turned on
      if ($query_log_file) //see if a log file was specifed
      {
         $this->log_queries = true;
         $this->query_log_file = $query_log_file;
         //verify that the file exists
         try {
            if ( !($file_handle = @fopen($query_log_file.date('Ymd').'_sql_log.txt', 'a')) )
            {
                throw new SubsystemException ("Function: setQueryLogFile - File $query_log_file could not be created for SQL logging.");
            }
            //close the handle to the file
            fclose($file_handle);
            // the file exsists, so turn on logging
            $logging_status = true;
         } catch( SubsystemException $e ) {
            // the file did not exist, so log the error
            $this->logError($e->getMessage(), $e->getExceptionData());
         }
      }
      return $logging_status;
   }

   /***************************************************************************************
   * @desc startErrorLogging - turns on error logging, does not make sure the file exits
   *
   */
   public function startErrorLogging()
   {
      $this->log_errors = true;
   }
   /***************************************************************************************
   * @desc stopErrorLogging - turns off error logging
   *
   */
   public function stopErrorLogging()
   {
      $this->log_errors = false;
   }


   /***************************************************************************************
   * @desc logError - error logging function
   *
   * @param string $error -  the error to be logged
   */
   private function logError($error)
   {
      // see if we should show the error on the screen
      if ( $this->display_errors )
      {
         echo '<table width="100%"><tr><td align="center" style="font-size:12; Color: Red">';
         echo $error;
         echo "</td></tr></table>";
      }
      //see if we should log the errors to a file
      if ( $this->log_errors )
      {
         $date = date('Y-m-d h:i:s A');
         $log_file = $this->query_log_file.'_'.date('Ymd');
         try {
            if ( !($file_handle = fopen($log_file, 'a')) )
            {
                throw new SubsystemException ('Function: logError -File '.$log_file.' could not be opened for Error logging.');
            }
            //log the error with a newline char
            if ( !fwrite($file_handle, "$date\tError:  $error\r\n") )
            {
               throw new SubsystemException ('Function: logError - File '.$log_filee.' could not be written to for Error logging.');
            }
            //close the handle to the file
            fclose($file_handle);
         } catch( SubsystemException $e ) {
            // the file did not exist, so log the error
            $this->logError($e->getMessage(), $e->getExceptionData());
         }
      }
      //see if we should trigger errors (used when a custom error handler is in place)
      if ( $this->trigger_errors )
      {
         trigger_error ($error, E_USER_WARNING);
      }
   }
}
?>