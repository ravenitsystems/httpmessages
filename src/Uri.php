<?php

namespace ravenitsystems\httpmessages;

use Exception;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private string $scheme = '';
    private string $host = '';
    private ?int $port = null;
    private string $user = '';
    private string $pass = '';
    private string $path = '';
    private string $query = '';
    private string $fragment = '';

    public static function createFromUrl(string $url): Uri
    {
        $parts = parse_url($url);
        if (!is_array($parts)) {
            throw new Exception("There was a problem parsing the given URL");
        }
        return self::createFromArray($parts);
    }

    public static function createFromArray(array $parts): Uri
    {
        foreach(['scheme', 'user', 'pass', 'host', 'path', 'query', 'fragment'] as $field) {
            $parts[$field] = $parts[$field] ?? '';
        }
        $parts['port'] = $parts['port'] ?? null;
        return new Uri($parts['scheme'], $parts['user'], $parts['pass'], $parts['host'], $parts['port'], $parts['path'], $parts['query'], $parts['fragment']);
    }

    public function __construct(string $scheme, string $user, string $pass, string $host, ?int $port, string $path, string $query, string $fragment)
    {
        $this->scheme = strtolower(trim($scheme));
        $this->user = trim($user);
        $this->pass = trim($pass);
        $this->host = strtolower(trim($host));
        $this->port = $port;
        $this->path = trim($path);
        $this->query = trim($query);
        $this->fragment = trim($fragment);
    }

    public function asString(): string
    {
        $authority = $this->getAuthority();
        $page = $this->path . ($this->query != '' ? '?' . $this->query : '') . ($this->fragment != '' ? '#' . $this->fragment : '');
        if ($authority != '') {
            return ($this->scheme != '' ? $this->scheme . ':' : '') . '//' . $authority . $page;
        }
        $page = '/' . $page;
        while (str_starts_with($page, '//')) {
            $page = substr($page, 1);
        }
        return $page;
    }

    public function asArray($include_empty = false): array
    {
        $data = [];
        foreach(['scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment'] as $field) {
            print "Processing {$field} which is " . $this->$field . PHP_EOL;
            if (($this->$field !== '' && $this->$field !== null) || $include_empty) {
                $data[$field] = $this->$field;
            }
        }
        return $data;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        if ($this->host == '') {
            return '';
        }
        $user_info = $this->getUserInfo();
        return ($user_info != '' ? $user_info . '@' : '') . $this->host;
    }

    public function getUserInfo(): string
    {
        return $this->user . ($this->pass != '' ? ':'. $this->pass : '');
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme($scheme): Uri
    {
        $parts = $this->asArray(true);
        $parts['scheme'] = $scheme;
        return self::createFromArray($parts);
    }

    public function withUserInfo($user, $password = null)
    {
        // TODO: Implement withUserInfo() method.
    }

    public function withHost($host)
    {
        // TODO: Implement withHost() method.
    }

    public function withPort($port)
    {
        // TODO: Implement withPort() method.
    }

    public function withPath($path)
    {
        // TODO: Implement withPath() method.
    }

    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
    }

    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    public function __toString(): string
    {
        return $this->asString();
    }
}