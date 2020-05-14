<?php

use UKMNorge\Slack\API\Chat;
use UKMNorge\Slack\API\Conversations;
use UKMNorge\Slack\Block\Composition\Markdown;
use UKMNorge\Slack\Block\Composition\PlainText;
use UKMNorge\Slack\Block\Section;
use UKMNorge\Slack\Payload\Message;
use UKMNorge\Some\Forslag\Ideer;
use UKMNorge\Some\Forslag\Write;
use UKMNorge\Some\Slack\Template;

$forslag = Ideer::getById(intval($_POST['forslag_id']));

try {
    Write::delete($forslag);
    UKMpr::getFlash()->success('Forslaget er slettet.');
} catch (Exception $e) {
    UKMpr::getFlash()->error('Kunne ikke slette forslag ' . $_POST['forslag_id'] . ". Systemet sa: \r\n\r\n " . $e->getMessage());
}

if (isset($_POST['alert']) && $_POST['alert'] == 'true') {
    try {
        $channel = Conversations::startWithUser($forslag->getEier()->getSlackId());

        $tekst = 'Hei! Dette forslaget har blitt fjernet fra idé-listen for sosiale medier.';

        $message = new Message(
            $channel->channel->id,
            new PlainText(
                $tekst
            )
        );
        $message->getBlocks()->add(
            new Section(
                new Markdown(
                    $tekst .'\n'.'>>>'
                )
            )
        );
        

        // Legg til standard-preview for forslag
        Template::getStatusSuggestionPreview( $message, $forslag );

        $res = Chat::post($message);

        $msg .= ' ' . $forslag->getEier()->getLink() . ' er varslet.';
        UKMpr::getFlash()->success($forslag->getEier()->getLink() . ' er varslet');
    } catch (Exception $e) {
        UKMpr::getFlash()->error('Kunne ikke varsle ' . $forslag->getEier()->getLink() . ". Systemet sa: \r\n\r\n " . $e->getMessage());
    }
}
