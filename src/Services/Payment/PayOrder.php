<?php

namespace App\Services\Payment;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PayOrder
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CashPayment
     */
    private $cashPayment;

    /**
     * @var CardPayment
     */
    private $cardPayment;

    /**
     * @var TipsCashPayment
     */
    private $tipsCashPayment;

    /**
     * @var TipsCardPayment
     */
    private $tipsCardPayment;

    /**
     * @var OrderValue
     */
    private $orderValue;

    /**
     * @param EntityManagerInterface $em
     * @param CashPayment $cashPayment
     * @param CardPayment $cardPayment
     * @param TipsCashPayment $tipsCashPayment
     * @param TipsCardPayment $tipsCardPayment
     * @param OrderValue $orderValue
     */
    public function __construct(
        EntityManagerInterface $em,
        CashPayment $cashPayment,
        CardPayment $cardPayment,
        TipsCashPayment $tipsCashPayment,
        TipsCardPayment $tipsCardPayment,
        OrderValue $orderValue
    ) {
        $this->em = $em;
        $this->cashPayment = $cashPayment;
        $this->cardPayment = $cardPayment;
        $this->tipsCashPayment = $tipsCashPayment;
        $this->tipsCardPayment = $tipsCardPayment;
        $this->orderValue = $orderValue;
    }

    /**
     * @throws Exception
     */
    public function payOrder(Client $client): void
    {
        $orderValue = $this->orderValue->getOrderValue($client);
        $payment = $this->getPaymentMethod();

        switch ($payment['paymentStrategy']) {
            case 'cash':
                $paymentStrategy = $this->cashPayment;
                $isEnoughMoney = $this->orderValue->isEnoughMoney($client);
                break;
            case 'card':
                $paymentStrategy = $this->cardPayment;
                $isEnoughMoney = $this->orderValue->isEnoughMoney($client);
                break;
            case 'cash_tips':
                $paymentStrategy = $this->tipsCashPayment;
                $isEnoughMoney = $this->orderValue->isEnoughMoney($client, $orderValue);
                break;
            case 'card_tips':
                $paymentStrategy = $this->tipsCardPayment;
                $isEnoughMoney = $this->orderValue->isEnoughMoney($client, $orderValue);
                break;
            default:
                throw new Exception('wrong payment strategy');
        }

        try {
            if ($isEnoughMoney) {
                $paymentStrategy->pay($client, $client->getConnectedOrder());
                $client->setStatus(Client::ORDER_PAYED);
                $this->em->flush();
            }
        } catch(\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getPaymentMethod(): array
    {
        $strategyNumber = rand(1,4);

        $paymentStrategy = match ($strategyNumber) {
            1 => 'card',
            2 => 'cash',
            3 => 'cash_tips',
            4 => 'card_tips'
        };

        return [
            'paymentStrategy' => $paymentStrategy
        ];

    }
}