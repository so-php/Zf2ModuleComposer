<?php


namespace Zf2ModuleComposer\Composer;


use Composer\Factory;
use Composer\IO\BaseIO;
use Composer\IO\BufferIO;
use Zf2ModuleComposer\Composer\Exception\ComposerJsonNotFound;

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

        return $this->composerFactoryCreate($io, $path);
    }

    /**
     * @param $directory
     * @throws ComposerJsonNotFound
     * @return string
     */
    protected function getPathToComposerJson($directory)
    {
        if(!$directory){
            throw new ComposerJsonNotFound("Could not locate composer.json");
        }

        $files = $this->scandir($directory, SCANDIR_SORT_DESCENDING);
        foreach ($files as $file) {
            if ($file == 'composer.json') {
                return $directory . '/' . $file;
            } else if($file == '..'){
                $parentDirectory = $this->realpath($directory . '/..');
                // realpath may always evaluate /.. as / (instead of invalid)
                if($parentDirectory == $directory) {
                    throw new ComposerJsonNotFound("Could not locate composer.json");
                }
                return $this->getPathToComposerJson($parentDirectory);
            }
        }
        // if scandir does not return .. at top level we could end up here
        throw new ComposerJsonNotFound("Could not locate composer.json");
    }

    /**
     * @param BaseIO $io
     * @param string $path
     * @return \Composer\Composer
     */
    protected function composerFactoryCreate(BaseIO $io, $path){
        return Factory::create($io, $path);
    }

    /**
     * For mocking
     * @param string $directory
     * @param int $sorting_order
     * @return array
     */
    protected function scandir($directory, $sorting_order = null){
        return scandir($directory, $sorting_order);
    }

    /**
     * For mocking
     * @param string $path
     * @return string|bool
     */
    protected function realpath($path){
        return realpath($path);
    }
} 