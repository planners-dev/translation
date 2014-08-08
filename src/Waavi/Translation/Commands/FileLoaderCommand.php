<?php

namespace Waavi\Translation\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Waavi\Translation\Providers\LanguageProvider as LanguageProvider;
use Waavi\Translation\Providers\LanguageEntryProvider as LanguageEntryProvider;

class FileLoaderCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'translator:load';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Load language files into the database.";

	/**
	 *  Create a new mixed loader instance.
	 *
	 *  @param  \Waavi\Lang\Providers\LanguageProvider        $languageProvider
	 *  @param  \Waavi\Lang\Providers\LanguageEntryProvider   $languageEntryProvider
	 *  @param  \Illuminate\Foundation\Application            $app
	 */
	public function __construct($languageProvider, $languageEntryProvider, $fileLoader)
	{
		parent::__construct();
		$this->languageProvider = $languageProvider;
		$this->languageEntryProvider = $languageEntryProvider;
		$this->fileLoader = $fileLoader;
		$this->finder = new Filesystem();

		$iter = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(app_path(), \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST, \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
		);
		$this->path = array();
		foreach ($iter as $path => $dir) {
			if ($dir->isDir()) {
				if ($dir->getFilename() == 'lang') {
					$this->path[] = $path;
				}
			}
		}
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		foreach($this->path as $path) {
		$localeDirs = $this->finder->directories($path);
		foreach ($localeDirs as $localeDir) {
			$locale = str_replace($path . DIRECTORY_SEPARATOR, '', $localeDir);
			$language = $this->languageProvider->findByLocale($locale);
			if ($language) {
				$langFiles = $this->finder->files($localeDir);
				foreach ($langFiles as $langFile) {
					$group = str_replace(array('/', $localeDir . DIRECTORY_SEPARATOR, '.php'), array(DIRECTORY_SEPARATOR, '', ''), $langFile);
					$lines = $this->fileLoader->loadRawLocale($locale, $group);
					$this->languageEntryProvider->loadArray($lines, $language, $group, null, $locale == $this->fileLoader->getDefaultLocale());
				}
			}
		}
	}
	}

}
