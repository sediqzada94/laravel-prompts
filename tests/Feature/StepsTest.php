<?php


use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\steps;
use function Laravel\Prompts\text;

it('can run multiple steps', function () {
    
    Prompt::fake([
        'S', 'a', 'm', 'i', Key::ENTER,
        Key::ENTER,
        Key::ENTER
    ]);
    
    $response = steps()
                ->add(fn () => text('Your name'))
                ->add(fn () => select("Your language", ["PHP", "JS", "JAVA"]))
                ->add(fn () => confirm("Sure?"))
                ->run();

    expect($response)->toBe([
        'Sami',
        'PHP',
        true
    ]);

});