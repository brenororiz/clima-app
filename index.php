<?php
// Função para buscar dados climáticos da API Open-Meteo
function getWeatherData($latitude, $longitude) {
    $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current_weather=true&hourly=temperature_2m,relativehumidity_2m,windspeed_10m";
    $response = file_get_contents($url);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return $data;
}

// Função para obter coordenadas geográficas (latitude e longitude) de uma cidade
function getCoordinates($city) {
    $url = "https://geocoding-api.open-meteo.com/v1/search?name=" . urlencode($city) . "&count=1";
    $response = file_get_contents($url);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    if (isset($data['results'][0])) {
        return [
            'latitude' => $data['results'][0]['latitude'],
            'longitude' => $data['results'][0]['longitude']
        ];
    }

    return null;
}

// Inicializa variáveis
$city = '';
$weatherData = null;
$error = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = trim($_POST['city']);
    if (!empty($city)) {
        $coordinates = getCoordinates($city);
        if ($coordinates) {
            $weatherData = getWeatherData($coordinates['latitude'], $coordinates['longitude']);
            if (!$weatherData) {
                $error = "Não foi possível encontrar dados climáticos para '{$city}'.";
            }
        } else {
            $error = "Cidade não encontrada.";
        }
    } else {
        $error = "Por favor, insira o nome de uma cidade.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicação de Clima</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Consulta de Clima</h1>
        <form id="weatherForm" method="POST" action="">
            <label for="city">Cidade:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
            <button type="submit">Buscar</button>
        </form>

        <!-- Ícone de carregamento -->
        <div id="loading" class="loading hidden">
            <div class="spinner"></div>
            <p>Carregando...</p>
        </div>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif ($weatherData): ?>
            <div class="weather-info animated fadeIn">
                <h2>Clima em <?php echo htmlspecialchars($city); ?></h2>
                <p><strong>Temperatura:</strong> <?php echo round($weatherData['current_weather']['temperature'], 1); ?>°C</p>
                <p><strong>Vento:</strong> <?php echo $weatherData['current_weather']['windspeed']; ?> km/h</p>
                <p><strong>Direção do Vento:</strong> <?php echo $weatherData['current_weather']['winddirection']; ?>°</p>
                <p><strong>Condição:</strong> <?php echo $weatherData['current_weather']['weathercode'] == 0 ? 'Céu Limpo' : 'Nublado'; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html>