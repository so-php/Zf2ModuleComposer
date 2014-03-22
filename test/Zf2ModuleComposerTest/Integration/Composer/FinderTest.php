<?php


namespace Zf2ModuleComposerTest\Integration\Composer;


use Zf2ModuleComposer\Composer\Finder;

class FinderTest extends \PHPUnit_Framework_TestCase {
    /** @var  Finder */
    protected $finder;

    public function setUp() {
        parent::setUp();
        $this->finder = new Finder();
    }


    /**
     * @expectedException \Zf2ModuleComposer\Composer\Exception\ComposerJsonNotFound
     */
    public function testFindThrowsExceptionWhenComposerJsonCanNotBeFound(){
        $dir = sys_get_temp_dir();
        $this->finder->find($dir);
    }
}
 