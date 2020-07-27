<?php
use RedBeanPHP\Facade as RedBean;
use Respect\Validation\Validator as DataValidator;
DataValidator::with('CustomValidations', true);

/**
 * @api {post} /user/get Get my information
 * @apiVersion 4.8.0
 *
 * @apiName Get my Information
 *
 * @apiGroup User
 *
 * @apiDescription This path retrieves information about the logged user.
 *
 * @apiPermission user
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_CREDENTIALS
 *
 * @apiSuccess {Object} data Information about an user
 * @apiSuccess {String} data.name Name of the user
 * @apiSuccess {String} data.email Email of the user
 * @apiSuccess {Boolean} data.verified Indicates if the user is verified
 * @apiSuccess {Object} data Information about an user
 * @apiSuccess {[Ticket](#api-Data_Structures-ObjectTicket)[]} data.tickets Array of tickets of the user
 *
 */

class GetUserController extends Controller {
    const PATH = '/get';
    const METHOD = 'POST';

    public function validations() {
        return [
            'permission' => 'user',
            'requestData' => []
        ];
    }

    public function handler() {
        
        if (Controller::isStaffLogged()) {
            throw new RequestException(ERRORS::INVALID_CREDENTIALS);
            return;
        }

        $user = Controller::getLoggedUser();
        $parsedTicketList = [];
        $ticketList = $user->sharedTicketList;

        foreach($ticketList as $ticket) {
            $parsedTicketList[] = $ticket->toArray(true);
        }

        $parsedDepartmentList = [];
        $departmentList = $this->getDepartmentList($user->id);

        foreach ($departmentList as $department) {
            $parsedDepartmentList[] = [
                'id' => $department->department_id,
                'name' => $department->name,
                'private' => $department->private
            ];
        }

        Response::respondSuccess([
            'name' => $user->name,
            'email' => $user->email,
            'staff' => false,
            'verified' => !$user->verificationToken,
            'tickets' => $parsedTicketList,
            'departments' => $parsedDepartmentList,
            'customfields' => $user->xownCustomfieldvalueList->toArray(),
            'users' => $user->supervisedrelation ? $user->supervisedrelation->sharedUserList->toArray() : null
        ]);
    }

    public function getDepartmentList($userId) {
        $query = '
            SELECT
                *
            FROM
                department dp
            JOIN department_staff
                dps
            ON
                dp.id = dps.department_id
            WHERE
                dps.staff_id = ?';
        $departments = Redbean::getAll($query, [$userId]);
        if ($departments) {
            return RedBean::convertToBeans('department', $departments);
        }
        return [];
    }
}
