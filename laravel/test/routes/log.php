<?php

function write_log($content)
{
    $logPath = ROOT_PATH . 'runtime/';

    if (!is_dir($logPath)) {
        mkdir($logPath, 0777, true);
    }

    $content .= "\n";

    file_put_contents($logPath . 'log.txt', $content, FILE_APPEND);
}