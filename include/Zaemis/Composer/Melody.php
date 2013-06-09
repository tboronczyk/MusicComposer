<?php
namespace Zaemis\Composer;

class Melody
{
    protected $pitchProb;

    public function __construct() {
        $this->pitchProb = array_fill_keys(
            array('F4', 'G4', 'A4', 'A#4', 'B4', 'C5', 'D5', 'E5', 'F5'),
            array(
                'pitches' => array(
                  'F4' => 0, 'G4' => 0, 'A4' => 0, 'A#4' => 0, 'B4' => 0,
                  'C5' => 0, 'D5' => 0, 'E5' => 0, 'F5' => 0
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
        $numNotes = count($noteData);
        for ($i = 0; $i < $numNotes - 1; $i++) {
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
