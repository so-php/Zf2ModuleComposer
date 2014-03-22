<?php


namespace Zf2ModuleComposerTest\Unit\Composer;


use Zf2ModuleComposer\Composer\Finder;
use Zf2ModuleComposerTest\Helper\Reflection;

class FinderTest extends \PHPUnit_Framework_TestCase {
    /** @var  Finder */
    protected $finder;

    public function setUp() {
        parent::setUp();

        $this->finder = new Finder();
    }

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|Finder
     */
    protected function getFinderMock($methods = array()){
        return $this->getMock('\Zf2ModuleComposer\Composer\Finder', $methods);
    }

    /**
     * @expectedException \Zf2ModuleComposer\Composer\Exception\ComposerJsonNotFound
     */
    public function testGetPathToComposerJsonThrowsExceptionIfDirectoryNotSpecified(){
        $this->finder->find(false);
    }

    public function testGetPathToComposerJsonReturnsPathToComposerJson(){
        $dir = '/path/to/foo';
        $pathToJson = $dir . '/composer.json';

        $finder = $this->getFinderMock(array('scandir'));
        $finder->expects($this->once())
            ->method('scandir')
            ->with($dir)
            ->will($this->returnValue(array('composer.json')));

        $test = Reflection::invokeArgs($finder, 'getPathToComposerJson', array($dir));
        $this->assertEquals($pathToJson, $test);
    }

    public function testGetPathToComposerJsonRecursivelySearchesParentPathUntilComposerIsFound(){
        $dir = '/path/to/foo';
        $pathToJson = '/path/composer.json';

        $finder = $this->getFinderMock(array('scandir','realpath'));
        $finder->expects($this->exactly(3))
            ->method('scandir')
            ->will($this->returnValueMap(array(
                array('/path/to/foo', SCANDIR_SORT_DESCENDING, array(uniqid(), '..','.')),
                array('/path/to', SCANDIR_SORT_DESCENDING, array(uniqid(), '..','.')),
                array('/path', SCANDIR_SORT_DESCENDING, array('composer.json', '..', '.'))
            )));

        $finder->expects($this->exactly(2))
            ->method('realpath')
            ->will($this->returnValueMap(array(
                array('/path/to/foo/..', '/path/to'),
                array('/path/to/..', '/path'),
            )));

        $test = Reflection::invokeArgs($finder, 'getPathToComposerJson', array($dir));
        $this->assertEquals($pathToJson, $test);
    }

    /**
     * @expectedException \Zf2ModuleComposer\Composer\Exception\ComposerJsonNotFound
     */
    public function testGetPathToComposerJsonRecursivelySearchesParentPathUntilNoMoreParentsToSearch(){
        $dir = '/path/to/foo';
        $pathToJson = '/path/composer.json';

        $finder = $this->getFinderMock(array('scandir','realpath'));
        $finder->expects($this->exactly(4))
            ->method('scandir')
            ->will($this->returnValueMap(array(
                array('/path/to/foo', SCANDIR_SORT_DESCENDING, array(uniqid(), '..','.')),
                array('/path/to', SCANDIR_SORT_DESCENDING, array(uniqid(), '..','.')),
                array('/path', SCANDIR_SORT_DESCENDING, array('..', '.')),
                array('/', SCANDIR_SORT_DESCENDING, array('.')),
            )));

        $finder->expects($this->exactly(3))
            ->method('realpath')
            ->will($this->returnValueMap(array(
                array('/path/to/foo/..', '/path/to'),
                array('/path/to/..', '/path'),
                array('/path/..', '/'),
            )));

        Reflection::invokeArgs($finder, 'getPathToComposerJson', array($dir));
    }

}