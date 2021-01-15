<?php
use RedBeanPHP\Facade as RedBean;

/**
 * @api {post} /client/get-clients Get clients list
 * @apiVersion 4.8.0
 *
 * @apiName Get clients list
 *
 * @apiGroup Client
 *
 * @apiDescription This path retrieves the list of clients.
 *
 * @apiPermission staff1
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_PAGE
 * @apiUse INVALID_ORDER
 *
 * @apiSuccess {Object} data
 * @apiSuccess {[Client](#api-Data_Structures-ObjectClient)[]} data.clients Array of clients found
 */

class GetClientsController extends Controller {
    const PATH = '/get-clients';
    const METHOD = 'POST';

    public function validations() {
        return[
            'permission' => 'staff_1',
            'requestData' => []
        ];
    }

    public function handler() {

        $clientList = $this->getClientList();
        $clientListArray = [];

        foreach ($clientList as $client) {
            $clientListArray[] = [
                'id' => $client->id,
                'name' => $client->name,
            ];
        }

        Response::respondSuccess([
            'clients' => $clientListArray,
        ]);
    }

    private function getClientList() {
        if ($userDepartment = $this->getUserDepartment()) {
            $clientQuery = 'SELECT * FROM client WHERE department_id = ?';
            $clients = RedBean::getAll($clientQuery, [$userDepartment]);
            return RedBean::convertToBeans('user', $clients);
        }
        return [];
    }

    private function getUserDepartment() {
        $user = Controller::getLoggedUser();

        $userDepartment = RedBean::getAll(
            'SELECT id, dpt_id FROM user_department WHERE user_id = ? LIMIT 1',
            [$user->id]
        );

        if (!empty($userDepartment)) {
            return $userDepartment[0]['dpt_id'];
        }

        return false;
    }
}
