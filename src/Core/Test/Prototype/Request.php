<?php
/**
 * @version 0.1.0
 */

namespace Core\Test\Prototype;

use Core\Prototype\Request as Subject;

/**
 * Test the Request object.
 */
class Request extends \Core\Test\Base
{
	/**
	 * Test its ability to decipher different variations of URI's
	 */
	public function decipherURI()
	{

		Subject::config(['available_formats' => ['format' => []]]);
		$_SERVER['REQUEST_METHOD'] = 'PUT';

		////
		$this->message('Testing Request with basic URI string');

		$mock_uri = '/one/two/three.format';
		$request = new Subject(['path' => $mock_uri]);

		assert($request->getFormat() === 'format');
		assert($request->getComponents() === ['one', 'two', 'three']);
		assert($request->getPath() === $mock_uri);

		////
		$this->message('Testing Request with multi-parameter route');

		$request = new Subject(['path' => 'one/two/three.format']);

		assert($request->getFormat() === 'format');
		assert($request->getComponents() === ['one', 'two', 'three']);
		assert($request->getPath() === $mock_uri);

		////
		$this->message('Testing server request method');

		$request = new Subject(['path' => 'one/two/three']);

		assert($request->getMethod() === 'PUT');

		////
		$this->message('Testing request host.');
		assert($request->getHost() === 'localhost');

		////
		$this->message('Testing custom request host.');
		$request = new Subject(['path' => 'one/two/three', 'host' => 'test.com']);
		assert($request->getHost() === 'test.com');

		////
		$this->message('Testing custom request payload.');
		$request = new Subject(['path' => 'one/two/three', 'payload' => ['test' => 'array']]);
		assert($request->getPayload() === ['test' => 'array']);

		////
		$this->message('Testing request injection');

		$this->message('Testing faux requests to httpbin.org');

		# This is the httpbin.org URI we are testing
		Subject::config(['rbhp_injection_token' => 'post']);

		$request = new Subject([
				'path' => 'one/two/three'
			,	'host' => 'httpbin.org'
			,	'payload' => 'test string'
			,	'format' => 'json'
			,	'method' => 'get'
		]);

		$response = $request->injectTo();
		$response = json_decode($response, true);
		$sent_request = unserialize($response['data']);
		assert($sent_request->getPath() === '/one/two/three');
		assert($sent_request->getPayload() === 'test string');
		assert($sent_request->getMethod() === 'get');
		assert($sent_request->getFormat() === 'json');
	}
}
