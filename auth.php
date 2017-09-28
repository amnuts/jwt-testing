<?php

namespace Demo;

require __DIR__.'/vendor/autoload.php';

use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Builder;

$config = include __DIR__ . '/config.php';
$faker = \Faker\Factory::create('en_GB');
$token = (new Builder())->setId(1)
    ->setAudience('http://client.service.dev')
    ->setIssuer('http://auth.service.dev')
    ->setId(uniqid())
    ->set('user', (object)[
        'author_id' => $faker->randomNumber(5),
        'username' => $faker->userName(),
        'email' => $faker->safeEmail(),
        'firstname' => $faker->firstName(),
        'lastname' => $faker->lastName(),
        'roles' => [
            $faker->toLower($faker->jobTitle()),
            $faker->toLower($faker->jobTitle()),
            $faker->toLower($faker->jobTitle())
        ]
    ])
    ->setHeader('test', '1234')
    ->sign(new Sha256(), new Key('file://./jwt-key'))
    ->getToken();

header('refresh:30;url=' . $config['url']['client'] . '/?token=' . (string)$token);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Auth provider</title>
</head>
<body>

<h1>You're logged in!</h1>

<p>This is just an example; naturally you'd actually take a username and password and authenticate them before generating a token!</p>
<p>However, a token has been generated with random information:</p>

<div class="highlight">
    <p>This is your JWT token:</p>
    <p><b><?php echo (string)$token; ?></b></p>
    <p>It has the headers:</p>
    <p><?php s($token->getHeaders()); ?></p>
    <p>It has the claims:</p>
    <p><?php s($token->getClaims()); ?></p>
    <p>It has the payload:</p>
    <p><?php s($token->getPayload()); ?></p>
</div>

<p>Within 30 seconds you'll be redirected back to the client to simulate what the auth provider would naturally do when you've authenticated and you should see that the information there corresponds to that above.</p>

<p><a href="<?php echo $config['url']['client']; ?>/?token=<?php echo (string)$token; ?>">Go there now manually</a></p>

</body>
</html>
