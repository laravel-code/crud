<?php

use Illuminate\Support\Facades\File;

// When testing inside of a Laravel installation, the base class would be Tests\TestCase
class CrudGeneratorTest extends Orchestra\Testbench\TestCase
{
    /** @test */
    public function testCrudGenerate()
    {
        $json = <<<JSON
{
  "routes": {
    "blog": {
        "type": "resource",
        "controller": "Api\\\UsersController",
        "path": "users",
        "options": {
            "only": ["index", "show", "store", "update"],
            "middleware": ["throttle:60"]
        }
    },
    "recipes": {
        "type": "resource",
        "controller": "Api\\\RecipesController",
        "path": "recipes",
        "actions": [
            {
                "method": "post",
                "action": "downloadAll",
                "path": "/recipes/download-all",
                "middleware": ["api:auth"]
            }
        ]
    },
    "dashboard": {
            "namespace": "Api\\\Dashboard",
            "prefix": "dashboard",
            "type": "middleware",
            "middleware": [
                "api:auth"
            ],
            "routes": {
                "accounts": {
                    "type": "resource"
                }
            }
        }
  }
}
JSON;
        File::shouldReceive('exists')->once()->with(base_path('.crud-specs.json'))->andReturn(true);
        File::shouldReceive('get')->once()->with(base_path('.crud-specs.json'))->andReturn($json);
        File::partialMock();

        $path = (realpath(__DIR__.'/../../test-data')).'/api.php';

        $this->artisan('crud:routes', [
            '--output' => $path,
            '--always' => true,
        ]);

        $this->assertFileExists($path);
        File::delete($path);
    }

    /** @test */
    public function testUnknownConfig()
    {
        $this->expectException(Exception::class);
        $this->artisan('crud:routes', [
            '--config' => 'something.json',
        ]);
    }

    /** @test */
    public function testJsonParseError()
    {
        $json = <<<JSON
{
  "routes": {
    "blog": {
        "type": "resource",
        "controller": "Api\\\UsersController",
        "path": "users",
        "options": {
            "only": ["index", "show", "store", "update"],
            "middleware": ["throttle:60"]
        }
    },
  }
}
JSON;
        $this->expectException(Exception::class);

        File::shouldReceive('exists')->once()->with(base_path('.crud-specs.json'))->andReturn(true);
        File::shouldReceive('get')->once()->with(base_path('.crud-specs.json'))->andReturn($json);
        File::partialMock();

        $path = (realpath(__DIR__.'/../../test-data')).'/api.php';

        $this->artisan('crud:routes', [
            '--output' => $path,
            '--always' => true,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            'LaravelCode\Crud\ServiceProvider',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $crudConfig = (include realpath(__DIR__.'/../../TestApp/config.php'));
        $app['config']->set('crud', $crudConfig);
    }
}
