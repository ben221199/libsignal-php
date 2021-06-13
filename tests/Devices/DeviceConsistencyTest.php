<?php
namespace WhisperSystems\LibSignal\Devices;

use PHPUnit\Framework\TestCase;
use WhisperSystems\LibSignal\InvalidMessageException;
use WhisperSystems\LibSignal\Protocol\DeviceConsistencyMessage;
use WhisperSystems\LibSignal\Util\KeyHelper;

class DeviceConsistencyTest extends TestCase{

    /**
     * @covers DeviceConsistencyCommitment
     * @throws InvalidMessageException
     */
    public function testDeviceConsistency(): void{
        $deviceOne = KeyHelper::generateIdentityKeyPair();
        $deviceTwo = KeyHelper::generateIdentityKeyPair();
        $deviceThree = KeyHelper::generateIdentityKeyPair();

        $keyList = [
            $deviceOne->getPublicKey(),
            $deviceTwo->getPublicKey(),
            $deviceThree->getPublicKey(),
        ];

        shuffle($keyList);
        $deviceOneCommitment = new DeviceConsistencyCommitment(1,$keyList);

        shuffle($keyList);
        $deviceTwoCommitment = new DeviceConsistencyCommitment(1,$keyList);

        shuffle($keyList);
        $deviceThreeCommitment = new DeviceConsistencyCommitment(1,$keyList);

        $this->assertTrue($deviceOneCommitment->toByteArray()===$deviceTwoCommitment->toByteArray());
        $this->assertTrue($deviceTwoCommitment->toByteArray()===$deviceThreeCommitment->toByteArray());

        $deviceOneMessage = new DeviceConsistencyMessage($deviceOneCommitment,$deviceOne);
        $deviceTwoMessage = new DeviceConsistencyMessage($deviceOneCommitment,$deviceTwo);
        $deviceThreeMessage = new DeviceConsistencyMessage($deviceOneCommitment,$deviceThree);

        $receivedDeviceOneMessage = new DeviceConsistencyMessage($deviceOneCommitment,$deviceOneMessage->getSerialized(),$deviceOne->getPublicKey());
        $receivedDeviceTwoMessage = new DeviceConsistencyMessage($deviceOneCommitment,$deviceTwoMessage->getSerialized(),$deviceTwo->getPublicKey());
        $receivedDeviceThreeMessage = new DeviceConsistencyMessage($deviceOneCommitment,$deviceThreeMessage->getSerialized(),$deviceThree->getPublicKey());

        $this->assertTrue($deviceOneMessage->getSignature()->getVrfOutput()===$receivedDeviceOneMessage->getSignature()->getVrfOutput());
        $this->assertTrue($deviceTwoMessage->getSignature()->getVrfOutput()===$receivedDeviceTwoMessage->getSignature()->getVrfOutput());
        $this->assertTrue($deviceThreeMessage->getSignature()->getVrfOutput()===$receivedDeviceThreeMessage->getSignature()->getVrfOutput());

        $codeOne = self::generateCode($deviceOneCommitment,$deviceOneMessage,$receivedDeviceTwoMessage,$receivedDeviceThreeMessage);
        $codeTwo = self::generateCode($deviceTwoCommitment,$deviceTwoMessage,$receivedDeviceThreeMessage,$receivedDeviceOneMessage);
        $codeThree = self::generateCode($deviceThreeCommitment,$deviceThreeMessage,$receivedDeviceTwoMessage,$receivedDeviceOneMessage);

        $this->assertEquals($codeOne, $codeTwo);
        $this->assertEquals($codeTwo, $codeThree);
    }

    private function generateCode(DeviceConsistencyCommitment $commitment,DeviceConsistencyMessage ...$messages): string{
        $signatures = [];

        foreach($messages AS $message){
            $signatures[] = $message->getSignature();
        }

        return DeviceConsistencyCodeGenerator::generateFor($commitment,$signatures);
    }

}