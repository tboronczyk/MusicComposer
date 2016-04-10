<?php
use Boronczyk\MusicComposer\Composer;
use Boronczyk\MusicComposer\MidiWriter;

return (function () use ($container) {
    $container['composer'] = function ($c) {
        $composer = new Composer;
        if (is_readable($c['settings']['path.data'])) {
            $pitches = file_get_contents($c['settings']['path.data']);
            $composer->fromJson($pitches);
        } else {
            foreach (glob($c['settings']['path.training'] . '/*.txt') as $f) {
                $pitches = file_get_contents($f);
                $pitches = trim($pitches);
                $pitches = explode(' ', $pitches);
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
