<?php
    // $Id: http_test.php,v 1.79 2005/01/02 22:46:10 lastcraft Exp $
    
    require_once(dirname(__FILE__) . '/../encoding.php');
    require_once(dirname(__FILE__) . '/../http.php');
    require_once(dirname(__FILE__) . '/../socket.php');
    Mock::generate('SimpleSocket');
    Mock::generate('SimpleRoute');
    Mock::generatePartial('SimpleRoute', 'PartialSimpleRoute', array('_createSocket'));
    Mock::generatePartial(
            'SimpleProxyRoute',
            'PartialSimpleProxyRoute',
            array('_createSocket'));

    class TestOfCookie extends UnitTestCase {
        
        function testCookieDefaults() {
            $cookie = new SimpleCookie("name");
            $this->assertFalse($cookie->getValue());
            $this->assertEqual($cookie->getPath(), "/");
            $this->assertIdentical($cookie->getHost(), false);
            $this->assertFalse($cookie->getExpiry());
            $this->assertFalse($cookie->isSecure());
        }
        
        function testCookieAccessors() {
            $cookie = new SimpleCookie(
                    "name",
                    "value",
                    "/path",
                    "Mon, 18 Nov 2002 15:50:29 GMT",
                    true);
            $this->assertEqual($cookie->getName(), "name");
            $this->assertEqual($cookie->getValue(), "value");
            $this->assertEqual($cookie->getPath(), "/path/");
            $this->assertEqual($cookie->getExpiry(), "Mon, 18 Nov 2002 15:50:29 GMT");
            $this->assertTrue($cookie->isSecure());
        }
        
        function testFullHostname() {
            $cookie = new SimpleCookie("name");
            $this->assertTrue($cookie->setHost("host.name.here"));
            $this->assertEqual($cookie->getHost(), "host.name.here");
            $this->assertTrue($cookie->setHost("host.com"));
            $this->assertEqual($cookie->getHost(), "host.com");
        }
        
        function testHostTruncation() {
            $cookie = new SimpleCookie("name");
            $cookie->setHost("this.host.name.here");
            $this->assertEqual($cookie->getHost(), "host.name.here");
            $cookie->setHost("this.host.com");
            $this->assertEqual($cookie->getHost(), "host.com");
            $this->assertTrue($cookie->setHost("dashes.in-host.com"));
            $this->assertEqual($cookie->getHost(), "in-host.com");
        }
        
        function testBadHosts() {
            $cookie = new SimpleCookie("name");
            $this->assertFalse($cookie->setHost("gibberish"));
            $this->assertFalse($cookie->setHost("host.here"));
            $this->assertFalse($cookie->setHost("host..com"));
            $this->assertFalse($cookie->setHost("..."));
            $this->assertFalse($cookie->setHost("host.com."));
        }
        
        function testHostValidity() {
            $cookie = new SimpleCookie("name");
            $cookie->setHost("this.host.name.here");
            $this->assertTrue($cookie->isValidHost("host.name.here"));
            $this->assertTrue($cookie->isValidHost("that.host.name.here"));
            $this->assertFalse($cookie->isValidHost("bad.host"));
            $this->assertFalse($cookie->isValidHost("nearly.name.here"));
        }
        
        function testPathValidity() {
            $cookie = new SimpleCookie("name", "value", "/path");
            $this->assertFalse($cookie->isValidPath("/"));
            $this->assertTrue($cookie->isValidPath("/path/"));
            $this->assertTrue($cookie->isValidPath("/path/more"));
        }
        
        function testSessionExpiring() {
            $cookie = new SimpleCookie("name", "value", "/path");
            $this->assertTrue($cookie->isExpired(0));
        }
        
        function testTimestampExpiry() {
            $cookie = new SimpleCookie("name", "value", "/path", 456);
            $this->assertFalse($cookie->isExpired(0));
            $this->assertTrue($cookie->isExpired(457));
            $this->assertFalse($cookie->isExpired(455));
        }
        
        function testDateExpiry() {
            $cookie = new SimpleCookie(
                    "name",
                    "value",
                    "/path",
                    "Mon, 18 Nov 2002 15:50:29 GMT");
            $this->assertTrue($cookie->isExpired("Mon, 18 Nov 2002 15:50:30 GMT"));
            $this->assertFalse($cookie->isExpired("Mon, 18 Nov 2002 15:50:28 GMT"));
        }
        
        function testAging() {
            $cookie = new SimpleCookie("name", "value", "/path", 200);
            $cookie->agePrematurely(199);
            $this->assertFalse($cookie->isExpired(0));
            $cookie->agePrematurely(2);
            $this->assertTrue($cookie->isExpired(0));
        }
    }
    
    class TestOfDirectRoute extends UnitTestCase {
        
        function testDefaultGetRequest() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("GET /here.html HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: a.valid.host\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleRoute(new SimpleUrl('http://a.valid.host/here.html'));
            
            $this->assertReference($route->createConnection('GET', 15), $socket);
            $socket->tally();
        }
        
        function testDefaultPostRequest() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("POST /here.html HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: a.valid.host\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleRoute(new SimpleUrl('http://a.valid.host/here.html'));
            
            $route->createConnection('POST', 15);
            $socket->tally();
        }
        
        function testGetWithPort() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("GET /here.html HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: a.valid.host:81\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleRoute(new SimpleUrl('http://a.valid.host:81/here.html'));
            
            $route->createConnection('GET', 15);
            $socket->tally();
        }
        
        function testGetWithParameters() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("GET /here.html?a=1&b=2 HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: a.valid.host\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleRoute(new SimpleUrl('http://a.valid.host/here.html?a=1&b=2'));
            
            $route->createConnection('GET', 15);
            $socket->tally();
        }
    }
    
    class TestOfProxyRoute extends UnitTestCase {
        
        function testDefaultGet() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("GET http://a.valid.host/here.html HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: my-proxy:8080\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleProxyRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleProxyRoute(
                    new SimpleUrl('http://a.valid.host/here.html'),
                    new SimpleUrl('http://my-proxy'));
            
            $route->createConnection('GET', 15);
            $socket->tally();
        }
        
        function testDefaultPost() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("POST http://a.valid.host/here.html HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: my-proxy:8080\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleProxyRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleProxyRoute(
                    new SimpleUrl('http://a.valid.host/here.html'),
                    new SimpleUrl('http://my-proxy'));
            
            $route->createConnection('POST', 15);
            $socket->tally();
        }
        
        function testGetWithPort() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("GET http://a.valid.host:81/here.html HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: my-proxy:8081\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleProxyRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleProxyRoute(
                    new SimpleUrl('http://a.valid.host:81/here.html'),
                    new SimpleUrl('http://my-proxy:8081'));
            
            $route->createConnection('GET', 15);
            $socket->tally();
        }
        
        function testGetWithParameters() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("GET http://a.valid.host/here.html?a=1&b=2 HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: my-proxy:8080\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 3);
            
            $route = &new PartialSimpleProxyRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleProxyRoute(
                    new SimpleUrl('http://a.valid.host/here.html?a=1&b=2'),
                    new SimpleUrl('http://my-proxy'));
            
            $route->createConnection('GET', 15);
            $socket->tally();
        }
        
        function testGetWithAuthentication() {
            $encoded = base64_encode('Me:Secret');

            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("GET http://a.valid.host/here.html HTTP/1.0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Host: my-proxy:8080\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("Proxy-Authorization: Basic $encoded\r\n"));
            $socket->expectArgumentsAt(3, 'write', array("Connection: close\r\n"));
            $socket->expectCallCount('write', 4);
            
            $route = &new PartialSimpleProxyRoute($this);
            $route->setReturnReference('_createSocket', $socket);
            $route->SimpleProxyRoute(
                    new SimpleUrl('http://a.valid.host/here.html'),
                    new SimpleUrl('http://my-proxy'),
                    'Me',
                    'Secret');
            
            $route->createConnection('GET', 15);
            $socket->tally();
        }
    }

    class TestOfHttpRequest extends UnitTestCase {
        
        function testReadingBadConnection() {
            $socket = &new MockSimpleSocket($this);
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            
            $request = &new SimpleHttpRequest($route, 'GET');
            
            $reponse = &$request->fetch(15);
            $this->assertTrue($reponse->isError());
        }
        
        function testReadingGoodConnection() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectOnce('write', array("\r\n"));
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            $route->expectArguments('createConnection', array('GET', 15));
            
            $request = &new SimpleHttpRequest($route, 'GET');
            
            $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
            $socket->tally();
            $route->tally();
        }
        
        function testWritingAdditionalHeaders() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("My: stuff\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("\r\n"));
            $socket->expectCallCount('write', 2);
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            
            $request = &new SimpleHttpRequest($route, 'GET');
            $request->addHeaderLine('My: stuff');
            $request->fetch(15);
            
            $socket->tally();
        }
        
        function testCookieWriting() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("Cookie: a=A\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("\r\n"));
            $socket->expectCallCount('write', 2);
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            
            $request = &new SimpleHttpRequest($route, 'GET');
            $request->setCookie(new SimpleCookie('a', 'A'));
            
            $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
            $socket->tally();
        }
        
        function testMultipleCookieWriting() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("Cookie: a=A;b=B\r\n"));
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            
            $request = &new SimpleHttpRequest($route, 'GET');
            $request->setCookie(new SimpleCookie('a', 'A'));
            $request->setCookie(new SimpleCookie('b', 'B'));
            
            $request->fetch(15);
            $socket->tally();
        }
    }
    
    class TestOfHttpPostRequest extends UnitTestCase {
        
        function testReadingBadConnection() {
            $socket = &new MockSimpleSocket($this);
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            
            $request = &new SimpleHttpRequest($route, 'POST', '');
            
            $reponse = &$request->fetch(15);
            $this->assertTrue($reponse->isError());
        }
        
        function testReadingGoodConnection() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("Content-Length: 0\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Content-Type: application/x-www-form-urlencoded\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("\r\n"));
            $socket->expectArgumentsAt(3, 'write', array(""));
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            $route->expectArguments('createConnection', array('POST', 15));
            
            $request = &new SimpleHttpRequest($route, 'POST', new SimpleFormEncoding());
            
            $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
            $socket->tally();
            $route->tally();
        }
        
        function testContentHeadersCalculated() {
            $socket = &new MockSimpleSocket($this);
            $socket->expectArgumentsAt(0, 'write', array("Content-Length: 3\r\n"));
            $socket->expectArgumentsAt(1, 'write', array("Content-Type: application/x-www-form-urlencoded\r\n"));
            $socket->expectArgumentsAt(2, 'write', array("\r\n"));
            $socket->expectArgumentsAt(3, 'write', array("a=A"));
            
            $route = &new MockSimpleRoute($this);
            $route->setReturnReference('createConnection', $socket);
            $route->expectArguments('createConnection', array('POST', 15));
            
            $request = &new SimpleHttpRequest(
                    $route,
                    'POST',
                    new SimpleFormEncoding(array('a' => 'A')));
            
            $this->assertIsA($request->fetch(15), 'SimpleHttpResponse');
            $socket->tally();
            $route->tally();
        }
    }
        
    class TestOfHttpHeaders extends UnitTestCase {
        
        function testParseBasicHeaders() {
            $headers = new SimpleHttpHeaders("HTTP/1.1 200 OK\r\n" .
                    "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n" .
                    "Content-Type: text/plain\r\n" .
                    "Server: Apache/1.3.24 (Win32) PHP/4.2.3\r\n" .
                    "Connection: close");
            $this->assertIdentical($headers->getHttpVersion(), "1.1");
            $this->assertIdentical($headers->getResponseCode(), 200);
            $this->assertEqual($headers->getMimeType(), "text/plain");
        }
        
        function testParseOfCookies() {
            $headers = new SimpleHttpHeaders("HTTP/1.1 200 OK\r\n" .
                    "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n" .
                    "Content-Type: text/plain\r\n" .
                    "Server: Apache/1.3.24 (Win32) PHP/4.2.3\r\n" .
                    "Set-Cookie: a=aaa; expires=Wed, 25-Dec-02 04:24:20 GMT; path=/here/\r\n" .
                    "Set-Cookie: b=bbb\r\n" .
                    "Connection: close");
            $cookies = $headers->getNewCookies();
            $this->assertEqual(count($cookies), 2);
            $this->assertEqual($cookies[0]->getName(), "a");
            $this->assertEqual($cookies[0]->getValue(), "aaa");
            $this->assertEqual($cookies[0]->getPath(), "/here/");
            $this->assertEqual($cookies[0]->getExpiry(), "Wed, 25 Dec 2002 04:24:20 GMT");
            $this->assertEqual($cookies[1]->getName(), "b");
            $this->assertEqual($cookies[1]->getValue(), "bbb");
            $this->assertEqual($cookies[1]->getPath(), "/");
            $this->assertEqual($cookies[1]->getExpiry(), "");
        }
        
        function testRedirect() {
            $headers = new SimpleHttpHeaders("HTTP/1.1 301 OK\r\n" .
                    "Content-Type: text/plain\r\n" .
                    "Content-Length: 0\r\n" .
                    "Location: http://www.somewhere-else.com/\r\n" .
                    "Connection: close");
            $this->assertIdentical($headers->getResponseCode(), 301);
            $this->assertEqual($headers->getLocation(), "http://www.somewhere-else.com/");
            $this->assertTrue($headers->isRedirect());
        }
        
        function testParseChallenge() {
            $headers = new SimpleHttpHeaders("HTTP/1.1 401 Authorization required\r\n" .
                    "Content-Type: text/plain\r\n" .
                    "Connection: close\r\n" .
                    "WWW-Authenticate: Basic realm=\"Somewhere\"");
            $this->assertEqual($headers->getAuthentication(), 'Basic');
            $this->assertEqual($headers->getRealm(), 'Somewhere');
            $this->assertTrue($headers->isChallenge());
        }
    }

    class TestOfHttpResponse extends UnitTestCase {
        
        function testBadRequest() {
            $socket = &new MockSimpleSocket($this);
            $socket->setReturnValue('getSent', '');

            $response = &new SimpleHttpResponse($socket, 'GET', new SimpleUrl('here'));
            $this->assertTrue($response->isError());
            $this->assertWantedPattern('/Nothing fetched/', $response->getError());
            $this->assertIdentical($response->getContent(), false);
            $this->assertIdentical($response->getSent(), '');
        }
        
        function testBadSocketDuringResponse() {
            $socket = &new MockSimpleSocket($this);
            $socket->setReturnValueAt(0, "read", "HTTP/1.1 200 OK\r\n");
            $socket->setReturnValueAt(1, "read", "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n");
            $socket->setReturnValue("read", "");
            $socket->setReturnValue('getSent', 'HTTP/1.1 ...');

            $response = &new SimpleHttpResponse($socket, 'GET', new SimpleUrl('here'));
            $this->assertTrue($response->isError());
            $this->assertEqual($response->getContent(), '');
            $this->assertEqual($response->getSent(), 'HTTP/1.1 ...');
        }
        
        function testIncompleteHeader() {
            $socket = &new MockSimpleSocket($this);
            $socket->setReturnValueAt(0, "read", "HTTP/1.1 200 OK\r\n");
            $socket->setReturnValueAt(1, "read", "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n");
            $socket->setReturnValueAt(2, "read", "Content-Type: text/plain\r\n");
            $socket->setReturnValue("read", "");
            
            $response = &new SimpleHttpResponse($socket, 'GET', new SimpleUrl('here'));
            $this->assertTrue($response->isError());
            $this->assertEqual($response->getContent(), "");
        }
        
        function testParseOfResponseHeaders() {
            $socket = &new MockSimpleSocket($this);
            $socket->setReturnValueAt(0, "read", "HTTP/1.1 200 OK\r\nDate: Mon, 18 Nov 2002 15:50:29 GMT\r\n");
            $socket->setReturnValueAt(1, "read", "Content-Type: text/plain\r\n");
            $socket->setReturnValueAt(2, "read", "Server: Apache/1.3.24 (Win32) PHP/4.2.3\r\nConne");
            $socket->setReturnValueAt(3, "read", "ction: close\r\n\r\nthis is a test file\n");
            $socket->setReturnValueAt(4, "read", "with two lines in it\n");
            $socket->setReturnValue("read", "");
            
            $response = &new SimpleHttpResponse($socket, 'GET', new SimpleUrl('here'));
            $this->assertFalse($response->isError());
            $this->assertEqual(
                    $response->getContent(),
                    "this is a test file\nwith two lines in it\n");
            $headers = $response->getHeaders();
            $this->assertIdentical($headers->getHttpVersion(), "1.1");
            $this->assertIdentical($headers->getResponseCode(), 200);
            $this->assertEqual($headers->getMimeType(), "text/plain");
            $this->assertFalse($headers->isRedirect());
            $this->assertFalse($headers->getLocation());
        }
        
        function testParseOfCookies() {
            $socket = &new MockSimpleSocket($this);
            $socket->setReturnValueAt(0, "read", "HTTP/1.1 200 OK\r\n");
            $socket->setReturnValueAt(1, "read", "Date: Mon, 18 Nov 2002 15:50:29 GMT\r\n");
            $socket->setReturnValueAt(2, "read", "Content-Type: text/plain\r\n");
            $socket->setReturnValueAt(3, "read", "Server: Apache/1.3.24 (Win32) PHP/4.2.3\r\n");
            $socket->setReturnValueAt(4, "read", "Set-Cookie: a=aaa; expires=Wed, 25-Dec-02 04:24:20 GMT; path=/here/\r\n");
            $socket->setReturnValueAt(5, "read", "Connection: close\r\n");
            $socket->setReturnValueAt(6, "read", "\r\n");
            $socket->setReturnValue("read", "");
            
            $response = &new SimpleHttpResponse($socket, 'GET', new SimpleUrl('here'));
            $this->assertFalse($response->isError());
            $headers = $response->getHeaders();
            $cookies = $headers->getNewCookies();
            $this->assertEqual($cookies[0]->getName(), "a");
            $this->assertEqual($cookies[0]->getValue(), "aaa");
            $this->assertEqual($cookies[0]->getPath(), "/here/");
            $this->assertEqual($cookies[0]->getExpiry(), "Wed, 25 Dec 2002 04:24:20 GMT");
        }
        
        function testRedirect() {
            $socket = &new MockSimpleSocket($this);
            $socket->setReturnValueAt(0, "read", "HTTP/1.1 301 OK\r\n");
            $socket->setReturnValueAt(1, "read", "Content-Type: text/plain\r\n");
            $socket->setReturnValueAt(2, "read", "Location: http://www.somewhere-else.com/\r\n");
            $socket->setReturnValueAt(3, "read", "Connection: close\r\n");
            $socket->setReturnValueAt(4, "read", "\r\n");
            $socket->setReturnValue("read", "");
            
            $response = &new SimpleHttpResponse($socket, 'GET', new SimpleUrl('here'));
            $headers = $response->getHeaders();
            $this->assertTrue($headers->isRedirect());
            $this->assertEqual($headers->getLocation(), "http://www.somewhere-else.com/");
        }
        
        function testRedirectWithPort() {
            $socket = &new MockSimpleSocket($this);
            $socket->setReturnValueAt(0, "read", "HTTP/1.1 301 OK\r\n");
            $socket->setReturnValueAt(1, "read", "Content-Type: text/plain\r\n");
            $socket->setReturnValueAt(2, "read", "Location: http://www.somewhere-else.com:80/\r\n");
            $socket->setReturnValueAt(3, "read", "Connection: close\r\n");
            $socket->setReturnValueAt(4, "read", "\r\n");
            $socket->setReturnValue("read", "");
            
            $response = &new SimpleHttpResponse($socket, 'GET', new SimpleUrl('here'));
            $headers = $response->getHeaders();
            $this->assertTrue($headers->isRedirect());
            $this->assertEqual($headers->getLocation(), "http://www.somewhere-else.com:80/");
        }
    }
?>