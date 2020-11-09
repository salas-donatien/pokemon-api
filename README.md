# Pokemon API

## Stack
- Docker
- Symfony 4.4
- MySQL 8.0.19
- PHP 7.4
- Nginx

## Installation instructions

Execute a full installation:

```bash
$ make install
```
The command `make install` will install all dependencies for running application.

If you need it, you can change the configuration in `docker-compose.override.yml` file.
In particular, you may want to change the nginx port. 
Then restart the containers with this command `make restart`. 

```bash
$ make help
```
The command `make help` will output this help screen.

## JWT - JSON Web Tokens 

Install JWT for token authentication:

```bash
$ mkdir config/jwt
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

The password for the installation is inside the file .env (JWT_PASSPHRASE)
