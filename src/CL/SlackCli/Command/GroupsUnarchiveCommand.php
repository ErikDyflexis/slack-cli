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

use CL\Slack\Payload\GroupsUnarchivePayload;
use CL\Slack\Payload\GroupsUnarchivePayloadResponse;
use CL\Slack\Payload\PayloadInterface;
use CL\Slack\Payload\PayloadResponseInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cas Leentfaar <info@casleentfaar.com>
 */
class GroupsUnarchiveCommand extends AbstractApiCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('groups:unarchive');
        $this->setDescription('Unarchives a group. The token\'s user is automatically added to the group');
        $this->addArgument('group-id', InputArgument::REQUIRED, 'The ID of the group to archive');
        $this->setHelp(<<<EOT
The <info>groups:unarchive</info> command unarchives a given group.
The user of the token is automatically added to the group.

For more information about the related API method, check out the official documentation:
<comment>https://api.slack.com/methods/groups.unarchive</comment>
EOT
        );
    }

    /**
     * @param InputInterface $input
     *
     * @return GroupsUnarchivePayload
     */
    protected function createPayload(InputInterface $input)
    {
        $payload = new GroupsUnarchivePayload();
        $payload->setGroupId($input->getArgument('group-id'));
        
        return $payload;
    }

    /**
     * {@inheritdoc}
     *
     * @param GroupsUnarchivePayloadResponse $payloadResponse
     * @param InputInterface                 $input
     * @param OutputInterface                $output
     */
    protected function handleResponse(PayloadResponseInterface $payloadResponse, InputInterface $input, OutputInterface $output)
    {
        if ($payloadResponse->isOk()) {
            $this->writeOk($output, 'Successfully un-archived group!');
        } else {
            $this->writeError($output, sprintf('Failed to un-archive group: %s', lcfirst($payloadResponse->getErrorExplanation())));
        }
    }
}
