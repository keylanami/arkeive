<?php

function getForecastData($userId) {
    return fetchWeatherData(
        $userId,
        'weather_forecast_',
        "https://api.tomorrow.io/v4/weather/forecast?location=bojongsoang&timesteps=1h&apikey=MnKGup06x6z6XGQW4Pzi2w6DTPt5HjIr"
    );
}

function getRealtimeData($userId) {
    return fetchWeatherData(
        $userId,
        'weather_realtime_',
        "https://api.tomorrow.io/v4/weather/realtime?location=bojongsoang&apikey=MnKGup06x6z6XGQW4Pzi2w6DTPt5HjIr"
    );
}

function getForecastDaily($userId) {
    return fetchWeatherData(
        $userId,
        'weather_forecastdaily_',
        "https://api.tomorrow.io/v4/weather/forecast?location=bojongsoang&timesteps=1d&apikey=MnKGup06x6z6XGQW4Pzi2w6DTPt5HjIr"
    );
}

function fetchWeatherData($userId, $cachePrefix, $url) {
    $cacheDir = '../json/';
    $cacheTime = 300; // 5 minutes

    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    $safeUserId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $userId);
    $cacheFile = $cacheDir . $cachePrefix . $safeUserId . '.json';

    $shouldUpdate = true;

    if (file_exists($cacheFile)) {
        $lastModified = filemtime($cacheFile);
        $now = time();

        $elapsed = $now - $lastModified;

        $fiveMinutesPassed = $elapsed >= $cacheTime;
        $currentMinute = (int)date('i', $now);
        $currentSecond = (int)date('s', $now);
        $isTopOfHour = ($currentMinute === 0 && $currentSecond === 0);

        if (!$fiveMinutesPassed && !$isTopOfHour) {
            $shouldUpdate = false;
        }
    }

    if ($shouldUpdate) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "accept-encoding: deflate, gzip, br"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            // cURL failed — fallback to cache
            if (file_exists($cacheFile)) {
                return file_get_contents($cacheFile);
            } else {
                return json_encode(['error' => 'Curl failed and no cache available.']);
            }
        }

        $decoded = json_decode($response, true);
        if (isset($decoded['code']) && $decoded['code'] == 429001) {
            // Rate limit hit — fallback to cache
            error_log("Weather API rate limit hit for user {$userId} at " . date('Y-m-d H:i:s'));
            if (file_exists($cacheFile)) {
                return file_get_contents($cacheFile);
            } else {
                return json_encode(['error' => 'Rate limit hit and no cache available.']);
            }
        }

        // Save fresh data to cache
        file_put_contents($cacheFile, $response);
    } else {
        // Load from cache
        $response = file_get_contents($cacheFile);
    }

    return $response;
}
?>
