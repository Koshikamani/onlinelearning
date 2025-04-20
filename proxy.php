<?php
if (isset($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $apiKey = ''; 
    $url = "https://api.wolframalpha.com/v1/result?appid=$apiKey&i=$query";

    $response = @file_get_contents($url);
    if ($response === false) {
        http_response_code(500);
        echo "Error fetching data from Wolfram Alpha.";
    } else {
        echo $response;
    }
} else {
    http_response_code(400);
    echo "Query parameter is missing.";
}
?>
