<?php

include_once dirname(__FILE__) . "/../corsheaders.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once dirname(__FILE__) . "/../gapiv2/dbconn.php";
include_once dirname(__FILE__) . "/../gapiv2/v2apicore.php";
include_once dirname(__FILE__) . "/../utils.php";
include_once dirname(__FILE__) . "/../security/security.config.php";
include_once dirname(__FILE__) . "/functions/getJobs.php";
include_once dirname(__FILE__) . "/functions/getCandidates.php";
include_once dirname(__FILE__) . "/functions/getEmployers.php";
include_once dirname(__FILE__) . "/functions/getSubmissions.php";
include_once dirname(__FILE__) . "/functions/getRecruiters.php";

// Get authentication details
$appid = getAppId();
$token = getToken();

// if (validateJwt($token, false) == false) {
//     http_response_code(401);
//     echo json_encode([
//         'error' => true,
//         'message' => 'Unauthorized'
//     ]);
//     die();
// }

// $user = getUserFromToken($token);
// $userid = $user->id;



// Define the configurations
$phindmeConfig = [
    "candidates" => [
        "tablename" => "candidates",
        "key" => "id",
        "select" => "getCandidates",
        "create" => [
            "recruiter_id",
            "name",
            "email",
            "phone",
            "summary",
            "resume_url"
        ],
        "update" => [
            "name",
            "email",
            "phone",
            "summary",
            "resume_url"
        ],
        "delete" => true,
        "subkeys" => [
            "education" => [
                "tablename" => "candidate_education",
                "key" => "candidate_id",
                "select" => [
                    "id",
                    "institution",
                    "degree",
                    "field_of_study",
                    "start_year",
                    "end_year",
                    "created_at",
                    "modified_at"
                ]
            ],
            "experience" => [
                "tablename" => "candidate_experience",
                "key" => "candidate_id",
                "select" => [
                    "id",
                    "job_title",
                    "company",
                    "start_date",
                    "end_date",
                    "description",
                    "created_at",
                    "modified_at"
                ]
            ],
            "skills" => [
                "tablename" => "candidate_skills",
                "key" => "candidate_id",
                "select" => [
                    "id",
                    "skill_name",
                    "experience_years",
                    "last_used_year",
                    "created_at",
                    "modified_at"
                ]
            ],
            "properties" => [
                "tablename" => "candidate_properties",
                "key" => "candidate_id",
                "select" => [
                    "id",
                    "name",
                    "value",
                    "created_at",
                    "modified_at"
                ]
            ]
        ]
    ],
    "candidate_skills" => [
        "tablename" => "candidate_skills",
        "key" => "id",
        "select" => false,
        "create" => [
            "candidate_id",
            "skill_name",
            "experience_years",
            "last_used_year"
        ],
        "update" => [
            "skill_name",
            "experience_years",
            "last_used_year"
        ],
        "delete" => true,
    ],
    "candidate_properties" => [
        "tablename" => "candidate_properties",
        "key" => "id",
        "select" => false,
        "create" => [
            "candidate_id",
            "name",
            "value"
        ],
        "update" => [
            "name",
            "value"
        ],
        "delete" => true,
    ],
    "candidate_experience" => [
        "tablename" => "candidate_experience",
        "key" => "id",
        "select" => false,
        "create" => [
            "candidate_id",
            "job_title",
            "company",
            "start_date",
            "end_date",
            "description"
        ],
        "update" => [
            "job_title",
            "company",
            "start_date",
            "end_date",
            "description"
        ],
        "delete" => true,
    ],
    "candidate_education" => [
        "tablename" => "candidate_education",
        "key" => "id",
        "select" => false,
        "create" => [
            "candidate_id",
            "institution",
            "degree",
            "field_of_study",
            "start_year",
            "end_year"
        ],
        "update" => [
            "institution",
            "degree",
            "field_of_study",
            "start_year",
            "end_year"
        ],
        "delete" => true,
    ],
    "employers" => [
        "tablename" => "employers",
        "key" => "id",
        "select" => "getEmployers",
        "create" => [
            "company_name",
            "website",
            "location",
            "company_overview",
            "industry",
            "headcount",
            "working_conditions",
            "signed_terms",
            "verified_within_24h"
        ],
        "update" => [
            "company_name",
            "website",
            "location",
            "company_overview",
            "industry",
            "headcount",
            "working_conditions",
            "signed_terms",
            "verified_within_24h"
        ],
        "delete" => true,
        "subkeys" => [
            "properties" => [
                "tablename" => "employer_properties",
                "key" => "employer_id",
                "select" => [
                    "id",
                    "name",
                    "value",
                    "created_at",
                    "modified_at"
                ]
            ],
            "recruiters" => [
                "tablename" => "employer_recruiters",
                "key" => "employer_id",
                "select" => [
                    "id",
                    "employer_id",
                    "recruiter_id",
                ],

            ],
            "jobs" => [
                "tablename" => "jobs",
                "key" => "employer_id",
                "select" => "getJobsForEmployer"
            ]
        ]
    ],
    "employer_properties" => [
        "tablename" => "employer_properties",
        "key" => "id",
        "select" => false,
        "create" => [
            "employer_id",
            "name",
            "value"
        ],
        "update" => [
            "name",
            "value"
        ],
        "delete" => true,
    ],
    "employer_recruiters" => [
        "tablename" => "employer_recruiters",
        "key" => "id",
        "select" => false,
        "create" => [
            "employer_id",
            "recruiter_id"
        ],
        "update" => [
            "recruiter_id"
        ],
        "delete" => true,
    ],
    "submissions" => [
        "tablename" => "candidate_submissions",
        "key" => "id",
        "select" => "getSubmissions",
        "create" => [
            "job_id",
            "candidate_id",
            "recruiter_id",
            "status"
        ],
        "update" => [
            "job_id",
            "candidate_id",
            "recruiter_id",
            "status"
        ],
        "delete" => true,
    ],
    "employer_notes" => [
        "tablename" => "employer_notes",
        "key" => "id",
        "select" => false,
        "create" => [
            "employer_id",
            "note"
        ],
        "update" => [
            "note"
        ],
        "delete" => true,
    ],
    "recruiter_notes" => [
        "tablename" => "recruiter_notes",
        "key" => "id",
        "select" => false,
        "create" => [
            "submission_id",
            "note"
        ],
        "update" => [
            "note"
        ],
        "delete" => true,
    ],
    "jobs" => [
        "tablename" => "jobs",
        "key" => "id",
        "select" => "getJobs",
        "create" => [
            "employer_id",
            "title",
            "description",
            "location",
            "address",
            "working_hours",
            "working_conditions",
            "salary_band",
            "benefits",
            "vacancies",
            "interview_process",
            "test_required",
            "candidate_test_repo",
            "ideal_start_date",
            "application_close_date",
            "open_to_agencies",
            "selected_agencies",
            "agency_fee_percent",
            "profile_limit_per_agency",
            "save_as_template"
        ],
        "update" => [
            "title",
            "description",
            "location",
            "address",
            "working_hours",
            "working_conditions",
            "salary_band",
            "benefits",
            "vacancies",
            "interview_process",
            "test_required",
            "candidate_test_repo",
            "ideal_start_date",
            "application_close_date",
            "open_to_agencies",
            "selected_agencies",
            "agency_fee_percent",
            "profile_limit_per_agency",
            "save_as_template"
        ],
        "delete" => true,
        "subkeys" => [
            "skills" => [
                "tablename" => "job_skills",
                "key" => "job_id",
                "select" => [
                    "id",
                    "skill_name",
                    "experience_years",
                    "created_at",
                    "modified_at"
                ]
            ],
            "properties" => [
                "tablename" => "job_properties",
                "key" => "job_id",
                "select" => [
                    "id",
                    "name",
                    "value",
                    "created_at",
                    "modified_at"
                ]
            ],
            "recruiters" => [
                "tablename" => "jobs_per_recruiters",
                "key" => "job_id",
                "select" => [
                    "id",
                    "job_id",
                    "recruiter_id",
                ]
            ]
        ]
    ],
    "jobskill" => [
        "tablename" => "job_skills",
        "key" => "id",
        "select" => false,
        "create" => [
            "job_id",
            "skill_name",
            "experience_years"
        ],
        "update" => [
            "skill_name",
            "experience_years"
        ],
        "delete" => true,
    ],
    "job_properties" => [
        "tablename" => "job_properties",
        "key" => "id",
        "select" => false,
        "create" => [
            "job_id",
            "name",
            "value"
        ],
        "update" => [
            "name",
            "value"
        ],
        "delete" => true,
    ],
    "jobs_per_recruiters" => [
        "tablename" => "jobs_per_recruiters",
        "key" => "id",
        "select" => false,
        "create" => [
            "job_id",
            "recruiter_id"
        ],
        "update" => [
            "recruiter_id"
        ],
        "delete" => true,
    ],
    "recruiters" => [
        "tablename" => "recruiters",
        "key" => "id",
        "select" => "getRecruiters",
        "create" => [
            "company_name",
            "website",
            "location",
            "company_overview",
            "headcount",
            "specialties",
            "candidates_placed",
            "rating",
            "years_in_practice",
            "signed_terms"
        ],
        "update" => [
            "company_name",
            "website",
            "location",
            "company_overview",
            "headcount",
            "specialties",
            "candidates_placed",
            "rating",
            "years_in_practice",
            "signed_terms"
        ],
        "delete" => true,
        "subkeys" => [
            "properties" => [
                "tablename" => "recruiter_properties",
                "key" => "recruiter_id",
                "select" => [
                    "id",
                    "name",
                    "value",
                    "created_at",
                    "modified_at"
                ]
            ],
            "employers" => [
                "tablename" => "employer_recruiters",
                "key" => "recruiter_id",
                "select" => [
                    "id",
                    "employer_id",
                    "recruiter_id",
                ]
            ]
        ]
    ],
    "recruiter_properties" => [
        "tablename" => "recruiter_properties",
        "key" => "id",
        "select" => false,
        "create" => [
            "recruiter_id",
            "name",
            "value"
        ],
        "update" => [
            "name",
            "value"
        ],
        "delete" => true,
    ],
];



runAPI($phindmeConfig);