[![Build Status](https://travis-ci.org/lokothodida/bank.svg?branch=master)](https://travis-ci.org/lokothodida/bank)

# Sample Banking App
A small application and domain model for a banking
system with the following actions:

* Open Account
* Deposit Funds (into account)
* Withdraw Funds (from account)
* Transfer Funds (between accounts)
* Freeze Account

This is a toy project to play about with a few concepts:

- Domain driven design
- Event sourcing
- Immutable objects
- Clean/hexagonal architecture

## Requirements
- PHP 7.2+

## Installation
```
cd app && composer install && cd ../
cd sqlite-adapter && composer install && cd ../
```

## TODO
- [x] Integrate Travis CI with the build
- [ ] Create terminal UI app
