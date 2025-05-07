<?php

function getJobs($config, $id = null)
{
    global $mysqli;
    $params = [];
    $query = "SELECT 
        j.*,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'skill', js.skill_name,
                'experience', js.experience_years
            )
            )
            FROM job_skills js
            WHERE js.job_id = j.id
        ) AS skills,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'name', jp.name,
                'value', jp.value
            )
            )
            FROM job_properties jp
            WHERE jp.job_id = j.id
        ) AS properties
        FROM jobs j WHERE 1=1";
    if ($id) {
        $query .= " AND j.id = ?";
        $params[] = $id;
    }
    $rows = executeSQL($query, $params, ["JSON" => ["skills", "properties"]]);

    return $rows;
}

function getJobsForEmployer($config)
{
  $employerId = $config["where"]["employer_id"];
    $params = [$employerId];
    $query = "SELECT 
        j.*,
        (
            SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'skill', js.skill_name,
                    'experience', js.experience_years
                )
            )
            FROM job_skills js
            WHERE js.job_id = j.id
        ) AS skills,
        (
            SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'name', jp.name,
                    'value', jp.value
                )
            )
            FROM job_properties jp
            WHERE jp.job_id = j.id
        ) AS properties
        FROM jobs j WHERE j.employer_id = ?";
    $rows = executeSQL($query, $params, ["JSON" => ["skills", "properties"]]);
    return $rows;
}
