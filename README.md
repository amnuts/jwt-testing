### Getting started

You'll need to fire up three different web servers, which you can do with:

```
php -S localhost:9001 index.php
php -S localhost:9002 auth.php
php -S localhost:9003 ws.php
```

Then just access http://localhost:9001 to test it out.

If you want to test out on different servers, change the `config.php` file.

### To generate keys

If you want to generate new keys, run these commands:

```
openssl genrsa -out jwt-key 2048
openssl rsa -in jwt-key -pubout > jwt-key.pub
```

