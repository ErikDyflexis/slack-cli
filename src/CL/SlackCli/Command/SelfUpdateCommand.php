<?php

namespace CL\SlackCli\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Herrera\Version\Parser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends AbstractCommand
{
    const MANIFEST_FILE = 'http://cleentfaar.github.io/slack-cli/manifest.json';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('self:update');
        $this->setDescription('Updates slack.phar to the latest version');
        $this->setAliases(['self.update']);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentVersion  = $this->getApplication()->getVersion();
        $manager         = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $lockMajor       = false;
        $allowPreRelease = false;

        if (substr($currentVersion, 0, 1) == 0) {
            $allowPreRelease = true;
        }

        if ($manager->update($currentVersion, $lockMajor, $allowPreRelease)) {
            $newVersion = $this->getNewVersion($currentVersion, $manager, $lockMajor, $allowPreRelease);

            $this->output->writeln(sprintf(
                '<info>Updated Slack CLI from <fg=yellow>%s</fg=yellow> to <fg=yellow>%s</fg=yellow></info>',
                $currentVersion,
                $newVersion
            ));
        } else {
            $this->output->writeln(sprintf(
                '<comment>You are already using the latest version (%s)</comment>',
                $currentVersion
            ));
        }
    }

    /**
     * @param string  $currentVersion
     * @param Manager $manager
     * @param bool    $lockMajor
     * @param bool    $allowPreRelease
     *
     * @return string
     */
    private function getNewVersion($currentVersion, Manager $manager, $lockMajor = false, $allowPreRelease = false)
    {
        $newVersionObject = $manager->getManifest()->findRecent(
            Parser::toVersion($currentVersion),
            $lockMajor,
            $allowPreRelease
        )->getVersion();

        $newVersion = sprintf(
            '%s.%s',
            $newVersionObject->getMajor(),
            implode('.', array_filter([
                $newVersionObject->getMinor(),
                $newVersionObject->getPatch(),
            ]))
        );

        return $newVersion;
    }
}
