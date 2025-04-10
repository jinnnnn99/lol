<?php
// Riot API 키 설정
$apiKey = "RGAPI-a2e88afc-622b-48c7-ab25-a020913338a6";

// 매치 ID 확인
if (!isset($_GET['matchId']) || empty($_GET['matchId'])) {
    echo "マッチIDが指定されていません。";
    exit;
}

$matchId = htmlspecialchars($_GET['matchId']);
$matchDetailUrl = "https://asia.api.riotgames.com/lol/match/v5/matches/{$matchId}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $matchDetailUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Riot-Token: $apiKey"
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    echo "APIエラー: HTTPコード $httpCode";
    exit;
}

$matchData = json_decode($response, true);
$participants = $matchData['info']['participants'] ?? [];

if (empty($participants)) {
    echo "マッチ情報を取得できませんでした。";
    exit;
}

// Summoner ID 기반으로 모스트 챔피언 가져오는 함수
function getMostPlayedChampions($summonerId, $apiKey) {
    $url = "https://asia.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/{$summonerId}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Riot-Token: $apiKey"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $championData = json_decode($response, true);
    if (!empty($championData)) {
        // 상위 3개 챔피언 가져오기
        $topChampions = array_slice($championData, 0, 3);
        return $topChampions;
    }
    return [];
}

// 챔피언 ID -> 이름 변환 함수
function getChampionName($championId) {
    $champions = [
        1 => "Annie", 2 => "Olaf", 3 => "Galio", // 예시, 실제 Riot 데이터 필요
        // 전체 챔피언 데이터를 포함한 JSON 또는 배열로 대체
    ];
    return $champions[$championId] ?? "Unknown";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マッチ詳細</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto Mono', monospace;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        .details {
            margin: 20px;
            padding: 20px;
            background-color: #161b22;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        .player {
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 1px solid #30363d;
        }

        .player:last-child {
            border-bottom: none;
        }

        h2, h3 {
            color: #58a6ff;
        }
    </style>
</head>
<body>
    <div class="details">
        <h2>マッチ詳細</h2>
        <?php foreach ($participants as $player): ?>
            <?php
            // Summoner ID 가져오기
            $summonerId = $player['summonerId'] ?? null;

            // 모스트 챔피언 가져오기
            $mostPlayed = getMostPlayedChampions($summonerId, $apiKey);
            ?>
            <div class="player">
                <h3>サモナー名: <?php echo htmlspecialchars($player['summonerName']); ?></h3>
                <p>チャンピオン: <?php echo htmlspecialchars($player['championName']); ?></p>
                <p>K/D/A: <?php echo htmlspecialchars($player['kills']); ?> / <?php echo htmlspecialchars($player['deaths']); ?> / <?php echo htmlspecialchars($player['assists']); ?></p>
                <p>モストプレイチャンピオン:</p>
                <ul>
                    <?php foreach ($mostPlayed as $champion): ?>
                        <li>
                            <?php echo htmlspecialchars(getChampionName($champion['championId'])); ?> 
                            (ポイント: <?php echo htmlspecialchars($champion['championPoints']); ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
