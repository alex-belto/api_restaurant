<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Stores and manages data about the staff working in it,
 * menu items, tip distribution strategy, and the restaurant's balance.
 */
#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    public const TIPS_STANDARD_STRATEGY = 1;
    public const TIPS_WAITER_STRATEGY = 2;
    private const WORK_HOURS = 8;
    private const MAX_VISITORS_PER_HOUR = 50;
    private const STANDARD_PAYMENT_STRATEGY = 'cashPayment';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Waiter::class)]
    private Collection $waiters;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Kitchener::class)]
    private Collection $kitcheners;

    #[ORM\Column]
    private float $balance = 0;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: MenuItem::class)]
    private Collection $menuItems;

    #[ORM\Column]
    private int $tipsStrategy = self::TIPS_STANDARD_STRATEGY;

    #[ORM\Column]
    private int $days = 0;

    #[ORM\Column(length: 32, nullable: false)]
    private string $paymentMethod = self::STANDARD_PAYMENT_STRATEGY;

    public function __construct()
    {
        $this->waiters = new ArrayCollection();
        $this->kitcheners = new ArrayCollection();
        $this->menuItems = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Waiter>| Waiter[]
     */
    public function getWaiters(): Collection
    {
        return $this->waiters;
    }

    public function addWaiter(Waiter $waiter): static
    {
        if (!$this->waiters->contains($waiter)) {
            $this->waiters->add($waiter);
            $waiter->setRestaurant($this);
        }

        return $this;
    }

    public function removeWaiter(Waiter $waiter): static
    {
        if ($this->waiters->removeElement($waiter)) {
            // set the owning side to null (unless already changed)
            if ($waiter->getRestaurant() === $this) {
                $waiter->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Kitchener>| Kitchener[]
     */
    public function getKitcheners(): Collection
    {
        return $this->kitcheners;
    }

    public function addKitchener(Kitchener $kitchener): static
    {
        if (!$this->kitcheners->contains($kitchener)) {
            $this->kitcheners->add($kitchener);
            $kitchener->setRestaurant($this);
        }

        return $this;
    }

    public function removeKitchener(Kitchener $kitchener): static
    {
        if ($this->kitcheners->removeElement($kitchener)) {
            // set the owning side to null (unless already changed)
            if ($kitchener->getRestaurant() === $this) {
                $kitchener->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection<int, MenuItem>| MenuItem[]
     */
    public function getMenuItems(): Collection
    {
        return $this->menuItems;
    }

    public function addMenuItem(MenuItem $menuItem): static
    {
        if (!$this->menuItems->contains($menuItem)) {
            $this->menuItems->add($menuItem);
            $menuItem->setRestaurant($this);
        }

        return $this;
    }

    public function removeMenuItem(MenuItem $menuItem): static
    {
        if ($this->menuItems->removeElement($menuItem)) {
            // set the owning side to null (unless already changed)
            if ($menuItem->getRestaurant() === $this) {
                $menuItem->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getTipsStrategy(): int
    {
        return $this->tipsStrategy;
    }

    public function setTipsStrategy(int $tipsStrategy): static
    {
        $this->tipsStrategy = $tipsStrategy;

        return $this;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function setDays(int $days): static
    {
        $this->days = $days;

        return $this;
    }

    public function getMaxVisitorsPerDay(): int
    {
        return Restaurant::WORK_HOURS * Restaurant::MAX_VISITORS_PER_HOUR;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

}
