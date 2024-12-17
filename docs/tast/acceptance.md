---
title: Acceptance Tests
permalink: '/tests/acceptance/'
---

Acceptance testing is primarily used to prevent unintended changes to the frontend.
Epigraf uses [Codeception](https://codeception.com/docs/AcceptanceTests) as the testing framework.
The test environment launches a browser and uses a [Selenium](https://www.selenium.dev/documentation/webdriver/) server
to perform browser actions via the WebDriver protocol.

All acceptance tests are located in the `tests/Codeception` directory.
The configuration of the test environment is to be found in `tests/Codeception/acceptance.suite.yml`.
All test scenarios are implemented in the subfolder `tests/Codeception/Acceptance`.

Developing and running acceptance tests can be challenging because the tests are sensitive to small timing differences
on different machines. When a lack of system resources delay the browser operation,
a test may fail although all functions work correctly. Further, screenshot comparisons rely on identical
rendering processes in the development and the test system.
In case tests fail without an obvious reason, it is advisable to rerun the tests individually.

Note that before running tests in the development system, the frontend assets (JavaScript and CSS bundles)
must be packed. The most easy way is to call `npm run build` which is configured to update all necessary bundles.

See the readme.md in the `tests/Codeception` directory for more information about how to develop and perform acceptence tests.
