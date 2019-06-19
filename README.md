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

The Vue plugin is not there yet, I'm working on it.

### Using the settings

TODO

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

### Using the settings

There are multiple options that you can modify to fit your needs. You will need to instanciate the Translator by calling the constructor by yourself, and pass an object with the following options:

| Setting | Description | Default value |
| ------- | ----------- | ------------- |
| fallbackLocale | A fallback locale | en |
| defaultDomain | The default domain when you don't provide one | messages |
| pluralSeparator | The separator of pluralization (you shouldn't touch that) | \| |
| vueDirectiveName | Sets the vue directive name | trans |
| catalogue | Default catalogue | |
| debug | Outputs messages in console | |
| onUntranslatedMessageCallback | A callback called when a translation could not be found | |

#### Example

```javascript
// assets/js/modules/translator/translator.js

import { Translator } from 'symfony-javascript';
import catalogue from './messages.json';
import wait from 'async-wait-until';

const debug = process.env.NODE_ENV !== 'production';
var missing = [];

const translator = new Translator({
    catalogue: catalogue,
    debug,
    async onUntranslatedMessageCallback(id) {
        if (debug) {
            wait(() => document.querySelector('.sf-toolbar-block-translation'), 3000)
            .then(() => {
                missing.push(id);

                const translationBlock = document.querySelector('.sf-toolbar-block-translation');

                if (!translationBlock) {
                    return;
                }

                translationBlock.classList.add('sf-toolbar-status-red');

                const valueContainer = translationBlock.querySelector('.sf-toolbar-value');
                const toolbarInfo = translationBlock.querySelector('.sf-toolbar-info');
                let value = Number(valueContainer.innerHTML) + missing.length;

                valueContainer.innerHTML = value;

                toolbarInfo.innerHTML += `<div class="sf-toolbar-info-piece">
                    <b><abbr title="You can't see them in the profiler yet: \n${missing.join('\n')}">Javascript missing</abbr></b>
                    <span class="sf-toolbar-status sf-toolbar-status-red">${missing.length}</span>
                </div>`;
            });
        }
    }
});

export default translator.getVuePlugin();
export { translator };
```

TODO
====

- [ ] Tests - I know. I should be developping tests at the same time as I was developping the bundle. I was in a hurry.
- [ ] Upload the Symfony recipe
