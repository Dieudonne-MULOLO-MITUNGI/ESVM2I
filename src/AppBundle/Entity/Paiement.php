<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paiement
 *
 * @ORM\Table(name="payement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PayementRepository")
 */
class Paiement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Paiement_date", type="string", length=50)
     */
    private $paiementDate;

    /**
     * @var string
     *
     * @ORM\Column(name="Paiement_nature", type="string", length=50)
     */
    private $paiementNature;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set paiementDate
     *
     * @param string $paiementDate
     *
     * @return Paiement
     */
    public function setPaiementDate($paiementDate)
    {
        $this->paiementDate = $paiementDate;

        return $this;
    }

    /**
     * Get paiementDate
     *
     * @return string
     */
    public function getPaiementDate()
    {
        return $this->paiementDate;
    }

    /**
     * Set paiementNature
     *
     * @param string $paiementNature
     *
     * @return Paiement
     */
    public function setPaiementNature($paiementNature)
    {
        $this->paiementNature = $paiementNature;

        return $this;
    }

    /**
     * Get paiementNature
     *
     * @return string
     */
    public function getPaiementNature()
    {
        return $this->paiementNature;
    }
}

