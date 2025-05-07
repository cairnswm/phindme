<?php

function getRecruiters($config, $id = null)
{
    global $mysqli;
    $params = [];
    $query = "SELECT 
        r.*,
        (
            SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'name', rp.name,
                    'value', rp.value
                )
            )
            FROM recruiter_properties rp
            WHERE rp.recruiter_id = r.id
        ) AS properties
        FROM recruiters r WHERE 1=1";
    if ($id) {
        $query .= " AND r.id = ?";
        $params[] = $id;
    }
    $rows = executeSQL($query, $params, ["JSON" => ["properties"]]);
    return $rows;
}
