# SkyLink Base Package

This package is a PHP SDK for the Retail Express V2 API (with capability to support multiple Retail Express API versions).

## Installing

To install this package, simply clone to your machine and run:

```
composer install
```

## Integration Tests

Behat is used for integration tests and is used to connect to a real Retail Express database. Because integraiton tests run in a controlled environment, you must use the correct database designed for these tests.

To begin, copy `.env.example` to `.env` and substitute real values for:

1. `V2_API_CLIENT_ID`
2. `V2_API_USERNAME`
3. `V2_API_PASSWORD`

To run the integration test suite, simply type:

```
vendor/bin/behat --tags '@completed'
```

Omit the `--tags` argument to run all work-in-progress tests as well.

## Spec Tests

*Coming soon.*