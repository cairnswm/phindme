<?php

function getSubmissions($config, $id = null)
{
    global $mysqli;
    $params = [];
    $query = "SELECT 
        cs.*,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'name', cp.name,
                'value', cp.value
            )
            )
            FROM candidate_properties cp
            WHERE cp.candidate_id = cs.candidate_id
        ) AS candidate_properties,
        (
            SELECT JSON_OBJECT(
                'id', c.id,
                'name', c.name,
                'email', c.email,
                'phone', c.phone,
                'summary', c.summary,
                'resume_url', c.resume_url,
                'skills', (
                    SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'skill_name', cs.skill_name,
                        'experience_years', cs.experience_years,
                        'last_used_year', cs.last_used_year
                    )
                    )
                    FROM candidate_skills cs
                    WHERE cs.candidate_id = c.id
                )
            )
            FROM candidates c
            WHERE c.id = cs.candidate_id
        ) AS candidate,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'name', jp.name,
                'value', jp.value
            )
            )
            FROM job_properties jp
            WHERE jp.job_id = cs.job_id
        ) AS job_properties,
        (
            SELECT JSON_OBJECT(
                'id', e.id,
                'company_name', e.company_name,
                'website', e.website,
                'location', e.location,
                'company_overview', e.company_overview,
                'industry', e.industry,
                'headcount', e.headcount
            )
            FROM employers e
            WHERE e.id = (
                SELECT j.employer_id
                FROM jobs j
                WHERE j.id = cs.job_id
            )
        ) AS employer,
        (
            SELECT JSON_OBJECT(
                'id', j.id,
                'title', j.title,
                'description', j.description,
                'skills', (
                    SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'skill_name', js.skill_name,
                        'experience_years', js.experience_years
                    )
                    )
                    FROM job_skills js
                    WHERE js.job_id = j.id
                )
            )
            FROM jobs j
            WHERE j.id = cs.job_id
        ) AS job,
        (
            SELECT JSON_OBJECT(
                'id', r.id,
                'company_name', r.company_name,
                'specialties', r.specialties
            )
            FROM recruiters r
            WHERE r.id = cs.recruiter_id
        ) AS recruiter,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'note', en.note
            )
            )
            FROM employer_notes en
            WHERE en.submission_id = cs.id
        ) AS employer_notes,
        (
            SELECT JSON_ARRAYAGG(
            JSON_OBJECT(
                'note', rn.note
            )
            )
            FROM recruiter_notes rn
            WHERE rn.submission_id = cs.id
        ) AS recruiter_notes
        FROM candidate_submissions cs WHERE 1=1";
    if ($id) {
        $query .= " AND cs.id = ?";
        $params[] = $id;
    }
    $rows = executeSQL($query, $params, ["JSON" => ["candidate_properties", "job_properties", "employer", "candidate", "job", "recruiter", "employer_notes", "recruiter_notes"]]);

    return $rows;
}
