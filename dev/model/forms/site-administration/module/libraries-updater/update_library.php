<?php
header('Content-Type: application/json');

function fetchLatestVersion($repo)
{
    $url = "https://api.github.com/repos/$repo/releases/latest";
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: MyApp"
        ]
    ];
    $context = stream_context_create($opts);
    $json = file_get_contents($url, false, $context);
    $latestData = json_decode($json, true);
    return str_replace('v', '', $latestData['tag_name']);
}

function downloadAndReplace($url, $filePath, $oldVersion)
{
    $dir = dirname($filePath) . '/backups/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    if (file_exists($filePath)) {
        rename($filePath, $dir . basename($filePath) . '.' . $oldVersion . '.bak');
    }

    $content = file_get_contents($url);
    
    if ($content) {
        file_put_contents($filePath, $content);
    }
}

// Define your libraries map
$repos = [
    'bootstrap' => [
        'repo' => 'twbs/bootstrap',
        'files' => [
            'css' => 'css/bootstrap.min.css',
            'js'  => 'js/bootstrap.bundle.min.js'
        ]
    ],
    'fontawesome' => [
        'repo' => 'FortAwesome/Font-Awesome',
        'files' => [
            'css' => 'css/all.min.css',
            'js'  => 'js/all.min.js'
        ]
    ],
    'jquery' => [
        'repo' => 'jquery/jquery',
        'files' => [
            'js' => 'jquery.min.js'
        ]
    ]
];

// Get POST params
$library = $_POST['library'];
$fileType = $_POST['type'];

$libData = json_decode(file_get_contents('libraries.json'), true);

foreach ($libData as &$lib) {
    if ($lib['name'] === $library) {
        $repoInfo = $repos[$library];
        $latest = fetchLatestVersion($repoInfo['repo']);

        foreach ($lib['files'] as $file) {
            if ($file['type'] === $fileType) {
                // Build download URL
                $cdnPath = $repoInfo['files'][$fileType];
                if ($library === 'bootstrap') {
                    $url = "https://cdn.jsdelivr.net/npm/bootstrap@$latest/dist/$cdnPath";
                } elseif ($library === 'fontawesome') {
                    $url = "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@$latest/$cdnPath";
                } elseif ($library === 'jquery') {
                    $url = "https://cdn.jsdelivr.net/npm/jquery@$latest/dist/$cdnPath";
                }
                
                $projectRoot = realpath(__DIR__ . "/../../../../../.."); 
                
                $filePath = $projectRoot . "/assets/$library/" . basename($file['path']);
                downloadAndReplace($url, $filePath, $lib['current_version']);

            }
        }

        $lib['current_version'] = $latest;
    }
}

// Save new version
file_put_contents('libraries.json', json_encode($libData, JSON_PRETTY_PRINT));

echo json_encode(['status' => 'success', 'message' => "$library $fileType updated"]);
