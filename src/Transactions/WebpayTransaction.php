<?php

namespace DarkGhostHunter\TransbankApi\Transactions;

use DarkGhostHunter\TransbankApi\Helpers\Helpers;

class WebpayTransaction extends AbstractTransaction
{
    /**
     * Set default attributes for the Item, depending on the WebpayClient type
     *
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        switch ($this->type) {
            case 'plus.normal':
            case 'plus.defer':
                $defaults = $this->filterDefaults(
                    'plus', ['plusReturnUrl', 'plusFinalUrl'], $defaults
                );
                break;
            case 'plus.mall.normal':
            case 'plus.mall.defer':
                $defaults = $this->filterDefaults(
                    'plusMall', ['plusMallReturnUrl', 'plusMallFinalUrl'], $defaults
                );
                break;
            case 'oneclick.register':
                $defaults = $this->filterDefaults(
                    'oneclick', ['oneclickReturnUrl'], $defaults
                );
                break;
            case 'patpass.subscription':
                $defaults = $this->filterDefaults(
                    'patpass', ['patpassReturnUrl', 'patpassFinalUrl'], $defaults
                );
                break;
            default:
                $defaults = [];
                break;
        }

        parent::setDefaults($defaults);
    }

    /**
     * Filter the defaults for the WebpayClient type
     *
     * @param string $service
     * @param array $only
     * @param array $defaults
     * @return array
     */
    protected function filterDefaults(string $service, array $only, array $defaults)
    {
        foreach (Helpers::arrayOnly($defaults, $only) as $name => $default) {
            $result[lcfirst(str_replace($service, '', $name))] = $default;
        }
        return $result ?? [];
    }

    /**
     * Fills Empty Attributes
     */
    protected function fillEmptyAttributes()
    {
        // Create the BuyOder with the required timestamp only when this
        // transaction is a Webpay Oneclick Charge.
        if ($this->type === 'oneclick.charge' && strlen((string)$this->buyOrder) < 14) {
            $this->buyOrder = date('YmdHis') . (substr((string)$this->buyOrder, -3) ?? '000');
        }
    }
}