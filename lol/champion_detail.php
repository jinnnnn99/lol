<?php
// Riot Data Dragon API 설정
$version = "13.19.1"; // 최신 버전 확인 후 업데이트
if (!isset($_GET['champion']) || empty($_GET['champion'])) {
    echo "チャンピオン名が指定されていません。";
    exit;
}

$championId = htmlspecialchars($_GET['champion']);
$championDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$version}/data/ja_JP/champion/{$championId}.json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $championDataUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$championData = json_decode($response, true);
if (!$championData || empty($championData['data'][$championId])) {
    echo "チャンピオン情報を取得できませんでした。";
    exit;
}

$champion = $championData['data'][$championId];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($champion['name']); ?>の詳細</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto Mono', monospace;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        .container {
            margin: 20px;
            padding: 20px;
            background-color: #161b22;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        h1, h2 {
            color: #58a6ff;
        }

        .skill {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($champion['name']); ?> (<?php echo htmlspecialchars($champion['title']); ?>)</h1>
        <p><?php echo htmlspecialchars($champion['lore']); ?></p>

        <h2>スキル</h2>
        <div class="skill">
            <h3>パッシブ: <?php echo htmlspecialchars($champion['passive']['name']); ?></h3>
            <p><?php echo htmlspecialchars($champion['passive']['description']); ?></p>
        </div>
        <?php foreach ($champion['spells'] as $spell): ?>
            <div class="skill">
                <h3><?php echo htmlspecialchars($spell['name']); ?></h3>
                <p><?php echo htmlspecialchars($spell['description']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
