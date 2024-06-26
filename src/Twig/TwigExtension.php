<?php

namespace App\Twig;

use Twig\TwigFilter;
use App\Utils\Convertir;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    private $convertir;
    public function __construct(Convertir $convertir)
    {
        $this->convertir = $convertir;
    }

    public function getFilters() {
        return [
            new TwigFilter('lettre', [$this, 'lettreConv'])
        ];
    }

    public function lettreConv($montant, $device, $lang){
        $lettre = $this->convertir->ConvNumberLetter($montant, $device, $lang);
        return $lettre;
    }
}
