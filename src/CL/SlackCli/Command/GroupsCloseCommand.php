<?php

/*
 * This file is part of the slack-cli package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\SlackCli\Command;

use CL\Slack\Payload\GroupsClosePayload;
use CL\Slack\Payload\GroupsClosePayloadResponse;
use CL\Slack\Payload\PayloadInterface;
use CL\Slack\Payload\PayloadResponseInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cas Leentfaar <info@casleentfaar.com>
 */
class GroupsCloseCommand extends AbstractApiCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('groups:close');
        $this->setDescription('Closes a given Slack group');
        $this->addArgument('group-id', InputArgument::REQUIRED, 'The ID of a private group to close');
        $this->setHelp(<<<EOT
The <info>groups:close</info> command let's you close a given Slack group.

For more information about the related API method, check out the official documentation:
<comment>https://api.slack.com/methods/groups.close</comment>
EOT
        );
    }

    /**
     * @param InputInterface $input
     *
     * @return GroupsClosePayload
     */
    protected function createPayload(InputInterface $input)
    {
        $payload = new GroupsClosePayload();
        $payload->setGroupId($input->getArgument('group-id'));
        
        return $payload;
    }

    /**
     * {@inheritdoc}
     *
     * @param GroupsClosePayloadResponse $payloadResponse
     */
    protected function handleResponse(PayloadResponseInterface $payloadResponse, InputInterface $input, OutputInterface $output)
    {
        if ($payloadResponse->isOk()) {
            if ($payloadResponse->isAlreadyClosed()) {
                $output->writeln('<comment>Couldn\'t close group: the group has already been closed</comment>');
            } else {
                $this->writeOk($output, 'Successfully closed group!');
            }
        } else {
            $this->writeError($output, sprintf('Failed to close group: %s', $payloadResponse->getErrorExplanation()));
        }
    }
}
