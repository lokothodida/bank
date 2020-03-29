<?php


namespace lokothodida\Bank\Infrastructure\Http;

use lokothodida\Bank\Query\Exception\AccountNotFound;
use lokothodida\Bank\Query\GetTransactions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetAccountTransactions
{
    private GetTransactions $query;

    public function __construct(GetTransactions $query)
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
            $status = 200;
            $body = ($this->query)($args['accountId']);
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
