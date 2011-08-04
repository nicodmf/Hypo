Hypo collections
================

Hypo collections is a little set of bundle which simplificate littles parts of symfony.

The goal is to have very ligth overhead but usefull function. To acomplish that, just twig is in the scope, and function and bundle contains just files needed.

Actually the purposes are:

 - Give a layout engine which decorate all pages
   - css and js centralisation for twig with the function addcss and addjs
 - Two abstact entity wich permet to simplificate file uploads.

Installation
------------

 - download the zip in src/Hypo
 - or if git installed : `git submodule add git://github.com/nicodmf/Hypo.git src/Hypo`
 - add this lines to app/AppKernel.php
 
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
 - configure the bundle in config.yml (a default configuration can be find in GlobalBundle/Resources/config/defaultconfiguration.yml)


 Use
 ---

 ###Layout engine###
 The layout engine works just with twig. It provide 4 twig functions or filters :

 - addcss
 - addjs
 - getcss
 - getjs

 The default layout is present in the view directory of the layout bundle (`[SymfonyDirectory]/src/Hypo/LayoutBundle/Resources/views/layout.html.twig`). But you can specify the file that you want to use in the configuration file.

###Document classes###
The document classes (\Hypo\DocumentBundle\Entity\File and \Hypo\DocumentBundle\Entity\Image) could be use to simplificate file creation they can be uses as in TestBundle (see \Hypo\TestBundle\Entity\File and \Hypo\TestBundle\Entity\Image)
 