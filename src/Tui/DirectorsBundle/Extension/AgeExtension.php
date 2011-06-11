<?php

namespace Tui\DirectorsBundle\Extension;

class AgeExtension extends \Twig_Extension
{
    public function getFunctions() {
        return array(
            'age_bracket'  => new \Twig_Filter_Method($this, 'age_bracket'),
        );
    }

    public function age_bracket($age)
    {
        if (!$age)
            return '';

        elseif ($age < 25)
            return 'Under 25';
        
        elseif ($age >= 25 && $age < 35)
            return '25-34';
        
        elseif ($age >= 35 && $age < 45)
            return '35-44';
        
        elseif ($age >= 45 && $age < 55)
            return '45-54';
        
        elseif ($age >= 55 && $age < 65)
            return '55-64';
        
        elseif ($age >= 65 && $age < 75)
            return '65-74';
        
        elseif ($age >= 75)
            return 'Over 75';
        

    }

    public function getName()
    {
        return 'age';
    }
}