<?php

namespace App\Entity;

use App\Repository\RDVRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RDVRepository::class)
 */
class RDV
{

    const HORAIRES_RDV=['09','10','11','14','15','16'];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $day;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hour;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $praticien;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rDVs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lastname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?\DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(\DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getHour(): ?string
    {
        return $this->hour;
    }

    public function setHour(string $hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getPraticien(): ?string
    {
        return $this->praticien;
    }

    public function setPraticien(string $praticien): self
    {
        $this->praticien = $praticien;

        return $this;
    }

    public function getLastname(): ?User
    {
        return $this->lastname;
    }

    public function setLastname(?User $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
