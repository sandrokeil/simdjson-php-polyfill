<?php

/**
 * @see       https://github.com/sandrokeil/simdjson-php-polyfill for the canonical source repository
 * @copyright https://github.com/sandrokeil/simdjson-php-polyfill/blob/master/COPYRIGHT.md
 * @license   https://github.com/sandrokeil/simdjson-php-polyfill/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace SimdjsonTest\Polyfill;

use PHPUnit\Framework\TestCase;
use Simdjson\Polyfill;

final class PolyfillTest extends TestCase
{

    /**
     * @test
     */
    public function it_decodes_json(): void
    {
        $json = '{"id": 1}';

        $object = Polyfill::simdjsonDecode($json);
        $this->assertIsObject($object);
        $this->assertSame(1, $object->id);

        $array = Polyfill::simdjsonDecode($json, true);
        $this->assertIsArray($array);
        $this->assertSame(1, $array['id']);
    }

    public function providerJsonIsValid(): array
    {
        return [
            [
                'json' => '{}',
                'expected' => true,
            ],
            [
                'json' => '[]',
                'expected' => true,
            ],
            [
                'json' => '[{"id": 1}]',
                'expected' => true,
            ],
            [
                'json' => '{"id": 1}',
                'expected' => true,
            ],
            [
                'json' => '[["id": 1]',
                'expected' => false,
            ],
            [
                'json' => '{"id": 1',
                'expected' => false,
            ],
            [
                'json' => "Invalid String",
                'expected' => false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerJsonIsValid
     */
    public function it_checks_if_json_is_valid(string $json, $expected): void
    {
        $this->assertSame($expected, Polyfill::simdjsonIsValid($json));
    }

    public function providerKeyValueArray(): array
    {
        $data = [
            'result' => [
                [
                    'id' => 1,
                    'value' => 'test',
                ],
                [
                    'id' => 2,
                    'value' => 'other',
                ],
            ],
            'count' => 2,
        ];

        $json = json_encode($data);

        return [
            [
                'json' => $json,
                'key' => 'result',
                'expected' => $data['result'],
            ],
            [
                'json' => $json,
                'key' => 'result/1',
                'expected' => $data['result'][1],
            ],
            [
                'json' => $json,
                'key' => 'result/0/id',
                'expected' => $data['result'][0]['id'],
            ],
            [
                'json' => $json,
                'key' => 'count',
                'expected' => $data['count'],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerKeyValueArray
     */
    public function it_returns_key_value_array(string $json, string $key, $expected): void
    {
        $this->assertSame($expected, Polyfill::simdjsonKeyValue($json, $key, true));
    }

    public function providerKeyValueObject(): array
    {
        $data = [
            'result' => [
                [
                    'id' => 1,
                    'value' => 'test',
                ],
                [
                    'id' => 2,
                    'value' => 'other',
                ],
            ],
            'count' => 2,
        ];

        $json = json_encode($data);

        return [
            [
                'json' => $json,
                'key' => 'result',
                'expected' => json_decode(json_encode($data['result'])),
            ],
            [
                'json' => $json,
                'key' => 'result/1',
                'expected' => json_decode(json_encode($data['result'][1])),
            ],
            [
                'json' => $json,
                'key' => 'result/0/id',
                'expected' => $data['result'][0]['id'],
            ],
        ];
    }

    /**
     * @test
     */
    public function it_returns_key_value_object(): void
    {
        $data = [
            'result' => [
                [
                    'id' => 1,
                    'value' => 'test',
                ],
                [
                    'id' => 2,
                    'value' => 'other',
                ],
            ],
            'count' => 2,
        ];

        $json = json_encode($data);

        $data = Polyfill::simdjsonKeyValue($json, 'result', false);
        $this->assertCount(2, $data);
        $this->assertSame(1, $data[0]->id);
        $this->assertSame(2, $data[1]->id);

        $data = Polyfill::simdjsonKeyValue($json, 'result/1', false);
        $this->assertIsObject($data);
        $this->assertSame('other', $data->value);

        $data = Polyfill::simdjsonKeyValue($json, 'result/0/id', false);
        $this->assertSame(1, $data);

        $data = Polyfill::simdjsonKeyValue($json, 'count', false);
        $this->assertSame(2, $data);
    }

    /**
     * @test
     */
    public function it_counts_keys(): void
    {
        $data = [
            'result' => [
                [
                    'id' => 1,
                    'value' => 'test',
                ],
                [
                    'id' => 2,
                    'value' => 'other',
                ],
            ],
            'count' => 2,
        ];

        $json = json_encode($data);

        $count = Polyfill::simdjsonKeyCount($json, 'result');
        $this->assertSame(2, $count);

        $count = Polyfill::simdjsonKeyCount($json, 'result/0');
        $this->assertSame(2, $count);
    }

    public function providerKeyExists(): array
    {
        $data = [
            'result' => [
                [
                    'id' => 1,
                    'value' => 'test',
                ],
                [
                    'id' => 2,
                    'value' => 'other',
                ],
            ],
            'count' => 2,
        ];

        $json = json_encode($data);

        return [
            [
                'json' => $json,
                'key' => 'result',
                'expected' => true,
            ],
            [
                'json' => $json,
                'key' => 'result/1',
                'expected' => true,
            ],
            [
                'json' => $json,
                'key' => 'result/0/id',
                'expected' => true,
            ],
            [
                'json' => $json,
                'key' => 'count',
                'expected' => true,
            ],
            [
                'json' => $json,
                'key' => 'result/3',
                'expected' => false,
            ],
            [
                'json' => $json,
                'key' => 'unknown',
                'expected' => false,
            ],
            [
                'json' => $json,
                'key' => 'result/0/unknown',
                'expected' => false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider providerKeyExists
     */
    public function it_checks_if_key_exists(string $json, string $key, $expected): void
    {
        $this->assertSame($expected, Polyfill::simdjsonKeyExists($json, $key));
    }
}
