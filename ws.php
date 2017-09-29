<?php

namespace Demo;

require __DIR__.'/vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

$signer = include __DIR__ . '/signer.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, HEAD, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept');
header('Content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$unauthd = function() {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['error' => 'unauthorized access']);
    exit;
};

$jwt = null;
if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
    if (preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        $jwt = $matches[1];
    }
}

if (empty($jwt)) {
    $unauthd();
}

try {
    $token = (new Parser())->parse($jwt);
} catch (\Exception $e) {}

$alg = new Sha256();
$key = new Key($signer['hmac']);

if (empty($token) || !$token->verify($alg, $key)) {
    $unauthd();
}

$response = null;
switch ($_GET['get']) {
    case 'valid':
        $response = var_export($token->verify($alg, $key), true);
        break;
    case 'issuer':
        $response = $token->getClaim('iss');
        break;
    case 'author':
        $response = $token->getClaim('user')->author_id;
        break;
    case 'email':
        $response = $token->getClaim('user')->email;
        break;
    default:
        $response = $token->getClaims();
        break;
}

echo json_encode(['response' => $response, 'token' => (string)$token]);



