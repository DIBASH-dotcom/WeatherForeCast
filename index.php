<?php


require_once 'config.php'; 


$weatherData = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city = trim($_POST["city"]);

    if (empty($city)) {
        $error = "Please enter a city name.";
    } else {
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";
        $response = @file_get_contents($url);

        if ($response === FALSE) {
            $error = "Failed to connect to API server.";
        } else {
            $data = json_decode($response, true);

            if ($data["cod"] != 200) {
                $error = "City not found: " . ucfirst($data["message"]);
            } else {
                $weatherData = [
                    "city" => $data["name"],
                    "country" => $data["sys"]["country"],
                    "temp" => $data["main"]["temp"],
                    "feels_like" => $data["main"]["feels_like"],
                    "humidity" => $data["main"]["humidity"],
                    "wind" => $data["wind"]["speed"],
                    "description" => ucfirst($data["weather"][0]["description"]),
                    "icon" => $data["weather"][0]["icon"]
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather Forecast App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg, #89f7fe, #66a6ff);
            color: #222;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            width: 400px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        form {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #aaa;
        }
        button {
            padding: 10px 15px;
            border: none;
            background: #0078ff;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #005fcc;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
        .result {
            text-align: center;
        }
        .result img {
            width: 100px;
        }
        .temp {
            font-size: 2rem;
            font-weight: bold;
        }
        .meta {
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Weather Forecast</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="city" placeholder="Enter city name (e.g., Butwal)" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($weatherData): ?>
        <div class="result">
            <h3><?= htmlspecialchars($weatherData["city"]) ?>, <?= htmlspecialchars($weatherData["country"]) ?></h3>
            <img src="https://openweathermap.org/img/wn/<?= $weatherData["icon"] ?>@2x.png" alt="Weather Icon">
            <div class="temp"><?= htmlspecialchars($weatherData["temp"]) ?>°C</div>
            <div><?= htmlspecialchars($weatherData["description"]) ?></div>
            <div class="meta">
                Feels like: <?= htmlspecialchars($weatherData["feels_like"]) ?>°C |  
                Humidity: <?= htmlspecialchars($weatherData["humidity"]) ?>% |  
                Wind: <?= htmlspecialchars($weatherData["wind"]) ?> m/s
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
