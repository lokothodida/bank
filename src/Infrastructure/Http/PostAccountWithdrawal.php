<?php

namespace lokothodida\Bank\Infrastructure\Http;

use lokothodida\Bank\WithdrawFromAccount;
use lokothodida\Bank\Domain\Exception\AccountNotFound;
use lokothodida\Bank\Domain\Exception\DomainException;
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
        } catch (DomainException $e) {
            $status = 422;
            $body = [
                'message' => $e->getMessage()
            ];
        }

        $response->getBody()->write((string) json_encode($body));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
