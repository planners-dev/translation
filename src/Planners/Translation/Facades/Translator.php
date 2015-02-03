<?php
namespace Planners\Translation\Facades;

use Illuminate\Translation\Translator as LaravelTranslator;

class Translator extends LaravelTranslator
{

    /**
     *    Returns the language provider:
     *    @return Planners\Translation\Providers\LanguageProvider
     */
    public function getLanguageProvider()
    {
        return $this->loader->getLanguageProvider();
    }

    /**
     *    Returns the language entry provider:
     *    @return Planners\Translation\Providers\LanguageEntryProvider
     */
    public function getLanguageEntryProvider()
    {
        return $this->loader->getLanguageEntryProvider();
    }

}
