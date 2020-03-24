<?php declare(strict_types=1);

use Moosh\Command\Generic\Apache\ApacheParsePerfLog;
use PHPUnit\Framework\TestCase;


final class UrlAnalyseTest extends TestCase
{
    public function testUrlWithScriptAndQuery(): void
    {


        $url = "/lib/ajax/service.php?sesskey=osfiPDIqXr&info=core_message_data_for_messagearea_messages";
        list($script, $query, $path, $type) = ApacheParsePerfLog::analyzeURL($url);

        $this->assertEquals(
                '/lib/ajax/service.php',
                $script
        );

        $this->assertEquals(
                'sesskey=osfiPDIqXr&info=core_message_data_for_messagearea_messages',
                $query
        );

        $this->assertNull($path);

        $this->assertEquals(
                'script',
                $type
        );
    }

    public function testUrlWithPath() : void
    {
        $url = "/lib/ajax/theme/image.php/_s/name/theme_name/1584436934/user";
        list($script, $query, $path, $type) = ApacheParsePerfLog::analyzeURL($url);

        $this->assertEquals(
                '/lib/ajax/theme/image.php',
                $script
        );

        $this->assertNull($query);

        $this->assertEquals(
                '/_s/name/theme_name/1584436934/user',
                $path
        );

        $this->assertEquals(
                'script',
                $type
        );
    }

    public function testCronEntry() : void
    {
        $url = "<cron>";
        list($script, $query, $path, $type) = ApacheParsePerfLog::analyzeURL($url);

        $this->assertEmpty($script);
        $this->assertNull($query);
        $this->assertNull($path);

        $this->assertEquals(
                'cli',
                $type
        );
    }

    public function testUrlWithNoScriptName() : void
    {
        $url = "/my/?view=sandbox";
        list($script, $query, $path, $type) = ApacheParsePerfLog::analyzeURL($url);

        $this->assertEquals(
                '/my/index.php',
                $script
        );

        $this->assertEquals(
                'view=sandbox',
                $query
        );

        $this->assertNull($path);

        $this->assertEquals(
                'script',
                $type
        );
    }

}
