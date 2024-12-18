<?php

namespace Tests\Unit;

use ReflectionClass;
use App\Services\Mapper;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Log;

class MapperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Log::shouldReceive('error')->andReturnNull();
        Log::shouldReceive('info')->andReturnNull();
    }

    public function test_map_data()
    {
        $source = [
            'name' => 'TestSource',
            'fields' => [
                'doc_id' => 'id',
                'published_at' => 'date',
                'category' => 'category',
                'author' => 'author',
                'title' => 'title',
                'url' => 'url',
                'summary' => 'summary',
            ],
            'articles_key' => 'response.articles'
        ];

        $data = [
            [
                'response' => [
                    'articles' => [
                        [
                            'id' => '1',
                            'date' => '2023-01-01',
                            'category' => 'News',
                            'author' => 'John Doe',
                            'title' => 'Test Article',
                            'url' => 'http://example.com',
                            'summary' => 'This is a test article.'
                        ]
                    ]
                ]
            ]
        ];

        $mapper = new Mapper($source);
        $mappedData = $mapper->mapData($data);

        $this->assertCount(1, $mappedData);
        $this->assertEquals('1', $mappedData[0]['doc_id']);
        $this->assertEquals('2023-01-01', $mappedData[0]['published_at']);
        $this->assertEquals('News', $mappedData[0]['category']);
        $this->assertEquals('John Doe', $mappedData[0]['author']);
        $this->assertEquals('Test Article', $mappedData[0]['title']);
        $this->assertEquals('http://example.com', $mappedData[0]['url']);
        $this->assertEquals('This is a test article.', $mappedData[0]['summary']);
    }

    public function test_get_field_value()
    {
        $mapper = $this->getMockBuilder(Mapper::class)
                       ->disableOriginalConstructor()
                       ->getMock();

        $reflection = new \ReflectionClass($mapper);
        $method = $reflection->getMethod('getFieldValue');
        $method->setAccessible(true);

        $article = [
            'id' => '1',
            'date' => '2023-01-01',
            'category' => 'News',
            'author' => 'John Doe',
            'title' => 'Test Article',
            'url' => 'http://example.com',
            'summary' => 'This is a test article.'
        ];

        $fieldValue = $method->invokeArgs($mapper, [$article, 'author']);
        $this->assertEquals('John Doe', $fieldValue);

        $fieldValue = $method->invokeArgs($mapper, [$article, 'nonexistent']);
        $this->assertNull($fieldValue);
    }

    public function test_map_data_field_not_found()
    {
        $source = [
            'name' => 'TestSource',
            'fields' => [
                'doc_id' => 'id',
                'published_at' => 'date',
                'category' => 'category',
                'author' => 'nonexistent_field',
                'title' => 'title',
                'url' => 'url',
                'summary' => 'summary',
            ],
            'articles_key' => 'response.articles'
        ];

        $data = [
            [
                'response' => [
                    'articles' => [
                        [
                            'id' => '1',
                            'date' => '2023-01-01',
                            'category' => 'News',
                            'author' => 'John Doe',
                            'title' => 'Test Article',
                            'url' => 'http://example.com',
                            'summary' => 'This is a test article.'
                        ]
                    ]
                ]
            ]
        ];

        $mapper = new Mapper($source);
        $mappedData = $mapper->mapData($data);

        $this->assertEmpty($mappedData);
    }

    public function test_get_field_value_null()
    {
        $mapper = $this->getMockBuilder(Mapper::class)
                       ->disableOriginalConstructor()
                       ->getMock();

        $reflection = new ReflectionClass($mapper);
        $method = $reflection->getMethod('getFieldValue');
        $method->setAccessible(true);

        $article = [
            'id' => '1',
            'date' => '2023-01-01',
            'category' => 'News',
            'author' => null,
            'title' => 'Test Article',
            'url' => 'http://example.com',
            'summary' => 'This is a test article.'
        ];

        $fieldValue = $method->invokeArgs($mapper, [$article, 'author']);
        $this->assertEquals('unknown', $fieldValue);
    }

    public function test_map_data_field_null_in_keeper()
    {
        $source = [
            'name' => 'TestSource',
            'fields' => [
                'doc_id' => 'id',
                'published_at' => 'date',
                'category' => 'category',
                'author' => null,
                'title' => 'title',
                'url' => 'url',
                'summary' => 'summary',
            ],
            'articles_key' => 'response.articles'
        ];

        $data = [
            [
                'response' => [
                    'articles' => [
                        [
                            'id' => '1',
                            'date' => '2023-01-01',
                            'category' => 'News',
                            'title' => 'Test Article',
                            'url' => 'http://example.com',
                            'summary' => 'This is a test article.'
                        ]
                    ]
                ]
            ]
        ];

        $mapper = new Mapper($source);
        $mappedData = $mapper->mapData($data);

        $this->assertCount(1, $mappedData);
        $this->assertEquals('1', $mappedData[0]['doc_id']);
        $this->assertEquals('2023-01-01', $mappedData[0]['published_at']);
        $this->assertEquals('News', $mappedData[0]['category']);
        $this->assertEquals('unknown', $mappedData[0]['author']);
        $this->assertEquals('Test Article', $mappedData[0]['title']);
        $this->assertEquals('http://example.com', $mappedData[0]['url']);
        $this->assertEquals('This is a test article.', $mappedData[0]['summary']);
    }

    public function test_map_data_with_nested_keys()
    {
        $source = [
            'name' => 'TestSource',
            'fields' => [
                'doc_id' => 'id',
                'published_at' => 'date',
                'category' => 'category',
                'author' => 'author.name',
                'title' => 'title',
                'url' => 'url',
                'summary' => 'summary',
            ],
            'articles_key' => 'response.articles'
        ];

        $data = [
            [
                'response' => [
                    'articles' => [
                        [
                            'id' => '1',
                            'date' => '2023-01-01',
                            'category' => 'News',
                            'author' => ['name' => 'John Doe'],
                            'title' => 'Test Article',
                            'url' => 'http://example.com',
                            'summary' => 'This is a test article.'
                        ]
                    ]
                ]
            ]
        ];

        $mapper = new Mapper($source);
        $mappedData = $mapper->mapData($data);

        $this->assertCount(1, $mappedData);
        $this->assertEquals('1', $mappedData[0]['doc_id']);
        $this->assertEquals('2023-01-01', $mappedData[0]['published_at']);
        $this->assertEquals('News', $mappedData[0]['category']);
        $this->assertEquals('John Doe', $mappedData[0]['author']);
        $this->assertEquals('Test Article', $mappedData[0]['title']);
        $this->assertEquals('http://example.com', $mappedData[0]['url']);
        $this->assertEquals('This is a test article.', $mappedData[0]['summary']);
    }

    public function test_map_data_with_missing_nested_key()
    {
        $source = [
            'name' => 'TestSource',
            'fields' => [
                'doc_id' => 'id',
                'published_at' => 'date',
                'category' => 'category',
                'author' => 'author.name',
                'title' => 'title',
                'url' => 'url',
                'summary' => 'summary',
            ],
            'articles_key' => 'response.articles'
        ];

        $data = [
            [
                'response' => [
                    'articles' => [
                        [
                            'id' => '1',
                            'date' => '2023-01-01',
                            'category' => 'News',
                            'author' => [],
                            'title' => 'Test Article',
                            'url' => 'http://example.com',
                            'summary' => 'This is a test article.'
                        ]
                    ]
                ]
            ]
        ];

        $mapper = new Mapper($source);
        $mappedData = $mapper->mapData($data);

        $this->assertEmpty($mappedData);
    }
}