<?php

namespace lokothodida\Bank\Http;

use lokothodida\Bank\Domain\Exception\AccountNotFound;
use lokothodida\Bank\Query\GetAccountBalance as GetAccountBalanceQuery;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetAccountBalance
{
    private GetAccountBalanceQuery $query;

    public function __construct(GetAccountBalanceQuery $query)
    {
        $this->query = $query;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $body = ($this->query)($args['accountId']);
            $status = 200;
        } catch (AccountNotFound $e) {
            $status = 404;
            $body = [
                'message' => $e->getMessage(),
            ];
        }

        $response->getBody()->write((string) json_encode($body));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
