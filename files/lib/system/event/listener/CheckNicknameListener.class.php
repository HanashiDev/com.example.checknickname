<?php

namespace wcf\system\event\listener;

use GuzzleHttp\Psr7\Request;
use Throwable;
use wcf\system\exception\UserInputException;
use wcf\system\io\HttpFactory;
use wcf\util\JSON;

final class CheckNicknameListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $client = HttpFactory::makeClient();
        $request = new Request('GET', 'https://localhost/api/check_nickname.php' . \http_build_query([
            'nickname' => $eventObj->username,
        ], '', '&'));

        $data = [];
        try {
            $response = $client->send($request);
            $data = JSON::decode((string)$response->getBody());
        } catch (Throwable $e) {
            // do nothing
        }

        if (!isset($data['available']) || $data['available']) {
            throw new UserInputException('nickname', 'notAvailable');
        }
    }
}
