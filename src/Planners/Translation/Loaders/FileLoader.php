<?php

namespace Planners\Translation\Loaders;

use Illuminate\Translation\FileLoader as LaravelFileLoader;
use Illuminate\Translation\LoaderInterface;
use Planners\Translation\Loaders\Loader;

class FileLoader extends Loader implements LoaderInterface
{

    /**
     * The laravel file loader instance.
     *
     * @var \Illuminate\Translation\FileLoader
     */
    protected $laravelFileLoader;

    protected $app;

    /**
     *     Create a new mixed loader instance.
     *
     *     @param  \Planners\Lang\Providers\LanguageProvider              $languageProvider
     *     @param     \Planners\Lang\Providers\LanguageEntryProvider        $languageEntryProvider
     *     @param     \Illuminate\Foundation\Application                      $app
     */
    public function __construct($languageProvider, $languageEntryProvider, $app)
    {
        $this->app = $app;
        parent::__construct($languageProvider, $languageEntryProvider, $app);
        $this->laravelFileLoader = new LaravelFileLoader($app['files'], $app['path'] . DIRECTORY_SEPARATOR . 'lang');
    }

    /**
     * Load the messages strictly for the given locale without checking the cache or in case of a cache miss.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    public function loadRawLocale($locale, $group, $namespace = null, $dir = null)
    {
        if ($dir) {
            $this->laravelFileLoader = new LaravelFileLoader($this->app['files'], $dir);
        }
        $namespace = $namespace ?: '*';
        return $this->laravelFileLoader->load($locale, $group, $namespace);
    }

}
