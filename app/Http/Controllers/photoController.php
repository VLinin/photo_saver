<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use VK\Client\VKApiClient;

class photoController extends Controller
{
    public function fwd($lvl,$peer){
        if(isset($lvl->fwd_messages)){
            foreach ($lvl->fwd_messages as $msg){
                if(isset($msg->attachments)){
                    $this->imageProcess($msg->attachments, $peer);
                }
                $this->fwd($msg,$peer);
            }
        }
    }
    public function imageProcess($attachments,$peer){
        $path="D:\photoVK\/";
        $vk = new VKApiClient();
        $tkn='';
        foreach ($attachments as $photo){
            $w=0;
            $z=0;
            $k=0;
            foreach ($photo->photo->sizes as $size){
                if($size->type=="w"){
                    $w=1;
                    $wN=$k;
                }
                if($size->type=="x"){
                    $z=1;
                    $zN=$k;
                }
                $k++;
            }
            if($w==1){

                $ReadFile = fopen ($photo->photo->sizes[$wN]->url, "rb");
                if ($ReadFile) {
                    $WriteFile = fopen ($path.$photo->photo->id.".jpg", "wb");
                    if ($WriteFile){
                        while(!feof($ReadFile)) {
                            fwrite($WriteFile, fread($ReadFile, 4096 ));
                        }
                        fclose($WriteFile);
                    }
                    fclose($ReadFile);
                }
            }elseif ($z==1){

                $ReadFile = fopen ($photo->photo->sizes[$zN]->url, "rb");
                if ($ReadFile) {
                    $WriteFile = fopen ($path.$photo->photo->id.".jpg", "wb");
                    if ($WriteFile){
                        while(!feof($ReadFile)) {
                            fwrite($WriteFile, fread($ReadFile, 4096 ));
                        }
                        fclose($WriteFile);
                    }
                    fclose($ReadFile);
                }
            }else{

                $ReadFile = fopen ($photo->photo->sizes[7]->url, "rb");
                if ($ReadFile) {
                    $WriteFile = fopen ($path.$photo->photo->id.".jpg", "wb");
                    if ($WriteFile){
                        while(!feof($ReadFile)) {
                            fwrite($WriteFile, fread($ReadFile, 4096 ));
                        }
                        fclose($WriteFile);
                    }
                    fclose($ReadFile);
                }
            }

        }
    }

    public function index(Request $request){
        $vk = new VKApiClient();
        $tkn='';
        $data = json_decode($request->getContent());
        $peer=$data->object->from_id;
        switch ($data->type) {
            case 'confirmation':
                return '8c9db1bb';
            case 'message_new':
                if (isset($data->object->attachments)) {

                    $photos = $data->object->attachments;
                    try {
                        if (!empty($photos)) {
                            $this->imageProcess($photos, $peer);
                        }
                        if (isset($data->object->fwd_messages)) {
                            $this->fwd($data->object,$peer);
                        }

                    } catch (\Exception $e) {
                        Info($e->getCode() . ' ' . $e->getMessage());
                    }
                }else{
                    $vk->messages()->send($tkn,
                        [
                            'peer_id' => $peer,
                            'message' => "я не умею с этим работать, где картинки?",
                            'v' => '5.85']);
                }

                return 'ok';
            default:
                return 'ok';
        }
    }
}
