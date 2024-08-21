# RetackSDK

RetackSDK is a PHP library for integrating with Retack AI's error reporting service.

## Installation

You can install the RetackSDK via Composer:

```bash
composer require retack/retack-sdk
```

## Uage

### Initializing the SDK

First, initialize the RetackSDK with your Environment key:

```bash
use Retack\RetackSDK;

$retackSDK = new RetackSDK('your_environment_key_here');
```

### Reporting Errors

To report an error asynchronously:

```bash
$error = "An error occurred";
$stackTrace = new Exception("Sample exception");
$userContextExtras = ['username' => exampleuser ];

$result = $retackSDK->reportErrorAsync($error, $stackTrace, $userContextExtras);

if ($result) {
    echo "Error reported successfully";
} else {
    echo "Failed to report error";
}
```
