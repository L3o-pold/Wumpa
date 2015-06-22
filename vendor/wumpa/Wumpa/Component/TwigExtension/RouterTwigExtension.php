<?php

namespace Wumpa\Component\TwigExtension;

use Wumpa\Component\App\App;

/**
 * A collection of function to be used with Twig
 * All functions are related to the request/url/App
 *
 * Functions:
 *   _baseUrl()   : return the base url of the App
 *   _url()       : return the current full url
 *   _generate()  : generate an url from the routing table (see Router->generate())
 *   _getArg()    : return the correspondig arg from the _GET array
 */
class RouterTwigExtension extends \Twig_Extension {

    public function getName() {
        return 'RouterTwigExtension';
    }

    public function getFunctions() {
        return array(
            '_baseUrl'  => new \Twig_Function_Method($this, 'getBaseUrl'),
            '_url'      => new \Twig_Function_Method($this, 'getUrl'),
            '_uri'      => new \Twig_Function_Method($this, 'getUri'),
            '_generate' => new \Twig_Function_Method($this, 'generate'),
            '_getArg'   => new \Twig_Function_Method($this, 'getGetArgs'),
        );
    }

    public function getBaseUrl() {
        return App::get()->getBaseUrl();
    }

    public function getUrl() {
        return App::getRouter()->getRequest()->getFullUrl();
    }

    public function generate($route, $parameters = null) {
        return App::getRouter()->generate($route, $parameters);
    }

    public function getGetArgs($argName) {
        return (isset($_GET[$argName])) ? $_GET[$argName] : false;
    }

    public function getUri() {
        return App::getRouter()->getRequest()->getUri();
    }

}
