<?php
namespace Boronczyk\MusicComposer;

class Composer
{
    protected $counts;

    public function __construct()
    {
        $this->initCounts();
    }

    /**
     * Initialize pitch count table
     *
     * $counts[pitchA]['sum'] = number of pitchA occurances
     * $counts[pitchA][pitchB] = number of times pitchB occurance followed pitchA
     */
    protected function initCounts()
    {
        $this->counts = array_fill_keys(
            ['C3', 'C#3', 'D3', 'D#3', 'E3', 'F3', 'F#3', 'G3', 'G#3', 'A3', 'A#3', 'B3',
             'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4',
             'C5', 'C#5', 'D5', 'D#5', 'E5', 'F5', 'F#5', 'G5', 'G#5', 'A5', 'A#5', 'B5'],
            ['sum' => 0,
             'C3'  => 0, 'C#3' => 0, 'D3'  => 0, 'D#3' => 0, 'E3'  => 0, 'F3' => 0,
             'F#3' => 0, 'G3'  => 0, 'G#3' => 0, 'A3'  => 0, 'A#3' => 0, 'B3' => 0,
             'C4'  => 0, 'C#4' => 0, 'D4'  => 0, 'D#4' => 0, 'E4'  => 0, 'F4' => 0,
             'F#4' => 0, 'G4'  => 0, 'G#4' => 0, 'A4'  => 0, 'A#4' => 0, 'B4' => 0,
             'C5'  => 0, 'C#5' => 0, 'D5'  => 0, 'D#5' => 0, 'E5'  => 0, 'F5' => 0,
             'F#5' => 0, 'G5'  => 0, 'G#5' => 0, 'A5'  => 0, 'A#5' => 0, 'B5' => 0 ]
        );
    }

    /**
     * Export pitch counts as JSON
     *
     * @return pitch data as a JSON string
     */
    public function toJson()
    {
        return json_encode($this->counts);
    }

    /**
     * Import pitch counts from JSON
     *
     * @param $json pitch data as a JSON string
     */
    public function fromJson($json)
    {
        $this->counts = json_decode($json, true);
    }

    /**
     * Tally pitch occurances
     *
     * @param $pitches sequence of pitches, e.g.: ['C4', 'D4', 'C4', 'E4', ...]
     */
    public function tally(array $pitches)
    {
        $last = count($pitches) - 1;
        for ($i = 0; $i < $last; ++$i) {
            $curr = $pitches[$i];
            $next = $pitches[$i + 1];
            ++$this->counts[$curr]['sum'];
            ++$this->counts[$curr][$next];
        }
    }

    /**
     * Return next pitch by weighted selection
     *
     * @param $pitch current pitch
     * @return next pitch
     */
    protected function weightedRand($pitch)
    {
        $row = $this->counts[$pitch];
        $rand = rand(0, $row['sum'] - 1);
        unset($row['sum']);

        $sum = 0;
        foreach ($row as $pitch => $count) {
            $sum += $count;
            if ($sum > $rand) {
                return $pitch;
            }
        }
        return $pitch;
    }

    public function compose($pitch, $numNotes)
    {
        $melody = [$pitch];
        while (--$numNotes) {
            $pitch = $this->weightedRand($pitch);
            $melody[] = $pitch;
        }
        return $melody;
    }
}
