<?php

include_once __DIR__ . '/../FileManager.php';
include_once __DIR__ . '/TicketDocumentDTO.php';

class TicketDocument extends FileManager {

    /**
     * @var TicketDocumentDTO
     */
    private $ticketData;

    /**
     * 
     */
    public function __construct(TicketDocumentDTO $ticketDocumentDTO)
    {
        $this->ticketData = $ticketDocumentDTO;
    }

    /**
     * 
     */
    public function generate()
    {
        $ticketDocumentHTML = file_get_contents(__DIR__ . '/TicketDocumentTemplate.html');

        $templateData = [
            '|||TICKET_NUMBER|||'    => $this->ticketData->getTicketNumber(),
            '|||TICKET_DATE|||'      => $this->ticketData->getTicketDate(),
            '|||SOCIAL_NAME|||'      => $this->ticketData->getSocialName(),
            '|||CNPJ_CPF|||'         => $this->ticketData->getCnpjCpf(),
            '|||FANTASY_NAME|||'     => $this->ticketData->getFantasyName(),
            '|||ADDRESS|||'          => $this->ticketData->getAddress(),
            '|||CEP|||'              => $this->ticketData->getCep(),
            '|||CITY_UF|||'          => $this->ticketData->getCityUf(),
            '|||PHONE|||'            => $this->ticketData->getPhone(),
            '|||MAIL_ADDRESS|||'     => $this->ticketData->getMailAddress(),
            '|||CLERK|||'            => $this->ticketData->getClerk(),
            '|||SUBJECT|||'          => $this->ticketData->getSubject(),
            '|||FORM_SERVICE|||'     => $this->ticketData->getFormService(),
            '|||STATUS|||'           => $this->ticketData->getStatus(),
            '|||DESCRIPTION_LIST|||' => $this->ticketData->getDescriptionList(),
            '|||HISTORIC_LIST|||'    => $this->ticketData->getHistoricList(),
        ];

        foreach ($templateData as $key => $data) {
            $ticketDocumentHTML = str_replace($key, $data, $ticketDocumentHTML);
        }

        echo $ticketDocumentHTML;

        // $test = "<p>Relatório</p>";

        // $filePath = $this->getLocalPath();
        // $this->setFileName($filePath . "test.txt");

        // file_put_contents($this->getFileName(), $test);
    }
}

$dto = (new TicketDocumentDTO())
    ->setTicketNumber('30')
    ->setSocialName('Next Tecnologia')
    ->setCnpjCpf('000x.1110-000/0000')
    ->setFantasyName('Next TI')
    ->setAddress('Rua X')
    ->setCep('123123-123')
    ->setCityUf('Cruzeiro - SP')
    ->setPhone('12 3131-3131')
    ->setMailAddress('next@nexttecnologia.com.br')
    ->setClerk('Carlos')
    ->setSubject('Testando documento')
    ->setFormService('Presencial')
    ->setStatus('Atendido')
    ->setDescriptionList('ABC')
    ->setHistoricList('ABC');

(new TicketDocument($dto))->generate();