<?php

namespace App\Traits;

use Berkayk\OneSignal\OneSignalFacade;

trait NotificationTrait
{

    public function sendNotificationToUser($user_id, $game_id, $title, $message){

        //android
        // $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        // //ios
        // $params['ios_sound'] = 'nofitication.caf';

        //huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';


        //big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }
        $params['huawei_msg_type'] = "data";//huawei


        OneSignalFacade::addParams($params)->sendNotificationToUser(
            $message,
            $user_id,
            $url = null, 
            $data = ['id' => $game_id.'', 'pageName'=>'FinishedMatches'],
            $buttons = null,
            $schedule = null,
            $title
        );
        
    }
    
    public function sendNotificationToAll($article_id, $message, $img_url=''){

        //android
        $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        //ios
        $params['ios_sound'] = 'nofitication.caf';

        //huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';


        //big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }
        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationToAll(
            $message,
            $url = null, 
            $data = ['articleId'=>$article_id.'', 'pageName'=>'news-detail'],
            $buttons = null,
            $schedule = null
        );
        
    }
    
    public function sendNotificationToLangS($lang, $article_id, $message, $img_url=''){
        //segment names
        $segments=['ckb'=>'سۆرانی', 'ar'=>'عربي', 'fa'=>'فارسی', 'kmr'=>'Kurdî', 'tr'=>'Türkçe', 'en'=>'English'];

        //android
        $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        //ios
        $params['ios_sound'] = 'nofitication.caf';

        // huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';


        // big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }
        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationToSegment(
            $message,
            $segments[$lang],
            $url = null, 
            $data = ['articleId'=>$article_id.'', 'pageName'=>'news-detail'],
            $buttons = null,
            $schedule = null
        );
        
    }
    
    public function sendNotificationToLangT($lang, $article_id, $message, $img_url=''){
        //lang map
        $langmap=['ckb'=>'he', 'ar'=>'ar', 'fa'=>'fa', 'kmr'=>'es', 'tr'=>'tr', 'en'=>'en'];

        //android
        $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        //ios
        $params['ios_sound'] = 'nofitication.caf';

        // huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';


        // big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }
        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationUsingTags(
            $message,
            array(
                ["field" => "tag", "key" => "languageCode", "relation" => "=", "value" => $langmap[$lang]],
            ),
            $url = null, 
            $data = ['articleId'=>$article_id.'', 'pageName'=>'news-detail'],
            $buttons = null,
            $schedule = null
        );
        
    }
    
    public function sendNotificationToLive($lang, $message){
        //lang map
        $langmap=['ckb'=>'he', 'ar'=>'ar', 'fa'=>'fa', 'kmr'=>'es', 'tr'=>'tr', 'en'=>'en'];

        //android
        $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        //ios
        $params['ios_sound'] = 'nofitication.caf';

        // huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationUsingTags(
            $message,
            array(
                ["field" => "tag", "key" => "languageCode", "relation" => "=", "value" => $langmap[$lang]],
            ),
            $url = null, 
            $data = ['articleId'=>'1', 'pageName'=>'MobileLiveTvScreen'],
            $buttons = null,
            $schedule = null
        );
        
    }
    
    public function sendNotificationToPage($lang, $menu_id, $message, $img_url=''){
        //lang map
        $langmap=['ckb'=>'he', 'ar'=>'ar', 'fa'=>'fa', 'kmr'=>'es', 'tr'=>'tr', 'en'=>'en'];

        //android
        $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        //ios
        $params['ios_sound'] = 'nofitication.caf';

        // huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';


        // big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }
        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationUsingTags(
            $message,
            array(
                ["field" => "tag", "key" => "languageCode", "relation" => "=", "value" => $langmap[$lang]],
            ),
            $url = null, 
            $data = ['menuId'=>$menu_id.'', 'pageName'=>'other-news-list'],
            $buttons = null,
            $schedule = null
        );
        
    }
    
    public function sendNotificationToURL($lang, $url, $message, $img_url=''){
        //lang map
        $langmap=['ckb'=>'he', 'ar'=>'ar', 'fa'=>'fa', 'kmr'=>'es', 'tr'=>'tr', 'en'=>'en'];

        //android
        $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        //ios
        $params['ios_sound'] = 'nofitication.caf';

        // huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';


        // big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }
        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationUsingTags(
            $message,
            array(
                ["field" => "tag", "key" => "languageCode", "relation" => "=", "value" => $langmap[$lang]],
            ),
            $url = $url, 
            $data = null,
            $buttons = null,
            $schedule = null
        );
        
    }
    
    public function sendGameNotificationCustom($game_id, $lang, $type, $title, $message, $img_url=''){
        // dd(implode(' | ', [$lang,$article_id,$category,$message,$img_url]));
        //lang map 
        // if($article_id.''!=='201763'){
            //dd('false');
        // }
        $langmap=['ckb'=>'he', 'ar'=>'ar', 'fa'=>'fa', 'kmr'=>'es', 'tr'=>'tr', 'en'=>'en'];

        //android
        // $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        // //ios
        // $params['ios_sound'] = 'nofitication.caf';

        // huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';
// dd($type);

        // big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }

        // $headings = [ 
        //     "en" => '', 
        // ]; 
        // $params['headings'] = $headings; 
        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationUsingTags(
            $message,
            array(
                ["field" => "tag", "key" => 'languageCode', "relation" => "=", "value" => $langmap[$lang]],
                ["field" => "tag", "key" => $type, "relation" => "=", "value" => "true"],
            ),
            $url = null, 
            $data = ['id'=>$game_id, 'pageName'=>'FinishedMatches'],
            $buttons = null,
            $schedule = null,
            $title
        );
        
    }
    
    public function sendPostNotificationCustom($post_id, $lang, $message, $img_url=''){
        // dd(implode(' | ', [$lang,$article_id,$category,$message,$img_url]));
        //lang map 
        // if($article_id.''!=='201763'){
            //dd('false');
        // }
        $langmap=['ckb'=>'he', 'ar'=>'ar', 'fa'=>'fa', 'kmr'=>'es', 'tr'=>'tr', 'en'=>'en'];

        // //android
        // $params['android_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';

        // //ios
        // $params['ios_sound'] = 'nofitication.caf';

        // huawei
        // $params['huawei_channel_id'] = '57c2b178-fd05-443f-859f-9c7d53e9ac96';


        // big image on expand
        if(!empty($img_url)){
            $params['big_picture'] = $img_url;//android
            $params['ios_attachments'] = ['image_id' => $img_url];//ios
            // $params['huawei'] = $img_url;//huawei
        }

        // $headings = [ 
        //     "en" => '', 
        // ]; 
        // $params['headings'] = $headings; 
        $params['huawei_msg_type'] = "data";//huawei

        OneSignalFacade::addParams($params)->sendNotificationUsingTags(
            $message,
            array(
                ["field" => "tag", "key" => 'languageCode', "relation" => "=", "value" => $langmap[$lang]],
                ["field" => "tag", "key" => '5', "relation" => "=", "value" => "true"],
            ),
            $url = null, 
            $data = ['id'=> $post_id, 'pageName'=>'DetailArticle'],
            $buttons = null,
            $schedule = null
        );
        
    }
}