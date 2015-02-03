<?php
namespace Planners\Translation\Loaders;

use Illuminate\Translation\LoaderInterface;
use Planners\Translation\Providers\LanguageEntryProvider as LanguageEntryProvider;
use Planners\Translation\Providers\LanguageProvider as LanguageProvider;

class Loader implements LoaderInterface
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The language provider, used to access the Language model dynamically.
     *
     * @var \Planners\Translation\Providers\LanguageProvider
     */
    protected $languageProvider;

    /**
     * The language entry provider, used to access the LanguageEntry model dynamically.
     *
     * @var \Planners\Translation\Providers\LanguageEntryProvider
     */
    protected $languageEntryProvider;

    /**
     * The default locale.
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     * The cache timeout in minutes.
     *
     * @var string
     */
    protected $cacheTimeout;

    /**
     * Cache enabled flag.
     *
     * @var boolean
     */
    protected $cacheEnabled;

    /**
     * All of the namespace hints.
     *
     * @var array
     */
    protected $hints = array();

    /**
     *     Create a new loader instance.
     *
     *     @param  \Planners\Lang\Providers\LanguageProvider              $languageProvider
     *     @param     \Planners\Lang\Providers\LanguageEntryProvider        $languageEntryProvider
     *    @param     \Illuminate\Foundation\Application                      $app
     */
    public function __construct($languageProvider, $languageEntryProvider, $app)
    {
        $this->setApp($app);
        $this->setProviders($languageProvider, $languageEntryProvider);
    }

    /**
     *    Sets the ioc container object to interact with Laravel.
     *    @param \Illuminate\Foundation\Application  $app
     *     @return void
     */
    protected function setApp($app)
    {
        $this->app           = $app;
        $this->defaultLocale = $app['config']['app.locale'];
        $this->cacheTimeout  = $app['config']['planners/translation::cache.timeout'];
        $this->cacheEnabled  = $app['config']['planners/translation::cache.enabled'] == 'on'
        || ($app['config']['planners/translation::cache.enabled'] == 'auto' && !$app['config']['app.debug']);
    }

    /**
     *    Sets the language and language entry providers.
     *     @param  \Planners\Translation\Providers\LanguageProvider                  $languageProvider
     *     @param     \Planners\Translation\Providers\LanguageEntryProvider        $languageEntryProvider
     *     @return void
     */
    protected function setProviders($languageProvider, $languageEntryProvider)
    {
        $this->languageProvider      = $languageProvider;
        $this->languageEntryProvider = $languageEntryProvider;
    }

    /**
     *    Sets the default locale.
     *    @param string     $locale
     *    @return void
     */
    protected function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;
    }

    /**
     *    Returns the default locale.
     *    @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     *    Returns the language provider:
     *    @return Planners\Translation\Providers\LanguageProvider
     */
    public function getLanguageProvider()
    {
        return $this->languageProvider;
    }

    /**
     *    Returns the language entry provider:
     *    @return Planners\Translation\Providers\LanguageEntryProvider
     */
    public function getLanguageEntryProvider()
    {
        return $this->languageEntryProvider;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        $namespace = $namespace ?: '*';
        $cacheKey  = "planners|translation|$locale.$group.$namespace";
        $lines     = $this->cacheEnabled && $this->app['cache']->has($cacheKey) ?
        $this->app['cache']->get($cacheKey) :
        $this->loadRaw($locale, $group, $namespace);

        if ($this->cacheEnabled && !$this->app['cache']->has($cacheKey)) {
            $this->app['cache']->put($cacheKey, $lines, $this->cacheTimeout);
        }
        return $lines;
    }

    /**
     * Load the messages for the given locale without checking the cache or in case of a cache miss. Merge with the default locale messages.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    public function loadRaw($locale, $group, $namespace = null)
    {
        return array_merge($this->loadRawLocale($this->defaultLocale, $group, $namespace), $this->loadRawLocale($locale, $group, $namespace));
    }

    /**
     * Load the messages strictly for the given locale without checking the cache or in case of a cache miss.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    public function loadRawLocale($locale, $group, $namespace = null)
    {
        return array();
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;

        if (isset($this->fileLoader)) {
            $this->fileLoader->addNamespace($namespace, $hint);
        } elseif (isset($this->laravelFileLoader)) {
            $this->laravelFileLoader->addNamespace($namespace, $hint);
        }
    }
}
