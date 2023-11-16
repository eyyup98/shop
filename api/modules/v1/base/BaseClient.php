<?php


namespace app\api\modules\v1\base;


use app\api\modules\v1\helpers\ExceptionHelper;
use app\api\modules\v1\models\Logs;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use yii\web\HttpException;

class BaseClient extends Client
{
    /**
     * Create and send an HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string              $method  HTTP method.
     * @param string|UriInterface $uri     URI object or string.
     * @param array               $options Request options to apply. See \GuzzleHttp\RequestOptions.
     *
     * @throws HttpException
     */
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        $options[RequestOptions::SYNCHRONOUS] = true;

        try {
            $response = $this->requestAsync($method, $uri, $options)->wait();
        } catch (Exception $e) {
            $log = new Logs();
            $log->log_type = 0;
            $log->text = $e->getMessage();
            $log->save();

            throw new HttpException(400, $e->getMessage(), 400);
        }

        return $response;
    }
}