<?php

namespace lokothodida\Bank\Infrastructure\Http;

use lokothodida\Bank\Query\Exception\CustomerNotFound;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use lokothodida\Bank\Query\GetAccounts as GetAccountsQuery;

final class GetAccounts
{
    private GetAccountsQuery $query;

    public function __construct(GetAccountsQuery $query)
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
        $customerId = $request->getHeader('X-Customer-ID')[0];
        try {
            $status = 200;
            $body = ($this->query)($customerId);
        } catch (CustomerNotFound $e) {
            $status = 404;
            $body = [
                'message' => $e->getMessage(),
            ];
        }

        $response->getBody()->write((string) json_encode($body));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
