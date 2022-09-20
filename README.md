# Slim 3 API skeleton

This is Slim 3 API skeleton project for Composer. Project uses [Zend Table Gateway](https://docs.zendframework.com/zend-db/table-gateway/) and [Phinx](https://phinx.org/) for database operations,  [Monolog](https://github.com/Seldaek/monolog) for logging, and [Fractal](http://fractal.thephpleague.com/) as a serializer. [Vagrant](https://www.vagrantup.com/) virtualmachine config and [Paw](https://geo.itunes.apple.com/us/app/paw-http-rest-client/id584653203?mt=12&at=1010lc2t) project files are included for easy development. The skeleton tries to follow DDD principles.

#Data structure

Main feature is to create and provide by API funnels.
General outline of the database:

`user` table own `website` (with API credentials).
`website` table own `funnel` which is representative for full funnel structure.
`funnel` has nested children defined in `funnel_element` table. 

Every element has type. Element types are defined in `funnel_element_type` table.
Every type has required fields:

`name` - type name (ex. city, keyword, region)

`pattern_url` - url pattern which it will be available in customer site (ex. [keyword]-in-[city])

`title` - meta title (ex. 'This is [city]')

`description` - meta title (ex. 'In [city] You can...')


Additionally every element can have multiple attributes (in `funnel_element_attribute`) 
defined similar way in `funnel_element_type_attribute`, in other words `funnel_element_attribute` 
store valuesfor `funnel_element_type_attribute` keys.

Example structure available by API:

```json
{
    "config": [{
        "name": "Paternity testing",
        "type": "keyword",
        "children": [{
            "name": "Pleasant Grove",
            "type": "city"
        }, {
            "name": "Jack Cullen Drive",
            "type": "city"
        }]
    },  {
        "name": "Mixed Martial Arts testing",
        "type": "keyword",
        "children": [{
            "name": "Pleasant Grove",
            "type": "city"
        }, {
            "name": "Jack Cullen Drive",
            "type": "city"
        }, {
            "name": "Texarkana Texas",
            "type": "city"
        }]
    }],
    "types": {
        "city": {
            "urlPattern": "[keyword]-in-[city]",
            "attributes": [],
            "metaTitlePattern": "Lab in [city]",
            "metaDescriptionPattern": "Lab in [city]"
        },
        "keyword": {
            "urlPattern": "[keyword]",
            "metaTitlePattern": " Labs [keyword]",
            "metaDescriptionPattern": "Labs [keyword]",
            "attributes": []
        }
    }
}
```

## Usage

If you have [Vagrant](https://www.vagrantup.com/) installed start the virtual machine.

``` bash
$ vagrant up
```

### Environment variables for development

Create and `.env` file and place it on the root directory with the following values:

```
DB_DSN=mysql:dbname=tin_dev;host=54.157.89.214
DB_NAME=tin_dev
DB_USER=tin_dev
DB_PASSWORD=KmNvbCv2d
DB_HOST=54.157.89.214
DB_DRIVER=pdo_mysql
DB_PORT=3306
JWT_SECRET=psku0t371wt7i5gkqonmulilzgwl
```



##Migrations
Migrations library docs: https://book.cakephp.org/3.0/en/phinx.html
```
$ phinx migrate -e development
$ php vendor/bin/phinx migrate -e development
```
