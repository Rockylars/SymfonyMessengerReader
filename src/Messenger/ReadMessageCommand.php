<?php

declare(strict_types=1);

namespace Rocky\SymfonyMessengerReader\Messenger;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Psr\Container\ContainerInterface;
use Rocky\SymfonyMessengerReader\Exception\RejectedArgumentException;
use Rocky\SymfonyMessengerReader\Exception\UnknownReceiverException;
use Rocky\SymfonyMessengerReader\Helper\StringHelper;
use Safe\Exceptions\JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Messenger\Command\FailedMessagesShowCommand;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * TODO: Add a test for this.
 * Simplification of {@see \Symfony\Component\Messenger\Command\ConsumeMessagesCommand}.
 *
 * These below were tried but found to be inadequate.
 * {@see FailedMessagesShowCommand::displaySingleMessage()}
 * {@see VarDumper::dump}
 *
 * If you want to use this, you need to Composer require jms/serializer, or go for the bundle version, though that one
 * is a bit too much IMHO.
 */
final class ReadMessageCommand extends Command
{
    /** @var ContainerInterface */
    private $receiverLocator;

    /** messenger.receiver_locator {@see ServiceLocator} */
    public function __construct(ContainerInterface $receiverLocator)
    {
        $this->receiverLocator = $receiverLocator;
        parent::__construct('messenger:read');
    }

    public function configure(): void
    {
        $this->setDescription('Read out a message from the Messenger')
            ->addArgument('receiver_name', InputArgument::REQUIRED, 'Which receiver/transport will be looked into?')
            ->addArgument('message_id', InputArgument::REQUIRED, 'What is the id of the message to be looked into?')
            ->addOption('pretty', 'p', InputOption::VALUE_NONE, 'Turns the JSON from copyable to readable');
    }

    /**
     * @throws RejectedArgumentException <br> > When sending a command argument that isn't the expected type
     * @throws unknownReceiverException <br> > When selecting a receiver not usable for finding messages
     * @throws jsonException <br> > When failing to decode or re-encode the serialized envelope content
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $receiverName = $input->getArgument('receiver_name');
        if (!is_string($receiverName)) {
            throw new RejectedArgumentException("Expected 'receiver_name' to become string, instead got " . StringHelper::getDebugType($receiverName));
        }

        $messageId = $input->getArgument('message_id');
        if (!is_string($messageId)) {
            throw new RejectedArgumentException("Expected 'message_id' to become string, instead got " . StringHelper::getDebugType($messageId));
        }

        $doPrettyPrint = $input->getOption('pretty');
        if (!is_bool($doPrettyPrint)) {
            throw new RejectedArgumentException("Expected 'pretty' to become bool, instead got " . StringHelper::getDebugType($doPrettyPrint));
        }

        $receiver = $this->receiverLocator->get($receiverName);
        if (!($receiver instanceof ListableReceiverInterface)) {
            throw new UnknownReceiverException("'$receiverName' was found to be " . StringHelper::getDebugType($receiver) . ' which is not a ' . StringHelper::shortClassName(ListableReceiverInterface::class));
        }

        // If it can't be pressed into the current classes it was made off, such as by one of the classes now requiring
        // an extra construct variable, then this will not run. The solution for that is to make your own, simpler
        // Messenger reader that directly talks to where it is stored and then ..does something, maybe show the text.

        // When copying a message over from the database/serialize to a PHP string for debugging with manual decoding
        // use a single quote wrap but also replace all `\\` with `\\\\` or use a file.

        $envelope = $receiver->find($messageId);
        if ($envelope === null) {
            $output->writeln("Message for '$messageId' not found in receiver/transport '$receiverName'");
            return Command::FAILURE;
        }
        $output->writeln('-----');
        $output->writeln(StringHelper::shortClassName($envelope->getMessage()));

        $serializer = SerializerBuilder::create()->build();
        $json = $serializer->serialize(
            $envelope->getMessage(),
            'json',
            (new SerializationContext())->setSerializeNull(true)
        );
        $output->writeln(
            $doPrettyPrint
                ? \Safe\json_encode(\Safe\json_decode($json, true, 512, JSON_INVALID_UTF8_SUBSTITUTE), JSON_PRETTY_PRINT)
                : $json
        );
        return Command::SUCCESS;
    }
}
