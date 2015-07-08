# Mailchimp API v3 - PHP Wrapper [![Build Status](https://travis-ci.org/pacely/mailchimp-api-v3.svg?branch=master)](https://travis-ci.org/pacely/mailchimp-api-v3)

* [Installation](#installation)
* [Usage](#usage)
    * [Pagination](#pagination)
    * [Filtering](#filtering)
    * [Partial Response](#partial-response)
* [Examples](#examples)
    * [Collection object](#collection-object)
    * [Create lists](#create-lists)
    * [Subresources](#subresources)
* [Further documentation](#further-documentation)

# Installation
Add the following to your composer.json

```json
{
    "require": {
        "pacely/mailchimp-apiv3": "dev-master"
    }
}
```

# Usage
There's one method to rule them all:

```php
request($resource, $arguments = [], $method = 'GET') // $arguments is used as POST data or GET parameters, depending on the method used.
```

But its clever enough to map these calls aswell:

```php
get($resource, array $options = [])
head($resource, array $options = [])
put($resource, array $options = [])
post($resource, array $options = [])
patch($resource, array $options = [])
delete($resource, array $options = [])
```

### Pagination
_We use `offset` and `count` in the query string to paginate data, because it provides greater control over how you view your data. Offset defaults to 0, so if you use offset=1, you'll miss the first element in the dataset. Count defaults to 10._

Source: http://kb.mailchimp.com/api/article/api-3-overview

### Filtering
_Most endpoints don't currently support filtering, but we plan to add these capabilities over time. Schemas will tell you which collections can be filtered, and what to include in your query string._

Source: http://kb.mailchimp.com/api/article/api-3-overview

### Partial Response
_To cut down on data transfers, pass a comma separated list of fields to include or exclude from a certain response in the query string. The parameters `fields` and `exclude_fields` are mutually exclusive and will throw an error if a field isn't valid in your request._

Source: http://kb.mailchimp.com/api/article/api-3-overview

# Examples

### Collection object
All queries will return an instance of the [Illuminate\Support\Collection](http://laravel.com/api/master/Illuminate/Support/Collection.html) object, which is really easy to work with. If you don't want to use the Collection object however, you can transform it into an array using `$result->toArray()`.

```php
$mc = new Mailchimp('<api-key>');

// Get 10 lists starting from offset 10 and include only a specific set of fields
$result = $mc->request('lists', [
    'fields' => 'lists.id,lists.name,lists.stats.member_count',
    'offset' => 10,
    'count' => 10
]);

// Will fire this query: 
// GET https://us1.api.mailchimp.com/3.0/lists?fields=lists.id,lists.name,lists.stats.member_count&count=10

// Returns object(Illuminate\Support\Collection)
var_dump($result);

// Returns the first item
var_dump($result->first());

// Returns 3 items
var_dump($result->take(3));

// Returns a JSON string
var_dump($result->toJson());

// Returns an array
var_dump($result->toArray());
```
    
You can use a simple foreach/for loop or use the built in `each(callable $callback)` provided by our Collection object to loop through your items.

```php
$result->each(function ($item) {
    echo $item['name'].' ('.$item['stats']['member_count'].')'.PHP_EOL;
});
```
There's alot more you can do with the [Collection](http://laravel.com/api/master/Illuminate/Support/Collection.html) object.

### Create lists

```php
// All these fields are required to create a new list.
$result = $mc->post('lists', [
    'name' => 'New list',
    'permission_reminder' => 'You signed up for updates on Greeks economy.',
    'email_type_option' => false,
    'contact' => [
        'company' => 'Doe Ltd.',
		'address1' => 'DoeStreet 1',
		'address2' => '',
		'city' => 'Doesy',
		'state' => 'Doedoe',
		'zip' => '1672-12',
		'country' => 'US',
		'phone' => '55533344412'
    ],
    'campaign_defaults' => [
        'from_name' => 'John Doe',
        'from_email' => 'john@doe.com',
        'subject' => 'My new campaign!',
        'language' => 'US'
    ]
]);
```

### Subresources

```php
$result = $mc->get('lists/e04d611199', [
    'fields' => 'id,name,stats.member_count'
]);
```

# Further documentation
You should read through Mailchimp's API v3 [documentation](http://kb.mailchimp.com/api/) (I know, it's pretty rough. Should get better soon.). To find out which resources is available, take a look at the [JSON API Schema for Mailchimp](https://us10.api.mailchimp.com/schema/3.0/).