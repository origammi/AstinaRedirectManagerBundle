# Astina Redirect Manager Bundle

[![Build Status](https://travis-ci.org/astina/AstinaRedirectManagerBundle.png?branch=master)](https://travis-ci.org/astina/AstinaRedirectManagerBundle)
[![Latest Stable Version](https://poser.pugx.org/astina/redirect-manager-bundle/v/stable.png)](https://packagist.org/packages/astina/redirect-manager-bundle)
[![Total Downloads](https://poser.pugx.org/astina/redirect-manager-bundle/downloads.png)](https://packagist.org/packages/astina/redirect-manager-bundle)

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

### Step 5: Update your DB schema

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
