<?php

namespace FutureGadgetLab\PhoneWave\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Config;
use FutureGadgetLab\PhoneWave\DMailMiddleware;
use FutureGadgetLab\PhoneWave\PhoneWaveServiceProvider;

class ExampleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [PhoneWaveServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function middleware_is_registered()
    {
        $kernel = App::make(Kernel::class);

        $this->assertTrue($kernel->hasMiddleware(DMailMiddleware::class));
    }

    /** @test */
    public function response_headers_are_empty_if_no_dmail_is_setup()
    {
        Config::set('phone-wave.d-mail', null);

        $request = new Request();
        $middleware = new DMailMiddleware();

        /** @var Response $response */
        $response = $middleware->handle($request, fn ($request) => response('El Psy Kongroo'));

        $this->assertEquals('El Psy Kongroo', $response->content());
        $this->assertArrayNotHasKey('D-Mail-Content', $response->headers->all());
    }
    
    /** @test */
    public function response_header_is_correctly_set_according_to_config()
    {
        Config::set('phone-wave.d-mail', 'Makise was stabbed');

        $request = new Request();
        $middleware = new DMailMiddleware();

        /** @var Response $response */
        $response = $middleware->handle($request, fn ($request) => response('El Psy Kongroo'));

        $this->assertEquals('El Psy Kongroo', $response->content());
        $this->assertEquals('Makise was stabbed', $response->headers->get('D-Mail-Content'));
    }
    
    /** @test */
    public function config_can_have_multiple_dmails()
    {
        Config::set('phone-wave.d-mail', $dmails = [
            'Something must be wrong for you to use my actual name.',
            "People's feelings are memories that transcend time."
        ]);

        $request = new Request();
        $middleware = new DMailMiddleware();

        /** @var Response $response */
        $response = $middleware->handle($request, fn ($request) => response('El Psy Kongroo'));

        $this->assertEquals('El Psy Kongroo', $response->content());
        $this->assertTrue(
            in_array($mail = $response->headers->get('D-Mail-Content'), $dmails),
            "'$mail' was not in the D-Mail-Content header."
        );
    }
}
