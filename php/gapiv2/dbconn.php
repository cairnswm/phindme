<?php

include_once __DIR__ . "/../phindmeconfig.php";

// Create a global $gapiconn exists for the MySQL connection
global $gapiconn;
$gapiconn = new mysqli($phindmeconfig["server"], $phindmeconfig["username"], $phindmeconfig["password"], $phindmeconfig["database"]);

if ($gapiconn->connect_error) {
    die("Connection failed: " . $gapiconn->connect_error);
}

$gapiconn->set_charset("utf8mb4");

/**
 * Execute an SQL query with prepared statements.
 *
 * Supports SELECT, INSERT, UPDATE, DELETE, and other query types.
 *
 * @param string $sql The SQL query with placeholders
 * @param array $params The parameters to bind to the query
 * @param array $options Optional settings:
 *                       - 'JSON' => field name or array of field names to decode from JSON (for result sets)
 * @return array For result-producing queries: array of rows;
 *               for others: ['affected_rows' => int, 'insert_id' => int|null]
 * @throws Exception if preparation or execution fails
 */
function executeSQL($sql, $params = [], $options = [])
{
    global $gapiconn;

    $stmt = $gapiconn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $gapiconn->error);
    }

    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }

        $bindParams = [$types];
        foreach ($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }

        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    // Check whether the statement returns a result set
    if ($stmt->field_count > 0) {
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        if (isset($options["JSON"])) {
            $jsonFields = is_string($options["JSON"]) ? [$options["JSON"]] : $options["JSON"];
            foreach ($rows as &$row) {
            foreach ($jsonFields as $field) {
                if (isset($row[$field])) {
                $decoded = json_decode($row[$field], true);
                $row[$field] = $decoded !== null ? $decoded : [];
                } else {
                $row[$field] = [];
                }
            }
            }
        }

        return $rows;
    } else {
        $response = [
            'affected_rows' => $stmt->affected_rows,
            'insert_id' => $stmt->insert_id ?: null
        ];
        $stmt->close();
        return $response;
    }
}
