<p align="center">
  <a href="https://www.tessa-dam.com/" target="_blank" rel="noopener noreferrer">
    <img src="tessa-logo.svg" width=250 alt="TESSA Logo"/>
  </a>
</p>

<p>&nbsp;</p>

<h1 align="center">
  TESSA Connector for Akeneo 5.0
</h1>

<p>&nbsp;</p>

With this Connector Bundle you seamlessly connect Akeneo with the Digital Asset Management solution "TESSA" (https://www.tessa-dam.com).
This provides you with a professional and fully integrated DAM solution for Akeneo to centrally store,
manage and use all additional files for your products (e.g. images, videos, documents, etc.) in all channels.

More informationen is available at our [website](https://www.tessa-dam.com/).

## Requirements

| Akeneo                        | Version |
|:-----------------------------:|:-------:|
| Akeneo PIM Community Edition  | ~5.0.0  |
| Akeneo PIM Enterprise Edition | ~5.0.0  |

<span style="color:red">__IMPORTANT!__</span> Ensure, that your Akeneo API ist working. Tessa needs an API connection to your Akeneo.
In some cases Apache is configured wrong, see https://api.akeneo.com/documentation/troubleshooting.html#apache-strip-the-authentication-header.

## Installation

1) Install the bundle with composer
```bash
composer require eikona-media/akeneo5-0-tessa-connector
```

2) Then add the following lines **at the end** of your config/routes/routes.yml :
```yaml
tessa_media:
    resource: "@EikonaTessaConnectorBundle/Resources/config/routing.yml"
```

3) Enable the bundle in the `config/bundles.php` file:
```php
return [
    // ...
    Eikona\Tessa\ConnectorBundle\EikonaTessaConnectorBundle::class => ['all' => true],
];

```

4) Run the following commands in your project root:
```bash
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod
php bin/console pim:installer:dump-require-paths --env=prod
php bin/console pim:installer:assets --env=prod
yarn run webpack
yarn run less
yarn run update-extensions
```

5) Update your database schema

```bash
php bin/console doctrine:schema:update --dump-sql # Show changes
php bin/console doctrine:schema:update --force # Update database schema
```

6) Configure the Tessa Connector in your Akeneo System Settings.

7) (Optionally) Create a cronjob to synchronize data with TESSA in the background

This is only necessary if you use the option "Sync in background" in the system settings

```
php bin/console eikona_media:tessa:notification_queue:execute --env=prod
```

Recommended to run every 5 minutes (`*/5 * * * *`). If the command is started twice at the same time, the second command exists with a notice.


## How to use with reference entities

1) Add the following lines **at the end** of your config/routes/routes.yml :
```yaml
tessa_api_reference_data:
  resource: "@EikonaTessaReferenceDataAttributeBundle/Resources/config/routing.yml"
```

2) Enable the ReferenceDataAttributeBundle in the `config/bundles.php` file:
```php
return [
    // ...
    Eikona\Tessa\ConnectorBundle\EikonaTessaConnectorBundle::class => ['all' => true], // Already registered
    Eikona\Tessa\ReferenceDataAttributeBundle\EikonaTessaReferenceDataAttributeBundle::class => ['all' => true], // New
];
```

3) Select TESSA in the type dropdown when you add a new reference entity attribute
