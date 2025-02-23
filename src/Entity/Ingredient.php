<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $imgUrl = null;

    /**
     * @var Collection<int, PlatIngredient>
     */
    #[ORM\OneToMany(targetEntity: PlatIngredient::class, mappedBy: 'ingredient', orphanRemoval: true)]
    private Collection $platIngredients;

    /**
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'ingredient', orphanRemoval: true)]
    private Collection $stocks;

    public function __construct()
    {
        $this->platIngredients = new ArrayCollection();
        $this->stocks = new ArrayCollection();
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

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    /**
     * @return Collection<int, PlatIngredient>
     */
    public function getPlatIngredients(): Collection
    {
        return $this->platIngredients;
    }

    public function addPlatIngredient(PlatIngredient $platIngredient): static
    {
        if (!$this->platIngredients->contains($platIngredient)) {
            $this->platIngredients->add($platIngredient);
            $platIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removePlatIngredient(PlatIngredient $platIngredient): static
    {
        if ($this->platIngredients->removeElement($platIngredient)) {
            // Set the owning side to null (unless already changed)
            if ($platIngredient->getIngredient() === $this) {
                $platIngredient->setIngredient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setIngredient($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getIngredient() === $this) {
                $stock->setIngredient(null);
            }
        }

        return $this;
    }
}
