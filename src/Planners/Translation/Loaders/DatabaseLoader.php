<?php
namespace Planners\Translation\Loaders;

use Illuminate\Translation\LoaderInterface;
use Planners\Translation\Loaders\Loader;
use Planners\Translation\Providers\LanguageProvider as LanguageProvider;

class DatabaseLoader extends Loader implements LoaderInterface
{

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
        $langArray = array();
        $namespace = $namespace ?: '*';
        $language  = $this->languageProvider->findByLocale($locale);
        if ($language) {
            $entries = $language->entries()->where('group', '=', $group)->where('namespace', '=', $namespace)->get();
            if ($entries) {
                foreach ($entries as $entry) {
                    array_set($langArray, $entry->item, $entry->text);
                }
            }
        }
        return $langArray;
    }
}
