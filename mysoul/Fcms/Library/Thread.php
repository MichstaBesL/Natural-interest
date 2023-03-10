<?php namespace Soulcms\Library;

/* *
 *
 * Copyright [2019]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 *
 * 本文件是框架系统文件，二次开发时不建议修改本文件
 *
 * */


/**
 * 线程处理
 */

class Thread
{

    /**
     * 线程非堵塞执行任务
     */
    function cron($param, $block = 0) {

        // 生成授权文件
        $param['auth'] = md5(mys_array2string($param));
        file_put_contents(WRITEPATH.'thread/'.$param['auth'].'.auth', SYS_TIME);

        $url = ROOT_URL.'index.php?s=api&c=run&m=cron&'.http_build_query($param);

        // 执行任务
        if (function_exists('fsockopen') || function_exists('pfsockopen')) {
            // 异步非堵塞执行
            $this->_fsockopen($url, $block);
        } else {
            file_get_contents($url);
        }

        return;
    }

    /**
     * fsockopen
     */
    private function _fsockopen($url, $block = 0) {

        $return = '';
        $uri = parse_url($url);
        $timeout = 10;

        isset($uri['host']) ||$uri['host'] = '';
        isset($uri['path']) || $uri['path'] = '';
        isset($uri['query']) || $uri['query'] = '';
        isset($uri['port']) || $uri['port'] = '';

        $host = $uri['host'];
        $path = $uri['path'] ? $uri['path'] . ($uri['query'] ? '?' . $uri['query'] : '') : '/';
        $port = !empty($uri['port']) ? $uri['port'] : 80;

        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: IP-".\Soulcms\Service::L('input')->ip_address()."\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: \r\n\r\n";

        $host = defined('SYS_HTTPS') && SYS_HTTPS ? 'ssl://'.$host : $host;
        $port = defined('SYS_HTTPS') && SYS_HTTPS ? 443 : $port;


        if (function_exists('fsockopen')) {
            $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        } elseif (function_exists('pfsockopen')) {
            $fp = @pfsockopen($host, $port, $errno, $errstr, $timeout);
        } else {
            $fp = false;
        }
        if (!$fp) {
            log_message('error', 'fsockopen函数调用失败');
            return ''; //note $errstr : $errno \r\n
        } else {
            //集阻塞/非阻塞模式流,$block==true则应用流模式
            stream_set_blocking($fp, $block);
            //设置流的超时时间
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            //$status = stream_get_meta_data($fp);
            //var_dump($status);
            /*
            //从封装协议文件指针中取得报头／元数据
            $status = stream_get_meta_data($fp);
            //timed_out如果在上次调用 fread() 或者 fgets() 中等待数据时流超时了则为 TRUE,下面判断为流没有超时的情况
            if (!$status['timed_out']) {
                while (!feof($fp)) {
                    if (($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n")) {
                        break;
                    }
                }
                $stop = false;
                //如果没有读到文件尾
                while (!feof($fp) && !$stop) {
                    //看连接时限是否=0或者大于8192  =》8192  else =》limit  所读字节数
                    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                    $return .= $data;
                    if ($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }
            }*/
            @fclose($fp);
            return $return;
        }
    }

}