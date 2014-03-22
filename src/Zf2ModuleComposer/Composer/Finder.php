<?php


namespace Zf2ModuleComposer\Composer;


use Composer\Factory;
use Composer\IO\BufferIO;
use Zf2InstrumentModule\Composer\Exception\ComposerJsonNotFound;

class Finder {
    /**
     * @param string $start
     * @throws ComposerJsonNotFound
     * @return \Composer\Composer
     */
    public function find($start = __DIR__){

        try {
            $path = $this->getPathToComposerJson($start);
        } catch (ComposerJsonNotFound $e){
            throw new ComposerJsonNotFound("Could not locate composer.json working up from directory: $start", 0, $e);
        }

        putenv('COMPOSER_HOME='.dirname($path));
        $io= new BufferIO();

        $composer = Factory::create($io, $path);
        return $composer;
    }

    /**
     * @param $directory
     * @throws \RuntimeException
     * @return string
     */
    protected function getPathToComposerJson($directory)
    {
        if(!$directory){
            throw new ComposerJsonNotFound("Could not locate composer.json");
        }

        $files = scandir($directory, SCANDIR_SORT_DESCENDING);
        foreach ($files as $file) {
            if ($file == 'composer.json') {
                return $directory . '/' . $file;
            } else if($file == '..'){
                return $this->getPathToComposerJson(realpath($directory . '/..'));
            }
        }

        throw new ComposerJsonNotFound("Could not locate composer.json");
    }
} 