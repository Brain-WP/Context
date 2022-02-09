# Context

Context is package that aims to collect "context" to pass to templates based on a query object.

**Best paired with a template engine**. And maybe with [Hierarchy](https://github.com/Brain-WP/Hierarchy).

----------

[![PHP Quality Assurance](https://github.com/Brain-WP/Context/actions/workflows/php-qa.yml/badge.svg?branch=master)](https://github.com/Brain-WP/Context/actions/workflows/php-qa.yml)
[![codecov.io](https://img.shields.io/codecov/c/github/Brain-WP/Context.svg?style=flat-square)](http://codecov.io/github/Brain-WP/Context?branch=master)
[![license](https://img.shields.io/packagist/l/brain/context.svg?style=flat-square)](http://opensource.org/licenses/MIT)
[![release](https://img.shields.io/github/release/Brain-WP/Context.svg?style=flat-square)](https://github.com/Brain-WP/Context/releases/latest)

----------

## Quick start

Let's assume a couple of classed designed to provide context for the homepage and the singular view,
respectively:

```php
use Brain\Context;

class HomepageContext implements Context\ProviderFactory
{
    public function create(\WP_Query $query, LoggerInterface $logger): ?Provider
    {
        return Context\Provider\ArrayMerge::new(fn() => $query->is_front_page())
            ->addProvider(new MyHeroProvider())
            ->addProvider(new Context\Provider\Posts(['posts_per_page' => 5], 'latest_posts'));
    }
}

class SingularContext implements Context\ProviderFactory
{
    public function create(\WP_Query $query, LoggerInterface $logger): ?Provider
    {
        return Context\Provider\ArrayMerge::new(fn() => $query->is_singular())
            ->addProvider(new Context\Provider\ByCallback(fn() => ['post' => $query->post]))
            ->addProvider(new Context\Provider\Comments(['post_id' => $query->post->ID]));
    }
}
```

Now we can make use of the `Context` class to generate the context for our templates:

```php
namespace MyTheme;

use Brain\Context;

add_action('template_redirect', function () {
    
    $context = Context\Context::new()
        ->withProviderFactory(new HomepageContext())
        ->withProviderFactory(new SingularContext())
        ->provide();
        
    // pass context to templates here ...
});
```

`Context` class emit the action `"brain.context.providers" that can be used to add providers from
different places:

```php
namespace MyTheme;

use Brain\Context;

add_action('brain.context.providers', function (Context\Context $context) {
    $context
        ->withProviderFactory(new HomepageContext())
        ->withProviderFactory(new SingularContext());
});

add_action('template_redirect', function () {
    $context = Context\Context::new()->provide();
    // pass context to templates here ...
});
```

## Examples using Hierarchy

Here's an example of using context in combination with [Brain Hierarchy](https://github.com/Brain-WP/Hierarchy) 
to render mustache templates passing them context.

```php
namespace My\Theme;

use Brain\Hierarchy\{Finder, Loader, QueryTemplate};
use Brain\Context;

class MustacheTemplateLoader implements Loader\Loader
{
   private $engine;

   public function __construct(\Mustache_Engine $engine)
   {
      $this->engine = $engine;
   }

   public function load(string $templatePath): string
   {
        // It will be possible to hook 'brain.context.providers' to add context providers
        $data = Context\Context::new()
            ->convertEntitiesToPlainObjects()
            ->forwardGlobals()
            ->provide();

        return $this->engine->render(file_get_contents($templatePath), $data);
   }
}

add_action('template_redirect', function() {
    if (!QueryTemplate::mainQueryTemplateAllowed()) {
        return;
    }

    $queryTemplate = new QueryTemplate(
        new Finder\BySubfolder('templates', 'mustache'),
        new MustacheTemplateLoader(new \Mustache_Engine())
    );

    $content = $queryTemplate->loadTemplate(null, true, $found);
    $found and die($content);
});
```

Above is *all* the necessary code to render `*.mustache` templates from a `/templates` subfolder
in current theme (or parent theme, if any), according to WP template hierarchy, passing to templates
context data that can be extended via ad-hoc "view context" classes which will implement 
`Context\ProviderFactory` interface.


## Providers

### Composite providers

The "Quick start" section above uses `Context\Provider\ArrayMerge` class to "merge" several 
providers.

Besides that class, there's also a `Context\Provider\ArrayMergeRecursive` "composite" provider.

### Atomic providers

The "composite" providers merge multiple "atomic" providers that can be either custom (anything 
implementing `Context\Provider`) or one of the shipped provider classes:

- `ByArray` - which provides a given array as-is
- `ByCallback` - which provides an array returned by a given callback
- `Comments` - which provides an array of comments using given comment query arguments
- `Posts` - which provides an array of posts using given post query arguments
- `Subquery` - which provides a `WP_Query` instance using given query arguments
- `Terms` - which provides an array of comments using given taxonomy terms query arguments
- `Users` - which provides an array of comments using given user query arguments


### Custom providers

The `Context\Provider` interface has a single method:

```php
public function provide(\WP_Query $query, LoggerInterface $logger): ?array;
```

Which can be implemented to build custom providers. In the case the provider should not be used
based on conditions, it can return `null`.

The given PSR-3 logger interface can be used to log errors and distinguish a provider that returns
null due to errors form another that returns `null` because, for example, not targeting the current 
query. 


## Logger

All providers support a PSR-3 logger. `Context` class implements PSR-3 `LoggerAwareInterface`, so
it is possible to call `setLogger` when instantiating it.

There's also a `"brain.context.logger"` action that passes a callback that can be used to set the
logger:

```php
add_action('brain.context.logger', function (callable $setter) {
    $setter(new MyPsr3Logger());
});
```


## Requirements

Context requires **PHP 7.1+** and [Composer](https://getcomposer.org/) to be installed.


## Installation

Best served by Composer, available on Packagist with name [`brain/context`](https://packagist.org/packages/brain/context).


## License

Context is released under MIT.
