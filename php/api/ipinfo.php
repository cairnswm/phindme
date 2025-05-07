<?php

include_once dirname(__FILE__) . "/../corsheaders.php";
include_once dirname(__FILE__) . "/../utils.php";
include_once dirname(__FILE__) . "/../trackerconfig.php";

function getIpInfo($ipAddress)
{
  // TODO move token to settings
  $token = 'd4ca2fb7404647';
  $url = "http://ipinfo.io/{$ipAddress}?token={$token}";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  curl_close($ch);

  return json_decode($response, true);
}

function updateIpAddress($ipAddress)
{
  global $mysqli;

  // Check if the IP address exists and is not older than 30 days
  $query = "SELECT id, last_updated FROM ip_geolocation_cache WHERE ip_address = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param("s", $ipAddress);
  $stmt->execute();
  
  $id = null;
  $lastUpdatedStr = null;
  $stmt->bind_result($id, $lastUpdatedStr);
  $found = $stmt->fetch();
  $stmt->close();

  $currentDate = new DateTime();
  $updateRequired = false;

  if ($found) {
    $lastUpdated = new DateTime($lastUpdatedStr);
    $interval = $currentDate->diff($lastUpdated);
    if ($interval->days > 30) {
      $updateRequired = true;
    }
  } else {
    $updateRequired = true;
  }

  if ($updateRequired) {
    // Generate random data for country, region, and city
    $ipInfo = getIpInfo($ipAddress);
    $country = isset($ipInfo['country']) ? $ipInfo['country'] : 'Unknown';
    $region = isset($ipInfo['region']) ? $ipInfo['region'] : 'Unknown';
    $city = isset($ipInfo['city']) ? $ipInfo['city'] : 'Unknown';
    $data = json_encode($ipInfo);

    if ($found) {
      // Update existing record
      $query = "UPDATE ip_geolocation_cache SET country = ?, region = ?, city = ?, data=?, last_updated = NOW() WHERE id = ?";
      $stmt = $mysqli->prepare($query);
      $stmt->bind_param("ssssi", $country, $region, $city, $data, $id);
    } else {
      // Insert new record
      $query = "INSERT INTO ip_geolocation_cache (ip_address, country, region, city, data, last_updated) VALUES (?, ?, ?, ?, ?, NOW())";
      $stmt = $mysqli->prepare($query);
      $stmt->bind_param("sssss", $ipAddress, $country, $region, $city, $data);
    }

    $stmt->execute();
    $stmt->close();
  }
}
?>
