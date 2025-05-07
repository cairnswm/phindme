<?php

require_once '../dbconnection.php';

/**
 * Expires adverts and removes priority from outdated ones.
 */
function markAdvertsExpired() {
    // Expire ads
    $sqlExpire = "
        UPDATE adverts
        SET status = 'expired'
        WHERE expiry_date < NOW() AND status != 'expired'
    ";
    $stmt1 = executeSQL($sqlExpire);
    $expiredCount = $stmt1->affected_rows;

    // Remove priority
    $sqlPriority = "
        UPDATE adverts
        SET priority = 0
        WHERE priority = 1 AND priority_expiry_date IS NOT NULL AND priority_expiry_date < NOW()
    ";
    $stmt2 = executeSQL($sqlPriority);
    $priorityRemoved = $stmt2->affected_rows;

    return [
        'expired_updated' => $expiredCount,
        'priority_removed' => $priorityRemoved
    ];
}
