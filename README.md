# Mailchimp API v3 [![Build Status](https://travis-ci.org/pacely/mailchimp-api-v3.svg?branch=master)](https://travis-ci.org/pacely/mailchimp-api-v3)

Simple PHP wrapper for Mailchimp API v3, used in Pacely(Secret project... Coming soon :-).

You should read through Mailchimp's API v3 [documentation](http://kb.mailchimp.com/api/?utm_source=apidocs&utm_medium=internal_ad&utm_campaign=api_v3) (I know, it's pretty rough. Should get better soon.). 
To find out which resources you can request, take a look at the [JSON API Schema for Mailchimp](https://us10.api.mailchimp.com/schema/3.0/).

**NOTE**: All queries will return an instance of the [Illuminate\Support\Collection](http://laravel.com/api/master/Illuminate/Support/Collection.html) object, which is really easy to work with.
If you don't want to use the Collection object though, you can transform it into an array using `$result->toArray()`.
 
# Install
Add the following to your composer.json

    {
        "require": {
            "pacely/mailchimp-apiv3": "dev-master"
        }
    }
    
# Usage
You are provided with one method: 

    call($resource, $arguments = [], $method = 'GET') // $arguments is used as POST data or GET parameters, depending on the method used.

# Example

    $mc = new Mailchimp('<api-key>');
    
    $lists = $mc->request('lists', [
        'fields' => 'lists.id,lists.name,lists.stats.member_count',
        'count' => 30
    ]);
    
    // Will fire this query: 
    // GET https://us1.api.mailchimp.com/3.0/lists?fields=lists.id,lists.name,lists.stats.member_count&count=30
    
    var_dump($lists); // object(Illuminate\Support\Collection)
    var_dump($lists->toArray()); // array(10) { ... }
    var_dump($lists->first()); // Returns the first item
    var_dump($lists->take(3)); // Returns 3 items
    var_dump($lists->toJson()); // Returns a JSON string
    
You can use a simple foreach/for loop or use the built in `each(callable $callback)` provided by our Collection object to loop through your items.

    $lists->each(function ($item) {
        echo $item['name'].' ('.$item['stats']['member_count'].')'.PHP_EOL;
    });
    
There's alot more you can do with the [Collection](http://laravel.com/api/master/Illuminate/Support/Collection.html) object.
