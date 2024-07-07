<?php

namespace Laravel\Prompts;

use Closure;

class StepBuilder {

         protected array $steps = [];
         protected array $responses = [];

        function add(Closure $step, Closure | false $revert = null, $key = null ) {

            if($revert === null){
                $revert = fn () => null;
            }

            $this->steps[] = [$step, $revert, $key];

            return $this;
          
        }

        function run(): array
        {

            $index = 0;

            $steps = [];
            $previousRevert = false;

            foreach($this->steps as [$step, $revert, $key]){
                $steps[] = [$step, $previousRevert, $key];
                $previousRevert = $revert;
            }

            while($index < count($steps)){
                 
                [$step, $revert, $key] = $steps[$index];

                $wasReverted = false;

                Prompt::$revertedUsing = $revert ? function () use (&$wasReverted) {
                      $wasReverted = true;
                } : null;

                $this->responses[$key ?? $index] = $step($this->responses);
                 
                if($wasReverted){
                    $revert($this->responses);
                    $index--;
                }
                else {
                    $index++;
                }
              
            }
            
            return $this->responses;
        }

}