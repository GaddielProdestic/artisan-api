<?php

namespace App\Utils;

use App\Entity\Admin\Agency;
use App\Entity\Extra\Setting;
use App\Repository\Extra\SettingRepository;

class Fonctions
{
    private $settingRepository;
    public function __construct(
        SettingRepository $settingRepository
    ) {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Calcul des frais en fonction de l'operateur
     * @param string $operator
     * @param float $amount
     * @return float
     */
    public function fee(string $operator, float $amount): float
    {
        $fees = 0;
        $setting = $this->settingRepository->findOneByAgency(null);
        if($operator === "WAVE") {
            $fees = $setting ? $setting->getPrcFraisWave() : 0;
        } elseif($operator === "ORANGE") {
            $fees = $setting ? $setting->getPrcFraisOrange() : 0;
        } elseif($operator === "MTN") {
            $fees = $setting ? $setting->getPrcFraisMtn() : 0;
        } elseif($operator === "MOOV") {
            $fees = $setting ? $setting->getPrcFraisMoov() : 0;
        } elseif($operator === "DEBITCARD") {
            $fees = $setting ? $setting->getPrcFraisDebitcard() : 0;
        }

        return (($amount * $fees) / 100);
    }
    
    /**
     * Generer un mot de passe aleatoire en fonction de caratere voulu
     * @param integer $longueur
     * @return string
     */
    public static function password(int $longueur): string
    {
        $listeCar = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789/[!@#$%^&*()_+-=\]{};:"|,.<>?';
        $chaine = '';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < $longueur; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        return $chaine;
    }

    /**
     * Recuperer le sender d'une agence de façon automatique
     * @param Agency|null $agency
     * @return string
     */
    public static function sender(?Agency $agency): string
    {
        $sender = $agency && $agency->getSetting() ? $agency->getSetting()->getSender() : "ZenAPI";
        return trim($sender);
    }

    /**
     * Vérifier si l'agence a des SMS en stock
     * @param Agency|null $agency
     * @return boolean
     */
    public function checkSmsCompte(?Agency $agency): bool
    {
        $setting = $this->settingRepository->findOneByAgency($agency);
        $nbSms = $setting->getNbrSms();
        if (isset($nbSms) && $nbSms !== 0) {
            return true;
        }
        return false;
    }

    public function indecatif(?Agency $agency): ?string
    {
        /** @var Setting $setting */
        $setting = $this->settingRepository->findOneByAgency($agency);
        return $setting && $setting->getCountry() ? "+".$setting->getCountry()->getIndicatif() : '+225';
    }

    /**
     * Varifier si l'agence a activé l'envoi des notifications (Mail/SMS)
     * @param Agency|null $agency
     * @return boolean
     */
    public function checkNotif(?Agency $agency): bool
    {
        $setting = $this->settingRepository->findOneByAgency($agency);
        if ($setting->getEtat() === Setting::ETAT["ACTIVE"]) {
            return true;
        }
        return false;
    }

    /**
     * Remplacer les variables predefinis
     * @param array $tab
     * @param string $message
     * @return string
     */
    public static function replace(array $tab, string $message): string
    {
        foreach ($tab as $key => $value) {
            $message = str_replace($key, $value, $message);
        }
        return $message;
    }

    /**
     * Retourne un tableau de date en fontction de la date debut et fin
     * @param \DateTime $dateD
     * @param \DateTime $dateF
     * @return array
     */
    public static function month(\DateTime $dateD, \DateTime $dateF): array
    {
        $m = [];
        $interval = ((int)($dateD->diff($dateF))->format('%m'));
        $nbrMois = $interval > 0 ? $interval : 1;
        $first = "first day of " . $dateD->format('M') . " " . $dateD->format('Y');
        $today = new \DateTime($first);
        $m[] = $today->format('Y-m-01') . ',' . $today->format('Y-m-t');
        for ($i = 1; $i <= $nbrMois; $i++) {
            $date = $nbrMois > 1 ? $today->modify('next month') : null;
            if($date) {
                $m[] = $today->format('Y-m-01') . ',' . $today->format('Y-m-t');
            }
        }
        return $m;
    }
}