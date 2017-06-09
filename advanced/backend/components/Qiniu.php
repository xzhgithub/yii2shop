<?php
namespace backend\components;

use yii\base\Component;
use yii\web\HttpException;

/**
 * upload file to qiniu.
 *
 * To use this extension, you may insert the following code in controller:
 *
 * use crazyfd\qiniu\Qiniu;
 * $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
 * $qiniu->uploadFile($file['tmp_name'], time());
 *
 * @author crazyfd <crazyfd@qq.com>
 * @version 1.0
 */

class Qiniu extends Component{

    public $up_host = 'http://up.qiniu.com';
    public $rs_host = 'http://rs.qbox.me';
    public $rsf_host = 'http://rsf.qbox.me';

    public $accessKey;
    public $secretKey;
    public $bucket;
    public $domain;
//
//    function __construct($accessKey, $secretKey, $domain, $bucket = '')
//    {
//        $this->accessKey = $accessKey;
//        $this->secretKey = $secretKey;
//        $this->domain = $domain;
//        $this->bucket = $bucket;
//    }

    /**
     * ͨ�������ļ��ϴ�
     * @param $filePath
     * @param null $key
     * @param string $bucket
     * @throws HttpException
     */

    public function uploadFile($filePath, $key = null, $bucket = '')
    {
        if(!file_exists($filePath)){
            throw new HttpException(400, "�ϴ����ļ�������");
        }
        $bucket = $bucket ? $bucket : $this->bucket;

        $uploadToken = $this->uploadToken(array('scope' => $bucket));
        $data = [];
        if (class_exists('\CURLFile')) {
            $data['file'] = new \CURLFile($filePath);
        } else {
            $data['file'] = '@' .$filePath;
        }
        $data['token'] = $uploadToken;
        if ($key) {
            $data['key'] = $key;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->up_host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = $this->response($result);
        if ($status == 200) {
            return $result;
        } else {
            throw new HttpException($status, $result['error']);
        }
    }

    /**
     * ͨ��ͼƬURL�ϴ�
     * @param $url
     * @param null $key
     * @param string $bucket
     * @throws HttpException
     */
    public function uploadByUrl($url, $key = null, $bucket = '')
    {
        $filePath = tempnam(sys_get_temp_dir(), 'QN');
        copy($url, $filePath);
        $result = $this->uploadFile($filePath, $key, $bucket);
        unlink($filePath);
        return $result;
    }

    /**
     * ��ȡ��Դ��Ϣ http://developer.qiniu.com/docs/v6/api/reference/rs/stat.html
     * @param $key
     * @param string $bucket
     * @return array
     */

    public function stat($key, $bucket = '')
    {
        $bucket = $bucket ? $bucket : $this->bucket;
        $encodedEntryURI = self::urlBase64Encode("{$bucket}:{$key}");
        $url = "/stat/{$encodedEntryURI}";
        return $this->fileHandle($url);
    }

    /**
     * �ƶ���Դ http://developer.qiniu.com/docs/v6/api/reference/rs/move.html
     * @param $key
     * @param $bucket2
     * @param bool $key2
     * @param string $bucket
     * @throws HttpException
     */
    public function move($key, $bucket2, $key2 = false, $bucket = '')
    {
        $bucket = $bucket ? $bucket : $this->bucket;
        if (!$key2) {
            $key2 = $bucket2;
            $bucket2 = $bucket;
        }
        $encodedEntryURISrc = self::urlBase64Encode("{$bucket}:{$key}");
        $encodedEntryURIDest = self::urlBase64Encode("{$bucket2}:{$key2}");
        $url = "/move/{$encodedEntryURISrc}/{$encodedEntryURIDest}";
        return $this->fileHandle($url);
    }

    /**
     * ��ָ����Դ����Ϊ��������Դ��http://developer.qiniu.com/docs/v6/api/reference/rs/copy.html
     * @param $key
     * @param $bucket2
     * @param bool $key2
     * @param string $bucket
     * @throws HttpException
     */
    public function copy($key, $bucket2, $key2 = false, $bucket = '')
    {
        $bucket = $bucket ? $bucket : $this->bucket;
        if (!$key2) {
            $key2 = $bucket2;
            $bucket2 = $bucket;
        }
        $encodedEntryURISrc = self::urlBase64Encode("{$bucket}:{$key}");
        $encodedEntryURIDest = self::urlBase64Encode("{$bucket2}:{$key2}");
        $url = "/copy/{$encodedEntryURISrc}/{$encodedEntryURIDest}";
        return $this->fileHandle($url);
    }

    /**
     * ɾ��ָ����Դ  http://developer.qiniu.com/docs/v6/api/reference/rs/delete.html
     * @param $key
     * @param string $bucket
     * @throws HttpException
     */
    public function delete($key, $bucket = '')
    {
        $bucket = $bucket ? $bucket : $this->bucket;
        $encodedEntryURI = self::urlBase64Encode("{$bucket}:{$key}");
        $url = "/delete/{$encodedEntryURI}";
        return $this->fileHandle($url);
    }


    /**
     * ����������batch�� http://developer.qiniu.com/docs/v6/api/reference/rs/batch.html
     * @param $operator   [stat|move|copy|delete]
     * @param $files
     * @throws HttpException
     */
    public function batch($operator, $files)
    {
        $data = '';
        foreach ($files as $file) {
            if (!is_array($file)) {
                $encodedEntryURI = self::urlBase64Encode($file);
                $data .= "op=/{$operator}/{$encodedEntryURI}&";
            } else {
                $encodedEntryURI = self::urlBase64Encode($file[0]);
                $encodedEntryURIDest = self::urlBase64Encode($file[1]);
                $data .= "op=/{$operator}/{$encodedEntryURI}/{$encodedEntryURIDest}&";
            }
        }
        return $this->fileHandle('/batch', $data);
    }

    /**
     * �о���Դ  http://developer.qiniu.com/docs/v6/api/reference/rs/list.html
     * @param string $limit
     * @param string $prefix
     * @param string $marker
     * @param string $bucket
     * @throws HttpException
     */
    public function listFiles($limit = '', $prefix = '', $marker = '', $bucket = '')
    {
        $bucket = $bucket ? $bucket : $this->bucket;
        $params = array_filter(compact('bucket', 'limit', 'prefix', 'marker'));
        $url = $this->rsf_host . '/list?' . http_build_query($params);
        return $this->fileHandle($url);
    }

    protected function fileHandle($url, $data = array())
    {
        if (strpos($url, 'http://') !== 0){
            $url = $this->rs_host . $url;
        }

        if (is_array($data)){
            $accessToken = $this->accessToken($url);
        }
        else{
            $accessToken = $this->accessToken($url, $data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: QBox ' . $accessToken,
        ));

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = $this->response($result);
        if ($status == 200) {
            return $result;
        } else {
            throw new HttpException($status ,$result['error']);
        }
    }

    public function uploadToken($flags)
    {
        if (!isset($flags['deadline'])) {
            $flags['deadline'] = 3600 + time();
        }
        $encodedFlags = self::urlBase64Encode(json_encode($flags));
        $sign = hash_hmac('sha1', $encodedFlags, $this->secretKey, true);
        $encodedSign = self::urlBase64Encode($sign);
        $token = $this->accessKey . ':' . $encodedSign . ':' . $encodedFlags;
        return $token;
    }

    public function accessToken($url, $body = false)
    {
        $parsed_url = parse_url($url);
        $path = $parsed_url['path'];
        $access = $path;
        if (isset($parsed_url['query'])) {
            $access .= "?" . $parsed_url['query'];
        }
        $access .= "\n";
        if ($body) {
            $access .= $body;
        }
        $digest = hash_hmac('sha1', $access, $this->secretKey, true);
        return $this->accessKey . ':' . self::urlBase64Encode($digest);
    }

    /**
     * ���Դ����base64����
     * @param $str
     * @return mixed
     */

    public static function urlBase64Encode($str)
    {
        $find = array("+", "/");
        $replace = array("-", "_");
        return str_replace($find, $replace, base64_encode($str));
    }

    /**
     * ��ȡ�ļ�������Դ����
     * @param string $key
     * @return string
     */
    public function getLink($key = '')
    {
//         $url = "http://{$this->domain}/{$key}";
        $url = rtrim($this->domain,'/')."/{$key}";
        return $url;
    }


    /**
     * ��ȡ��Ӧ����
     * @param  string $text ��Ӧͷ�ַ���
     * @return array        ��Ӧ�����б�
     */
    private function response($text)
    {
        return json_decode($text, true);
    }

//    public function __destruct()
//    {
//    }
}