<?php

namespace Demo;

require __DIR__.'/vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, HEAD, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$unauthd = function() {
    header('HTTP/1.0 401 Unauthorized');
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
$key = new Key('file://./jwt-key.pub');

if (empty($token) || !$token->verify($alg, $key)) {
    $unauthd();
}

switch ($_GET['get']) {
    case 'valid':
        var_dump($token->verify($alg, $key));
        break;
    case 'issuer':
        echo $token->getClaim('iss');
        break;
    case 'author':
        echo $token->getClaim('user')->author_id;
        break;
    case 'email':
        echo $token->getClaim('user')->email;
        break;
    default:
        echo json_encode($token->getClaims());
        break;
}
