<?php

namespace App\Models;

use Exception;
use DiDom\Document;
use App\Common\Helper;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Common\Constant as Constant;
use App\Common\GlobalVariable;
use Illuminate\Support\Facades\Gate;
use GuzzleHttp\Exception\GuzzleException;
use DiDom\Exceptions\InvalidSelectorException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Pagination\Paginator;
use Spatie\Permission\Models\Permission;

class Channel extends BaseModel
{
    use HasFactory;

    /**
     * @throws GuzzleException
     */
    public function getTiktokInfo(string $id)
    {
        try {
            $client = new Client();

            $response = $client->request('GET', 'https://' . Constant::TIKTOK_X_RAPIDAPI_HOST . '/profile/' . $id . '?schemaType=1', [
                'headers' => [
                    'X-RapidAPI-Key' => env("X_RAPIDAPI_KEY"),
                    'X-RapidAPI-Host' => Constant::TIKTOK_X_RAPIDAPI_HOST
                ],
            ]);
            $data = json_decode($response->getBody());
            return [$data->uniqueId, $data->avatarLarger];
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @throws GuzzleException|InvalidSelectorException
     */
    public function getYoutubeInfo(string $id)
    {
        try {
            if (!str_contains($id, '@')) {
                $client = new Client();

                $response = $client->request('GET', 'https://' . Constant::YOUTUBE_X_RAPIDAPI_HOST . '/channel/details?channel_id=' . $id, [
                    'headers' => [
                        'X-RapidAPI-Host' => Constant::YOUTUBE_X_RAPIDAPI_HOST,
                        'X-RapidAPI-Key' => env("X_RAPIDAPI_KEY"),
                    ],
                ]);

                $data = json_decode($response->getBody());
                return [$data->title, $data->avatar[2]->url];
            } else {
                $document = new Document('https://www.youtube.com/' . $id, true);
                $image = $document->find("link[rel='image_src']")[0]->getAttribute('href');
                $title = $document->find('title')[0]->text();
                return [$title, $image];
            }
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function getFacebookPicture(string $id)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://graph.facebook.com/' . $id . '/picture?type=large&redirect=false');
            $data = json_decode($response->getBody());
            return $data->data->url;
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @throws InvalidSelectorException
     * @throws GuzzleException
     */
    public function getFacebookInfo(string $id)
    {
        try {
            $client = new Client();
            $res = $client->get('https://www.facebook.com/' . $id)->getBody()->getContents();
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
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @throws InvalidSelectorException
     * @throws GuzzleException
     */
    public function getWebsiteInfo(string $url)
    {
        try {

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
                    $faviconLink = 'https://facebook.com' . $faviconLink;
            }
            return [$title, $faviconLink];
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
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
        if (Platform::TIKTOK === $result->name) {
            $data = $this->getTiktokInfo($channel_id);
        }
        if (Platform::YOUTUBE === $result->name) {
            $data = $this->getYoutubeInfo($channel_id);
        }
        if (Platform::FACEBOOK === $result->name) {
            $data = $this->getFacebookInfo($channel_id);
        }
        if (Platform::WEBSITE === $result->name) {
            $data = $this->getWebsiteInfo($channel_id);
        }
        return ['logo' => $data[1], 'name' => $data[0]];
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

    public function queryWithCustomFormat(Request $request)
    {
        $model = parent::queryWithCustomFormat($request);
        $global = app(GlobalVariable::class);
        $user = $global->currentUser;
        $abilities = User::ABILITIES;
        
        if (Gate::allows($abilities[0])) {
            return $model;
        } else if (Gate::allows($abilities[1])) {
            // Get user's teams
            $teamIDs = DB::table(TeamUser::retrieveTableName())
                ->where('user_id', '=', $user->id)
                ->pluck('team_id');

            // Get all user from all the team
            $userIds = DB::table(TeamUser::retrieveTableName())
                ->whereIn('team_id', $teamIDs)
                ->pluck('user_id');

            // Get all channel from all the user
            $channels = DB::table(ChannelUser::retrieveTableName())
                ->whereIn('user_id', $userIds)
                ->pluck('channel_id');

            // Get all unassigned channel
            $unassignedChannels = DB::table(ChannelUser::retrieveTableName())
                ->where('is_responsible', '=', 0)
                ->pluck('channel_id');

            if (count($unassignedChannels) <= 0) {
                return $model->whereIn('id', $channels)->toQuery()->paginate(BaseModel::CUSTOM_LIMIT);
            }

            // Combine all channel id
            $channelsId = $channels->merge($unassignedChannels)->unique();

            return $model->whereIn('id', $channelsId)->toQuery()->paginate(BaseModel::CUSTOM_LIMIT);
        } else if (Gate::allows($abilities[2])) {
            $channelIds = DB::table(ChannelUser::retrieveTableName())
                ->where('is_responsible', '=', 0)
                ->pluck('channel_id');

            if (count($channelIds) <= 0) {
                $result = new Paginator($channelIds, BaseModel::CUSTOM_LIMIT);
                return $result->setPath(request()->url());
            }

            return $model->whereIn('id', $channelIds)->toQuery()->paginate(BaseModel::CUSTOM_LIMIT);
        }
    }

    /**
     * @return BelongsTo
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * @return HasMany
     */
    public function categoryChannel(): hasMany
    {
        return $this->hasMany(CategoryChannel::class);
    }

    /**
     * @return HasMany
     */
    public function channelGroup(): hasMany
    {
        return $this->hasMany(ChannelGroup::class);
    }
}
