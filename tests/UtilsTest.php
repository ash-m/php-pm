<?php

namespace PHPPM\Tests;

use PHPPM\Utils;

use PHPUnit\Framework\Attributes\DataProvider;

class UtilsTest extends PhpPmTestCase
{
    public static function providePaths()
    {
        return [
            ['/images/foo.png', '/images/foo.png'],
            ['/images/../foo.png', '/foo.png'],
            ['/images/sub/../../foo.png', '/foo.png'],
            ['/images/sub/../foo.png', '/images/foo.png'],

            ['/../foo.png', false],
            ['../foo.png', false],
            ['//images/d/../../foo.png', '/foo.png'],
            ['/images//../foo.png', '/images/foo.png'],
            ["/images/\0/../foo.png", '/images/foo.png'],
            ["/images/\0../foo.png", '/foo.png'],
        ];
    }

    /**
     * @param string $path
     * @param string $expected
     */
    #[DataProvider('providePaths')]
    public function testParseQueryPath($path, $expected)
    {
        $this->assertEquals($expected, Utils::parseQueryPath($path));
    }

    public function testHijackProperty()
    {
        $object = new \PHPPM\SlavePool();
        Utils::hijackProperty($object, 'slaves', ['SOME VALUE']);

        $r = new \ReflectionObject($object);
        $p = $r->getProperty('slaves');
        $p->setAccessible(true);
        $this->assertEquals(['SOME VALUE'], $p->getValue($object));
    }
}
