<?php

require_once(__DIR__ . '/AbstractDb.php');

class MySql extends AbstractDb
{
    public function __construct(string $db, string $hostname = null, string $username = null, string $password = null, string $prefix = '')
    {
        $this->db = new \mysqli($hostname, $username, $password, $db);
        $this->prefix = $prefix;
    }
    // The following function free us from requiring mysqlnd
    public function get_result($Statement)
    {
        $RESULT = array();
        $Statement->store_result();
        for ($i = 0; $i < $Statement->num_rows; $i++) {
            $Metadata = $Statement->result_metadata();
            $PARAMS = array();
            while ($Field = $Metadata->fetch_field()) {
                $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
            }
            call_user_func_array(array( $Statement, 'bind_result' ), $PARAMS);
            $Statement->fetch();
        }
        return $RESULT;
    }

    private function prepareStmt(string $sql, array $params)
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $typestr = '';
        foreach ($params as $p) {
            $type = gettype($p);
            switch ($type) {
                case 'integer':
                    $typestr .= 'i';
                    break;
                case 'double':
                case 'float':
                    $typestr .= 'd';
                    break;
                default:
                case 'string':
                    $typestr .= 's';
                    break;
            }
        }
        // Splat operator needs PHP 5.6+
        $stmt->bind_param($typestr, ...$params);
        return $stmt;
    }

    protected function query(string $sql, array $params): array
    {
        $stmt = $this->prepareStmt($sql, $params);
        if (!$stmt) {
            return false;
        }
        if (!$stmt->execute()) {
            return false;
        }
        $dbresult = get_result($stmt);
        
        $result = array();
        //while ($data = $dbresult) {
            // Loop through results here $data[]
        //    $result[] = $data;
        //}
        $result = $dbresult;


        $stmt->close();
        return $result;
    }

    protected function execute(string $sql, array $params): bool
    {
        $stmt = $this->prepareStmt($sql, $params);
        if (!$stmt) {
            return false;
        }
        $result = $stmt->execute();
        if ($result) {
            $stmt->close();
        }
        return $result;
    }
}
