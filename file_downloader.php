<?php
/**
 * Developer Name: Roshan Hewawansa
 * Company Name: RED Media Corporation
 * Developer Website: https://dev.redmedia.lk
 * Company Website: https://redmedia.lk
 * Project: Remote File Downloader to cPanel
 * Description: A PHP-based solution to download remote files directly to the server with progress tracking.
 */

define('DOWNLOAD_FOLDER', __DIR__ . '/downloads/');

if (!file_exists(DOWNLOAD_FOLDER)) {
    mkdir(DOWNLOAD_FOLDER, 0777, true);
}

session_start();

// Handle file metadata check
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = $_POST['url'];
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode(['error' => 'Invalid URL']);
        exit;
    }

    $allowed_extensions = ['zip', 'xml', 'json', 'jsonl', 'tar.gz'];
    $file_info = pathinfo($url);
    $extension = strtolower($file_info['extension'] ?? '');

    if (!in_array($extension, $allowed_extensions)) {
        echo json_encode(['error' => 'Unsupported file type']);
        exit;
    }

    $headers = get_headers($url, 1);
    $file_size = isset($headers['Content-Length']) ? $headers['Content-Length'] : 'Unknown';
    $filename = basename($url);

    echo json_encode(['success' => true, 'file' => $filename, 'size' => $file_size, 'url' => $url]);
    exit;
}

// Handle file download
if (isset($_GET['download']) && filter_var($_GET['download'], FILTER_VALIDATE_URL)) {
    $url = $_GET['download'];
    $filename = basename($url);
    $destination = DOWNLOAD_FOLDER . $filename;

    // Initialize progress
    $_SESSION['progress'] = 0;

    $fp = fopen($destination, 'w+');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_NOPROGRESS, false);
    curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $download_size, $downloaded, $upload_size, $uploaded) {
        if ($download_size > 0) {
            $_SESSION['progress'] = round(($downloaded / $download_size) * 100);
        }
    });

    curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
    } else {
        echo json_encode(['success' => true, 'file' => $filename]);
    }

    curl_close($ch);
    fclose($fp);
    exit;
}

// Handle progress requests
if (isset($_GET['progress'])) {
    echo json_encode(['progress' => $_SESSION['progress'] ?? 0]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Downloader</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        input, button {
            padding: 10px;
            margin: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
        }
        input {
            width: 60%;
        }
        button {
            background-color: white;
            color: black;
            cursor: pointer;
        }
        button:hover {
            background-color: gray;
        }
        #progress-container {
            width: 60%;
            margin: 20px auto;
            border: 1px solid white;
            border-radius: 5px;
            background: gray;
            overflow: hidden;
            display: none;
        }
        #progress-bar {
            width: 0;
            height: 20px;
            background: green;
        }
        #progress-text {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Remote File Downloader</h1>
    <form id="download-form">
        <input type="url" id="file-url" placeholder="Enter remote file URL" required>
        <button type="button" onclick="checkFile()">Check File</button>
    </form>
    <div id="file-info" style="display: none;">
        <p><strong>File Name:</strong> <span id="file-name"></span></p>
        <p><strong>File Size:</strong> <span id="file-size"></span></p>
        <button onclick="startDownload()">Download to Server</button>
    </div>
    <div id="progress-container">
        <div id="progress-bar"></div>
    </div>
    <p id="progress-text"></p>

    <script>
        function checkFile() {
            const url = document.getElementById('file-url').value;
            if (!url) {
                alert('Please enter a valid URL.');
                return;
            }

            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'url=' + encodeURIComponent(url)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    document.getElementById('file-info').style.display = 'block';
                    document.getElementById('file-name').innerText = data.file;
                    document.getElementById('file-size').innerText = data.size + ' bytes';
                    document.getElementById('file-url').dataset.url = data.url;
                }
            });
        }

        function startDownload() {
            const url = document.getElementById('file-url').dataset.url;
            if (!url) {
                alert('No file URL available.');
                return;
            }

            // Show progress bar
            document.getElementById('progress-container').style.display = 'block';

            fetch('?download=' + encodeURIComponent(url))
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        monitorProgress();
                    }
                })
                .catch(err => alert('An error occurred: ' + err));
        }

        function monitorProgress() {
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const interval = setInterval(() => {
                fetch('?progress')
                    .then(response => response.json())
                    .then(data => {
                        const progress = data.progress || 0;
                        progressBar.style.width = progress + '%';
                        progressText.innerText = progress + '% Complete';
                        if (progress >= 100) {
                            clearInterval(interval);
                            progressText.innerText = 'Download Complete';
                        }
                    });
            }, 500);
        }
    </script>
</body>
</html>
