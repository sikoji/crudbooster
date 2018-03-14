<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use CRUDBooster;
use crocodicstudio\crudbooster\Modules\SettingModule\SettingRepo;
use Session;
use Schema;
use DB;
use Config;

class CBAuthAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (SettingRepo::getSetting('api_debug_mode') !== 'false') {
            return;
        }

        $this->validateRequest();

        list($user_agent, $server_token, $server_token_screet) = $this->getTokens();

        $sender_token = Request::header('X-Authorization-Token');

        $this->tokenMissMatchResponse($sender_token, $server_token);

        $this->tokenMissMatchDevice($sender_token, $user_agent, $server_token);

        $id = array_search($sender_token, $server_token);
        $server_screet = $server_token_screet[$id];
        DB::table('cms_apikey')->where('screetkey', $server_screet)->increment('hit');

        $expired_token = date('Y-m-d H:i:s', strtotime('+5 seconds'));
        Cache::put($sender_token, $user_agent, $expired_token);

        return $next($request);
    }

    /**
     * @return array
     */
    private function validateRequest()
    {
        $validator = Validator::make([

            'X-Authorization-Token' => Request::header('X-Authorization-Token'),
            'X-Authorization-Time' => Request::header('X-Authorization-Time'),
            'useragent' => Request::header('User-Agent'),
        ], [
            'X-Authorization-Token' => 'required',
            'X-Authorization-Time' => 'required',
            'useragent' => 'required',
        ]);
        $result = [];
        if (!$validator->fails()) {
            return [];
        }
        $message = $validator->errors()->all();
        $result['api_status'] = 0;
        $result['api_message'] = implode(', ', $message);
        sendAndTerminate(response()->json($result, 200));

        return $result;
    }

    /**
     * @return array
     */
    private function getTokens()
    {
        $userAgent = Request::header('User-Agent');
        $time = Request::header('X-Authorization-Time');

        $keys = DB::table('cms_apikey')->where('status', 'active')->pluck('screetkey');
        $serverToken = [];
        $serverTokenSecret = [];
        foreach ($keys as $key) {
            $serverToken[] = md5($key.$time.$userAgent);
            $serverTokenSecret[] = $key;
        }

        return [$userAgent, $serverToken, $serverTokenSecret];
    }

    /**
     * @param $sender_token
     * @param $server_token
     * @return mixed
     */
    private function tokenMissMatchResponse($sender_token, $server_token)
    {
        if (Cache::has($sender_token) || in_array($sender_token, $server_token)) {
            return;
        }
        $result = [];
        $result['api_status'] = false;
        $result['api_message'] = "THE TOKEN IS NOT MATCH WITH SERVER TOKEN";
        $result['sender_token'] = $sender_token;
        $result['server_token'] = $server_token;
        sendAndTerminate(response()->json($result, 200));
    }

    /**
     * @param $senderToken
     * @param $userAgent
     * @param $serverToken
     */
    private function tokenMissMatchDevice($senderToken, $userAgent, $serverToken)
    {
        if (! Cache::has($senderToken) || Cache::get($senderToken) == $userAgent) {
            return;
        }
        $result = [];
        $result['api_status'] = false;
        $result['api_message'] = "THE TOKEN IS ALREADY BUT NOT MATCH WITH YOUR DEVICE";
        $result['sender_token'] = $senderToken;
        $result['server_token'] = $serverToken;
        sendAndTerminate(response()->json($result, 200));
    }
}
