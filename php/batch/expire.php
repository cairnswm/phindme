<?php

require_once 'phindme_functions.php';

$result = markAdvertsExpired();

echo "Expired adverts updated: " . $result['expired_updated'] . "\n";
echo "Priority removed from adverts: " . $result['priority_removed'] . "\n";
