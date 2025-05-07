<?php

function getCandidates($config, $id = null)
{
    global $mysqli;
    $params = [];
    $query = "SELECT 
        c.*,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'institution', ce.institution,
                'degree', ce.degree,
                'field_of_study', ce.field_of_study,
                'start_year', ce.start_year,
                'end_year', ce.end_year
            )
            )
            FROM candidate_education ce
            WHERE ce.candidate_id = c.id
        ) AS education,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'job_title', ce.job_title,
                'company', ce.company,
                'start_date', ce.start_date,
                'end_date', ce.end_date,
                'description', ce.description
            )
            )
            FROM candidate_experience ce
            WHERE ce.candidate_id = c.id
        ) AS experience,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'skill_name', cs.skill_name,
                'experience_years', cs.experience_years,
                'last_used_year', cs.last_used_year
            )
            )
            FROM candidate_skills cs
            WHERE cs.candidate_id = c.id
        ) AS skills,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'name', cp.name,
                'value', cp.value
            )
            )
            FROM candidate_properties cp
            WHERE cp.candidate_id = c.id
        ) AS properties
        FROM candidates c WHERE 1=1";
    if ($id) {
        $query .= " AND c.id = ?";
        $params[] = $id;
    }
    $rows = executeSQL($query, $params, ["JSON" => ["education", "experience", "skills", "properties"]]);

    return $rows;
}
