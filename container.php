<?php
use Zaemis\MusicComposer\Composer;
use Zaemis\MusicComposer\MidiWriter;

return (function () use ($container) {
    $container['composer'] = function ($c) {
        $composer = new Composer;
        if (is_readable($c['settings']['path.data'])) {
            $pitches = file_get_contents($c['settings']['path.data']);
            $composer->fromJson($pitches);
        } else {
            foreach (glob($c['settings']['path.training'] . '/*.txt') as $f) {
                $pitches = file_get_contents($f);
                $pitches = array_filter(preg_split('|[\s]+|', $pitches));
                $composer->tally($pitches);
            }
            file_put_contents($c['settings']['path.data'], $composer->toJson());
        }
        return $composer;
    };
    
    $container['midiwriter'] = function ($c) {
        return new MidiWriter;
    };
    
    return true;
})();
