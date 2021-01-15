<?php
use RedBeanPHP\Facade as RedBean;
use Respect\Validation\Validator as DataValidator;

/**
 * @api {post} /client/get-clients-departments Get clients list
 * @apiVersion 4.8.0
 *
 * @apiName Get client users list
 *
 * @apiGroup Client
 *
 * @apiDescription This path retrieves the list of users of specific client.
 *
 * @apiPermission staff1
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_PAGE
 * @apiUse INVALID_ORDER
 *
 * @apiSuccess {Object} data
 * @apiSuccess {[Client](#api-Data_Structures-ObjectClient)[]} data.client.users Array of users of client found
 */

class GetClientDepartmentsController extends Controller {
    const PATH = '/get-clients-departments';
    const METHOD = 'POST';

    public function validations() {
        return[
            'permission' => 'staff_1',
            'requestData' => [
                'departmentId' => [
                    'validation' => DataValidator::numeric(),
                    'error' => ERRORS::INVALID_CLIENT
                ]
            ]
        ];
    }

    public function handler() {

        $clientUsersList = $this->getClientUsers();
        $clientUsersListArray = [];

        foreach ($clientUsersList as $clientUser) {
            $clientUsersListArray[] = [
                'id' => $clientUser->id,
                'name' => $clientUser->name,
            ];
        }

        Response::respondSuccess([
            'clients' => $clientUsersListArray,
        ]);
    }

    private function getClientUsers() {
        $departmentId = Controller::request('departmentId');
        $query = 'SELECT * FROM `client` 
        WHERE `client`.`department_id` = ?';
        if (!$clientUsers = RedBean::getAll($query, [$departmentId])) {
            return[];
        }
        return RedBean::convertToBeans('client', $clientUsers);
    }
}
