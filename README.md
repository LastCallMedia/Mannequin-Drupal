Mannequin Drupal is brought to you by your friends at [Last Call Media](https://www.lastcallmedia.com), it is a command line application that you can use to visualize and work on your Drupal Twig Templates in the browser. For example, you might use it to work on the templates inside of an existing custom theme.

Quick Start
-----------
Using [Composer](https://getcomposer.org/doc/00-intro.md), install Mannequin Drupal from your project root.  From the command line:
```bash
$ composer require lastcall/mannequin-drupal
```
Next, create a new `.mannequin.php` file in your project root.  This file is where you will configure Mannequin.  Inside of that file:
```php
<?php
// .mannequin.php
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Drupal\DrupalExtension;
use Symfony\Component\Finder\Finder;

// Describe where to find Drupal templates.
// See https://symfony.com/doc/current/components/finder.html
$drupalFinder = Finder::create()
    // Templates can live in your normal templates directory.
    ->in(__DIR__.'/themes/mytheme/templates')
    ->files()
    ->name('*.twig');
    
$drupalExtension = new DrupalExtension([
    'finder' => $drupalFinder,
    'drupal_root' => __DIR__,
]);

return MannequinConfig::create()
  ->addExtension($drupalExtension)
  ->setGlobalJs([
    // themes/mytheme/js/theme.js  
  ])
  ->setGlobalCss([
    // themes/mytheme/css/theme.css
  ]);
```

See all of the [configuration options](docs/configuration.md), or [view an example project](https://github.com/LastCallMedia/Drupal-Scaffold/blob/master/.mannequin.php)


Setting up Components
---------------------
While the `.mannequin.php` file tells Mannequin where to find your components, you still need to describe them to Mannequin.  To do that, we use a special YAML formatted comment block at the top of your twig files. For example, to describe a "Card" component living inside of `card.html.twig`, you would add the following at the top of the file:
```twig
{# @Component
name: Card
group: Molecule
samples:
    Small:
        title: I am a small card
        body: Lorem ipsum...
        classes: ['small']
#}
<div class="{{ classes|join(' ') }}">
  ... 
</div>
```
See the full YAML reference [here](docs/components.md)


Workflow
--------

When you're ready to begin work on your templates, you can use the following worklow:

1. Fire up a live development server so you can see your component.  From the command line:
    ```bash
    $ vendor/bin/mannequin start
    ```
2. This will output a URL for the Mannequin UI.  Visit it in your browser.
3. In the UI, find the component you want to work on.
4. Open the file, and make your changes.
5. Reload the UI to see how your changes look.
