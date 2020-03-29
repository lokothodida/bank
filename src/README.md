# Architecture
The core, coarse-grained use cases are presented within this level of the directory structure.
It [screams out](https://blog.cleancoder.com/uncle-bob/2011/09/30/Screaming-Architecture.html) what
the system is designed to do at the highest level.

[Domain](Domain) provides the core, more complicated rules governing the objects that interact.

[Infrastructure](Infrastructure) provides adapters for storage (which allow the use cases to actually
operate), as well as a delivery mechanism for serving content (e.g. HTTP).

[Query](Query) provides a [read model](https://cqrs.nu/tutorial/cs/03-read-models) for some basic queries in the system.
The implementation of these queries is left to the [Infrastructure](Infrastructure) (since different storage which
result in different ways of querying the data).

Tests for the use cases are available [here](../test/ManagingAccountsTest.php).
