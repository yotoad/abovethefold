<?php
use PHPUnit\Framework\TestCase;

class RestEndpointTest extends TestCase {
    public function test_links_endpoint_returns_expected_response() {
        // Mock input and expected output
        $request = new WP_REST_Request('POST', '/wp-json/abtf/v1/links');
        $request->set_body_params(['links' => [['href' => 'https://example.com', 'text' => 'Example']]]);
        $response = ROCKET_WP_CRAWLER\your_links_callback_function($request);

        $this->assertInstanceOf('WP_REST_Response', $response);
        $this->assertEquals(200, $response->get_status());
    }
}