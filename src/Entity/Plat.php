<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $tempsCuisson = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    /**
     * @var Collection<int, PlatIngredient>
     */
    #[ORM\OneToMany(targetEntity: PlatIngredient::class, mappedBy: 'plat', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $platIngredients;

    public function __construct()
    {
        $this->platIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTempsCuisson(): ?int
    {
        return $this->tempsCuisson;
    }

    public function setTempsCuisson(int $tempsCuisson): static
    {
        $this->tempsCuisson = $tempsCuisson;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, PlatIngredient>
     */
    public function getPlatIngredients(): Collection
    {
        return $this->platIngredients;
    }

    public function addPlatIngredient(PlatIngredient $platIngredient): self
    {
        if (!$this->platIngredients->contains($platIngredient)) {
            $this->platIngredients[] = $platIngredient;
            $platIngredient->setPlat($this);
        }
        return $this;
    }

    public function removePlatIngredient(PlatIngredient $platIngredient): self
    {
        if ($this->platIngredients->removeElement($platIngredient)) {
            if ($platIngredient->getPlat() === $this) {
                $platIngredient->setPlat(null);
            }
        }
        return $this;
    }
}
