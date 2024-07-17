<?php
/*
* available commands
* /start
* /sign up
* /all button commands
* /all chose input's
*
* available text
* input text of the user without '/'
* input file
* input button
*/
class Controller{
    public string $type;
    public bool $model = false;
    public  bool $greet;
    public ?array $json;
    public ?string $username;
    public string $name;
    public string $chat_id;
    public ?string $message_id;
    public string $text;
    public bool $adminToken;
    public bool $notFound;
    public bool $home;
    public array $downloadRequest;
    public string $title;
    public bool $addRequest;
    public bool $deleteRequest;
    public  string $callbackData;
    public bool $downloadMessage;
    public bool $deleteMessage;
    public bool $addMessage;
    public bool $seeMessage;
    public bool $signup;

    function __construct($request){
        $this->json = $this->jsonDecoder($request);
        $this->jsonParser($this->json);
    }

    private function jsonDecoder($encoded){
        return json_decode($encoded, true);
    }

    private function jsonParser(array $decoded){
        //command : /home(DB) & /start(NO DB)
        //text with no reply : admin and not found
        //text with reply : for download , add , see , delete
        //Callback Query : for Signup , Download, see , add , delete
        if(isset($decoded['callback_query'])) {
            $this->type = "callback_query";
            $this->chat_id = $decoded['callback_query']['message']['chat']['id'];
            $this->username = $decoded['callback_query']['from']['username']??null;
            $this->name = $decoded['callback_query']['from']['first_name'];
            $this->callbackData = $decoded['callback_query']['data'];
            $this->message_id = $decoded['callback_query']['message']['message_id'];
            $this->text = "NULL";


            switch ($this->callbackData){
                case 'signUp':
                    $this->model = true;
                    $this->signup = true;
                    break;
                case 'seeMessage':
                    $this->model = true;
                    $this->seeMessage = true;
                    break;
                case 'download':
                    $this->downloadMessage = true;
                    break;
                case 'deleteMessage':
                    $this->deleteMessage = true;
                    break;
                case 'addMessage':
                    $this->addMessage = true;
                    break;
            }

//        }elseif(DOCUMENT){  has to be admin
        }else{
            $this->type = "string";
            $this->chat_id = $decoded['message']['chat']['id'];
            $this->username = $decoded['message']['from']['username']??'null';
            $this->name = $decoded['message']['from']['first_name'];
            $this->text = $decoded['message']['text'];

            $isCommand = preg_match('/^\//', $this->text);
            if($isCommand){
                if ($this->text == '/start'){$this->greet = true;}
                if ($this->text == '/home'){$this->model = true;$this->home = true;}
//                    if ($this->text == '/signup'){$this->model = true;}
            }else{
                //for plain text
                if (isset($decoded['message']['reply_to_message'])){
                    //for reply plain text
                    //see , add , delete : DB
                    //donwload : file_id -> dowloadAPI
                    $this->validateReply($decoded['message']['reply_to_message']);
                    $requestType = $decoded['message']['reply_to_message']['text'];
                    $downloadRequest = preg_match('/'.DOWNLOAD_REQUEST.'/', $requestType);
                    $addRequest = preg_match('/'.ADD_REQUEST.'/', $requestType);
                    $deleteRequest = preg_match('/'.DELETE_REQUEST.'/', $requestType);
                    switch (true){
                        case $downloadRequest != false:
                            $this->textValidator();
                            $this->API();
                            break;
                        case $addRequest != false:
                            $this->addRequest = true;
                            $this->model = true;

                            break;
                        case $deleteRequest != false :
                            $this->deleteRequest = true;
                            $this->model = true;

                            break;
                        default:
                            controller::ErrorHandlling($this,NULL);

                    }
                }else{
                    //for plain text with no reply : admin or not found
                    if (strlen($this->text) == 128){
                        $result = $this->adminValidator($this->text);
                        if ($result){
                            $this->adminToken =true;
                            $this->model = true;
                        }else{
                            $this->notFound = true;
                        }
                    }else{
                        $this->notFound = true;
                    }

                }
            }
        }


    }
    private function adminValidator($token){
        return $token == ADMIN_TOKEN;
    }


    public static function ErrorHandlling(object $obj , string|null $e) {
        if (isset($e)){
            controller::logger($e);
        }
        //echo URL."sendMessage?chat_id=".$obj->chat_id."&text=".ERROR;
        file_get_contents(URL."sendMessage?chat_id=".$obj->chat_id."&text=".ERROR);

        //sleep(2);
        //file_get_contents(URL."setWebhook?url=https://youcantkeepon.000webhostapp.com&drop_pending_updates=true");
        exit();
    }
    private static function logger($e) {
        $stream = fopen("log.txt","a");
        fwrite($stream,$e."\n");
        fclose($stream);
    }
    private function validateReply($decoded) {
        $is_bot = $decoded['from']['is_bot'] == true;
        $usernameBot = $decoded['from']['username'] == 'zirnevesBot';
        if (!$is_bot or !$usernameBot){
            controller::ErrorHandlling($this,null);
        }
    }
    private function textValidator() {
        //no more than 30 char and no short than 4 word
        //just english character
        $text = strtolower($this->text);
        $charSize = strlen($text) > 30 || strlen($text) < 4;
        if ($charSize){controller::ErrorHandlling($this,NULL);}
        $english = ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9"];
        $charEn = false;
        for ($i=0;$i<strlen($text);$i++){
            if(!in_array($text[$i],$english)){
                $charEn = true;
                break;
            }
        }
        if ($charEn){controller::ErrorHandlling($this,NULL);}

    }



    private function API(){
        $film2sub = "https://film2subtitle.com/?s=";
        $text = str_replace(" ","+",$this->text);
        $url = $film2sub.$text;
        $moviePage = $this->movieSelector($url);
        $this->downloadRequest = $this->linkSelector($moviePage);
    }
    private function movieSelector($url) {
        $searchResult = file_get_contents($url);
        $div = array();
        $moviePage = array();
        $title = array();
        preg_match('/<div class="sub-article-detail">(.*?)<\/div>/s',$searchResult,$div);
        if(!$div){controller::ErrorHandlling($this,NULL);}
        preg_match("/<h1>(.*?)<\/h1>/s",$div[0],$title);
        $this->title = $title[1];
        preg_match("/href=([^\s>]+)/",$div[0],$moviePage);
        //TODO:if empty return NULL
        return $moviePage[1];
    }
    private function linkSelector($moviePage) {
        $movie = file_get_contents($moviePage);
        $div = array();
        $links = array();
        preg_match('/<div class="sub-download-box">(.*?)<\/div>/s',$movie,$div);
        preg_match_all('/<a[^>]+href="([^"]+)"[^>]*>/i',$div[0],$links);
        return $links[1];
    }

}