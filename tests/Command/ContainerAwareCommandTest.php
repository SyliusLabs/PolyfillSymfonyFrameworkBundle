<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class ContainerAwareCommandTest extends TestCase
{
    /** @test */
    public function it_makes_symfony_application_inject_container_to_the_command(): void
    {
        $kernel = new class ('test', true) extends Kernel {
            public function registerBundles(): iterable
            {
                return [new FrameworkBundle()];
            }

            public function registerContainerConfiguration(LoaderInterface $loader): void
            {
                $loader->load(static function (ContainerBuilder $container) use ($loader) {
                    $container->setParameter('foo', 'bar');
                });
            }

            public function getCacheDir(): string
            {
                return sys_get_temp_dir() . '/PolyfillSymfonyFrameworkBundle/cache/' . $this->getEnvironment();
            }

            public function getLogDir(): string
            {
                return sys_get_temp_dir() . '/PolyfillSymfonyFrameworkBundle/logs';
            }
        };

        $application = new Application($kernel);

        $application->add(new class extends ContainerAwareCommand {
            public function __construct()
            {
                parent::__construct('test');
            }

            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                Assert::assertSame('bar', $this->getContainer()->getParameter('foo'));

                return 0;
            }
        });

        Assert::assertSame(0, $application->doRun(new ArrayInput(['test']), new NullOutput()));
    }
}
