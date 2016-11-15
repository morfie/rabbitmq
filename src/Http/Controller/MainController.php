<?php

namespace Http\Controller;

use Database\MessageLoggerInterface;
use Http\Rendering\JsonRendering;
use Http\Rendering\PhpRendering;
use Http\ValueObject\Request;
use Http\ValueObject\Response;
use Queue\Message\NumberHandlerMessage;
use Queue\Publisher;

class MainController {

    /**
     * @var PhpRendering
     */
    private $phpRendering;

    /**
     * @var JsonRendering
     */
    private $jsonRendering;

    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @var MessageLoggerInterface
     */
    private $dbGateWay;

    /**
     * MainController constructor.
     *
     * @param PhpRendering $renderer
     */
    public function __construct(
        PhpRendering $phpRendering, JsonRendering $jsonRendering, Publisher $publisher, MessageLoggerInterface $dbGateway
    ) {
        $this->phpRendering = $phpRendering;
        $this->jsonRendering = $jsonRendering;
        $this->publisher = $publisher;
        $this->dbGateWay = $dbGateway;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function homeAction(Request $request) {
        return $this->phpRendering->render(dirname(__DIR__) . '/Resource/view/home.php');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function generateAction(Request $request) {

        $message = $this->publisher->createMessage();
        $message->setNumber(mt_rand(1000, 9999));

        /** @var $message NumberHandlerMessage */
        $this->publisher->send($message);

        return $this->jsonRendering->render([
            'number' => $message->getNumber(),
            'correlationId' => $message->getId(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function readInfoAction(Request $request) {
        $correlationId = $request->get('correlationId');
        $logs = [];
        foreach ($this->dbGateWay->findMessageByCorrelationId($correlationId) as $item) {
            $logs[] = $item['message'];
        }

        return $this->jsonRendering->render([
            'correlationId' => $correlationId,
            'log' => $logs,
        ]);
    }
}