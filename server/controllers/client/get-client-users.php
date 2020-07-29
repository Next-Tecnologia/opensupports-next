<?php
use RedBeanPHP\Facade as RedBean;
use Respect\Validation\Validator as DataValidator;

/**
 * @api {post} /client/get-client-users Get clients list
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

class GetClientUsersController extends Controller {
    const PATH = '/get-client-users';
    const METHOD = 'POST';

    public function validations() {
        return[
            'permission' => 'staff_1',
            'requestData' => [
                'clientId' => [
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
            'clientUsers' => $clientUsersListArray,
        ]);
    }

    private function getClientUsers() {
        $clientId = Controller::request('clientId');
        $query = 'SELECT u.id, u.name FROM user as u INNER JOIN client as c ON u.client_id = c.id WHERE c.id = ?';
        if (!$clientUsers = RedBean::getAll($query, [$clientId])) {
            return[];
        }
        return RedBean::convertToBeans('client', $clientUsers);
    }
}
