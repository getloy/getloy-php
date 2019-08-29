<?php

namespace Getloy\PaymentProviders\Configuration;

use \Exception;

/**
 * Abstract payment provider config class.
 */
abstract class PaymentProviderConfig
{
  
    protected $config;

    /**
     * Instantiate a payment provider configuration.
     * @param array $config Configuration for the payment method.
     */
    public function __construct(array $config)
    {
        $this->config = $this->evaluateConfig($config, $this->configValidation());
    }

    /**
     * Get configuration value.
     * @param string $configOption Name of the configuration option.
     * @return mixed The configuration value, or an empty string if the option is not set.
     */
    public function get(string $option)
    {
        if (!array_key_exists($option, $this->config)) {
            return '';
        }
        return $this->config[$option];
    }

    /**
     * Generate validation configuration for payment method-specific configuration options.
     * @return array Validation configuration
     */
    abstract protected function paymentMethodConfigValidation(): array;

    /**
     * Generate validation configuration for general and payment method-specific configuration
     * options.
     * @return array Validation configuration
     */
    protected function configValidation(): array
    {
        $configValidation = $this->paymentMethodConfigValidation();
        if (!array_key_exists('testMode', $configValidation)) {
            $configValidation['testMode'] = [
                'type' => 'boolean',
                'required' => false,
                'default' => true,
            ];
        }
        return $configValidation;
    }

    /**
     * Validates the specified config option against the validation rules and returns the config
     * value.
     * @param string $configOption Name of the configuration option to evaluate.
     * @param array $config Configuration for the payment method.
     * @param array $configValidation Validation configuration.
     * @return mixed The config value after validation, or an empty string if there is no value for
     *               the option.
     * @throws Exception If the validation fails.
     */
    protected function evaluateConfigValue(
        string $configOption,
        array $config,
        array $configValidation
    ) {

        if (!array_key_exists($configOption, $configValidation)) {
            throw new Exception(sprintf('Unknown configuration option "%s"', $configOption));
        }

        $optionValidation = $configValidation[$configOption];
        if (!array_key_exists($configOption, $config)) {
            if (array_key_exists('required', $optionValidation) && $optionValidation['required']) {
                throw new Exception(sprintf(
                    'Configuration is missing required option "%s"',
                    $configOption
                ));
            }
            if (array_key_exists('default', $optionValidation)) {
                return $optionValidation['default'];
            }
            return '';
        }

        $optionValue = $config[$configOption];
        if (array_key_exists('type', $optionValidation)
            && gettype($optionValue) !== $optionValidation['type']) {
            throw new Exception(sprintf(
                'Validation of option "%s" failed: expected value of type "%s", but found "%s"!',
                $configOption,
                $optionValidation['type'],
                gettype($optionValue)
            ));
        }

        if (array_key_exists('allowedValues', $optionValidation)
            && !in_array($optionValue, $optionValidation['allowedValues'], true)) {
            throw new Exception(sprintf(
                'Validation of option "%s" failed: value "%s" is not one of the allowed values "%s"!',
                $configOption,
                $optionValue,
                implode('", "', $optionValidation['allowedValues'])
            ));
        }

        return $optionValue;
    }

    /**
     * Validates the specified config options against the validation rules and returns evaluated
     * config.
     * @param array $config Configuration for the payment method.
     * @param array $configValidation Validation configuration.
     * @return array The config after validation.
     * @throws Exception If the validation fails.
     */
    protected function evaluateConfig(array $config, array $configValidation): array
    {
        $evaluatedConfig = [];
        foreach ($configValidation as $option => $validation) {
            $evaldValue = $this->evaluateConfigValue($option, $config, $configValidation);
            if (!is_null($evaldValue)) {
                $evaluatedConfig[$option] = $evaldValue;
            }
        }
        return $evaluatedConfig;
    }
}
