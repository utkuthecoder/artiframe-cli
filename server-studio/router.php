<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$projectRoot = getenv('ARTIFRAME_PROJECT_ROOT');
$publicDir = $projectRoot . '/public';
$requestedFile = $publicDir . $uri;

$resolvedFile = 'public/index.php';
$isStatic = false;

if ($uri !== '/' && file_exists($requestedFile) && !is_dir($requestedFile)) {
    $resolvedFile = 'public' . $uri;
    $isStatic = true;
}

$globalErrorMessage = null;

// Function to send log to Electron
function sendLogToElectron($statusCode = null) {
    $loggerPort = getenv('ARTIFRAME_STUDIO_LOGGER_PORT');
    if (!$loggerPort) return;
    
    global $uri, $resolvedFile, $globalErrorMessage, $startTime;
    
    if ($statusCode === null) {
        $statusCode = http_response_code() ?: 200;
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            $statusCode = 500;
            if (!$globalErrorMessage) {
                $globalErrorMessage = $error['message'] . ' in ' . $error['file'] . ':' . $error['line'];
            }
        }
    }
    
    $parsedParams = [];
    foreach ($_GET as $k => $v) {
        $val = is_array($v) ? 'Array' : $v;
        $parsedParams[] = '$_GET[\'' . $k . '\'] = ' . $val;
    }
    
    $log = [
        'time' => date('H:i:s'),
        'method' => strtoupper($_SERVER['REQUEST_METHOD']),
        'url' => $_SERVER['REQUEST_URI'],
        'resolvedFile' => $resolvedFile,
        'params' => implode(', ', $parsedParams),
        'statusCode' => $statusCode,
        'errorMessage' => $globalErrorMessage,
        'duration' => isset($startTime) ? round((microtime(true) - $startTime) * 1000) . 'ms' : '0ms',
        'memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB'
    ];
    
    $payload = json_encode($log);
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => $payload,
            'timeout' => 0.5
        ]
    ];
    $context  = stream_context_create($options);
    @file_get_contents("http://127.0.0.1:{$loggerPort}", false, $context);
}

// Register shutdown to guarantee logging even on fatals
register_shutdown_function('sendLogToElectron');

if ($isStatic) {
    return false;
}

// Intercept output to capture status code for index.php execution
ob_start();

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicDir . '/index.php';
chdir($publicDir);

try {
    require $publicDir . '/index.php';
} catch (\Throwable $e) {
    $globalErrorMessage = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    echo "<h1>500 Internal Server Error</h1>";
    echo "<pre>" . $globalErrorMessage . "\n" . $e->getTraceAsString() . "</pre>";
    http_response_code(500);
}

$output = ob_get_clean();
$statusCode = http_response_code() ?: 200;

// Apply JSON 404 Guard if needed
$isJson404GuardActive = file_exists(__DIR__ . '/.json-404-guard');
if ($statusCode === 404 && $isJson404GuardActive) {
    header('Content-Type: application/json');
    $output = json_encode(['error' => 'Not Found', 'message' => 'JSON 404 Guard is active.', 'code' => 404]);
}

echo $output;
