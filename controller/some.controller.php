<?php

use UKMNorge\Slack\API\App;
use UKMNorge\Some\Forslag\Ideer;

App::initFromBotToken(SLACK_BOT_TOKEN);

if( isset($_GET['forslag'])) {
    UKMpr::addViewData('forslag', Ideer::getById(intval($_GET['forslag'])));
    UKMpr::setAction('forslag/detaljer');
} elseif (isset($_GET['forslag_delete'])) {
    UKMpr::addViewData('forslag', Ideer::getById($_GET['forslag_delete']));
    UKMpr::setAction('forslag/delete');
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        switch ($_POST['action']) {
            // FORSLAG: DELETE
            case 'forslag_delete':
                UKMpr::require('some/delete/forslag.php');
                break;
        }
    }

    UKMpr::addViewData('ideer', Ideer::loadAll());
}
