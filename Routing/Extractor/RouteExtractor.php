<?php

/*
 * This file is part of the JavascriptBundle package.
 *
 * © Enzo Innocenzi <enzo.inno@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Hawezo\JavascriptBundle\Routing\Extractor;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

/**
 * @author Enzo Innocenzi <enzo.inno@gmail.com>
 */
class RouteExtractor implements ExtractorInterface
{
    /**
     * @var array
     */
    protected $routes;

    /**
     * @var bool
     */
    protected $routeArrayIsWhitelist;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var LogoutUrlGenerator
     */
    protected $logoutGenerator;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array
     */
    protected $availableDomains;

    public function __construct(RouterInterface $router, LogoutUrlGenerator $logoutGenerator, array $routes = [], bool $whitelist = false)
    {
        $this->router = $router;
        $this->logoutGenerator = $logoutGenerator;
        $this->routes = $routes;
        $this->routeArrayIsWhitelist = $whitelist;
        $this->context = $router->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function extract(): array
    {
        $collection = $this->router->getRouteCollection();
        $routes = [];

        foreach ($collection->all() as $name => $route) {
            if (in_array($name, array_keys($routes))) {
                throw new \InvalidArgumentException(sprintf('Route %s is declared twice.'));
            }

            if (!$this->isRouteExposed($name, $route)) {
                continue;
            }

            $routes[$name] = $this->serializeRoute($route);
        }

        return $this->serialize($routes);
    }

    /**
     * Gets the base URL of this router context.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->context->getBaseUrl() ?: '';
    }

    /**
     * Gets the HTTP scheme of this router context.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->context->getScheme();
    }

    /**
     * Gets the HTTP host of this router context.
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->context->getHost();
    }

    protected function serialize($routes)
    {
        try {
            $logoutUrl = $this->logoutGenerator->getLogoutUrl();
            $logoutPath = $this->logoutGenerator->getLogoutPath();
        } catch (\Throwable $th) {
        }

        return [
            'routes'   => $routes,
            'scheme'   => $this->getScheme(),
            'host' => $this->getHost(),
            'base_url' => $this->getBaseUrl(),
            'logout_url' => $logoutUrl ?? null,
            'logout_path' => $logoutPath ?? null,
        ];
    }

    /**
     * Serializes the route.
     *
     * @return string
     */
    protected function serializeRoute($route)
    {
        $serialization = $route->__serialize();

        $compiled = $route->compile();
        $serialization['tokens'] = $compiled->getTokens();
        $serialization['host_tokens'] = method_exists($compiled, 'getHostTokens') ? $compiled->getHostTokens() : array();

        return array_filter($serialization, function ($key) {
            if (!in_array($key, ['options'])) {
                return true;
            }
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Checks if the given route is exposed.
     *
     * @return bool
     */
    protected function isRouteExposed($name, $route)
    {
        $exposed = !$this->routeArrayIsWhitelist;

        if ('' !== $pattern = $this->getPattern()) {
            $exposed = preg_match($pattern, $name);
        }

        return $route->hasOption('expose') || $exposed;
    }

    /**
     * Generates a regular expression based on the 'routes' parameter.
     *
     * @return string
     */
    protected function getPattern()
    {
        $_routes = [];
        foreach ($this->routes as $route) {
            $_routes[] = '(' . $route . ')';
        }

        return implode('|', $_routes);
    }
}
