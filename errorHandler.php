<?php

function registerErrorHandling(): void {
    error_reporting(E_ALL); // ← to jest kluczowe
    ini_set('display_errors', '0');

    $logFile = __DIR__ . '/logs/error.log';
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0777, true);
    }

    set_error_handler(function($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    set_exception_handler(function($e) use ($logFile) {
        http_response_code(500);

        $error = [
            'success' => false,
            'error' => 'Server error',
            'details' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]
        ];

        // Log to file
        error_log(
            '[' . date('Y-m-d H:i:s') . "] {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n",
            3,
            $logFile
        );

        echo json_encode($error, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    });
}
