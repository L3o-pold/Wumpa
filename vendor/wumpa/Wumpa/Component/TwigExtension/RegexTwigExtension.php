<?php

namespace Wumpa\Component\TwigExtension;

use Wumpa\Component\App\App;

class RegexTwigExtension extends \Twig_Extension {

    public function getName() {
        return 'RegexTwigExtension';
    }

    public function getFunctions() {
        return array(
            '_pregReplace'  => new \Twig_Function_Method($this, 'pregReplace'),
            '_pregMatch'    => new \Twig_Function_Method($this, 'pregMatch'),
        );
    }

    public function pregReplace($patern, $replace, $subject, $limit = -1) {
        return preg_replace($patern, $replace, $subject, $limit);
    }

    public function pregMatch($patern, $subject) {
        return preg_match($patern, $subject);
    }

}
