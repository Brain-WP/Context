Context
=========

> Context is package that aims to collect "context" to pass to templates based on a query object.
> Best paired with a template engine. And maybe with [Hierarchy](https://github.com/Brain-WP/Hierarchy).

----------

[![travis-ci status](https://img.shields.io/travis/Brain-WP/Context.svg?style=flat-square)](https://travis-ci.org/Brain-WP/Context)
[![codecov.io](https://img.shields.io/codecov/c/github/Brain-WP/Context.svg?style=flat-square)](http://codecov.io/github/Brain-WP/Context?branch=master)
[![license](https://img.shields.io/packagist/l/brain/context.svg?style=flat-square)](http://opensource.org/licenses/MIT)
[![release](https://img.shields.io/github/release/Brain-WP/Context.svg?style=flat-square)](https://github.com/Brain-WP/Context/releases/latest)

----------

# TOC

- [What, Why?](#what-why)
- [An assumption: context is based on query](#an-assumption-context-is-based-on-query)
- [Context Providers](#context-providers)
- [Context Collectors](#context-collectors)
- [Context Loader](#context-loader)
- [Examples](#examples)
- [Requirements](#requirements)
- [Installation](#installation)
- [License](#license)

----------

# What, Why?

If you use WordPress the "canonical" way, then you will never need this.

Feel free to close tab, it was a pleasure, anyway.

If you use a template engine with WordPress then, you may probably know what I'm going to talk about.

Most of template engines, "render" templates by using some "context", which often means replace some
placeholders with some values provided (context).

This package aims to solve the question "where that context comes from"?
 
# An assumption: context is based on query

This package makes the assumption that the context to pass to templates is based on a `WP_Query`.

Most of the times it will probably be the "main" query, but it is not enforced (nor assumed) anywhere
in the package.

There are two "things" in this package:

- Context providers
- Context collector

# Context Providers

Context Providers are classes that implements `ContextProviderInterface` or `UpdatableContextProviderInterface`
(which is an extension of the former).

`ContextProviderInterface` has just to methods:

```php
    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function accept(\WP_Query $query);

    /**
     * @return array
     */
    public function provide();
```

The `accept()` method, receives a query object and has to return `true` or `false`.

When it returns `false` the context provided is completely ignored, when it returns `true` the context
will be "collected".

Better explained with an example:

```php
class HomePageContext implements ContextProviderInterface
{

    /**
     * @inheritdoc
     */
    public function accept(\WP_Query $query)
    {
        return $query->is_front_page();
    }

    /**
     * @return array
     */
    public function provide()
    {
        return [
            'welcome_msg'  => 'Hi, welcome to my awesome website!',
            'register_page => get_page_by_title('Register'),
            'in_evidence'  => get_posts(['meta_key' => 'in_evidence', 'meta_value' => 1]),
        ];
    }
}
```

This example implementation can be used to pass some data to templates when the current query is for
the front page.

We don't have to worry that `provide()` method can contain expensive routines, it will only be
run if `accepts()` return `true`, so only when we need the data.

In real world, there will be more and more "providers" like this.

There are things you want to pass to all templates? Create a provider that just return `true` in
its `accept` method :)

The other provider interface, `UpdatableContextProviderInterface`, besides the two methods inherited 
from its parent, has another method:

```php
    /**
     * @return array
     */
    public function update(array $context);
}
```


This method will receive the currently collected context from other provide, giving the chance to 
edit it.

# Context Collectors

Context Collectors are object that collects context form providers. Their interface
`ContextCollectorInterface` extends `ContextProviderInterface`, so a collector has the same two methods
of any other provider, plus another method:

```php
    /**
     * @param ContextProviderInterface $provider
     * @return \Brain\Context\ContextCollectorInterface
     */
    public function addProvider(ContextProviderInterface $provider);
```

that, as you might have guessed, is used to add providers to the collector.

In short, when `provide()` method is called, it returns the context from all the providers that
were added to it.

Tha package ships with one implementation `WpTemplateContextCollector` that uses `array_merge` to 
build a context array from the array "provided" by the added providers.

# Context Loader

When I said that this package contains "two" things, I lied.

It also contain a third thing, a "context loader" that contains a single method, that provide a
convenient way to glue together the "pieces" in the package.

The class is named `WpContextLoader` and the method is `load()` that accepts a query object and return
a complete "collected" context for it.

# Examples

I think best way to explain code, is to show the code. This is why I added to this repository
**two examples**

- One is very basic, as no dependencies, and show the bare-minimum usage of this package
- The second example make use of a template engine (mustache) and of Brain\Hierarchy to build a
  quite advanced WordPress template rendering workflow.


# Requirements

Context requires **PHP 5.5+** and [Composer](https://getcomposer.org/) to be installed.


# Installation

Best served by Composer, available on Packagist with name [`brain/context`](https://packagist.org/packages/brain/context).

# License

Context is released under MIT.
