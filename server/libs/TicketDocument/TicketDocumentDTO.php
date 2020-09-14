<?php

final class TicketDocumentDTO
{
    /**
     * @var string
     */
    private $ticketNumber;

    /**
     * @var string
     */
    private $ticketDate;

    /**
     * @var string
     */
    private $socialName;

    /**
     * @var string
     */
    private $fantasyName;

    /**
     * @var string
     */
    private $cnpjCpf;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $cep;

    /**
     * @var string
     */
    private $cityUf;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $mailAddress;

    /**
     * @var string
     */
    private $clerk;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $formService;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $descriptionList;

    /**
     * @var string
     */
    private $historicList;


    /**
     * @param string $ticketNumber
     */
    public function setTicketNumber($ticketNumber): self
    {
        $this->ticketNumber = $ticketNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getTicketNumber(): string
    {
        return $this->ticketNumber;
    }

    /**
     * @param string $ticketDate
     */
    public function setTicketDate($ticketDate): self
    {
        $this->ticketDate = $ticketDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getTicketDate(): string
    {
        return !is_null($this->ticketDate) ? $this->ticketDate : date('d/m/Y');
    }

    /**
     * @param string $socialName
     */
    public function setSocialName($socialName): self
    {
        $this->socialName = $socialName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSocialName(): string
    {
        return $this->socialName;
    }

    /**
     * @param string $fantasyName
     */
    public function setFantasyName($fantasyName): self
    {
        $this->fantasyName = $fantasyName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFantasyName(): string
    {
        return $this->fantasyName;
    }

    /**
     * @param string $cnpjCpf
     */
    public function setCnpjCpf($cnpjCpf): self
    {
        $this->cnpjCpf = $cnpjCpf;
        return $this;
    }

    /**
     * @return string
     */
    public function getCnpjCpf(): string
    {
        return $this->cnpjCpf;
    }

    /**
     * @param string $address
     */
    public function setAddress($address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $cep
     */
    public function setCep($cep): self
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * @return string
     */
    public function getCep(): string
    {
        return $this->cep;
    }

    /**
     * @param string $cityUf
     */
    public function setCityUf($cityUf): self
    {
        $this->cityUf = $cityUf;
        return $this;
    }

    /**
     * @return string
     */
    public function getCityUf(): string
    {
        return $this->cityUf;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $mailAddress
     */
    public function setMailAddress($mailAddress): self
    {
        $this->mailAddress = $mailAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getMailAddress(): string
    {
        return $this->mailAddress;
    }

    /**
     * @param string $clerk
     */
    public function setClerk($clerk): self
    {
        $this->clerk = $clerk;
        return $this;
    }

    /**
     * @return string
     */
    public function getClerk(): string
    {
        return $this->clerk;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $formService
     */
    public function setFormService($formService): self
    {
        $this->formService = $formService;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormService(): string
    {
        return $this->formService;
    }

    /**
     * @param string $status
     */
    public function setStatus($status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $descriptionList
     */
    public function setDescriptionList($descriptionList): self
    {
        $this->descriptionList = $descriptionList;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionList(): string
    {
        return $this->descriptionList;
    }

    /**
     * @param string $historicList
     */
    public function setHistoricList($historicList): self
    {
        $this->historicList = $historicList;
        return $this;
    }

    /**
     * @return string
     */
    public function getHistoricList(): string
    {
        return $this->historicList;
    }
}