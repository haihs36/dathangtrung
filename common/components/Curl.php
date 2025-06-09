<?php
    namespace common\components;

class Curl {
    // set trực tiếp các thuộc tính trước khi run() nếu cần

    public $url = '';
    public $isAuth = FALSE; // request is authentication
    public $accAuth = ''; // basic|digest acc: 'username:password'

    public $sslVerify = FALSE;

    // login page
    public $login_url = '';
    public $login_username = '';    // usernameField=username
    public $login_password = '';    // passwordField=password

    // post params
    public $posts = array();

//    public $headers = array(
//        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
//        "Accept-Language: en-us,en;q=0.5",
//        "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
//        "Keep-Alive: 115",
//        "Connection: keep-alive",
//    );
    public $headers  = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Encoding: gzip, deflate',
        'Accept-Language: en-US,en;q=0.5',
        'Cache-Control: no-cache',
        'Content-Type: 	application/json; charset=utf-8',
        'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
    ];

    public $cookie          = '';   // set cookie: fruit=apple; colour=red
    public $remoteIp        = '';
    public $proxy           = '';   // ip:port
    public $referer         = '';   // url referer
    public $userAgent       = '';
    public $includeHeader   = FALSE;
    public $noBody          = FALSE;

    // attributes return after run()
    public $content         = NULL;
    public $headerResponse  = '';
    public $code            = NULL;
    public $message         = NULL;


    public function __construct($url = ''){
        $this->url = $url;
    }

    // set random userAgents
    public function setRandomSearchEngineUserAgent($userAgents = array())
    {
        if(!$userAgents){
            $userAgents = array(
                'Google - Googlebot/2.1 ( http://www.googlebot.com/bot.html)',
                'Google Image - Googlebot-Image/1.0 ( http://www.googlebot.com/bot.html)',
                'MSN Live - msnbot-Products/1.0 (+http://search.msn.com/msnbot.htm)',
                'Yahoo - Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)',
            );
        }
        $randKey = array_rand($userAgents, 1);
        $this->userAgent = $userAgents[$randKey];
    }

    // set $this->headerResponse = header trả về sau khi run()
    function setHeaderFunction($curl, $headerStr){
        $this->headerResponse .= $headerStr;
        return strlen($headerStr);
    }

    // set $this->message = message trả về sau khi run()
    protected function setMessageFunction(){
        $headers = explode("\r\n\r\n", $this->headerResponse);

        $header = NULL;

        $i = count($headers)-1;
        while ($i>=0) {
            $header=trim($headers[$i--]);
            if (!empty($header)) break;
        }

        preg_match('|^\s*HTTP/\d+\.\d+\s+(\d+)\s*(.*\S)|', $header, $m);
        if(isset($m[2])){
            $this->message = $m[2];
        }
    }

    // Chạy Curl
    public function run($url = NULL)
    {
        $url = $url ? $url : $this->url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
//        curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:80");
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, (array)400);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerify);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.87 Safari/537.36');
        curl_setopt($ch,CURLOPT_ENCODING , 'gzip, deflate, br');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, 'cna=hZYzFJHMMmwCAbSUjuk3VyFX; tracknick=haihs26; tg=0; enc=nyB6lJOwux%2FWJcNagHbIN68XN5O%2FkjejPX9oZ93tx7%2FgsEMqJxe%2B%2FNLEGEbHAv0Pi1wV9pyKUi4xzTuqeSRePw%3D%3D; _fbp=fb.1.1550110963523.1272713856; x=e%3D1%26p%3D*%26s%3D0%26c%3D0%26f%3D0%26g%3D0%26t%3D0%26__ll%3D-1%26_ato%3D0; thw=xx; hng=GLOBAL%7Czh-CN%7CUSD%7C999; t=3a73a10310dbf4be8db8727799cfc339; uc3=nk2=CywqUPV86w%3D%3D&lg2=WqG3DMC9VAQiUQ%3D%3D&vt3=F8dByuawqO5268k98gQ%3D&id2=UNN5E%2BRcEXrFPA%3D%3D; lgc=haihs26; uc4=nk4=0%40CWsmabVLvff88Pd8qxVYbUc2&id4=0%40UgQxlJi%2FXEe7pG3NWp%2FiMtxTS%2Bfs; _cc_=UIHiLt3xSw%3D%3D; v=0; cookie2=79f8bca802657ed80b34d69abae087c2; _tb_token_=3ef7e9ee9e074; _m_h5_tk=116e63ff005eeaa34647da8b4b5715c2_1573454414179; _m_h5_tk_enc=550ee87de69c91b034a05d7b505a699f; mt=ci=-1_0; l=dBSxeOhcqZymDLp2BOCNNQebAf_OSIRAguS-l3iwi_5CM18s6f7OkCHlgeJ6VAWfTkLB4cPM8Re9-etkqELsf3D8sxAJwxDc.; isg=BMfHLX8fVpY639IXt7alKO3ZVns90ji5osHCFZm049Z9COfKoZwr_gXKqpDz4HMm; uc1=cookie14=UoTbnryiLQ0fzw%3D%3');

        if($this->remoteIp){
            $this->headers = array_merge($this->headers, array(
                "REMOTE_ADDR: {$this->remoteIp}",
                "HTTP_X_FORWARDED_FOR: {$this->remoteIp}"
            ));
        }

        if($this->headers)
            curl_setopt($ch,CURLOPT_HTTPHEADER, $this->headers);

        if($this->proxy)
            curl_setopt($ch,CURLOPT_PROXY,$this->proxy);

        if($this->referer)
            curl_setopt($ch,CURLOPT_REFERER,$this->referer);

        if($this->userAgent)
            curl_setopt($ch,CURLOPT_USERAGENT,$this->userAgent);

        if($this->includeHeader)
            curl_setopt($ch,CURLOPT_HEADER,true);

        if($this->noBody)
            curl_setopt($ch,CURLOPT_NOBODY,true);

        if($this->cookie)
            curl_setopt($ch,CURLOPT_COOKIE, $this->cookie);

        // login trước
        if($this->login_url){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $this->login_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{$this->login_username}&{$this->login_password}");
            //fopen('curl_cookie.txt', 'w');
            //echo is_writable(realpath('curl_cookie.txt')) ? 'writable' : 'not writable'; die;
            curl_setopt($ch, CURLOPT_COOKIEJAR, realpath('curl_cookie.txt'));
            curl_setopt($ch, CURLOPT_COOKIEFILE, realpath('curl_cookie.txt'));
            curl_exec($ch);
        }

        // lấy dữ liệu
        curl_setopt($ch, CURLOPT_URL, $url);
        if($this->posts){
            if(is_array($this->posts)){
                $this->posts = http_build_query($this->posts, FALSE, '&');
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->posts);
        }
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'setHeaderFunction'));

        $this->content  = curl_exec($ch);

        $this->setMessageFunction();
        $this->code   = curl_getinfo($ch,CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $this->content;
    }

    public static function chuyenKhongDau($txt) {
        return self::makeFriendlyTxt($txt);
        $arraychar = array(
            array("đ", "Đ"),
            array("ó", "ỏ", "o", "ò", "o", "ọ", "o", "õ", "ô", "ỗ", "ổ", "ồ", "ố", "ộ", "ơ", "ỡ", "ớ", "ờ", "ở", "ợ"),
            array("ì", "í", "ỉ", "ì", "ĩ", "ị"),
            array("ê", "ệ", "ế", "ể", "ễ", "ề", "é", "ẹ", "ẽ", "è", "ẻ"),
            array("ả", "á", "ạ", "ã", "à", "â", "ẩ", "ấ", "ầ", "ậ", "ẫ", "ă", "ẳ", "ắ", "ằ", "ặ", "ẵ"),
            array("ũ", "ụ", "ú", "ủ", "ù", "ư", "ữ", "ự", "ứ", "ử", "ừ"),
            array("ỹ", "ỵ", "ý", "ỷ", "ỳ"),
            array("/", "&quot;")
        );

        $arrayconvert = array("d", "o", "i", "e", "a", "u", "y", "+");

        for ($i = 0; $i < sizeof($arraychar); $i++) {
            foreach ($arraychar[$i] as $key => $value) {
                $txt = str_replace($value, $arrayconvert[$i], $txt);
            }
        }
        return $txt;
    }

    public static function removeUnwantedChars($str, $charlist = []) {
        foreach ($charlist as $item) {
            $str = str_replace($item, '', $str);
        }
        return $str;
    }

    public static function remove_special_char($name) {
        $retVal = '';
        for ($i = 0; $i < strlen($name); $i++) {
            if ($name[$i] == '-') {
                $retVal = $retVal . $name[$i];
                continue;
            }
            $charater = ord($name[$i]);
            // Check valid character
            if ((($charater >= 97 && $charater <= 122) || ($charater >= 65 && $charater <= 90) || ($charater >= 48 && $charater <= 57) || $charater == 32)) {
                $retVal = $retVal . $name[$i];
            }
        }
        return $retVal;
    }

    public static function convert_encoding($from_encoding, $to_encoding, $text) {
        $chracters_map['utf8'] = array("A", "Á", "À", "Ả", "Ã", "Ạ", "Â", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "Ă", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "E", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ê", "Ế", "Ề", "Ể", "Ễ", "Ệ", "I", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "O", "Ó", "Ò", "Ỏ", "Õ", "Ọ", "Ô", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "U", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ư", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Y", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "Đ",
                                       "a", "á", "à", "ả", "ã", "ạ", "â", "ấ", "ầ", "ẩ", "ẫ", "ậ", "ă", "ắ", "ằ", "ẳ", "ẵ", "ặ", "e", "é", "è", "ẻ", "ẽ", "ẹ", "ê", "ế", "ề", "ể", "ễ", "ệ", "i", "í", "ì", "ỉ", "ĩ", "ị", "o", "ó", "ò", "ỏ", "õ", "ọ", "ô", "ố", "ồ", "ổ", "ỗ", "ộ", "ơ", "ớ", "ờ", "ở", "ỡ", "ợ", "u", "ú", "ù", "ủ", "ũ", "ụ", "ư", "ứ", "ừ", "ử", "ữ", "ự", "y", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "đ", ")", "(", "%", "&", "/", "  ", "amp;", "*", "~", "- -", ".", ",", "#", "'", "°", "ö", "Ð", "¿a", "­", "ç‰ä„®â€¬æ½”æ±µç™©ç‰¥ - æ•”æ±¬ä´ â¥æ¡", "»", "«", "ñ", "ç", ".", "©", "Å", "́", "„", "œ", "ë", "°", "›", "§", "€", "́", "β", "ι", "", "ο", "ς", "Ü", "", "ộ", "ồ", "ầ", "039");

        //βιος
        $chracters_map['none'] = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "Y", "D",
                                       "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "y", "d", " ", " ", "", "", "", " ", "", " ", " ", "-", "", " ", "", "", "", "o", "d", "", "", "", "", "", "n", "c", " ", "", "a", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "o", "o", "a", '');

        if (isset($chracters_map[$from_encoding]) AND isset($chracters_map[$to_encoding])) {
            $new_string = str_replace($chracters_map[$from_encoding], $chracters_map[$to_encoding], $text);
            $new_string = self::remove_special_char($new_string);
            return trim($new_string);
        }
    }

    public static function chuyenKhongdau2($txt) {
        $txt = self::convert_encoding("utf8", "none", $txt);
        //$txt = _name_cleaner($txt);
        $txt = trim(str_replace("  ", " ", $txt));

        return (mb_strtolower($txt));
    }

    public static function convert_to_slug($txt) {
        $txt = self::chuyenKhongdau2($txt);
        $patern = '[\W+]';
        return preg_replace($patern, '-', $txt);
    }
}



