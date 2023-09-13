<?php

namespace App\Entity;

use App\Repository\ChambreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChambreRepository::class)]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $Tarif = null;

    #[ORM\Column]
    private ?float $Superficie = null;

    #[ORM\Column]
    private ?int $VueSurMer = null;

    #[ORM\Column]
    private ?int $ChaineALaCarte = null;

    #[ORM\Column]
    private ?int $Climatisation = null;

    #[ORM\Column]
    private ?int $TelevisionEcranPlat = null;

    #[ORM\Column]
    private ?int $Telephone = null;

    #[ORM\Column]
    private ?int $ChaineSatellite = null;

    #[ORM\Column]
    private ?int $ChaineDuCable = null;

    #[ORM\Column]
    private ?int $CoffreFort = null;

    #[ORM\Column]
    private ?int $MaterielDeRepassage = null;

    #[ORM\Column]
    private ?int $WifiGratuit = null;

    #[ORM\Column]
    private ?int $Type = null;

    #[ORM\OneToMany(mappedBy: 'chambre', targetEntity: Reserver::class)]
    private Collection $Reserver;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Hotel $Hotel = null;

    #[ORM\ManyToOne(inversedBy: 'Chambre')]
    private ?Hotel $hotel = null;

    public function __construct()
    {
        $this->Reserver = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarif(): ?float
    {
        return $this->Tarif;
    }

    public function setTarif(float $Tarif): static
    {
        $this->Tarif = $Tarif;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->Superficie;
    }

    public function setSuperficie(float $Superficie): static
    {
        $this->Superficie = $Superficie;

        return $this;
    }

    public function getVueSurMer(): ?int
    {
        return $this->VueSurMer;
    }

    public function setVueSurMer(int $VueSurMer): static
    {
        $this->VueSurMer = $VueSurMer;

        return $this;
    }

    public function getChaineALaCarte(): ?int
    {
        return $this->ChaineALaCarte;
    }

    public function setChaineALaCarte(int $ChaineALaCarte): static
    {
        $this->ChaineALaCarte = $ChaineALaCarte;

        return $this;
    }

    public function getClimatisation(): ?int
    {
        return $this->Climatisation;
    }

    public function setClimatisation(int $Climatisation): static
    {
        $this->Climatisation = $Climatisation;

        return $this;
    }

    public function getTelevisionAEcranPlat(): ?int
    {
        return $this->TelevisionEcranPlat;
    }

    public function setTelevisionAEcranPlat(int $TelevisionEcranPlat): static
    {
        $this->TelevisionEcranPlat = $TelevisionEcranPlat;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->Telephone;
    }

    public function setTelephone(int $Telephone): static
    {
        $this->Telephone = $Telephone;

        return $this;
    }

    public function getChaineSatellite(): ?int
    {
        return $this->ChaineSatellite;
    }

    public function setChaineSatellite(int $ChaineSatellite): static
    {
        $this->ChaineSatellite = $ChaineSatellite;

        return $this;
    }

    public function getChaineDuCable(): ?int
    {
        return $this->ChaineDuCable;
    }

    public function setChaineDuCable(int $ChaineDuCable): static
    {
        $this->ChaineDuCable = $ChaineDuCable;

        return $this;
    }

    public function getCoffreFort(): ?int
    {
        return $this->CoffreFort;
    }

    public function setCoffreFort(int $CoffreFort): static
    {
        $this->CoffreFort = $CoffreFort;

        return $this;
    }

    public function getMaterielDeRepassage(): ?int
    {
        return $this->MaterielDeRepassage;
    }

    public function setMaterielDeRepassage(int $MaterielDeRepassage): static
    {
        $this->MaterielDeRepassage = $MaterielDeRepassage;

        return $this;
    }

    public function getWifiGratuit(): ?int
    {
        return $this->WifiGratuit;
    }

    public function setWifiGratuit(int $WifiGratuit): static
    {
        $this->WifiGratuit = $WifiGratuit;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->Type;
    }

    public function setType(int $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    /**
     * @return Collection<int, Reserver>
     */
    public function getReserver(): Collection
    {
        return $this->Reserver;
    }

    public function addReserver(Reserver $reserver): static
    {
        if (!$this->Reserver->contains($reserver)) {
            $this->Reserver->add($reserver);
            $reserver->setChambre($this);
        }

        return $this;
    }

    public function removeReserver(Reserver $reserver): static
    {
        if ($this->Reserver->removeElement($reserver)) {
            // set the owning side to null (unless already changed)
            if ($reserver->getChambre() === $this) {
                $reserver->setChambre(null);
            }
        }

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->Hotel;
    }

    public function setHotel(?Hotel $Hotel): static
    {
        $this->Hotel = $Hotel;

        return $this;
    }
}
