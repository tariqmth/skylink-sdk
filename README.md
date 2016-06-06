# SkyLink Base Package

This package is a PHP SDK for the Retail Express V2 API (with capability to support multiple Retail Express API versions).

## Installing

To install this package, simply clone to your machine and run:

```
composer install
```

## Testing

SkyLink makes use of Composer's inbuilt [scripts](https://getcomposer.org/doc/articles/scripts.md#writing-custom-commands) functionality to automate the running of several test suites and reports.

These are:

1. [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
2. [PHP Copy/Paste Detector](https://github.com/sebastianbergmann/phpcpd)
3. [PHPLOC \[lines of code\]](https://github.com/sebastianbergmann/phploc)
4. Unit tests (see below)
5. Integration tests (see below)

To execute all of the aforementioned tools, simply run:

```
composer test
```

You will see lots of output and any errors will halt execution. You may run unit/integration tests on their own (see below) and you will have more in-depth feedback (rather than just a summary that is shown during `composer test`).

### Unit Tests (Spec Tests)

[phpspec](http://www.phpspec.net/) is used for unit testing and is designed to be run in isolation - with no connection to any Retail Express databases.

To run spec tests, run:

```
vendor/bin/phpspec run
```

A coverage report will be generated as part of running phpspec, and this will be located under `build/coverage/index.html`. Open this file in your browser to view the report.

> **Important:** [Xdebug](https://xdebug.org) is required in order to generate coverage reports.

### Integration Tests

[Behat](http://behat.org/) is used for integration tests and is used to connect to a real Retail Express database. Because integration tests run in a controlled environment, you must use the correct database designed for these tests.

To begin, copy `.env.example` to `.env` and substitute real values for:

1. `V2_API_URL`
2. `V2_API_CLIENT_ID`
2. `V2_API_USERNAME`
3. `V2_API_PASSWORD`

To run the integration test suite, simply type:

```
vendor/bin/behat --tags '@completed'
```

Omit the `--tags` argument to run all work-in-progress tests as well.
