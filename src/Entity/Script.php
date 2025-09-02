<?php

namespace OHMedia\ScriptBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\ScriptBundle\Repository\ScriptRepository;
use OHMedia\TimezoneBundle\Util\DateTimeUtil;
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScriptRepository::class)]
class Script
{
    use BlameableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $starts_at = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan(propertyPath: 'starts_at', message: 'The End date must be after the Start date.')]
    private ?\DateTimeImmutable $ends_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $dismissible = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true, options: ['unsigned' => true])]
    #[Assert\GreaterThan(0)]
    private ?int $dismissible_days = 1;

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeImmutable
    {
        return $this->starts_at;
    }

    public function setStartsAt(?\DateTimeImmutable $starts_at): static
    {
        $this->starts_at = $starts_at;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->ends_at;
    }

    public function setEndsAt(?\DateTimeImmutable $ends_at): static
    {
        $this->ends_at = $ends_at;

        return $this;
    }

    public function isDismissible(): ?bool
    {
        return $this->dismissible;
    }

    public function setDismissible(?bool $dismissible): static
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    public function getDismissibleDays(): ?int
    {
        return $this->dismissible_days;
    }

    public function setDismissibleDays(?int $dismissible_days): static
    {
        $this->dismissible_days = $dismissible_days;

        return $this;
    }

    public function getCookieName()
    {
        return 'script_bar_'.$this->id;
    }

    public function getCookieString()
    {
        $maxAge = $this->dismissible_days * 86400;

        $cookieParts = [
            $this->getCookieName().'=1',
            'max-age='.$maxAge,
            'path=/',
        ];

        return implode(';', $cookieParts);
    }

    public function isDraft(): bool
    {
        return !$this->starts_at;
    }

    public function isScheduled(): bool
    {
        return $this->starts_at && DateTimeUtil::isFuture($this->starts_at);
    }

    public function isPublished(): bool
    {
        if (!$this->starts_at || DateTimeUtil::isFuture($this->starts_at)) {
            return false;
        }

        return !$this->ends_at || DateTimeUtil::isFuture($this->ends_at);
    }

    public function isExpired(): bool
    {
        if (!$this->starts_at || !$this->ends_at) {
            return false;
        }

        return DateTimeUtil::isPast($this->ends_at);
    }
}
