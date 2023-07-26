<?php

namespace App\Tests\Services\Payment;

use App\Entity\Client;
use App\Entity\Order;
use App\Services\Payment\CardPaymentProcessor;
use App\Services\Payment\CashPaymentProcessor;
use App\Services\Payment\PaymentHandler;
use App\Services\Payment\TipsCardPaymentDecorator;
use App\Services\Payment\TipsCashPaymentDecorator;
use App\Services\Tips\TipsDistributor;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PaymentHandlerTest extends TestCase
{
    public function testPayOrderByCash(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $client = $this->createMock(Client::class);
        $order = $this->createMock(Order::class);

        $cashPaymentProcessor = $this->getMockBuilder(CashPaymentProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->method('isEnoughMoney')->willReturn(true);
        $client->method('getConnectedOrder')->willReturn($order);

        $container->method('get')
            ->willReturn($cashPaymentProcessor);

        $cashPaymentProcessor->expects($this->once())
            ->method('pay')
            ->with($this->equalTo($client), $this->equalTo($order));

        $paymentHandler = new PaymentHandler($container);
        $paymentHandler->payOrder($client);
    }

    public function testPayOrderByCard(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $client = $this->createMock(Client::class);
        $order = $this->createMock(Order::class);

        $cardPaymentProcessor = $this->getMockBuilder(CardPaymentProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->method('isEnoughMoney')->willReturn(true);
        $client->method('getConnectedOrder')->willReturn($order);

        $container->method('get')
            ->willReturn($cardPaymentProcessor);

        $cardPaymentProcessor->expects($this->once())
            ->method('pay')
            ->with($this->equalTo($client), $this->equalTo($order));

        $paymentHandler = new PaymentHandler($container);
        $paymentHandler->payOrder($client);
    }

    public function testPayOrderByCashWithTips(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $client = $this->createMock(Client::class);
        $order = $this->createMock(Order::class);
        $tipsCashPaymentDecorator = $this->getMockBuilder(TipsCashPaymentDecorator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->method('isEnoughMoney')->willReturn(true);
        $client->method('getConnectedOrder')->willReturn($order);


        $container->method('get')
            ->willReturn($tipsCashPaymentDecorator);

        $tipsCashPaymentDecorator->expects($this->once())
            ->method('pay')
            ->with($this->equalTo($client), $this->equalTo($order));

        $paymentHandler = new PaymentHandler($container);
        $paymentHandler->payOrder($client);
    }

    public function testPayOrderByCardWithTips(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $client = $this->createMock(Client::class);
        $order = $this->createMock(Order::class);
        $tipsCardPaymentDecorator = $this->getMockBuilder(TipsCardPaymentDecorator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->method('isEnoughMoney')->willReturn(true);
        $client->method('getConnectedOrder')->willReturn($order);

        $container->method('get')
            ->willReturn($tipsCardPaymentDecorator);

        $tipsCardPaymentDecorator->expects($this->once())
            ->method('pay')
            ->with($this->equalTo($client), $this->equalTo($order));

        $paymentHandler = new PaymentHandler($container);
        $paymentHandler->payOrder($client);
    }

    public function testIsEnoughMoney(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $client = $this->createMock(Client::class);

        $client->method('isEnoughMoney')->willReturn(false);
        $this->expectException(Exception::class);

        $paymentHandler = new PaymentHandler($container);
        $paymentHandler->payOrder($client);
    }
}