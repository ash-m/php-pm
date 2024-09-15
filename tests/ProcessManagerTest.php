<?php

namespace PHPPM\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

class ProcessManagerTest extends PhpPmTestCase
{
    public static function provideReplaceHeader()
    {
        return [
            [
                "Content-Type: plain/text\r\nX-Real-Ip: 1337.1337.1337.1337\r\n\r\n",
                "Content-Type: plain/text\r\nX-Real-IP: 127.0.0.1\r\n\r\n",
                ['X-Real-IP' => '127.0.0.1']
            ],
            [
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nConnection: keep-alive\r\n\r\n",
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nConnection: keep-alive\r\nX-PHP-PM-Remote-IP: 127.0.0.1\r\n\r\n",
                ['X-PHP-PM-Remote-IP' => '127.0.0.1']
            ],
            [
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nConnection: keep-alive\r\nX-php-pm-REMOTE-IP: 137.137.137.137\r\n\r\n",
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nConnection: keep-alive\r\nX-PHP-PM-Remote-IP: 127.0.0.1\r\n\r\n",
                ['X-PHP-PM-Remote-IP' => '127.0.0.1']
            ],
            [
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nX-php-pm-REMOTE-IP: 137.137.137.137\r\nConnection: keep-alive\r\n\r\n",
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nX-PHP-PM-Remote-IP: 127.0.0.1\r\nConnection: keep-alive\r\n\r\n",
                ['X-PHP-PM-Remote-IP' => '127.0.0.1']
            ],
            [
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nX-php-pm-REMOTE-IP: 137.137.137.137\r\nConnection: keep-alive\r\n\r\n",
                "GET /images/spinners/octocat-spinner-128.gif HTTP/1.1\r\nHost: assets-cdn.github.com\r\nX-PHP-PM-Remote-IP: 192.168.1.1\r\nConnection: keep-alive\r\n\r\n",
                ['X-PHP-PM-Remote-IP' => '192.168.1.1']
            ]
        ];
    }

    /**
     * @param string $originHeader
     * @param string $expectedNewHeader
     * @param array $replaceHeaders
     */
    #[DataProvider('provideReplaceHeader')]
    public function testReplaceHeader($originHeader, $expectedNewHeader, array $replaceHeaders)
    {
        $replaceHeader = $this->getRequestHandlerMethod('replaceHeader');
        $replacedHeader = $replaceHeader($originHeader, $replaceHeaders);
        $this->assertEquals($expectedNewHeader, $replacedHeader);
    }

    public static function provideIsHeaderEnd()
    {
        return [
            ["Content-Type: plain/text\r\nX-Real-Ip: 1337.1337.1337.1337\r\n\r\n", true],
            ["Content-Type: plain/text\r\nX-Real-Ip: 1337.1337.1337.1337\r", false],
            ["Content-Type: plain/text\r\nX-Real-Ip: 1337.1337.1337.1337\r\n", false],
            ["Content-Type: plain/text\r\nX-Real-Ip: 1337.133", false],
            ["Content-Type: plain/text\r\n\r\n", true],
            ["Content-Type: plain/text\r\nX-Real-Ip: 1337.1337.1337.1337\r\n\r\nThis is some content", true],
        ];
    }

    /**
     * @param string $header
     * @param boolean $isEnd
     */
    #[DataProvider('provideIsHeaderEnd')]
    public function testIsHeaderEnd($header, $isEnd)
    {
        $isHeaderEnd = $this->getRequestHandlerMethod('isHeaderEnd');
        $this->assertEquals($isHeaderEnd($header), $isEnd);
    }
}
