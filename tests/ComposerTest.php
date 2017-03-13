<?php
declare(strict_types=1);

namespace Zaemis\MusicComposer\Tests;

use PHPUnit\Framework\TestCase;
use Zaemis\MusicComposer\Composer;

chdir(__DIR__);
require_once '../vendor/autoload.php';

class ComposerTest extends TestCase
{
    public function testJson()
    {
        $json = json_encode(['C3' => 1, 'C#3' => 2, 'D3' => 3]);

        $composer = new Composer;
        $composer->fromJson($json);

        $result = $composer->toJson();

        $this->assertEquals($json, $result);
    }

    public function testTally()
    {
        $composer = new Composer;
        $composer->tally(['C4', 'C4', 'C4', 'C4', 'C4']);

        $pitches = json_decode($composer->toJson(), true);

        $this->assertEquals(4, $pitches['C4']['sum']);
        $this->assertEquals(4, $pitches['C4']['C4']);
    }
}
