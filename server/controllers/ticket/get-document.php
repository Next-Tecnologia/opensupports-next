<?php
use Respect\Validation\Validator as DataValidator;
DataValidator::with('CustomValidations', true);
/**
 * @api {post} /ticket/get-document Get ticket document
 * @apiVersion 4.8.0
 *
 * @apiName Get ticket document
 *
 * @apiGroup Ticket
 *
 * @apiDescription This path retrieves a document with informations about specific ticket.
 *
 * @apiPermission user
 *
 * @apiParam {Number} ticketNumber The number of the ticket.
 *
 * @apiUse INVALID_TICKET
 * @apiUse INVALID_TOKEN
 * @apiUse NO_PERMISSION
 *
 * @apiSuccess {[Ticket](#api-Data_Structures-ObjectTicket)} data Information about the requested ticket.
 *
 */


class TicketGetDocumentController extends Controller {
    const PATH = '/get-document';
    const METHOD = 'POST';

    private $ticket;

    public function validations() {
        $session = Session::getInstance();
        return [
            'permission' => 'user',
            'requestData' => [
                'ticketNumber' => [
                    'validation' => DataValidator::validTicketNumber(),
                    'error' => ERRORS::INVALID_TICKET
                ]
            ]
        ];
    }

    public function handler() {
        $this->ticket = Ticket::getByTicketNumber(Controller::request('ticketNumber'));
        
        if ($this->shouldDenyPermission()) {
            throw new RequestException(ERRORS::NO_PERMISSION);
        }

        (new TicketDocument)->generate();

        Response::respondSuccess(['success' => 'ok']);
    }

    private function shouldDenyPermission() {
        $user = Controller::getLoggedUser();
        return !$user->canManageTicket($this->ticket);
    }
}
