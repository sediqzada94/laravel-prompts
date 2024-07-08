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
            $previousStep = $this->steps[count($this->steps)-1] ??  null;
            $this->steps[] = new Step($step, $revert, $key, $previousStep);

            return $this;
          
        }

        function run(): array
        {

            $index = 0;

            // $steps = $this->normalizeSteps();

            while($index < count($this->steps)){
                 
                $step = $this->steps[$index];

                $wasReverted = false;

                Prompt::$revertedUsing = $step->revert ? function () use (&$wasReverted) {
                      $wasReverted = true;
                } : null;

                $this->responses[$step->key ?? $index] = $step->run($this->responses);

                if(!$wasReverted){
                   $index++;
                   continue;

                }

                 $step->revert($this->responses);
                 $index--;
              
            }
            
            Prompt::$revertedUsing = null;
            
            return $this->responses;
        }

}