<?php
class view {
    public ?string $username;
    public string $name;
    public string $chat_id;
    public ?string $message_id;
    public string $text;

    public function __construct($obj){
        //see if the input object is model or controller
        $this->username = $obj->username;
        $this->text = $obj->text;
        $this->name = $obj->name;
        $this->chat_id = $obj->chat_id;
        $this->message_id = $obj->message_id ?? NULL;
        $this->indetifier($obj);

    }
    private function indetifier($obj) {
        if (isset($obj->greet)) {
            $this->sendGreeting();
            return;
        }
        if (isset($obj->setAdmin)){
            $this->setAdmin();
            return;
        }
        if (isset($obj->home)){
            $this->home($obj);
            return;
        }
        if (isset($obj->notFound)){
            $this->notFound();
            return;
        }

        if (isset($obj->addRequest)){
            $this->queueAdd($obj);
            return;
        }
        if (isset($obj->deleteRequest)){
            $this->queueDelete($obj);
            return;
        }
        if(isset($obj->downloadRequest)){
            $this->downloadRequest($obj);
            return;
        }
        if (isset($obj->signup)) {
            $this->signup($obj);
            return;
        }
        if(isset($obj->downloadMessage)){
            $this->downloadMessage();
            return;
        }
        if(isset($obj->deleteMessage)){
            $this->deleteMessage();
            return;
        }
        if(isset($obj->addMessage)){
            $this->addMessage();
            return;
        }
        if (isset($obj->seeMessage)) {
            $this->seeMessage($obj);
            return;
        }


    }




    //answer to the commands
    private function sendGreeting() {
        //start menu with 2 button , one for signup and one for download
        $text = urlencode(GREET1.$this->name.GREET2);
        $result = file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id."&text=".$text."&reply_markup=".json_encode(GREET_JSON));
    }
    private function home($obj) {
        if ($obj->home){
            $text = urlencode(HOME_SIGNED);
            $result = file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text.'&reply_markup='.json_encode(HOME_SIGNED_JSON));
        }else{
            $text = urlencode(HOME_NOT_SIGNED);
            $result = file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text.'&reply_markup='.json_encode(HOME_NOT_SIGNED_JSON));
        }

    }





    //answer to the admin
    private function setAdmin() {
        $text = urlencode(SET_ADMIN."\n".RETURN_HOME);
        file_get_contents(URL."sendMessage?chat_id=".$this->chat_id."&text=".$text);
    }
    //answer to plain text with no reply
    private function notFound() {
        $text = urlencode(NOT_FOUND);
        file_get_contents(URL."sendMessage?chat_id=".$this->chat_id."&text=".$text);

    }







    //answer to text from user replied on bot's message
    private function queueAdd($obj) {
        if($obj->addRequest){
            $text =urlencode(ADD_REQUEST_OK);
        }else {
            $text = urlencode(ADD_REQUEST_FAIL);
        }
        file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text);
    }

    private function queueDelete($obj) {
        if ($obj->deleteRequest){
            $text = urlencode(DELETE_OK);
        }else{
            $text = urlencode(NOT_FOUND);
        }
        file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text);
    }
    private function downloadRequest($obj){
        foreach ($obj->downloadRequest as $key => $value){
            if ($value == "#"){
                continue;
            }
            file_get_contents(URL.'sendDocument?chat_id='.$this->chat_id.'&document='.$value);
        }

        $text = urlencode(RETURN_HOME);
        file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text);

    }





    //answer to inline keyboard
    private function signup($obj) {
        file_get_contents(URL."deleteMessage?chat_id=".$this->chat_id."&message_id=".$this->message_id);
        if ($obj->errorUnique) {
            file_get_contents(URL."sendMessage?chat_id=".$this->chat_id."&text=".SIGNUP_FALSE);
            $text = urlencode(HOME_NOT_SIGNED);
        } else{
            file_get_contents(URL."sendMessage?chat_id=".$this->chat_id."&text=".SIGNUP_TRUE);
            $text = urlencode(HOME_SIGNED);
        }
        file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text.'&reply_markup='.json_encode(HOME_SIGNED_JSON));

    }
    private function downloadMessage(){
        file_get_contents(URL."deleteMessage?chat_id=".$this->chat_id."&message_id=".$this->message_id);
        //reply message , send your movie name
        $text = urlencode(DOWNLOAD_MESSAGE);
        file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text.'&reply_markup='.json_encode(FORCE_REPLY));
    }
    private function deleteMessage() {
        //reply message , send your desire delete subtitle form your list
        file_get_contents(URL."deleteMessage?chat_id=".$this->chat_id."&message_id=".$this->message_id);
        $text = urlencode(DELETE_MESSAGE);
        file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text.'&reply_markup='.json_encode(FORCE_REPLY));
    }
    private function addMessage() {
        //reply message , send you desire subtitle to add to your list
        file_get_contents(URL."deleteMessage?chat_id=".$this->chat_id."&message_id=".$this->message_id);
        $text = urlencode(ADD_MESSAGE);
        file_get_contents(URL.'sendMessage?chat_id='.$this->chat_id.'&text='.$text.'&reply_markup='.json_encode(FORCE_REPLY));
    }
    private function seeMessage($obj) {
        file_get_contents(URL."deleteMessage?chat_id=".$this->chat_id."&message_id=".$this->message_id);
        if($obj->errorQueue){
            file_get_contents(URL."sendMessage?chat_id=".$this->chat_id."&text=".NOT_FOUND);
        }else{
            $queue = SEE_MESSAGE."\n";
            foreach ($obj->seeMessage as $key => $value) {
                $queue .= $value . "\n";
            }
            $queue .= RETURN_HOME;
            $text = urlencode($queue);
            file_get_contents(URL."sendMessage?chat_id=".$this->chat_id."&text=".$text);
        }

    }


}