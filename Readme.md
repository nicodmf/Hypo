Hypo collections
================

Hypo collections is a little set of bundle which simplificate little part of symfony.

Actually the purposes are:
 * give a layout engine wich decorate all pages
   *css and js centralisation for twig
 * Two abstact entity wich permet to simplificate file uploads.

Installation
 - download the zip in src/Hypo
 - or if git installed : `git submodule add `
 - add this line to app/AppKernel.php
 
```php
			new Hypo\GlobalBundle\HypoGlobalBundle(),
			new Hypo\DocumentBundle\HypoDocumentBundle(),
			new Hypo\LayoutBundle\HypoLayoutBundle(),
            new Hypo\TestBundle\HypoTestBundle(),
```

 - add this line to app/config/routing.yml

 ```yaml
 HypoTestBundle:
    resource: "@HypoTestBundle/Controller/"
    type:     annotation
    prefix:   /hypo
 
 ```

 - go to http://www.website/app_dev.php/hypo
 - verificate all works
 - remove route and TestBundle
 - configure the bundle in config.yml