<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Intl\Locales;

class AppExtension extends AbstractExtension{

    private $_localeCodes;
    private $_locales;

    public function __construct($locales, $defaultLocale){
        $localCodes = explode('|', $locales);
        sort($localCodes);
        $this->_localeCodes= $localCodes;
    }

    public function getFunctions()
    {
        return[
            new TwigFunction('locales', [$this, 'getLocales']),
        ];
    }

    public function getLocales(){
        $this->_locales = [];
        foreach ($this->_localeCodes as $localCode){
            $this->_locales[] =
                [
                    'code' => $localCode,
                    'name' => Locales::getName($localCode)
                ];
        }

        return $this->_locales;
    }
}