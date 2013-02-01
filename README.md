Astina Redirect Manager Bundle
==============================

##Install

### Step 1: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Astina\RedirectManagerBundle\AstinaRedirectManagerBundle(),
    );
}
```

### Step 2: Import routing files

In YAML:

``` yaml
# app/config/routing.yml
astina_redirect_manager:
    resource: "@AstinaRedirectManagerBundle/Resources/config/routing.yml"
    prefix:   /redirect/
```

### Step 3: Translations

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```