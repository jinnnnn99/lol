<?php
// Riot Data Dragon에서 챔피언 데이터 로드
$version = "13.19.1"; // 최신 버전 확인 후 업데이트
$championDataUrl = "https://ddragon.leagueoflegends.com/cdn/{$version}/data/en_US/champion.json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $championDataUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$championData = json_decode($response, true);
$champions = $championData['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>League of Legends 戦績検索</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto Mono', monospace;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        header {
            text-align: center;
            padding: 20px;
            background-color: #161b22;
            border-bottom: 2px solid #30363d;
        }

        .search-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }

        .search-container form {
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        .search-input {
            width: 300px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #0d1117;
            border: 2px solid #30363d;
            color: #c9d1d9;
            font-size: 1em;
            border-radius: 8px;
        }

        .search-button {
            padding: 10px 20px;
            background-color: #58a6ff;
            border: none;
            color: #0d1117;
            font-size: 1em;
            border-radius: 8px;
            cursor: pointer;
        }

        .champion-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin: 20px;
        }

        .champion {
            text-align: center;
            cursor: pointer;
        }

        .champion img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .champion img:hover {
            transform: scale(1.1);
        }

        .champion-name {
            font-size: 0.8em;
            margin-top: 5px;
            color: #58a6ff;
        }
    </style>
</head>
<body>
    <header>
        <h1>League of Legends 戦績検索</h1>
    </header>

    <div class="search-container">
        <form action="match_stats.php" method="GET">
            <input type="text" name="username" class="search-input" placeholder="例: Player#1234" required>
            <button type="submit" class="search-button">検索</button>
        </form>
    </div>

    <div class="champion-grid">
        <?php foreach ($champions as $champion): ?>
            <div class="champion">
                <a href="champion_detail.php?champion=<?php echo htmlspecialchars($champion['id']); ?>">
                    <img src="https://ddragon.leagueoflegends.com/cdn/<?php echo $version; ?>/img/champion/<?php echo htmlspecialchars($champion['image']['full']); ?>" alt="<?php echo htmlspecialchars($champion['name']); ?>">
                    <div class="champion-name"><?php echo htmlspecialchars($champion['name']); ?></div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
１