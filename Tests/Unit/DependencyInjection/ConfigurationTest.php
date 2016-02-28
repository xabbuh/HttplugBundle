<?php

namespace Http\HttplugBundle\Tests\Unit\DependencyInjection;

use Http\HttplugBundle\DependencyInjection\Configuration;
use Http\HttplugBundle\DependencyInjection\HttplugExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;

/**
 * @author David Buchmann <mail@davidbu.ch>
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension()
    {
        return new HttplugExtension();
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testEmptyConfiguration()
    {
        $expectedConfiguration = [
            'main_alias' => [
                'client' => 'httplug.client.default',
                'message_factory' => 'httplug.message_factory.default',
                'uri_factory' => 'httplug.uri_factory.default',
                'stream_factory' => 'httplug.stream_factory.default',
            ],
            'classes' => [
                'client' => null,
                'message_factory' => null,
                'uri_factory' => null,
                'stream_factory' => null,
            ],
            'clients' => [],
            'toolbar' => [
                'enabled' => 'auto',
                'formatter' => null,
            ],
            'plugins' => [
                'authentication' => [],
                'cache' => [
                    'enabled' => false,
                    'stream_factory' => 'httplug.stream_factory',
                    'config' => [
                        'default_ttl' => null,
                        'respect_cache_headers' => true,
                    ],
                ],
                'cookie' => [
                    'enabled' => false,
                ],
                'decoder' => [
                    'enabled' => true,
                    'use_content_encoding' => true,
                ],
                'history' => [
                    'enabled' => false,
                ],
                'logger' => [
                    'enabled' => true,
                    'logger' => 'logger',
                    'formatter' => null,
                ],
                'redirect' => [
                    'enabled' => true,
                    'preserve_header' => true,
                    'use_default_for_multiple' => true,
                ],
                'retry' => [
                    'enabled' => true,
                    'retry' => 1,
                ],
                'stopwatch' => [
                    'enabled' => true,
                    'stopwatch' => 'debug.stopwatch',
                ],
            ],
        ];

        $formats = array_map(function ($path) {
            return __DIR__.'/../../Resources/Fixtures/'.$path;
        }, [
            'config/empty.yml',
            'config/empty.xml',
            'config/empty.php',
        ]);

        foreach ($formats as $format) {
            $this->assertProcessedConfigurationEquals($expectedConfiguration, [$format]);
        }
    }

    public function testSupportsAllConfigFormats()
    {
        $expectedConfiguration = [
            'main_alias' => [
                'client' => 'my_client',
                'message_factory' => 'my_message_factory',
                'uri_factory' => 'my_uri_factory',
                'stream_factory' => 'my_stream_factory',
            ],
            'classes' => [
                'client' => 'Http\Adapter\Guzzle6\Client',
                'message_factory' => 'Http\Message\MessageFactory\GuzzleMessageFactory',
                'uri_factory' => 'Http\Message\UriFactory\GuzzleUriFactory',
                'stream_factory' => 'Http\Message\StreamFactory\GuzzleStreamFactory',
            ],
            'clients' => [],
            'toolbar' => [
                'enabled' => true,
                'formatter' => 'my_toolbar_formatter',
            ],
            'plugins' => [
                'authentication' => [
                    'my_basic' => [
                        'type' => 'basic',
                        'username' => 'foo',
                        'password' => 'bar',
                    ],
                    'my_wsse' => [
                        'type' => 'wsse',
                        'username' => 'foo',
                        'password' => 'bar',
                    ],
                    'my_brearer' => [
                        'type' => 'bearer',
                        'token' => 'foo',
                    ],
                    'my_service' => [
                        'type' => 'service',
                        'service' => 'my_auth_serivce',
                    ],
                ],
                'cache' => [
                    'enabled' => true,
                    'cache_pool' => 'my_cache_pool',
                    'stream_factory' => 'my_other_stream_factory',
                    'config' => [
                        'default_ttl' => 42,
                        'respect_cache_headers' => false,
                    ],
                ],
                'cookie' => [
                    'enabled' => true,
                    'cookie_jar' => 'my_cookie_jar',
                ],
                'decoder' => [
                    'enabled' => false,
                    'use_content_encoding' => true,
                ],
                'history' => [
                    'enabled' => true,
                    'journal' => 'my_journal',
                ],
                'logger' => [
                    'enabled' => false,
                    'logger' => 'logger',
                    'formatter' => null,
                ],
                'redirect' => [
                    'enabled' => false,
                    'preserve_header' => true,
                    'use_default_for_multiple' => true,
                ],
                'retry' => [
                    'enabled' => false,
                    'retry' => 1,
                ],
                'stopwatch' => [
                    'enabled' => false,
                    'stopwatch' => 'debug.stopwatch',
                ],
            ],
        ];

        $formats = array_map(function ($path) {
            return __DIR__.'/../../Resources/Fixtures/'.$path;
        }, [
            'config/full.yml',
            'config/full.xml',
            'config/full.php',
        ]);

        foreach ($formats as $format) {
            $this->assertProcessedConfigurationEquals($expectedConfiguration, [$format]);
        }
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Nonexisting\Class
     */
    public function testMissingClass()
    {
        $file = __DIR__.'/../../Resources/Fixtures/config/invalid_class.yml';
        $this->assertProcessedConfigurationEquals([], [$file]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage password, service, username
     */
    public function testInvalidAuthentication()
    {
        $file = __DIR__.'/../../Resources/Fixtures/config/invalid_auth.yml';
        $this->assertProcessedConfigurationEquals([], [$file]);
    }
}
