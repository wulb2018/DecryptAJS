<?php
namespace DecryptAJS;


class Resolve
{
    private $config;
    private $jar;
    private $domain;//不需要加http
    private $client;
    private $response;
    private $cookieName = 'qtoken2016';
    private $theme;
    private $query;

    public function __construct($domain,$theme = 'https')
    {
        $this->config = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ];
        $this->domain = $domain;
        $this->jar = \GuzzleHttp\Cookie\CookieJar::fromArray(
            [
                'qtoken2016' => '',
            ],
            $this->domain
        );
        $this->client = new \GuzzleHttp\Client(['cookies' => $this->jar]);
        $this->theme = $theme;
    }

    public function getRes()
    {



        if ($response->getStatusCode() == 200) {
            $bodyStr = $response->getBody();
            $resStr = $this->getCookieStr($bodyStr);

            $cookies = $this->client->getConfig()['cookies'];
            $cookies->setCookie(new SetCookie([
                'Domain'  => $this->domain,
                'Name'    => $this->cookieName,
                'Value'   => $resStr,
                'Discard' => true
            ]));


        }
    }

    public function getToken()
    {
        $response = $this->client->request('GET', $this->theme.'://'.$this->domain.'/main/whois.asp?act=gettok',$this->config);
        if ($response->getStatusCode() == 200) {
            $bodyStr = $response->getBody();
            $resStr = Decrypt::getCookieStr($bodyStr);
            $cookies = $this->client->getConfig()['cookies'];
            $cookies->setCookie(new SetCookie([
                'Domain'  => $this->domain,
                'Name'    => $this->cookieName,
                'Value'   => $resStr,
                'Discard' => true
            ]));
            return true;
        }
        return false;
    }

    public function setDomain($queryDomain)
    {
        $this->query['queryDomain'] = $queryDomain;
    }
    public function setSuffixs($arr)
    {
        $suffixs = '';
        foreach ($arr as $value) {
            $suffixs .= $value.'%2C';
        }
        $suffixs = trim($suffixs,'%2C');
        $this->query['suffixs'] = $suffixs;
    }

    public function getDomainCheck()
    {
        $v = '0.'.strrev(microtime(1)*10000);
        $response = $this->client->request('GET', $this->theme.'://'.$this->domain.'/main/whois.asp?act=query&domains='.$this->query['queryDomain'].'&suffixs='.$this->query['suffixs'].'&du=&v='.$v,$this->config);
        return $this->analysis($response->getBody()->getContents());
    }

    private function analysis($str)
    {
        $res = preg_match('/200 ok,a:'.$this->query['queryDomain'].'/',$str,$arr);
        if ($res == 0) {
            return false;
        }
        return true;
    }

    public function setUserAgent($userAgent)
    {
        if (isset($this->config['headers'])) {
            $this->config['headers']['User-Agent'] = $userAgent;
            return;
        }
        $this->config = [
            'headers' => [
                'User-Agent' => $userAgent
            ]
        ];
        return;
    }
}