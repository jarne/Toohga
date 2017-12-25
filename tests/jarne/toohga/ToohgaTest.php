<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 08.03.17
 * Time: 20:55
 */

namespace jarne\toohga;

use jarne\toohga\utils\MethodType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class ToohgaTest extends TestCase {
    /**
     * Test the redirect function
     */
    public function testRedirect(): void {
        $toohga = new Toohga();
        $output = $toohga->redirect(array(), "127.0.0.1", "tooh.ga", MethodType::GET, array());

        $crawler = new Crawler($output);

        $this->assertEquals("Toohga", $crawler->filterXPath("//h1")->text());
    }
}