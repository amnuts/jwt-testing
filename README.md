### Getting started

You'll need to fire up three different web servers, which you can do with:

```
php -S localhost:9001 index.php
php -S localhost:9002 auth.php
php -S localhost:9003 ws.php
```

Then just access http://localhost:9001 to test it out.

If you want to use different servers or ports, change the `config.php` file so that it matches where you have located the three different services.

### To generate secret key

Basically just change the key `$secret` for whatever you want.