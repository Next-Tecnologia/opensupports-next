<?php
$clientControllers = new ControllerGroup();
$clientControllers->setGroupPath('/client');

$clientControllers->addController(new GetClientUsersController());
$clientControllers->addController(new GetClientsController());

$clientControllers->finalize();