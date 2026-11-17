<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Store\Domain;

use Ramsey\Uuid\Uuid;

final class Store
{
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    /** @var string */      private $id;
    /** @var string */      private $name;
    /** @var string|null */ private $industry;
    /** @var string */      private $status;
    /** @var string */      private $createdAt;
    /** @var bool */        private $onboardingCompleted;
    /** @var string */      private $theme;
    /** @var string|null */ private $logoPath;

    private function __construct(
        string $id,
        string $name,
        ?string $industry,
        string $status,
        string $createdAt,
        bool $onboardingCompleted,
        string $theme,
        ?string $logoPath
    ) {
        $this->id                  = $id;
        $this->name                = $name;
        $this->industry            = $industry;
        $this->status              = $status;
        $this->createdAt           = $createdAt;
        $this->onboardingCompleted = $onboardingCompleted;
        $this->theme               = $theme;
        $this->logoPath            = $logoPath;
    }

    public static function create(string $name): self
    {
        return new self(
            Uuid::uuid4()->toString(),
            $name,
            null,
            self::STATUS_ACTIVE,
            date('Y-m-d H:i:s'),
            false,
            'verde',
            null
        );
    }

    public static function fromPersistence(
        string $id,
        string $name,
        ?string $industry,
        string $status,
        string $createdAt,
        bool $onboardingCompleted,
        string $theme,
        ?string $logoPath
    ): self {
        return new self($id, $name, $industry, $status, $createdAt, $onboardingCompleted, $theme, $logoPath);
    }

    public function id(): string                { return $this->id; }
    public function name(): string              { return $this->name; }
    public function industry(): ?string         { return $this->industry; }
    public function status(): string            { return $this->status; }
    public function createdAt(): string         { return $this->createdAt; }
    public function isActive(): bool            { return $this->status === self::STATUS_ACTIVE; }
    public function onboardingCompleted(): bool { return $this->onboardingCompleted; }
    public function theme(): string             { return $this->theme; }
    public function logoPath(): ?string         { return $this->logoPath; }

    public function rename(string $name): void           { $this->name = $name; }
    public function setIndustry(?string $industry): void { $this->industry = $industry; }
    public function setTheme(string $theme): void        { $this->theme = $theme; }
    public function setLogoPath(?string $path): void     { $this->logoPath = $path; }
    public function completeOnboarding(): void           { $this->onboardingCompleted = true; }
    public function suspend(): void                      { $this->status = self::STATUS_SUSPENDED; }
    public function activate(): void                     { $this->status = self::STATUS_ACTIVE; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'industry'            => $this->industry,
            'status'              => $this->status,
            'theme'               => $this->theme,
            'logoPath'            => $this->logoPath,
            'onboardingCompleted' => $this->onboardingCompleted,
            'createdAt'           => $this->createdAt,
        ];
    }
}
