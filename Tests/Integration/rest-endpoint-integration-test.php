<?php
class RestEndpointIntegrationTest extends WP_UnitTestCase {
    public function test_links_endpoint_registration() {
        $routes = rest_get_server()->get_routes();
        $this->assertArrayHasKey('/abtf/v1/links', $routes);
    }
}