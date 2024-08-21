<?php

namespace Retack;

use Exception;

class RetackSDK {
    private $retackConfig;

    public function __construct(string $envKey) {
        $this->retackConfig = new RetackConfig($envKey);
    }

    public function reportErrorAsync(string $error, ?\Throwable $stackTrace = null, ?array $userContextExtras = null): bool {
        $userContext = $userContextExtras ? new UserContext($userContextExtras) : null;
        $errorReport = new ErrorReportRequest($error, $stackTrace, $userContext);

        $baseUrl = "https://api.retack.ai";
        $endpoint = "/observe/error-log/";

        $headers = [
            "Content-Type: application/json",
            "ENV-KEY: " . $this->retackConfig->getEnvKey()
        ];

        $body = json_encode([
            "title" => $errorReport->getError(),
            "stack_trace" => $errorReport->getStackTrace(),
            "user_context" => $errorReport->getUserContext() ? $errorReport->getUserContext()->toJson() : null
        ]);

        $ch = curl_init($baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            error_log("Unable to report error to Retack AI.");
            error_log($error);
            return false;
        } else {
            return true;
        }
    }
}

class RetackConfig {
    private $envKey;

    public function __construct(string $envKey) {
        $this->envKey = $envKey;
    }

    public function getEnvKey(): string {
        return $this->envKey;
    }
}

class UserContext {
    private $extras;

    public function __construct(array $extras) {
        $this->extras = $extras;
    }

    public function toJson(): string {
        return json_encode($this->extras);
    }
}

class ErrorReportRequest {
    private $error;
    private $stackTrace;
    private $userContext;

    public function __construct(string $error, ?\Throwable $stackTrace, ?UserContext $userContext = null) {
        $this->error = $error;
        $this->stackTrace = $stackTrace;
        $this->userContext = $userContext;
    }

    public function getError(): string {
        return $this->error;
    }

    public function getStackTrace(): ?string {
        return $this->stackTrace;
    }

    public function getUserContext(): ?UserContext {
        return $this->userContext;
    }

}
