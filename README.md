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
use extas\components\Item;
use extas\components\expands\Expand;
/**
 * @var Psr\Http\Message\RequestInterface $request
 * @var Psr\Http\Message\ResponseInterface $response
 */
$app = new class([
    'name' => 'example app'
]) extends Item {
    protected function getSubjectForExtension() : string{
        return 'app';
    }
};
$expand = new Expand([
    Expand::FIELD__PSR_REQUEST => $request,
    Expand::FIELD__PSR_RESPONSE => $response,
    Expand::FIELD__ARGUMENTS => [
        Expand::ARG__EXPAND => 'app.version'
    ]
]);
$app = $expand->expand($app);
```

В момент вызова метода `expand` произойдёт запуск двух стадий:
 - `extas.expand.parse` : на этой стадии разбирается строка `expand` из аргументов (`Expand::FIELD__ARGUMENTS`).
 - `extas.expand.app.version` : на этой стадии происходит сама распаковка.
 
В extas-совместимой конфигурации:

```json
{
  "plugins": [
    {
      "class": "\\PluginAppExpandVersion",
      "stage": "extas.expand.app.version"
    }
  ]
}
```

 
 Результат применения:
 
 ```php
use extas\components\Item;
use extas\components\expands\Expand;
/**
 * @var Psr\Http\Message\RequestInterface $request
 * @var Psr\Http\Message\ResponseInterface $response
 */
$app = new class([
    'name' => 'example app'
]) extends Item {
    protected function getSubjectForExtension() : string{
        return 'app';
    }
};
$expand = new Expand([
    Expand::FIELD__PSR_REQUEST => $request,
    Expand::FIELD__PSR_RESPONSE => $response,
    Expand::FIELD__ARGUMENTS => [
        Expand::ARG__EXPAND => 'app.version'
    ]
]);
$app = $expand->expand($app);

print_r($app->__toArray());
```

Результат примерно следующий:

```php
Array
(
  "name" => "example app"
  "version" => "1.0"
  "expand" => ["app.version", "app.player"]
)
```

# Плагины из коробки

Пакет из коробки предоставляет два плагина для парсинга:

- Поддержка вайлдкарда: позволяет использовать экспанды вида `app.*`, которые распакуют всё, что есть для сущности.
- Проверка на пустоту: удаляет пустые элементы экспанда

Чтобы подключить эти плагины, их необходимо импортировать:

`extas.json`

```json
{
  "import": {
    "from": {
        "extas/expands": {
          "plugins": "*"
        }
    },
    "parameters": {
        "on_miss_package": {
          "name": "on_miss_package",
          "value": "continue"
        },
        "on_miss_section": {
          "name": "on_miss_section",
          "value": "throw"
        }
    }
  }
}
```