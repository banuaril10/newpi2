<?php
function execPrint($command)
{
    $result = [];
    exec($command, $result);
    echo "<pre>";
    foreach ($result as $line) {
        echo $line . "\n";
    }
    echo "</pre>";
}

// Deteksi OS
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // ----- WINDOWS -----
    if (file_exists('D:\\')) {
        $drive = 'D:';
    } else {
        $drive = 'E:';
    }

    $cmd = $drive . ' && cd \xampp\htdocs\pi && '
        . 'git config --global --add safe.directory "*" && '
        . 'git config --global user.email "banuaril100@gmail.com" && '
        . 'git stash && git pull';

    execPrint($cmd);
    echo "Running on Windows, drive: $drive";

} else {
    // ----- LINUX -----
    $password = "123456"; // ganti dengan password sudo kamu
    $cmd = 'cd /var/www/html/pi && '
        . 'echo "' . $password . '" | sudo -S git config --global --add safe.directory /var/www/html/pi && '
        . 'echo "' . $password . '" | sudo -S git config --global user.email "banuaril100@gmail.com" && '
        . 'echo "' . $password . '" | sudo -S git stash && '
        . 'echo "' . $password . '" | sudo -S git pull && '
        . 'php update.php';

    execPrint($cmd);
    echo "Running on Linux";
}
