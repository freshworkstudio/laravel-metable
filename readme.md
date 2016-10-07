# Freshwork Metable

## Install 
```bash
composer require freshwork/metable
```
Add `Freshwork\Metable\MetableServiceProvider` to your `app.php` config file

```php
'providers' => [
    ...
    Freshwork\Metable\MetableServiceProvider::class
]
```

Run laravel migrations to install `metas` table on your database
```bash 
php artisan migrate
```

## Usage
Add `Freshwork\Metable\Traits\Metable` trait to your eloquent model

``` php
namespace App;

use Freshwork\Metable\Traits\Metable;

class Post extends Model {
    use Metable;
    ...
}
```
#### Add meta
By the moment, addMeta inmediately executes a sql query to save.

```php
$post->addMeta('key', 'value');
$post->addMeta('foo', ['bar', 'baz']); //saved as json
$post->addMeta('third', 'value1');
$post->addMeta('third', 'value2');

```
#### Get meta
Remember that you can add multiple meta for the same key, so by default, `getMeta()` returns an array, even if there are just one value for that key. You can use the `single` param to get the first value directly 

`getMeta($key, $single = false, $cacheAll = true)`

Eager loading: If `$cacheAll` is set to true, when you getMeta you retrieve all the metas of the model, so the next `getMeta` call don't touch the database. 

```php
$post->getMeta('key'); // ['value']
$post->getMeta('foo'); // [ ['bar', 'baz'] ]
$post->getMeta('third'); // [ 'value1', 'values2' ]

//If you want to get the first element of the array
$post->getMeta('key', true); // 'value'
$post->getMeta('foo', true); // ['bar', 'baz']
$post->getMeta('third', true); // 'value1'
```
#### Get all meta
```php
//Load $post->metadata variable
$post->loadMeta();

//Then you can 
dd($post->metadata->key); //['value']

```

#### Remove meta
```php
//Load $post->metadata variable
$post->removeMeta('key'); //remove all the ocurrences of metas with that key in the current model
```




