<?php
namespace Zaemis\Composer;

class Melody
{
    protected $pitchProb;

    public function __construct() {
        $this->pitchProb = array_fill_keys(
            array(
                'C3', 'C#3', 'D3', 'D#3', 'E3', 'F3', 'F#3', 'G3', 'G#3', 'A3',
                'A#3', 'B3',
                'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4',
                'A#4', 'B4',
                'C5', 'C#5', 'D5', 'D#5', 'E5', 'F5', 'F#5', 'G5', 'G#5', 'A5',
                'A#5', 'B5'
            ),
            array(
                'pitches' => array(
                    'C3' => 0, 'C#3' => 0, 'D3' => 0, 'D#3' => 0, 'E3' => 0,
                    'F3' => 0, 'F#3' => 0, 'G3' => 0, 'G#3' => 0, 'A3' => 0,
                    'A#3' => 0, 'B3' => 0,
                    'C4' => 0, 'C#4' => 0, 'D4' => 0, 'D#4' => 0, 'E4' => 0,
                    'F4' => 0, 'F#4' => 0, 'G4' => 0, 'G#4' => 0, 'A4' => 0,
                    'A#4' => 0, 'B4' => 0,
                    'C5' => 0, 'C#5' => 0, 'D5' => 0, 'D#5' => 0, 'E5' => 0,
                    'F5' => 0, 'F#5' => 0, 'G5' => 0, 'G#5' => 0, 'A5' => 0,
                    'A#5' => 0, 'B5' => 0
                ),
                'sum' => 0
            )
        );
    }

    protected function weightedSelect($note) {
        $pitches = $this->pitchProb[$note]['pitches'];
        $sum = $this->pitchProb[$note]['sum'];

        $w = rand(1, $sum);
        foreach($pitches as $p => $weight) {
            $w -= $weight;
            if ($w <= 0) {
                return $p;
            }
        }
    }

    public function toJSON() {
        return json_encode($this->pitchProb);
    }

    public function fromJSON($json) {
        $this->pitchProb = json_decode($json, true);
    }

    public function train($noteData) {
        $lastKey = count($noteData) - 1;
        for ($i = 0; $i < $lastKey; $i++) {
            $current = $noteData[$i];
            $next = $noteData[$i + 1];
            $this->pitchProb[$current]['pitches'][$next]++;
            $this->pitchProb[$current]['sum']++;
        }
    }

    public function compose($note, $numNotes) {
        $melody = array($note);
        while (--$numNotes) {
            $note = $this->weightedSelect($note);
            $melody[] = $note;
        }
        return $melody;
    }
}
