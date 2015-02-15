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

use CL\Slack\Payload\GroupsSetPurposePayload;
use CL\Slack\Payload\GroupsSetPurposePayloadResponse;
use CL\Slack\Payload\PayloadResponseInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cas Leentfaar <info@casleentfaar.com>
 */
class GroupsSetPurposeCommand extends AbstractApiCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('groups:set-purpose');
        $this->setDescription('Change the purpose of a group. The calling user must be a member of the group.');
        $this->addArgument('group-id', InputArgument::REQUIRED, 'The ID of the group to change the purpose of');
        $this->addArgument('purpose', InputArgument::REQUIRED, 'The new purpose');
        $this->setHelp(<<<EOT
The <info>groups:set-purpose</info> command changes the purpose of a group.
The calling user must be a member of the group.

For more information about the related API method, check out the official documentation:
<comment>https://api.slack.com/methods/groups.setPurpose</comment>
EOT
        );
    }

    /**
     * @param InputInterface $input
     *
     * @return GroupsSetPurposePayload
     */
    protected function createPayload(InputInterface $input)
    {
        $payload = new GroupsSetPurposePayload();
        $payload->setGroupId($input->getArgument('group-id'));
        $payload->setPurpose($input->getArgument('purpose'));

        return $payload;
    }

    /**
     * {@inheritdoc}
     *
     * @param GroupsSetPurposePayloadResponse $payloadResponse
     * @param InputInterface                  $input
     * @param OutputInterface                 $output
     */
    protected function handleResponse(PayloadResponseInterface $payloadResponse, InputInterface $input, OutputInterface $output)
    {
        if ($payloadResponse->isOk()) {
            $this->writeOk($output, sprintf('Successfully changed purpose of group to: "%s"', $payloadResponse->getPurpose()));
        } else {
            $this->writeError($output, sprintf('Failed to change purpose of group: %s', lcfirst($payloadResponse->getErrorExplanation())));
        }
    }
}
