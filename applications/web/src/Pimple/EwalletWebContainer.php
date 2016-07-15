<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Pimple;

use Ewallet\Pimple\ServiceProviders\{DoctrineServiceProvider, FormsServiceProvider, TwigServiceProvider};
use Ewallet\Slim\ServiceProviders\{ApplicationServiceProvider, MiddlewareServiceProvider};
use Slim\{App, Container};

class EwalletWebContainer extends Container
{
    /**
     * Add service providers and application options.
     *
     * @param array $arguments
     * @param App $app
     */
    public function __construct(array $arguments = [], App $app)
    {
        parent::__construct($arguments);
        $this->register(new DoctrineServiceProvider());
        $this->register(new TwigServiceProvider());
        $this->register(new FormsServiceProvider());
        $this->register(new ApplicationServiceProvider());
        $this->register(new MiddlewareServiceProvider());
    }
}
