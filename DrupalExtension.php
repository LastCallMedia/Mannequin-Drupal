<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use LastCall\Mannequin\Twig\TwigExtension;
use Symfony\Component\HttpFoundation\Request;

class DrupalExtension extends TwigExtension
{
    public function __construct(array $config = [])
    {
        $config += [
            'drupal_root' => null,
            'drupal' => function () {
                return $this->bootDrupal();
            },
            'twig_loader' => function () {
                return $this['drupal']->get('twig.loader.filesystem');
            },
        ];
        parent::__construct($config);

        $this['_drupal_root'] = function () {
            if (is_dir($this['drupal_root'])) {
                return $this['drupal_root'];
            }
            throw new \InvalidArgumentException(
                sprintf('Invalid Drupal Root: %s', $this['drupal_root'])
            );
        };
        $this['twig'] = function () {
            return $this['drupal']->get('twig');
        };
    }

    protected function bootDrupal()
    {
        $drupal_root = $this['_drupal_root'];
        chdir($drupal_root);
        $autoloader = require $drupal_root.'/autoload.php';
        require_once $drupal_root.'/core/includes/bootstrap.inc';

        $request = Request::create(
            '/',
            'GET',
            [],
            [],
            [],
            ['SCRIPT_NAME' => $drupal_root.'/index.php']
        );
        $kernel = DrupalKernel::createFromRequest(
            $request,
            $autoloader,
            'prod',
            false
        );
        Settings::initialize(
            $drupal_root,
            DrupalKernel::findSitePath($request),
            $autoloader
        );
        $kernel->boot();
        $kernel->preHandle($request);

        return $kernel->getContainer();
    }
}
