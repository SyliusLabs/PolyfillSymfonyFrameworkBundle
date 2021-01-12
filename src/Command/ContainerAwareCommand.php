<?php

declare(strict_types=1);

namespace SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as SymfonyContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

if (\class_exists(SymfonyContainerAwareCommand::class)) {
    abstract class ContainerAwareCommand extends SymfonyContainerAwareCommand
    {

    }
} else {
    /**
     * @link https://github.com/symfony/symfony/blob/v4.4.18/src/Symfony/Bundle/FrameworkBundle/Command/ContainerAwareCommand.php
     */
    abstract class ContainerAwareCommand extends Command implements ContainerAwareInterface
    {
        /**
         * @var ContainerInterface|null
         */
        private $container;

        /**
         * @return ContainerInterface
         *
         * @throws \LogicException
         */
        protected function getContainer()
        {
            if (null === $this->container) {
                $application = $this->getApplication();
                if (null === $application) {
                    throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
                }

                $this->container = $application->getKernel()->getContainer();
            }

            return $this->container;
        }

        /**
         * {@inheritdoc}
         */
        public function setContainer(ContainerInterface $container = null)
        {
            $this->container = $container;
        }
    }
}
