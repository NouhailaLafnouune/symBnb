<?php

namespace App\Entity;
use App\Entity\Booking;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: BookingRepository::class)]
/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 *
 */
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $booker = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ad $ad = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    
    private ?\DateTimeInterface $startDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
   
    private ?\DateTimeInterface $endDate;

    #[ORM\Column()]
    private ?\DateTimeImmutable $createdAt = NULL;

    #[ORM\Column()]
    private ?float $amount = NULL;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment;

   

    public function isBookableDates(){
        //1.il faut connaintre les dates qui sont inmpossibles pour l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        //2.if faut comparer les dates choisies avec les dates impossibles
        $bookingDays = $this->getDays();
        $formatDay = function($day){
            return $day->format('Y-m-d');
        };
        //Tableau des chanes de caracteres de mes journees
        $days = array_map($formatDay, $bookingDays);
        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach($days as $day){
            if(array_search($day, $notAvailable) !== false) return false;
        }
        return true;
        

    }

    /**
     * @return array Un tableau d'objet Datetime represenatnt les jours de la reservation
     * 
     */
    public function getDays(){
        $resultat = range (
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60 
        );
        $days = array_map(function($dayTimestamp){
            return new \DateTimeImmutable(date('Y-m-d', $dayTimestamp));
        },$resultat);
        return $days;
        
    }
    

   // public function getDuration(){
     //   $diff = $this->endDate->diff($this->startDate);
       // return $diff->days;
    //}


   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
