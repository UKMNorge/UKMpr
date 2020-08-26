<?php

use UKMNorge\Slack\API\App;
use UKMNorge\Slack\Cache\Team\Teams;
use UKMNorge\Some\Forslag\Ideer;

$team = Teams::getBySlackId( SLACK_UKMMEDIA_TEAM_ID );
App::initFromBotToken( $team->getBot()->getAccessToken() );

if( isset($_GET['forslag'])) {
    UKMpr::addViewData('forslag', Ideer::getById(intval($_GET['forslag'])));
    UKMpr::setAction('forslag/vis');
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
