<?php

namespace Demo;

require __DIR__.'/vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

$config = include __DIR__ . '/config.php';

$token = $error = null;
if (!empty($_GET['token'])) {
    try {
        $token = (new Parser())->parse($_GET['token']);
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
<body>

<?php if (!$token instanceof \Lcobucci\JWT\Token): ?>
    <p><a href="<?php echo $config['url']['auth']; ?>">Login with auth provider</a>
    <?php if (!empty($error)): ?>
        <p>Looks like you have a token but it cannot be parsed, the reason being <?php echo $error; ?></p>
        <hr>
        <p><a href="<?php echo $config['url']['client']; ?>">Start afresh</a></p>
    <?php endif; ?>
<?php else: ?>
    <h1>Success!</h1>
    <p>You have logged into the system and have received a token.</p>
    <h3>headers</h3>
    <p><?php s($token->getHeaders()); ?></p>
    <h3>claims</h3>
    <p><?php s($token->getClaims()); ?></p>

    <h1>Web service tests</h1>
    <p><a href="valid" id="test-token" class="ws-test">Test if the token is valid</a></p>
    <p id="test-token-response"></p>
    <p><a href="issuer" id="test-issuer" class="ws-test">Get the issuer</a> (should match <?php echo $token->getClaim('iss'); ?>)</p>
    <p id="test-issuer-response"></p>
    <p><a href="author" id="test-author" class="ws-test">Get the author_id</a> (should match <?php echo $token->getClaim('user')->author_id; ?>)</p>
    <p id="test-author-response"></p>
    <p><a href="email" id="test-email" class="ws-test">Get the user's email</a> (should match <?php echo $token->getClaim('user')->email; ?>)</p>
    <p id="test-email-response"></p>
    <p><a href="fake" id="test-fake" class="ws-test">Fake an invalid token</a></p>
    <p class="test-fake-response">(should throw a 401, check the network logs)</p>

    <hr>
    <p><a href="<?php echo $config['url']['client']; ?>">Start afresh</a></p>

    <script>
        var $token = '<?php echo (string)$token; ?>';
        var $fakeToken = '<?php echo (string)$token; ?>x';

        $(document).on('click', '.ws-test', function(e) {
            e.preventDefault();
            var $id = $(this).attr('id');
            $.ajax({
                url: '<?php echo $config['url']['ws']; ?>/?get=' + $(this).attr('href'),
                dataType: 'json',
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Authorization', 'Bearer ' + ($id == 'test-fake' ? $fakeToken : $token));
                },
                success: function(d) {
                    if (d.hasOwnProperty('token') && d.token !== '') {
                        $token = d.token;
                        if (d.hasOwnProperty(('response'))) {
                            $('#' + $id + '-response').html(d.response);
                        }
                    }
                },
                error: function(d) {
                    $token = null;
                    location.href = '<?php echo $config['url']['client']; ?>';
                },
                crossDomain: true
            });
        });
    </script>
<?php endif; ?>

</body>
</html>
