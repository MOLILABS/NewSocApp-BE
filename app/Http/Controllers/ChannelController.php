<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChannelController extends Controller
{
    public $model = Channel::class;

    /**
     * @throws GuzzleException
     * @throws InvalidSelectorException
     */
    public function getInfoTiktok(){
        $client = new Client();
        $res = $client->get('https://stackjava.com/')->getBody()->getContents();
        $document = new Document();
        $document->loadHtml($res);
        $title = $document->find('title')[0]->text();
        echo $title;
        return $document;
        $script = $document->find('script:contains("pageID")')[0]->text();
        if (preg_match('/pageID\"\:\"(.*?)\"/', $script, $match) == 1) {
            $pageId = $match[1];
        }
    }
}
