<p align="center">
	<img src="https://gitlab.com/jbrassard/revolvairwebclient/raw/4a76604864d0a21dd2c71dced5ef267bcbde389a/src/assets/resources/RevolvAirLogo.png">
</p>

## About RevolvAir

RevolvAir is a IoT and software development project aiming for a better understanding of air pollution. The project is mainly developed and maintained by students.

## Project setup

 - git clone https://gitlab.com/jbrassard/revolvairapi.git
 - composer install
 - php artisan migrate:fresh --seed
 - php artisan passport:keys
 - php artisan passport:install
 - phpunit
 - php -S localhost:8080

To simplify the environment setup, consider using Homestead and Vagrant

https://github.com/laravel/homestead

Think about reading the following tutorial on how to create a REST API with Laravel.

https://atomrace.com/tutoriels-sur-laravel-5-5-comment-creer-une-api-rest-robuste-et-la-deployer-sur-le-web/

## Useful commands

 - php artisan cache:clear
 - php artisan cache:clear --env=testing
 - php artisan route:clear
 - php artisan route:list
 - composer dump-autoload
 - phpunit --filter ClassOrTestName

## Contributors

Christopher Brière, Cédric Toupin, Dominic Jobin, Dominic Michaud, Guillaume Richard, Guillaume Simard et Jasmin Brassard

Alexandra Bacon-Vollant, Marc-Olivier Bouchard, Jonathan Doiron, Jessy Gagnon, Ilef Ikhelef Mohammed, Jean-Philippe Leclerc	
Sébastien Martel, Nicolas Mostert-Bellemare, Vincent-Gabriel Proulx, Olivier Richard, Nicolas Roberge, Jean-Benoît Rossignol, Francis Villeneuve	

Émilien	Cloutier, Alex Collin, Jean-Simon Cormier, Jean-Nicolas Gauthier, Charlie Grenier, Ariane-Isabel Héon, Émile Jacques, Louis-Jacob Lebel, Rodrigo Lisboa Mirco, Pierre-Etienne Morin, Philippe Mostert-Bellemare, Francis Poirier, Williams Tardif, Mickael Tremblay

## Contributing

If you would like to contribute to the project, contact us!

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.


## Comment configurer MailGun
Pour pouvoir envoyer des courriels avec l'application, l'API de MailGun a été utilisé avec le package Bogardo.

Étapes pour configurer un nouvel adresse courriel pour le gestionnaire de l'application:

- Créer un compte MailGun sur https://signup.mailgun.com/new/signup (un compte gratuit peut être créé, mais seulement le domaine Sandbox pourra être utilisé).
- Une fois connecté, accéder Account Settings>Authorized Recipients et y ajouter l'adresse courriel qui recevra les messages.
- Dans le fichier .env, modifier les variables d'environnement liées à MailGun (MAILGUN_DOMAIN, MAILGUN_SECRET, MAILGUN_PUBLIC_SECRET) par les valeurs du domaine Sandbox créé par MailGun (Sur votre compte MailGun -> Domains>sandbox....mailgun.org vous trouverez toutes les informations). Changer également la variable ADMIN_EMAIL par l'adresse courriel que vous avez configuré sur MailGun.

## LIGNES À RAJOUTER/MODIFIER SUR LE FICHIER .ENV POUR L'ENVOI DE COURRIEL

 - MAILGUN_DOMAIN=sandboxd95f2619960245...c58.mailgun.org
 - MAILGUN_SECRET=key-fedf1ca03b2389064...ec8d9af
 - MAILGUN_PUBLIC_SECRET=pubkey-a93d4b73c...52b0f79d6495bff
 - ADMIN_EMAIL=revolvair@yopmail.com

## Installer la librairie Geocoder
Pour installer la librairie Geocoder, nécessaire pour la recherche des stations, utiliser la commande suivante:
$ composer require geocoder-php/google-maps-provider php-http/guzzle6-adapter php-http/message

## Information concernant les tests
Il y a un test qui dépend de l'API de Geocoder et, donc, peut ne pas passer même si notre API fonctionne correctement.
Tests\Feature\GeolocationServiceTest::test_getCoordinatesFromAddress_should_return_coordinates_when_address_is_valid 

## Client

## Information additionnelle pour la validation des courriels

La validation des courriels d'Angular diffère de celle de Laravel. Tandis qu'un courriel de format adresse@courriel est accepté par Angular, la validation de Laravel retournera une erreur. Ainsi, il n'est pas possible d'envoyer des courriels dont l'adresse de l'émetteur suit le format "adresse@courriel".
TODO: changer la validation d'Angular pour que l'utilisateur soit averti avant l'envoi de la requête à l'API.
## License

MIT
