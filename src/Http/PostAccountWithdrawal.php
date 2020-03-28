<?php

namespace lokothodida\Bank\Http;

use lokothodida\Bank\Command\DepositIntoAccount;
use lokothodida\Bank\Command\WithdrawFromAccount;
use lokothodida\Bank\Domain\Exception\AccountNotFound;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostAccountWithdrawal
{
    private WithdrawFromAccount $withdrawFromAccount;

    public function __construct(WithdrawFromAccount $withdrawFromAccount)
    {
        $this->withdrawFromAccount = $withdrawFromAccount;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $requestBody = json_decode($request->getBody()->getContents());

        try {
            ($this->withdrawFromAccount)($args['accountId'], $requestBody->amount);
            $status = 200;
            $body = [
                'message' => 'success',
            ];
        } catch (AccountNotFound $e) {
            $status = 404;
            $body = [
                'message' => $e->getMessage()
            ];
        }

        $response->getBody()->write((string) json_encode($body));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
