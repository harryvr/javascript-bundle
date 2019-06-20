# Javascript Bundle

![Packagist Version](https://img.shields.io/packagist/v/hawezo/javascript-bundle.svg?style=flat-square)
![Packagist](https://img.shields.io/packagist/dm/hawezo/javascript-bundle.svg?style=flat-square)
![npm](https://img.shields.io/npm/v/symfony-javascript.svg?style=flat-square)
![npm](https://img.shields.io/npm/dw/symfony-javascript.svg?style=flat-square) 

**Symfony bundle for front-end translation and routing**

Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require hawezo/javascript-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Downloading the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require hawezo/javascript-bundle
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
    
    Hawezo\JavascriptBundle\JavascriptBundle::class => ['all' => true],
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

The bundle provides two commands to extract your route and translation files. 

```console
$ php bin/console javascript:extract-translations
$ php bin/console javascript:extract-routes
```

You will need to include the created file in your Javascript. You will need to run these commands once before launching to production, and each time you edit your routes or translations.

Be sure to clear cache before and after your extractions if you have any trouble.


Working with Javascript
-----------------------

Now that your translation and routes are ready to use, you will need the `symfony-javascript` NPM package. 

```console
# Yarn
$ yarn add symfony-javascript 

# NPM
$ npm install symfony-javascript
```

### Loading the route collection

TODO

The minimal configuration is the following:

```javascript
// assets/js/app.js
import { Router } from './symfony-javascript';
import routes from './routes.json'; // Or .js if you choosed so

const router = new Router({
    collection: routes
});
```

### Using the router

The router has a `path`, `url`, `absoluteUrl`, `logoutUrl` and `logoutPath` that behaves the same as their Twig equivalent, with the Javascript restrictions. 

```javascript
router.path('route_name', {arg: 'value'});
router.url('route_name', {arg: 'value'});
```

### Using with Vue

The Router includes a Vue plugin. 

```javascript
Vue.use(router.getVuePlugin());
``` 

It offers the five methods and instance methods of components, as well as a `path` and `url` directives.

```html
<span v-path="{
    name: 'foo', 
    parameters: {'foo': 'bar'}
}" />

<span v-url="{
    name: 'foo', 
    parameters: {'foo': 'bar'}
}" />

<a :href="path('foo')" />
```

### Router settings reference

The following options are available when passing an object to the Router constructor:

```js
const DefaultSettings = {

	/**
	 * Will display console messages if set to yes.
	 */
	debug: false,

	/**
     * A collection of routes to be loaded by default.
     */
    collection: {},

    /**
     * Forces HTTPS scheme. No effect if `forceCurrentScheme` is true.
     */
    forceHttps: false,
    
    /**
     * Sets the URL generator scheme to the current protocol.
     */
    forceCurrentScheme: false,

    /**
     * Sets a fallback scheme if none is provided.
     */
    scheme: 'http',
    
    /**
     * Sets a fallback base URL if none is provided.
     */
    fallbackBaseUrl: '',
    
    /**
     * Sets a fallback host if none is provided.
     */
    fallbackHost: '',
    
    /**
     * The name of the Vue directive for translating.
     */
    vueDirectiveName: 'trans',
};
```

---

### Loading the translation catalogue

The minimal configuration is the following:

```javascript
// assets/js/app.js

import { Translator } from 'symfony-javascript';
import translations from './messages.json'; // Or .js if you choosed so

let i18n = new Translator({
    catalogue: translations
});
```

### Using the Translator

The `trans` method of the `Translator` object behave the same as the Symfony translation component one. Credits to @willdurand for his work.

```javascript
// assets/js/app.js

// Catalogue contains { 'en': { 'security': { 'login.label': 'Login' }} }
import i18n from 'symfony-javascript';

// trans(id, domain, locale)
i18n.trans('login.label', 'security'); // outputs "Login"
```

### Using with Vue

The Translator includes a Vue plugin. 

```javascript
Vue.use(translator.getVuePlugin());
``` 

It offers the global `v-trans` directive and the `trans` method. It takes an object as a parameter, which can contains the following keys: 

```html
<span v-trans="{
    key: 'foo.bar', 
    parameters: {'foo': 'bar'},
    domain: 'foo-bar', 
    locale: 'en'
}" />
```

### Translator settings reference

```javascript
const DefaultSettings = {

	/**
     * The fallback locale for untranslated messages.
     */
	fallbackLocale: 'en',

	/**
     * The default domain for message translations.
     */
	defaultDomain: 'messages',

	/**
     * A callback executed when the message does not have a translation.
     */
	// eslint-disable-next-line no-unused-vars
    onUntranslatedMessageCallback: (_id, _domain, _locale) => {},
    
    /**
     * Adds missing messages in Symfony's web debug toolbar. This is experimental and only available when debug is true.
     */
    addMissingInWebToolbar: false,

	/**
	 * Will display console messages if set to yes.
	 */
	debug: false,

	/**
     * A catalogue to be loaded.
     */
	catalogue: {},

	/**
     * The character separating multiple translations for pluralization.
     */
    pluralSeparator: '|',
    
    /**
     * The name of the Vue directive for translating.
     */
    vueDirectiveName: 'trans',

	sPluralRegex: new RegExp(/^\w+: +(.+)$/),
	cPluralRegex: new RegExp(/^\s*((\{\s*(-?\d+[\s*,\s*\-?\d+]*)\s*\})|([[\]])\s*(-Inf|-?\d+)\s*,\s*(\+?Inf|-?\d+)\s*([[\]]))\s?(.+?)$/),
	iPluralRegex: new RegExp(/^\s*(\{\s*(\?\d+[\s*,\s*\-?\d+]*)\s*\})|([[\]])\s*(-Inf|-?\d+)\s*,\s*(\+?Inf|-?\d+)\s*([[\]])/),
};
```

#### Example

```javascript
// assets/js/modules/translator/translator.js

import { Translator } from 'symfony-javascript';
import catalogue from './messages.json';

const debug = process.env.NODE_ENV !== 'production';

const translator = new Translator({
    debug,
    catalogue: require('./symfony/messages.json'),
    addMissingInWebToolbar: true
});

export default translator.getVuePlugin();
export { translator };
```

TODO
====

- [ ] Tests - I know. I should be developping tests at the same time as I was developping the bundle. I was in a hurry.
- [ ] Upload the Symfony recipe
