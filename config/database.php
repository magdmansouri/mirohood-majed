<?php
// config/database.php

class Database {

    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST .
                   ";dbname=" . DB_NAME .
                   ";charset=" . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND =>
                    "SET NAMES " . DB_CHARSET .
                    " COLLATE utf8mb4_unicode_ci"
            ];

            $this->connection = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                $options
            );
        } catch(PDOException $e) {
            error_log(
                'Database connection failed: ' .
                $e->getMessage()
            );
            throw new Exception(
                'Database connection failed'
            );
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Execute raw SQL (DDL, multi-statement, etc.) using PDO::exec.
     */
    public function exec($sql) {
        return $this->connection->exec($sql);
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchColumn($sql, $params = []) {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Insert امن
     * - جلوگیری از id خالی
     * - پشتیبانی از ستون‌هایی مثل order
     */
    public function insert($table, $data) {
        // اگر id خالی ارسال شد حذف شود
        if (
            isset($data['id']) &&
            ($data['id'] === '' || $data['id'] === null)
        ) {
            unset($data['id']);
        }

        $columns = array_keys($data);
        $safeColumns = array_map(function($column){
            return "`" . $column . "`";
        }, $columns);

        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO {$table} (" .
            implode(',', $safeColumns) .
            ") VALUES (" .
            implode(',', $placeholders) .
            ")";

        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }

    public function update(
        $table,
        $data,
        $where,
        $whereParams = []
    ) {
        $sets = [];
        foreach(array_keys($data) as $col) {
            $sets[] = "`{$col}` = ?";
        }

        $sql = "UPDATE {$table} SET " .
            implode(',', $sets) .
            " WHERE {$where}";

        return $this->query(
            $sql,
            array_merge(
                array_values($data),
                $whereParams
            )
        )->rowCount();
    }

    public function delete(
        $table,
        $where,
        $params = []
    ) {
        return $this->query(
            "DELETE FROM {$table} WHERE {$where}",
            $params
        )->rowCount();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }
}

function db() {
    return Database::getInstance();
}
