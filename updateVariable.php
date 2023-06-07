<?php
// Retrieve the variable from wherever it's stored
$myVariable = "Initial value";

// Update the variable value
$myVariable = "New value";

// Prepare the response as JSON
$response = json_encode(array("updatedVariable" => $myVariable));

// Set the response content type
header("Content-Type: application/json");

// Send the response back to the JavaScript code
echo $response;
?>
