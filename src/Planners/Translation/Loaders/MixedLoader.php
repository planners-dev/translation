<?php
namespace Planners\Translation\Loaders;

use Illuminate\Translation\LoaderInterface;
use Planners\Translation\Loaders\Loader;

class MixedLoader extends Loader implements LoaderInterface
{

    /**
     *    The file loader.
     *    @var \Planners\Translation\Loaders\FileLoader
     */
    protected $fileLoader;

    /**
     *    The database loader.
     *    @var \Planners\Translation\Loaders\DatabaseLoader
     */
    protected $databaseLoader;

    /**
     *     Create a new mixed loader instance.
     *
     *     @param  \Planners\Lang\Providers\LanguageProvider              $languageProvider
     *     @param     \Planners\Lang\Providers\LanguageEntryProvider        $languageEntryProvider
     *    @param     \Illuminate\Foundation\Application                      $app
     */
    public function __construct($languageProvider, $languageEntryProvider, $app)
    {
        parent::__construct($languageProvider, $languageEntryProvider, $app);
        $this->fileLoader     = new FileLoader($languageProvider, $languageEntryProvider, $app);
        $this->databaseLoader = new DatabaseLoader($languageProvider, $languageEntryProvider, $app);
    }

    /**
     * Load the messages strictly for the given locale.
     *
     * @param  Language      $language
     * @param  string          $group
     * @param  string          $namespace
     * @return array
     */
    public function loadRawLocale($locale, $group, $namespace = null)
    {
        $namespace = $namespace ?: '*';
        return array_merge($this->databaseLoader->loadRawLocale($locale, $group, $namespace), $this->fileLoader->loadRawLocale($locale, $group, $namespace));
    }
}
