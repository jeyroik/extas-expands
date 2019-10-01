# extas-expands

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
/**
 * @var $request extas\interfaces\servers\requests\IServerRequest
 * @var $response extas\interfaces\servers\responses\IServerResponse
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
  * @param extas\interfaces\servers\requests\IServerRequest $request
  * $param extas\interfaces\servers\responses\IServerResponse
  */
 public function __invoke(IExpandingBox &$box, IServerRequest $request, IServerResponse $response);
 ```
 
 ## Пример плагина для expand'a
 
 ```php
class PluginAppExpandVersion extends Plugin
{
    public function __invoke(IExpandingBox &$box, IServerRequest $request, IServerResponse $response)
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

Дефолтный плагин позволяет определить права доступа к экспанду, а также самостоятельно пропимывает имя expand'a по шаблону `имя_родительской_коробки.имя_экспанда`, т.е. для примера выше имя получится такое же как в примере - `app.version`.

```php
use extas\interfaces\access\IAccess;
use extas\interfaces\expands\IExpandongBox;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;

class PluginAppExpandVersion extends PluginExpandAbstract
{
    protected $access = [
        IAccess::FIELD__SECTION => 'app',
        IAccess::FIELD__SUBJECT => 'index',
        IAccess::FIELD__OPERATION => 'view'
    ];
    
    protected function dispatch(IExpandingBox &$box, IServerRequest $request, IServerResponse $response)
    {
        $box->addToValue('version', getenv('APP_VERSION') ?: '1.0');
    }
}
```

Если в правах доступа не указан объект (`IAccess::FIELD__OBJECT`), то берутся алиасы текущего пользователя.