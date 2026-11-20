<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Domain;

use Ramsey\Uuid\Uuid;

final class Expense
{
    public const CATEGORY_PROVEEDOR  = 'proveedor';
    public const CATEGORY_SERVICIO   = 'servicio';
    public const CATEGORY_PERSONAL   = 'personal';
    public const CATEGORY_ALQUILER   = 'alquiler';
    public const CATEGORY_OTRO       = 'otro';

    /** @var string[] */
    public static $validCategories = ['proveedor', 'servicio', 'personal', 'alquiler', 'otro'];

    public const PAYMENT_EFECTIVO      = 'efectivo';
    public const PAYMENT_TRANSFERENCIA = 'transferencia';
    public const PAYMENT_DEBITO        = 'debito';
    public const PAYMENT_CREDITO       = 'credito';

    /** @var string[] */
    public static $validPaymentMethods = ['efectivo', 'transferencia', 'debito', 'credito'];

    /** @var string */      private $id;
    /** @var string */      private $storeId;
    /** @var int */         private $amount;
    /** @var string */      private $category;
    /** @var string */      private $recipient;
    /** @var string|null */ private $description;
    /** @var string */      private $paymentMethod;
    /** @var string */      private $responsibleId;
    /** @var string */      private $responsibleName;
    /** @var string */      private $date;
    /** @var string */      private $createdAt;

    private function __construct(string $id, string $storeId, int $amount, string $category,
        string $recipient, ?string $description, string $paymentMethod,
        string $responsibleId, string $responsibleName, string $date, string $createdAt)
    {
        $this->id              = $id;
        $this->storeId         = $storeId;
        $this->amount          = $amount;
        $this->category        = $category;
        $this->recipient       = $recipient;
        $this->description     = $description;
        $this->paymentMethod   = $paymentMethod;
        $this->responsibleId   = $responsibleId;
        $this->responsibleName = $responsibleName;
        $this->date            = $date;
        $this->createdAt       = $createdAt;
    }

    public static function create(string $storeId, int $amount, string $category,
        string $recipient, ?string $description, string $paymentMethod,
        string $responsibleId, string $responsibleName, string $date): self
    {
        return new self(
            Uuid::uuid4()->toString(), $storeId, $amount, $category,
            $recipient, $description, $paymentMethod,
            $responsibleId, $responsibleName, $date, date('Y-m-d H:i:s')
        );
    }

    public static function fromPersistence(string $id, string $storeId, int $amount,
        string $category, string $recipient, ?string $description, string $paymentMethod,
        string $responsibleId, string $responsibleName, string $date, string $createdAt): self
    {
        return new self($id, $storeId, $amount, $category, $recipient, $description,
            $paymentMethod, $responsibleId, $responsibleName, $date, $createdAt);
    }

    public function id(): string              { return $this->id; }
    public function storeId(): string         { return $this->storeId; }
    public function amount(): int             { return $this->amount; }
    public function category(): string        { return $this->category; }
    public function recipient(): string       { return $this->recipient; }
    public function description(): ?string    { return $this->description; }
    public function paymentMethod(): string   { return $this->paymentMethod; }
    public function responsibleId(): string   { return $this->responsibleId; }
    public function responsibleName(): string { return $this->responsibleName; }
    public function date(): string            { return $this->date; }
    public function createdAt(): string       { return $this->createdAt; }

    public function updateDetails(int $amount, string $category, string $recipient,
        ?string $description, string $paymentMethod, string $date): void
    {
        $this->amount        = $amount;
        $this->category      = $category;
        $this->recipient     = $recipient;
        $this->description   = $description;
        $this->paymentMethod = $paymentMethod;
        $this->date          = $date;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'storeId'         => $this->storeId,
            'amount'          => $this->amount,
            'category'        => $this->category,
            'recipient'       => $this->recipient,
            'description'     => $this->description,
            'paymentMethod'   => $this->paymentMethod,
            'responsibleId'   => $this->responsibleId,
            'responsibleName' => $this->responsibleName,
            'date'            => $this->date,
            'createdAt'       => $this->createdAt,
        ];
    }
}
