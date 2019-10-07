# Javascript Bundle

<div align="center">
<b>Symfony bundle for front-end translation and routing</b>

![Packagist Version](https://img.shields.io/packagist/v/symfony-javascript/javascript-bundle.svg?style=flat-square)
![Packagist](https://img.shields.io/packagist/dm/symfony-javascript/javascript-bundle.svg?style=flat-square)
![npm](https://img.shields.io/npm/v/@symfony-javascript/router.svg?style=flat-square)
![npm](https://img.shields.io/npm/dw/@symfony-javascript/router.svg?style=flat-square) 
![npm](https://img.shields.io/npm/v/@symfony-javascript/translator.svg?style=flat-square)
![npm](https://img.shields.io/npm/dw/@symfony-javascript/translator.svg?style=flat-square) 
</div>

Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require symfony-javascript/javascript-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Downloading the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require symfony-javascript/javascript-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enabling the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
    // ...
    
    SymfonyJavascript\JavascriptBundle\JavascriptBundle::class => ['all' => true],
];
```

### Step 3: Configuring the Bundle

You can configure the bundle if the default values do not suit your project. I recommand changing the extract paths to your needs.

```yaml
# config/packages/javascript.yaml

javascript:
    translation:

        # The extract path of the message file.
        extract_path: public/build/messages.js

        # The locales to be exported
        locales: []

        # The domains to be exported
        domains: []
    routing:

        # The extract path of the message file.
        extract_path: public/build/routes.js

        # Defines a list of routes to be exposed or hidden, depending on the whitelist argument. Supports RegEx.
        routes: []

        # Defines wehther or not the routes parameter will be used as a whitelist.
        whitelist: false
```

Usage
=====

Using the commands
------------------

The bundle provides two commands to extract your route and translation files. It also provides a command to extract them all.

```console
$ php bin/console javascript:extract:translations
$ php bin/console javascript:extract:routes
$ php bin/console javascript:extract:all
```

You will need to include the created file in your Javascript. You will need to run these commands once before launching to production, and each time you edit your routes or translations.

Be sure to clear cache before and after your extractions if you have any trouble.


Working with Javascript
-----------------------

### Step 1: Installation

You can chose to use whatever you want, but there are three usable libraries. There is a router, a translator, and a [Vue](vuejs/vu) adapter. 

Either install everything:
```console
$ yarn add @symfony-javascript/vue-adapter @symfony-javascript/router @symfony-javascript/translator
```

Or only what you want
```console
$ yarn add @symfony-javascript/vue-adapter 
$ yarn add @symfony-javascript/router 
$ yarn add @symfony-javascript/translator
```

### Step 2: Configuration

The easiest way is to configure the bundle to export the `messages` and `routes` files as `json` in a directory containing everything related to this bundle.

```yaml
# config/packages/javascript.yaml
javascript:
    translation:
        extract_path: assets/js/symfony/messages
    routing:
        extract_path: assets/js/symfony/routes
```

#### Using Vue

You will then need to configure both the `router` and `translator`. Here is a good starter that you can use with minimal changes required.

```javascript
// assets/js/symfony/index.ts
import Vue, { VueConstructor } from 'vue';
import { VueRouter, VueTranslator } from '@symfony-javascript/vue-adapter';
import { RouterSettings } from '@symfony-javascript/router/dist/router/RouterSettings';
import { Router } from '@symfony-javascript/router/dist/router/Router';
import { TranslatorSettings } from '@symfony-javascript/translator/dist/translator/TranslatorSettings';
import { Translator } from '@symfony-javascript/translator/dist/translator/Translator';
import messages from './messages.json';
import routes from './routes.json';

const debug = process.env.NODE_ENV !== 'production';

const RouterConfig: RouterSettings = {
  data: routes,
  forceCurrentScheme: true,
  fallbackScheme: 'https',
  debug,
};

const TranslatorConfig: TranslatorSettings = {
  data: messages,
  locale: 'fr',
  strategy: 'strict',
  debug,
};

export const translator = new Translator(TranslatorConfig);
export const router = new Router(RouterConfig);

export default function(Vue: VueConstructor<Vue>) {
  Vue.use(VueRouter, RouterConfig);
  Vue.use(VueTranslator, TranslatorConfig);
}
```

> **Note 1** - I exported a standalone `translator` and `router` in case you need them to configure another extension. It's especially usefull for Vue extension that require internationalization.

> **Note 2** - This example is in TypeScript, but nothing it's pretty much the same in Javascript, just remove the typings.

```javascript
// assets/js/main.ts
import Vue from 'vue';
import RegisterSymfony from './symfony';

RegisterSymfony(Vue);

// initialize your Vue below
```

```javascript
// assets/js/Components/SomeComponent.vue
<script>
export default {
    mounted() {
        this.$_('some_message', {}, 'messages', 'en'); // translate something
        this.$path('some_route', {}); // generate a route
        this.$router.path('some_route', {}); // same as above
        this.$router.url('some_route', {}); // same as above, but full URL
        this.$router.logoutPath(); // get the logout path
        this.$router.logoutUrl(); // get the logout url
    }
}
</script>
```

#### Without Vue

You can totally use this without Vue. Configuration is pretty much the same.

```javascript
// assets/js/symfony/index.ts
import { RouterSettings } from '@symfony-javascript/router/dist/router/RouterSettings';
import { Router } from '@symfony-javascript/router/dist/router/Router';
import { TranslatorSettings } from '@symfony-javascript/translator/dist/translator/TranslatorSettings';
import { Translator } from '@symfony-javascript/translator/dist/translator/Translator';
import messages from './messages.json';
import routes from './routes.json';

const debug = process.env.NODE_ENV !== 'production';

const RouterConfig: RouterSettings = {
  data: routes,
  forceCurrentScheme: true,
  fallbackScheme: 'https',
  debug,
};

const TranslatorConfig: TranslatorSettings = {
  data: messages,
  locale: 'fr',
  strategy: 'strict',
  debug,
};

export const translator = new Translator(TranslatorConfig);
export const router = new Router(RouterConfig);
```

```javascript
// assets/js/scripts/some-file.js
import { translator, router } from '../symfony';

translator.trans('some_message', {}, 'messages', 'en'); // translate something
$router.path('some_route', {}); // same as above
$router.url('some_route', {}); // same as above, but full URL
$router.logoutPath(); // get the logout path
$router.logoutUrl(); // get the logout url
```

#### Router Configuration

The `Router` constructor can take an object containing its settings. 

| Property      | Default Value | Possible Values | Description |
| ------------- | ------------- | --------------- | ----------- |
| data  | {}  | Any object containing a valid routing configuration | An object containing the routing configuration, exported from the bundle.
| debug  | false  | true \| false | Enables debugging (actually does nothing for now)
| forceHttps | false | true \| false | Forces HTTPS on generated links
| forceCurrentScheme | false | true \| false | Force the current protocol on generated links. If yout current page is on `http`, all links will be `http`.
| fallbackScheme | `https` | Any protocol | Will be the scheme if no scheme is provided in the routing configuration.
| fallbackBaseUrl | '' | Any URL | Will be the fallback base URL if none is provided in the routing configuration.
| fallbackHost | '' | Any host | Will be the fallback host if none is provided in the routing configuration.

#### Translator Configuration

The `Translator` constructor can take an object containing its settings. 

| Property      | Default Value | Possible Values | Description |
| ------------- | ------------- | --------------- | ----------- |
| data  | {}  | Any object containing a valid translation configuration | An object containing the translation configuration, exported from the bundle.
| debug  | false  | true \| false | Enables debugging (actually does nothing for now)
| locale | `en` | Any language string | The default locale for translating.
| domain | `domain` | Any domain | The default domain for translating.
| strategy | `strict` | `strict` \| `fallback` | If `strict`, the translator will translate messages matching the exact domain and locale. If `fallback`, the translator will try to translate message from the domain and locale, and will fallback to the configured locales.
| pluralVariables | `%count%`, `{{ count }}`, `{{count}}`, `$count` | An array of string | The variables the translator will look for in order to pluralize a translation. See [below](#translator-pluralization).
| pluralSeparator | `|` | Any character | The separator for pluralization. Do not changed that unless you know what to do. Default Symfony language files don't need to change that.

#### Translator Pluralization

As you probably know, the Translator component of Symfony is able to pluralize. To do so, ou can pass a number variable that will determine which translation will be used.

For example, take this translation:

```yaml
item_selected.precise: One item|$count items
item.selected: One item|Multiple items
```

```javascript
translator.trans('item.selected.precise', { $count: 1 }); // Output: One item
translator.trans('item.selected.precise', { $count: 2 }); // Output: 2 items
translator.trans('item.selected', { $count: 2 }); // Output: Multiple items
```

#### Adding translation messages at runtime

The translator has a `add` method that allows you to add messages to its catalogue at runtime. 

```javascript
translator.add('some_key', 'Some translated content', 'messages', 'en');
```

If you omit the domain or locale, the ones provided in your current configuration will be used.

The typing of the `add` method is the following: 

```typescript
add(id: string, value: string, domain?: string, locale?: string): this;
```

#### Translation events

There are two events available: `messageNotFound` and `translated`. I don't know why you would need them, but I felt like adding them anyway.


```javascript
translator.on('messageNotFound', (data) => {
    console.warn(`Message '${data.id}' does not exist in domain '${data.domain}' for locale '${data.locale}'.`);
}));

// the `.off` method exists as well
```

The `data` variable is a `MessageEventHandler` object, which has the following typing: 

```typescript
interface MessageEventHandler {
    id?: string;
    domain?: string;
    locale?: string;
    catalogue?: Catalogue;
    variables?: Variables;
}
```

### Types

The libraries are written in TypeScript, so their types are available. As a reference, here is the router methods:

```typescript
settings: RouterSettings;
collection: RouteCollection | undefined;
getCollection(): RouteCollection;
url(name: string, parameters?: any, schemeRelative?: boolean): string;
path(name: string, parameters?: any): string;
absoluteUrl(path: string, schemeRelative?: boolean): string;
logoutUrl(): string | null;
logoutPath(): string | null;
```

And the translator has the following:

```typescript
settings: TranslatorSettings;
readonly catalogue: Catalogue;
add(id: string, value: string, domain?: string, locale?: string): this;
trans(id: string, variables?: Variables, domain?: string, locale?: string): string;
on(event: MessageEvents, handler: {
    (data: MessageEventHandler): void;
}): void;
off(event: MessageEvents, handler: {
    (data: MessageEventHandler): void;
}): void;
```
