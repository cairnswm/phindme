<?php

function getEmployers($config, $id = null)
{
    global $mysqli;
    $params = [];
    $query = "SELECT 
      e.*,
      (
        SELECT JSON_ARRAYAGG(
        JSON_OBJECT(
          'name', ep.name,
          'value', ep.value
        )
        )
        FROM employer_properties ep
        WHERE ep.employer_id = e.id
      ) AS properties,
      (
        SELECT JSON_ARRAYAGG(
        JSON_OBJECT(
          'id', r.id,
          'company_name', r.company_name,
          'website', r.website,
          'location', r.location,
          'rating', r.rating
        )
        )
        FROM employer_recruiters er
        JOIN recruiters r ON er.recruiter_id = r.id
        WHERE er.employer_id = e.id
      ) AS approved_recruiters,
      (
        SELECT JSON_ARRAYAGG(
        JSON_OBJECT(
          'id', j.id,
          'title', j.title,
          'description', j.description,
          'location', j.location,
          'salary_band', j.salary_band,
          'vacancies', j.vacancies
        )
        )
        FROM jobs j
        WHERE j.employer_id = e.id
      ) AS jobs
      FROM employers e WHERE 1=1";
    if ($id) {
      $query .= " AND e.id = ?";
      $params[] = $id;
    }
    $rows = executeSQL($query, $params, ["JSON" => ["properties", "approved_recruiters", "jobs"]]);

    return $rows;
}
