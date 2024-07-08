<?php

namespace Laravel\Prompts;

use Closure;
use PDO;

class Step {

    function __construct(
        public Closure $step,
        public Closure|false $revert,
        public ?string $key,
        public ?self $previousStep
    )
    {
        
    }


    public function run(array $responses) : mixed {

           return ($this->step)($responses);
           
    }

    public function revert(array $responses) : void {
           
           if($this->previousStep->revert){
              ($this->previousStep->revert)($responses);
           }

    }



}