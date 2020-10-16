<?php

require __DIR__ . '/../../../bootstrap.php';
$time = time();

function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig(__DIR__ . '/../../../src/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = __DIR__ . '/../../../token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

$client = getClient();
$service = new Google_Service_Sheets($client);

$spreadsheetId = '1QUH7WpcPgMIJ6QIjvzF17jufxMZR2Nhwi1tSOG6Coho';

$category_list = [];
$range = 'A2:A';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$sheet_values = $response->getValues();
if ($sheet_values) {
    \App\Models\Category::truncate();
    foreach ($sheet_values as $row) {
        if (!in_array($row[0], $category_list)) {
            $category_list[] = $row[0];
            \App\Models\Category::create([
                'title' => $row[0]
            ]);
        }
    }
}

$subcategory_list = [];
$range = 'B2:A';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$sheet_values = $response->getValues();
if ($sheet_values) {
    \App\Models\SubCategory::truncate();
    foreach ($sheet_values as $row) {
        if (isset($row[1])) {
            if (!in_array($row[1], $subcategory_list)) {
                $subcategory_list[] = $row[1];
                \App\Models\SubCategory::create([
                    'title' => $row[1],
                    'category_id' => \App\Models\Category::where('title', $row[0])->first()->id
                ]);
            }
        }
    }
}

$product_list = [];
$product_array = [];
$range = 'E2:A';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$sheet_values = $response->getValues();
if ($sheet_values) {
    \App\Models\Product::truncate();
    foreach ($sheet_values as $row) {
        if (!in_array($row[2], $subcategory_list)) {
            $product_list[] = $row[2];

            $product_array = [];
            $product_array['title'] = $row[2];
            $product_array['price'] = $row[3];
            $product_array['amount'] = $row[4];
            if (isset($row[2])) {
                $product_array['subcategory_id'] = \App\Models\SubCategory::where('title', $row[1])->first()->id;
            }
            $product_array['category_id'] = \App\Models\Category::where('title', $row[0])->first()->id;

            \App\Models\Product::create($product_array);
        }
    }
}
$new_time = time() - $time;
echo $new_time;