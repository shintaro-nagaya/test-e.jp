<?php

namespace App\Entity\Contact;

use App\Entity\Interfaces\InquiryInterface;
use App\Entity\Traits\InquiryTrait;
use App\Entity\Traits\ModifiedTimeTrait;
use App\Repository\Contact\DataRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use TripleE\Utilities\Entity\Interfaces\CsvExportInterface;

#[ORM\Entity(repositoryClass: DataRepository::class)]
#[ORM\Table(name: "inquiry_contact_data")]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
class Data implements InquiryInterface, CsvExportInterface
{
    use SoftDeleteableEntity;
    use ModifiedTimeTrait;
    use InquiryTrait;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    public function getCsvRow(): array
    {
        return [
            [
                "header" => "ID",
                "value" => $this->getId()
            ],
            [
                "header" => "日時",
                "value" => $this->created_at->format('Y/m/d H:i:s')
            ],
            [
                "header" => "氏名",
                "value" => $this->getName()
            ],
            [
                "header" => "Email",
                "value" => $this->getEmail()
            ],
            [
                "header" => "お問い合わせ内容",
                "value" => $this->getMessage()
            ],
            [
                "header" => "IPアドレス",
                "value" => $this->getIp()
            ]
        ];
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
