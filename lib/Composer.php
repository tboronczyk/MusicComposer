<?php
declare(strict_types=1);

namespace Zaemis\MusicComposer;

class Composer
{
    protected $counts;

    public function __construct()
    {
        $this->initCounts();
    }

    /**
     * Initialize the pitch count table
     */
    protected function initCounts()
    {
        $pitches = [
            'C3', 'C#3', 'D3', 'D#3', 'E3', 'F3', 'F#3', 'G3', 'G#3', 'A3', 'A#3', 'B3',
            'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4',
            'C5', 'C#5', 'D5', 'D#5', 'E5', 'F5', 'F#5', 'G5', 'G#5', 'A5', 'A#5', 'B5'
        ];

        // $counts[pitchA]['sum'] = number of pitchA occurances
        // $counts[pitchA][pitchB] = number of times pitchB follows pitchA
        $this->counts = array_fill_keys(
            $pitches,
            array_merge(['sum' => 0], array_fill_keys($pitches, '0'))
        );
    }

    /**
     * Export pitch counts as JSON
     *
     * @return string pitch data in JSON format
     */
    public function toJson(): string
    {
        return json_encode($this->counts);
    }

    /**
     * Import pitch counts from JSON
     *
     * @param string $json pitch data
     */
    public function fromJson(string $json)
    {
        $this->counts = json_decode($json, true);
    }

    /**
     * Tally pitch occurances
     *
     * @param array $pitches sequence of pitches, e.g.: ['C4', 'D4', 'C4', ...]
     */
    public function tally(array $pitches)
    {
        for ($i = 0, $last = count($pitches) - 1; $i < $last; ++$i) {
            $curr = $pitches[$i];
            $next = $pitches[$i + 1];
            ++$this->counts[$curr]['sum'];
            ++$this->counts[$curr][$next];
        }
    }

    /**
     * Return the next pitch by weighted selection
     *
     * @param string $pitch current pitch
     * @return string next pitch
     */
    protected function weightedRand(string $pitch): string
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

    /**
     * Compose a melody
     *
     * @param string $pitch the starting pitch
     * @param int $numNotes the number of notes to return
     *
     * @return array a melody as an array of pitches
     */
    public function compose(string $pitch, int $numNotes): array
    {
        $melody = [$pitch];
        while (--$numNotes) {
            $pitch = $this->weightedRand($pitch);
            $melody[] = $pitch;
        }
        return $melody;
    }
}
