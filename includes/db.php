<?php
if (!defined('HC_APP')) { die('Acesso negado'); }

function lf_db_conn()
{
    if (function_exists('db_connect')) {
        return db_connect();
    }

    static $conn = null;
    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        throw new RuntimeException('Falha ao conectar no banco de dados.');
    }

    if (!@mysqli_set_charset($conn, 'utf8mb4')) {
        @mysqli_set_charset($conn, 'utf8');
    }

    return $conn;
}

function lf_db_table_exists($table)
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
    if ($table === '') {
        return false;
    }

    $sql = "SELECT 1
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
            LIMIT 1";
    $stmt = mysqli_prepare(lf_db_conn(), $sql);
    if (!$stmt) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, 's', $table);
    mysqli_stmt_execute($stmt);
    $exists = null;
    mysqli_stmt_bind_result($stmt, $exists);
    $hasRow = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $hasRow ? true : false;
}

function lf_db_column_exists($table, $column)
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
    $column = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
    if ($table === '' || $column === '') {
        return false;
    }

    $sql = "SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
            LIMIT 1";
    $stmt = mysqli_prepare(lf_db_conn(), $sql);
    if (!$stmt) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, 'ss', $table, $column);
    mysqli_stmt_execute($stmt);
    $exists = null;
    mysqli_stmt_bind_result($stmt, $exists);
    $hasRow = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $hasRow ? true : false;
}

function lf_db_stmt_bind_execute($stmt, $types, $params)
{
    if (!($stmt instanceof mysqli_stmt)) {
        throw new InvalidArgumentException('Statement invalido.');
    }

    $types = (string)$types;
    $params = is_array($params) ? $params : array();
    if ($types !== '' && !empty($params)) {
        $bindParams = array($types);
        foreach ($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);
    }

    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        throw new RuntimeException('Falha ao executar consulta preparada: ' . $error);
    }
}

function lf_db_stmt_fetch_all_assoc($stmt)
{
    $rows = array();
    $meta = mysqli_stmt_result_metadata($stmt);
    if (!$meta) {
        mysqli_stmt_close($stmt);
        return $rows;
    }

    $fields = array();
    $refs = array();
    while ($field = mysqli_fetch_field($meta)) {
        $fields[$field->name] = null;
        $refs[] = &$fields[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $refs);
    while (mysqli_stmt_fetch($stmt)) {
        $row = array();
        foreach ($fields as $key => $value) {
            $row[$key] = $value;
        }
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);
    return $rows;
}

function lf_db_fetch_all_prepared($sql, $types, $params)
{
    $stmt = mysqli_prepare(lf_db_conn(), $sql);
    if (!$stmt) {
        throw new RuntimeException('Falha ao preparar consulta.');
    }
    lf_db_stmt_bind_execute($stmt, $types, $params);
    return lf_db_stmt_fetch_all_assoc($stmt);
}

function lf_db_fetch_one_prepared($sql, $types, $params)
{
    $rows = lf_db_fetch_all_prepared($sql, $types, $params);
    return isset($rows[0]) ? $rows[0] : false;
}

function lf_db_execute_prepared($sql, $types, $params)
{
    $stmt = mysqli_prepare(lf_db_conn(), $sql);
    if (!$stmt) {
        throw new RuntimeException('Falha ao preparar consulta.');
    }
    lf_db_stmt_bind_execute($stmt, $types, $params);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected;
}
