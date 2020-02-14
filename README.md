# BuTrackero
## Home Budget Web API

**Technology stack:**
- PHP 7.3
- Symfony 4.4
- MySQL 5.7
- PHPUnit 6.5

**Why it's coded:**
- challenge
- learning
- own use

**What I've learned:**
- basics of Symfony framework
- creating RESTful API
- better understanding of OOP
- how to writing cleaner code
- using good practice and design patterns
- how to refactoring code
- writing simple unit tests
- concepts of TDD methodology
- JWT authentication method
- better understanding of Doctrine ORM
- creating valid entities
- using UUID (instead of auto increment id)
- using code analysis tools
- manual testing API using Postman


**How to configure:**
- git clone https://github.com/kzadlo/butrackero.git
- server configuration: docker or vhost, apache etc.
- composer install
- create config/packages/doctrine.yaml from config/packages/doctrine.yaml.dist and complete database credentials
- create auth keys and complete JWT_PASSPHRASE in .env:
    * openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    * openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
- composer.deploy
- start use! :)