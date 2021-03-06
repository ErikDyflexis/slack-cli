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

use CL\Slack\Payload\FilesUploadPayload;
use CL\Slack\Payload\FilesUploadPayloadResponse;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Cas Leentfaar <info@casleentfaar.com>
 */
class FilesUploadCommand extends AbstractApiCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('files:upload');
        $this->setDescription('Create or upload an existing file to Slack');
        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'The path to the file to upload');
        $this->addOption('content', 'c', InputOption::VALUE_REQUIRED, 'The raw content of the file to upload (alternative for `--path`)');
        $this->addOption('filetype', 'ft', InputOption::VALUE_REQUIRED, 'Slack-internal file type identifier (e.g. `php`)');
        $this->addOption('filename', 'fn', InputOption::VALUE_REQUIRED, 'Filename of the file');
        $this->addOption('title', null, InputOption::VALUE_REQUIRED, 'Title of the file');
        $this->addOption('initial-comment', null, InputOption::VALUE_REQUIRED, 'Initial comment to add to the file');
        $this->addOption('channels', null, InputOption::VALUE_REQUIRED, 'Comma-separated list of channel IDs to share the file into');
        $this->setHelp(<<<EOT
The <info>files:upload</info> command allows you to create or upload an existing file.

The type of data in the file will be intuited from the filename and the magic bytes in the file, for supported formats.
Using the `--filetype` option will override this behavior (if a valid type is given).

The file can also be shared directly into channels on upload, by specifying the `--channels` option.
Channel IDs should be comma separated if there is more than one.

For more information about the related API method, check out the official documentation:
<comment>https://api.slack.com/methods/files.upload</comment>
EOT
        );
    }

    /**
     * @return FilesUploadPayload
     */
    protected function createPayload()
    {
        $payload = new FilesUploadPayload();

        if ($this->input->getOption('path')) {
            $content = file_get_contents($this->input->getOption('path'));
        } elseif ($this->input->getOption('content')) {
            $content = $this->input->getOption('content');
        } else {
            throw new \LogicException('Either the `--path` or the `--content` option must be used');
        }

        $payload->setContent($content);
        $payload->setChannels($this->input->getOption('channels'));
        $payload->setFilename($this->input->getOption('filename'));
        $payload->setFileType($this->input->getOption('filetype'));
        $payload->setTitle($this->input->getOption('title'));

        return $payload;
    }

    /**
     * {@inheritdoc}
     *
     * @param FilesUploadPayloadResponse $payloadResponse
     */
    protected function handleResponse($payloadResponse)
    {
        if ($payloadResponse->isOk()) {
            $this->writeOk('Successfully upload file to Slack:');
            $file = $payloadResponse->getFile();
            $this->renderKeyValueTable($file);
        } else {
            $this->writeError(sprintf('Failed to upload file: %s', lcfirst($payloadResponse->getErrorExplanation())));
        }
    }
}
