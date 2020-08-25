![PHP Composer](https://github.com/jeyroik/extas-expands/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-expands/coverage.svg?branch=master)
<a href="https://github.com/phpstan/phpstan"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat" alt="PHPStan Enabled"></a> 
<a href="https://codeclimate.com/github/jeyroik/extas-expands/maintainability"><img src="https://api.codeclimate.com/v1/badges/93d2094728c65c5f2be5/maintainability" /></a>
[![Latest Stable Version](https://poser.pugx.org/jeyroik/extas-expands/v)](//packagist.org/packages/jeyroik/extas-q-crawlers)
[![Total Downloads](https://poser.pugx.org/jeyroik/extas-expands/downloads)](//packagist.org/packages/jeyroik/extas-q-crawlers)
[![Dependents](https://poser.pugx.org/jeyroik/extas-expands/dependents)](//packagist.org/packages/jeyroik/extas-q-crawlers)

# Описание

Пакет позволяет создавать распаковывающиеся объекты, т.е. примерно такие:

`GET /index`
`Accept: application/json`
Response:
```json
{
  "app": {
    "name": "example app",
    "expand": ["app.version", "app.player"]
  }
}
```

`GET /index?expand=app.version,app.player`
`Accept: application/json`
Response:
```json
{
  "app": {
    "name": "example app",
    "version": "1.0",
    "player": {
      "name": "root",
      "title": "...",
      "description": "...",
      "expand": ["player.aliases", "player.identities", "player.settings"]
    },
    "expand": ["app.version", "app.player"]
  }
}
```

`GET /index?expand_root=app&expand=version,player,player.aliases`

`GET /index?expand=app.version,app.player,player.aliases`
`Accept: application/json`
Response:
```json
{
  "app": {
    "name": "example app",
    "version": "1.0",
    "player": {
      "name": "root",
      "title": "...",
      "description": "...",
      "aliases": ["root", "admin", "authorized"],
      "expand": ["player.aliases", "player.identities", "player.settings"]
    },
    "expand": ["app.version", "app.player"]
  }
}
```

# Применение экспандов

```php
/**
 * @var Psr\Http\Message\RequestInterface $request
 * @var Psr\Http\Message\ResponseInterface $response
 */
$box = Expander::getExpandingBox('index', 'app');
$box->expand($request, $response);
```

В момент вызова метода `expand` произойдёт запуск двух стадий:
 - `expand.app`
 - `expand.index.app`
 
## Интерфейс стадии
 
 ```php 
 /**
  * @param extas\interfaces\expands\IExpandingBox $box
  * @param Psr\Http\Message\RequestInterface $request
  * $param Psr\Http\Message\ResponseInterface $response
  */
 public function __invoke(IExpandingBox &$box, RequestInterface $request, ResponseInterface $response);
 ```
 
 ## Пример плагина для expand'a
 
 ```php
class PluginAppExpandVersion extends Plugin
{
    public function __invoke(IExpandingBox &$box, RequestInterface $request, ResponseInterface $response)
    {
        $box->addExpand($box->getName() . '.version')
            ->addToValue(
                'version',
                '1.0'
            );
    }
}
```

В extas-совместимой конфигурации:

```json
{
  "plugins": [
    {
      "class": "\\PluginAppExpandVersion",
      "stage": "expand.app"
    }
  ]
}
```

 
 Результат применения:
 
 ```php
$box = Expander::getExpandingBox('index', 'app');
$box->expand($request, $response)->pack();

print_r($box->getValue());
```

Результат примерно следующий:

```json
{
  "app": {
    "expand": ["app.version"]
  }
}
```

Чтобы распаковать объект, необходимо параметром `expand` запроса передать `app.version`, тогда результат будет примерно следующим:

```json
{
  "app": {
    "version": "1.0",
    "expand": ["app.version"]
  }
}
```

## Использование дефолтного плагина для expand'a

Дефолтный плагин самостоятельно прописывает имя expand'a по шаблону `имя_родительской_коробки.имя_экспанда`, т.е. для примера выше имя получится такое же как в примере - `app.version`.

```php
use extas\interfaces\access\IAccess;
use extas\interfaces\expands\IExpandongBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PluginAppExpandVersion extends PluginExpandAbstract
{
    protected $access = [
        IAccess::FIELD__SECTION => 'app',
        IAccess::FIELD__SUBJECT => 'index',
        IAccess::FIELD__OPERATION => 'view'
    ];
    
    protected function dispatch(IExpandingBox &$box, RequestInterface $request, ResponseInterface $response)
    {
        $box->addToValue('version', getenv('APP_VERSION') ?: '1.0');
    }
}
```

Если в правах доступа не указан объект (`IAccess::FIELD__OBJECT`), то берутся алиасы текущего пользователя.