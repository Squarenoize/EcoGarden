<?php

namespace App\Entity;

use App\Repository\TipRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TipRepository::class)]
class Tip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getTips'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getTips'])]
    #[Assert\NotBlank(
        message: 'Le titre du conseil ne peut pas être vide.'
    )]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le titre du conseil doit contenir au moins {{ limit }} caractère.',
        max: 255,
        maxMessage: 'Le titre du conseil ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['getTips'])]
    #[Assert\NotBlank(
        message: 'Le texte du conseil ne peut pas être vide.'
    )]
    private ?string $text = null;

    #[ORM\ManyToMany(targetEntity: Month::class, inversedBy: 'tips')]
    #[ORM\JoinTable(name: 'tip_month')]
    #[Groups(['getTips'])]
    #[Assert\Count(
        min: 1,
        minMessage: 'Vous devez sélectionner au moins un mois pour ce conseil.'
    )]
    private Collection $months;

    public function __construct()
    {
        $this->months = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getMonths(): Collection
    {
        return $this->months;
    }

    public function addMonth(Month $month): static
    {
        if (!$this->months->contains($month)) {
            $this->months->add($month);
        }

        return $this;
    }

    public function removeMonth(Month $month): static
    {
        $this->months->removeElement($month);

        return $this;
    }
}
