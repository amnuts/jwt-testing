### To generate keys

```
openssl genrsa -out jwt-key 2048
openssl rsa -in jwt-key -pubout > jwt-key.pub
```

### To simulate services on different machines

```
php -S localhost:9001 index.php
php -S localhost:9002 auth.php
php -S localhost:9003 ws.php
```

