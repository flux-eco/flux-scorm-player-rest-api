<?php

namespace FluxScormPlayerRestApi\Adapter\Server;

use FluxRestApi\Adapter\Api\RestApi;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;
use FluxScormPlayerRestApi\Adapter\Api\ScormPlayerRestApi;

class ScormPlayerRestApiServer
{

    private function __construct(
        private readonly RestApi $rest_api,
        private readonly RouteCollector $route_collector,
        private readonly SwooleServerConfigDto $swoole_server_config
    ) {

    }


    public static function new(
        ?ScormPlayerRestApiServerConfigDto $scorm_player_rest_api_server_config = null
    ) : static {
        $scorm_player_rest_api_server_config ??= ScormPlayerRestApiServerConfigDto::newFromEnv();

        return new static(
            RestApi::new(),
            ScormPlayerRestApiServerRouteCollector::new(
                ScormPlayerRestApi::new(
                    $scorm_player_rest_api_server_config->scorm_player_rest_api_config
                )
            ),
            SwooleServerConfigDto::new(
                $scorm_player_rest_api_server_config->https_cert,
                $scorm_player_rest_api_server_config->https_key,
                $scorm_player_rest_api_server_config->listen,
                $scorm_player_rest_api_server_config->port,
                $scorm_player_rest_api_server_config->max_upload_size
            )
        );
    }


    public function init() : void
    {
        $this->rest_api->initSwooleServer(
            $this->route_collector,
            null,
            $this->swoole_server_config
        );
    }
}
