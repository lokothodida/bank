<?php

namespace lokothodida\Bank\Http;

use lokothodida\Bank\Command\DepositIntoAccount;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostAccountDeposit
{
    private DepositIntoAccount $depositIntoAccount;

    public function __construct(DepositIntoAccount $depositIntoAccount)
    {
        $this->depositIntoAccount = $depositIntoAccount;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $body = json_decode($request->getBody()->getContents());
        ($this->depositIntoAccount)($args['accountId'], $body->amount);
        $response->getBody()->write((string) json_encode([
            'message' => 'success',
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
