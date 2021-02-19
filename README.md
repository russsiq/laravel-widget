## Дополнительный функционал для компонентов Laravel 8.x

- [Введение](#introduction)
- [Установка](#installation)
- [Конфигурирование](#configuration)
- [Написание виджета](#writing-widget)
    - [Генерация класса виджета и его шаблона](#generating-widget)
    - [Структура класса виджета](#widget-structure)
    - [Отрисовка виджета](#rendering-widget)
- [Удаление пакета](#removing-package)
- [Лицензия](#license)

<a name="introduction"></a>
### Введение

Виджеты – обертка для стандартных компонентов Laravel, но с расширенным функционалом: валидация входящих параметров.

> Виджеты поддерживают компоненты только на основе классов и при наличии обязательного отдельного шаблона представления или нескольких шаблонов.

<a name="installation"></a>
### Установка

Для добавления зависимости в новый проект на Laravel, добавьте в файле `composer.json`

```json
"require": {
    // ...
    "russsiq/laravel-widget": "dev-master"
}
```

Для подключения в уже созданный проект, используйте менеджер пакетов Composer:

```console
composer require russsiq/laravel-widget
```

Если в вашем приложении включен отказ от обнаружения пакетов в директиве `dont-discover` в разделе `extra` файла `composer.json`, то необходимо самостоятельно добавить в файле `config/app.php` поставщик службы в раздел `providers`:

```php
'providers' => [
    /*
     * Package Service Providers...
     */
    Russsiq\Widget\WidgetServiceProvider::class,
],
```

<a name="configuration"></a>
### Конфигурирование

Перед использованием пакета вам необходимо сверить соответствие параметра `classes-namespace` конфигурационного файла с желаемым пространством имен классов ваших будущих виджетов. Для этого необходимо опубликовать конфигурационный файл в ваше приложение:

    php artisan vendor:publish --provider="Russsiq\Widget\WidgetServiceProvider" --tag=config

По умолчанию этот параметр имеет значение `App\View\Components\Widgets`.

По желании вы можете опубликовать шаблон, отображающий ошибки валидации входящих параметров виджета:

    php artisan vendor:publish --provider="Russsiq\Widget\WidgetServiceProvider" --tag=views

<a name="writing-widget"></a>
### Написание виджета

<a name="generating-widget"></a>
#### Генерация класса виджета и его шаблона

Чтобы создать виджет, вы можете использовать команду `make:laravel-widget` Artisan. Чтобы проиллюстрировать, как использовать виджеты, мы создадим простой виджет `ArticlesFeatured`:

    php artisan make:laravel-widget ArticlesFeatured

Выполненная команда `make:laravel-widget` поместит по умолчанию класс виджета в каталог `App\View\Components\Widgets`, а его шаблон `articles-featured.blade.php` в `resources\views\components\widgets`.

<a name="widget-structure"></a>
#### Структура класса виджета

Сгенерированный класс виджета будет иметь следующую структуру:

```php
<?php

namespace App\View\Components\Widgets;

use Russsiq\Widget\WidgetAbstract;

class ArticlesFeatured extends WidgetAbstract
{
    /**
     * Get the template name relative to the widgets directory.
     *
     * @var string
     */
    protected $template = 'components.widgets.articles-featured';

    /**
     * Create a new widget instance.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }

    /**
     * Get the validation rules that apply to the widget parameters.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
```

<a name="rendering-widget"></a>
#### Отрисовка виджета

После того, как ваш компонент был создан, он может быть отображен с использованием псевдонима тега c передачей набора необходимых вам параметров через массив `parameters`:

```html
<x-widget::articles-featured :parameters="[
    'parameter_1' => 'value_1',
    'parameter_2' => 'value_2',
]" />
```

<a name="removing-package"></a>
### Удаление пакета

Для удаления пакета из вашего проекта на Laravel используйте команду:

```console
composer remove russsiq/laravel-widget
```

<a name="license"></a>
### Лицензия

`laravel-widget` – программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](LICENSE).

