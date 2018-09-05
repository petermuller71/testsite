print "abcdef";
#exit;
/************************************************************************************************************************************************
 *
 * Class:  rgyveio_database1
 * CHECK line 74 (name = rgyveio_database1)
 *
 * Perform all queries on database
 * If error, than static class rgyveio_errors will be executed
 *
 *************************************************************************************************************************************************/

$sql   = "INSERT INTO forge_testdb.user (user_id, user_name, user_time) VALUES (NULL, 'ccc', '3');";
$res   = rgyveio_database1::insert($sql);

#$sql   = "select * from user;";
#$res   = rgyveio_database1::selectjson($sql);

#output and exit
#rgyveio_output::show_query_results($res['num_rows'],$res['data']);

class rgyveio_database1 {
    
    #https://www.ibm.com/us-en/marketplace/72
    
    private static $db;
    private $connection;
    
    # localhost
    # fake user account details
    static private $host   = "myaurora.ccjpctn7gjxp.eu-central-1.rds.amazonaws.com";    # fake
    static private $user   = "aurormaster";                                             # fake
    static private $pass   = "fdsFxxs320876%dapDfd34$#fds";                             # fake
    static private $dbase  = "forge_testdb";                                            # fake 
 
    static public function nothing() 
    {
        //
    }
    
    private function __construct()
    {

        $this->connection = mysqli_init();
       
        $timeout = 3;
        mysqli_options ($this->connection, MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
        $link = mysqli_real_connect ($this->connection, self::$host, self::$user, self::$pass, self::$dbase, 3306, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
        $this->connection->set_charset("utf8");          

    }
    
    function __destruct()
    {
        $this->connection->close();
    }
    
    private function __clone() { }
    
    public static function getConnection()
    {
        if (self::$db == null)
        {
            self::$db = new rgyveio_database1();
        }
        
        return self::$db->connection;
    }
    
    
    /**
     * rgyveio_query
     *
     * @param      string       $sql              sql query to be performed
     * 
     * @return     array        result of query   num_rows
     *                                            result-set
     */
    

    ###################################
    # SQL: select JSON
    #
    # output:
    # - num_rows
    # - result (in json format)
    ###################################
    
    public static function selectjson(string $sql)
    {
        
        $mysqli = self::getConnection();
        
        $result = $mysqli->query($sql);
        
        if (!$result)
        {
           
            $e          = mysqli_error($mysqli);
            $nr         = $mysqli->errno;
           
            $exception = $e."(MySQL errnr: $nr)";
            
            throw new Exception($exception);
        }
        
        $output['num_rows'] = $result->num_rows;
        
        if ($result->num_rows == 0)
        {
            $output['data'] = "null";
        }
        else
        {
            $rows = array();
            while ( $r = $result->fetch_assoc() )
            {
                $rows[] = $r;
            }
            
            $output['data']     = json_encode($rows);
        }
        
        return $output;
    }
    
 
       
    
    ###################################
    # SQL: select (standard)
    #
    # output:
    # - result
    ###################################
    
    public static function select(string $sql)
    {
        
        $mysqli = self::getConnection();
        
        $result = $mysqli->query($sql);
        
        if (!$result)
        {
            
            $e          = mysqli_error($mysqli);
            $nr         = $mysqli->errno;
            
            $exception = $e."(MySQL errnr: $nr)";
            
            throw new Exception($exception);
        }
        
        return $result;
    }
    
    
    
    ###################################
    # SQL: insert
    #
    # output:
    # - insert_id
    # - affected_rows
    ###################################
    
    public static function insert(string $sql)
    {
        
        $mysqli = self::getConnection();
        
        $result = $mysqli->query($sql);
        
        if (!$result)
        {
            $e          = mysqli_error($mysqli);
            $nr         = $mysqli->errno;
            
            $exception = $e."(MySQL errnr: $nr)";
            
            throw new Exception($exception);
        }
        
        $output['insert_id']     = $mysqli->insert_id;
        $output['affected_rows'] = $mysqli->affected_rows;
        
        if ($output['affected_rows'] < 1)
        {

            $exception = "Unknown error. No insert is done.";
            
            throw new Exception($exception);
        }
        
        
        return $output;
    }
    
    ###################################
    # SQL: update
    #
    # output:
    # - affected_rows
    ###################################
    
    public static function update(string $sql)
    {
        
        $mysqli = self::getConnection();
        
        $result = $mysqli->query($sql);
        
        if (!$result)
        {
            $e          = mysqli_error($mysqli);
            $nr         = $mysqli->errno;
            
            $exception = $e."(MySQL errnr: $nr)";
            
            throw new Exception($exception);
        }
        
        $output['affected_rows'] = $mysqli->affected_rows;     
        
        return $output;
    }
    
}



class rgyveio_output {
    
    
    static public function nothing()
    {
        //
    }
    
    public static function show_query_results(int $num_rows, string $data)
    {
        
        $execution_time  = round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],4);
        
        $size_dataset = round(strlen($data) / 1024,2);
        
        $json =
        '{
    "status"               : "success",
    "code"                 : "200",
    "executiontime"        : '.$execution_time.',
    "num_rows"             : '.$num_rows.',
    "size"                 : '.$size_dataset.',
    "result"               : '.$data.'
}';
        
        $status = "Status: 200 OK";
        header('Content-Type: application/json;charset=utf-8');
        header("Status: $status");
        print $json;
        exit;
        
    }
    
    
    
    public static function show_insert_results(int $insert_id, int $affected_rows)
    {
        
        $execution_time  = round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],4);
        
        $size_dataset = round(strlen($data) / 1024,2);
        
        $json =
        '{
    "status"               : "success",
    "code"                 : "200",
    "executiontime"        : '.$execution_time.',
    "insert_id"            : '.$insert_id.',
    "affected_rows"        : '.$affected_rows.'
}';
        
        $status = "Status: 200 OK";
        header('Content-Type: application/json;charset=utf-8');
        header("Status: $status");
        print $json;
        exit;
        
    }
    
}


