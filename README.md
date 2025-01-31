# Exaba Laravel Driver Project test&demo README

## Overview
This project consists of the following components:
- A basic demo application
- Two utility tools
- A set of test scripts

## Prerequisites
Before running the application, ensure that the **Maxio Driver** is installed locally and all dependencies are correctly configured. Use the following commands to configure and install the required dependencies:

```sh
composer config repositories.eloquent-maxio-driver path ../eloquent-maxio-driver
composer require exaba/eloquent-maxio-driver
```

Additionally, a **Minio service** must be running and properly configured before executing the application. Ensure that both `.env` and `.env.testing` files are correctly modified to reflect the required settings. A bucket must be created in Minio before establishing a connection.

## Running the Demo
After installing dependencies via `composer install`, the demo application can be accessed at:

```
http://localhost:8000/
```

## Features and Components
- **DBViewer**: This utility tool provides a graphical interface for viewing database data created using the **Exaba Driver**.
- **Students Demo**: A basic demo application to demonstrate core functionalities.

## Running Tests

### Relationship Functionality Tests
To test relationship-related functionalities, execute the following command:

```sh
php tests/unit/runRelationshipTest.php
```

This script initializes test data and works in conjunction with `tests/Driver Check List - 1 (version 1).xlsb.xlsx`. The `runRelationshipTest.php` script is an interactive command-line unit testing tool designed to facilitate testing of individual methods.

### Real Project Code Tests
To test actual project code provided by Thomas, use the command:

```sh
php tests/unit/runRealProjectCodeTest.php
```

This script contains mock models and initializes test data. It is designed to work with `tests/RealProjectCode.xlsx`.

---
Ensure that all dependencies and configurations are set up correctly before running the tests or demo application. For any issues, refer to the documentation or reach out to the project maintainers.

