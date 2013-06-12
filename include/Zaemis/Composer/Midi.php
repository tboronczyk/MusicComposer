<?php
namespace Zaemis\Composer;

class Midi
{
    protected $noteValues = array(
        'C3' => 0x32, 'C#3' => 0x33,
        'D3' => 0x34, 'D#3' => 0x35,
        'E3' => 0x36,
        'F3' => 0x37, 'F#3' => 0x38,
        'G3' => 0x39, 'G#3' => 0x39,
        'A3' => 0x3A, 'A#3' => 0x3B,
        'B3' => 0x3C,
        'C4' => 0x3D, 'C#4' => 0x3E,
        'D4' => 0x3F, 'D#4' => 0x40,
        'E4' => 0x41,
        'F4' => 0x42, 'F#4' => 0x43,
        'G4' => 0x44, 'G#4' => 0x45,
        'A4' => 0x46, 'A#4' => 0x47,
        'B4' => 0x48,
        'C5' => 0x49, 'C#5' => 0x4A,
        'D5' => 0x4B, 'D#5' => 0x4C,
        'E5' => 0x4D,
        'F5' => 0x4E, 'F#5' => 0x4F,
        'G5' => 0x50, 'G#5' => 0x51,
        'A5' => 0x52, 'A#5' => 0x53,
        'B5' => 0x53
    );

    public function __construct() {
    }

    public function generate($noteData) {
        // MIDI type 1, 1 track, Time division 2032
        $header = 'MThd' . pack('Nn*', 6, 1, 1, 2032);

        $data = pack('C*',
            0x00, 0xB0, 0x00, 0x00, // T:0, Controller Chan 0 Bank 0
            0x00, 0xC0, 0x00, 0x00, // T:0, Program Change Chan 0 Piano
            0x00, 0xB0, 0x5B, 0x00  // T:0, Controller Chan 0 Effects Depth 0
        );
        foreach ($noteData as $note) {
            $data .= pack('C*',
                // T:0, Note On, Note X, velocity 62
                0x00, 0x90, $this->noteValues[$note], 0x3E,
                // T:254 (127|0x80 + 127), Note Off, Note X, velocity 62
                0x8F, 0x7F, 0x80, $this->noteValues[$note], 0x3E
            );
        }
        // T:0 End track
        $data .= pack('C*', 0x00, 0xFF, 0x2F, 0X00);
        $track = 'MTrk' . pack('N', strlen($data)) . $data;

        return $header . $track;
    }
}
