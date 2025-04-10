<?php
// Riot API 키 설정
$apiKey = "RGAPI-a2e88afc-622b-48c7-ab25-a020913338a6";

// Riot ID가 입력되지 않은 경우 처리
if (!isset($_GET['username']) || empty($_GET['username'])) {
    echo "ユーザーIDを入力してください。";
    exit;
}

// 입력된 Riot ID를 처리
$username = htmlspecialchars($_GET['username']);
if (!strpos($username, '#')) {
    echo "正しい形式のユーザーIDを入力してください (例: Player#1234)。";
    exit;
}

list($gameName, $tagLine) = explode("#", $username);
$gameName = urlencode($gameName);
$tagLine = urlencode($tagLine);

// Riot API로 사용자 정보 가져오기
$accountUrl = "https://asia.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $accountUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Riot-Token: $apiKey",
    "User-Agent: Mozilla/5.0",
    "Accept-Language: ja-JP",
    "Accept-Charset: application/x-www-form-urlencoded; charset=UTF-8",
    "Origin: https://developer.riotgames.com"
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    echo "APIエラー: HTTPコード $httpCode";
    exit;
}

$accountData = json_decode($response, true);
$puuid = $accountData['puuid'] ?? null;

if (!$puuid) {
    echo "ユーザー情報を取得できませんでした。";
    exit;
}

// 최근 5개 매치 ID 가져오기
$matchListUrl = "https://asia.api.riotgames.com/lol/match/v5/matches/by-puuid/{$puuid}/ids?start=0&count=5";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $matchListUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Riot-Token: $apiKey"
]);
$response = curl_exec($ch);
curl_close($ch);

$matches = json_decode($response, true);

if (empty($matches)) {
    echo "最近のマッチデータが見つかりません。";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>検索結果</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto Mono', monospace;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        .results {
            margin: 20px;
            padding: 20px;
            background-color: #161b22;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        .match {
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 1px solid #30363d;z
        }

        .match:last-child {
            border-bottom: none;
        }

        a {
            color: #58a6ff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="results">
        <h2>検索結果</h2>
        <?php foreach ($matches as $match): ?>
            <div class="match">
                <a href="match_detail.php?matchId=<?php echo htmlspecialchars($match); ?>">
                    マッチID: <?php echo htmlspecialchars($match); ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
