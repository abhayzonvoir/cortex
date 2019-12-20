# Quickly dump and load databases

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-db-snapshots.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-db-snapshots)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-db-snapshots/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-db-snapshots)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-db-snapshots.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-db-snapshots)
[![StyleCI](https://styleci.io/repos/85295298/shield?branch=master)](https://styleci.io/repos/85295298)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-db-snapshots.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-db-snapshots)

This package provides Artisan commands to quickly dump and load databases in a Laravel application.

```bash
# Create a dump
php artisan snapshot:create my-first-dump

# Make some changes to your db
# ...

# Create another dump
php artisan snapshot:create my-second-dump

# Load up the first dump
php artisan snapshot:load my-first-dump

# List all snapshots
php artisan snapshot:list

# Remove old snapshots. Keeping only the most recent
php artisan snapshot:cleanup --keep=2
```

This package supports MySQL, PostgreSQL and SQLite.

## Installation

You can install the package via Composer:

``` bash
composer require spatie/laravel-db-snapshots
```

You should add a disk named `snapshots` to `app/config/filesystems.php` on which the snapshots will be saved. This would be a typical configuration:

```php
// ...
'disks' => [
    // ...
    'snapshots' => [
        'driver' => 'local',
        'root' => database_path('snapshots'),
    ],
// ...    
```

Optionally, you may publish the configuration file with:

```bash
php artisan vendor:publish --provider="Spatie\DbSnapshots\DbSnapshotsServiceProvider" --tag="config"
```

This is the content of the published file:

```php
return [

    /**
     * The name of the disk on which the snapshots are stored.
     */
    'disk' => 'snapshots',

    /**
     * The connection to be used to create snapshots. Set this to null
     * to use the default configured in `config/databases.php`
     */
    'default_connection' => null,

    /**
     * The directory where temporary files will be stored.
     */
    'temporary_directory_path' => storage_path('app/laravel-db-snapshots/temp'),

    /*
     * Create dump files that are gzipped
     */
    'compress' => false,
];
```

## Usage

To create a snapshot (which is just a dump from the database) run:

```bash
php artisan snapshot:create my-first-dump
```

Giving your snapshot a name is optional. If you don't pass a name the current date time will be used:

```bash
# Creates a snapshot named something like `2017-03-17 14:31`
php artisan snapshot:create
```

When creating snapshots, you can optionally create compressed snapshots.  To do this either pass the `--compress` option on the command line, or set the `db-snapshots.compress` configuration option to `true`:

```bash
# Creates a snapshot named my-compressed-dump.sql.gz
php artisan snapshot:create my-compressed-dump --compress
```

After you've made some changes to the database you can create another snapshot:

```bash
php artisan snapshot:create my-second-dump
```

To load a previous dump issue this command:

```bash
php artisan snapshot:load my-first-dump
```

To load a previous dump to another DB connection:

```bash
php artisan snapshot:load my-first-dump --connection=connectionName
```

To list all the dumps run:

```bash
php artisan snapshot:list
```

A dump can be deleted with:

```bash
php artisan snapshot:delete my-first-dump
```

To remove all backups except the most recent 2

```bash
php artisan snapshot:cleanup --keep=2
```

## Events

There are several events fired which can be used to perform some logic of your own:

- `Spatie\DbSnapshots\Events\CreatingSnapshot`: will be fired before a snapshot is created
- `Spatie\DbSnapshots\Events\CreatedSnapshot`: will be fired after a snapshot has been created
- `Spatie\DbSnapshots\Events\LoadingSnapshot`: will be fired before a snapshot is loaded
- `Spatie\DbSnapshots\Events\LoadedSnapshot`: will be fired after a snapshot has been loaded
- `Spatie\DbSnapshots\Events\DeletingSnapshot`: will be fired before a snapshot is deleted
- `Spatie\DbSnapshots\Events\DeletedSnapshot`: will be fired after a snapshot has been deleted

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
