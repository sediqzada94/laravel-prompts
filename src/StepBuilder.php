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

            while($index < count($this->steps)){
                 
                [$step, $revert, $key] = $this->steps[$index];

                $wasReverted = false;

                Prompt::$revertedUsing = function () use (&$wasReverted) {
                      $wasReverted = true;
                };

                $this->responses[$key ?? $index] = $step($this->responses);
                 
                if($wasReverted){
                    $index--;
                }
                else {
                    $index++;
                }
              
            }
            
            return $this->responses;
        }

}