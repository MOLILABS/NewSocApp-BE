<?php

namespace App\Models;

use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Common\Constant as Constant;
use Illuminate\Http\Request;

class Channel extends BaseModel
{
    /**
     * @throws GuzzleException
     */
    public function getTiktokInfo(string $id): array
    {

        $client = new Client();

        $response = $client->request('GET', 'https://tiktok28.p.rapidapi.com/profile/' . $id . '?schemaType=1', [
            'headers' => [
                'X-RapidAPI-Key' => env("X_RAPIDAPI_KEY"),
                'X-RapidAPI-Host' => Constant::Tiktok_X_RapidAPI_Host
            ],
        ]);
        var_dump($response);

        $data = json_decode($response->getBody());
        return [$data->uniqueId, $data->avatarLarger];
    }

    /**
     * @throws GuzzleException|InvalidSelectorException
     */
    public function getYoutubeInfo(String $id): array
    {
        if (!str_contains($id, '@'))
        {
            $client = new Client();

            $response = $client->request('GET', 'https://youtube-v2.p.rapidapi.com/channel/details?channel_id='. $id, [
                'headers' => [
                    'X-RapidAPI-Host' => Constant::Youtube_X_RapidAPI_Host,
                    'X-RapidAPI-Key' => env("X_RAPIDAPI_KEY"),
                ],
            ]);

            $data = json_decode($response->getBody());
            return [$data->title, $data->avatar[2]->url];
        }
        else
        {
            $document = new Document('https://www.youtube.com/' .$id, true);
            $image = $document->find("link[rel='image_src']")[0]->getAttribute('href');
            $title = $document->find('title')[0]->text();
            return [$title, $image];
        }
    }

    /**
     * @throws GuzzleException
     */
    public function getFacebookPicture(String $id)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://graph.facebook.com/'. $id .'/picture?type=large&redirect=false');
        $data = json_decode($response->getBody());
        return $data->data->url;
    }

    /**
     * @throws InvalidSelectorException
     * @throws GuzzleException
     */
    public function getFacebookInfo(string $id): array
    {
        $client = new Client();
        $res = $client->get('https://www.facebook.com/'. $id)->getBody()->getContents();
        $document = new Document();
        $document->loadHtml($res);
        $title = $document->find('title')[0]->text();
        $logo = '';
        $script = $document->find('script:contains("pageID")')[0]->text();
        if (preg_match('/pageID\"\:\"(.*?)\"/', $script, $match) == 1) {
            $pageId = $match[1];
            $logo = $this->getFacebookPicture($pageId);
        }
        return [$title, $logo];
    }

    /**
     * @throws InvalidSelectorException
     * @throws GuzzleException
     */
    public function getWebsiteInfo(string $url): array
    {

        if (substr($url, -1) === '/') {
            $url = substr($url, 0, -1);
        }
        $client = new Client();
        $res = $client->get($url)->getBody()->getContents();
        $document = new Document();
        $document->loadHtml($res);
        $title = $document->find('title')[0]->text();
        $faviconLink = $document->first('link[rel="icon"], link[rel="shortcut icon"]');
        if ($faviconLink) {
            $faviconLink = $faviconLink->getAttribute('href');
            if (!strstr($faviconLink, 'http'))
                $faviconLink = 'https://facebook.com'.$faviconLink;
        }
        return [$title, $faviconLink];
    }

    /**
     * @throws GuzzleException
     * @throws InvalidSelectorException
     */
    protected function getAdditionalStoreFields(Request $request): array
    {
        $channel_id = $request['channel_id'];
        $platform_id = $request['platform_id'];
        $result = Platform::query()->where('id', $platform_id)->first();
        $data = [];
        if ('tiktok' === $result->name){
            $data = $this->getTiktokInfo($channel_id);
        }
        if ('youtube' === $result->name){
            $data = $this->getYoutubeInfo($channel_id);
        }
        if ('facebook' === $result->name){
            $data = $this->getFacebookInfo($channel_id);
        }
        if ('website' === $result->name){
            $data = $this->getWebsiteInfo($channel_id);
        }
        return ['logo' => $data[1], 'name' => $data[0] ];
    }

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'channel_id' => [
                    'required'
                ],
                'platform_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }


}
