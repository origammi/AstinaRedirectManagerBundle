# Astina Redirect Manager Bundle

[![Build Status](https://travis-ci.org/astina/AstinaRedirectManagerBundle.png?branch=master)](https://travis-ci.org/astina/AstinaRedirectManagerBundle)
[![Latest Stable Version](https://poser.pugx.org/astina/redirect-manager-bundle/v/stable.png)](https://packagist.org/packages/astina/redirect-manager-bundle)
[![Total Downloads](https://poser.pugx.org/astina/redirect-manager-bundle/downloads.png)](https://packagist.org/packages/astina/redirect-manager-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d7f07a9b-ba1d-466a-84ec-a09f97dd6a75/mini.png)](https://insight.sensiolabs.com/projects/d7f07a9b-ba1d-466a-84ec-a09f97dd6a75)

## Install

### Step 1: Add to composer.json

```
"require" :  {
    // ...
    "astina/redirect-manager-bundle":"dev-master",
}
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Astina\Bundle\RedirectManagerBundle\AstinaRedirectManagerBundle(),
    );
}
```

### Step 3: Import routing file

Import routing file of bundle. Change prefix attribute to suit your needs.

In YAML:

``` yaml
# app/config/routing.yml
astina_redirect_manager:
    resource: "@AstinaRedirectManagerBundle/Resources/config/routing.yml"
    prefix:   /redirect/
```

### Step 4: Translations

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```
### Step 5: Configuration Options

In an application with multiple entity managers please [configure a new manager](http://symfony.com/doc/current/cookbook/doctrine/multiple_entity_managers.html) or ensure the default manager can access the schema.
```yml
# app/config/config.yml
astina_redirect_manager:
    storage:
      entity_manager: redirect # Optional entity manager name, if ommitted default entity manager is used
    base_layout: "SomeBundle:SomeDir:index.html.twig"    # Override default Astina layout
    enable_listeners: false # Set to false to disable redirect listeners. Useful for service oriented architectures. Defaults to true
```

### Step 6: Update your DB schema

``` bash
$ php app/console doctrine:schema:update --force
```

## Usage

### Importing urls with command

Bundle knows how to import csv file of url for redirection.
CSV has to contain two columns, where the first one contain urlFrom and second urlTo.

```bash
$ php app/console armb:import /path/to/csv/file.csv [--redirect-code=302] [--count-redirects]
```

### Using subDomain listener to redirect to somewhere else

If listener detects that subDomain is used it redirects visitor to url with name `route_name` with `route_params` and redirect code `redirect_code`.

```yaml
# app/config/config.yml

astina_redirect_manager:
    redirect_subdomains:
        route_name:           ~ # Required
        route_params:
            param1: some-value
            param2: some-different-value
        redirect_code:        301

```

*Warning*

You have to set parameter `router.request_context.host` in parameters.yml file. Otherwise value `localhost` will be used as domain name.

```yaml
# app/config/parameters.yml

# ...
router.request_context.host: example.com
# ...
```
