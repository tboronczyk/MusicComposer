<?php
declare(strict_types=1);

namespace Zaemis\MusicComposer\Tests;

use PHPUnit\Framework\TestCase;
use Zaemis\MusicComposer\MidiWriter;

chdir(__DIR__);
require_once '../vendor/autoload.php';

class MidiWriterTest extends TestCase
{
    public function testWrite()
    {
        $melody = ['C4', 'D4', 'E4'];

// @codingStandardsIgnoreStart
        $data = pack(
            'C*',
            0x00, 0xB0, 0x00, 0x00, 0x00, 0xC0, 0x00, 0x00, 0x00, 0xB0, 0x5B, 0x00,
            0x00, 0x90, 0x3D, 0x3E, 0x8F, 0x7F, 0x80, 0x3D, 0x3E,
            0x00, 0x90, 0x3F, 0x3E, 0x8F, 0x7F, 0x80, 0x3F, 0x3E,
            0x00, 0x90, 0x41, 0x3E, 0x8F, 0x7F, 0x80, 0x41, 0x3E,
            0x00, 0xFF, 0x2F, 0x00
        );
// @codingStandardsIgnoreEnd
        $midi = 'MThd' . pack('Nn*', 6, 1, 1, 2032) .
                'MTrk' .  pack('N', strlen($data)) . $data;

        $m = new MidiWriter;
        $this->assertEquals($midi, $m->write($melody));
    }
}
