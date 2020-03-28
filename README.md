# Sample Banking App
This is a small toy banking application designed to illustrate some software design concepts.

* [Domain driven design](https://en.wikipedia.org/wiki/Domain-driven_design) - the domain is a very crude, simplified look at a Bank that offers accounts.
* [Clean/hexagonal architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html) - the storage mechanism and web server are easily replaceable - they are *plugins* to the main application.
* [CQRS](https://martinfowler.com/bliki/CQRS.html) - application commands and queries are separated within the codebase.
* [Event sourcing](https://www.youtube.com/watch?v=I3uH3iiiDqY) - events are persisted, rather than current state, meaning that current state is a projection of the stream of events.
  There are multiple views of the same event store (e.g. `accounts`, `transactions`, `balance`).

## Use cases
- [x] Open an account
- [x] Deposit funds into an account
- [x] Withdraw funds from an account
- [ ] Transfer funds between accounts
- [x] Freeze/unfreeze an account (no more withdrawals)
- [x] Close an account (no more deposits or withdrawals)
- [x] View current balance
- [x] View transaction history
- [ ] View transfers
- [ ] View transfer state

## Quickstart
### With docker
1. Install dependencies
    ```
    make docker-build
    ```
2. Run the web server
    ```
    make docker-up
    ```

You can access the running container with

```
make docker-ssh
```



### Without docker
1. Install dependencies
    ```
    composer install
    ```
2. Run the web server:
    ```
    php public/index.php
    ```
