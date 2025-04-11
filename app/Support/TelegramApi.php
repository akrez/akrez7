<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class TelegramApi
{
    protected string $fullTelegramToken;

    public function __construct(protected string $telegramToken)
    {
        $this->fullTelegramToken = 'bot'.$telegramToken;
    }

    private function getUrl($path)
    {
        return implode('/', [
            config('services.telegram_bot.base_url'),
            $this->fullTelegramToken,
            $path,
        ]);
    }

    private function sendPostForm($path, $data = [], $headers = [])
    {
        $url = $this->getUrl($path);

        return Http::withHeaders($headers)
            ->asForm()
            ->post($url, $data)
            ->json();
    }

    public function getMe()
    {
        return $this->sendPostForm('getMe');
    }

    public function setMyCommands($commands, $optionalParameters = [])
    {
        $requiredParameters = [
            'commands' => [],
        ];

        foreach ($commands as $command => $description) {
            $requiredParameters['commands'][] = [
                'command' => $command,
                'description' => $description,
            ];
        }
        $requiredParameters['commands'] = json_encode($requiredParameters['commands']);

        return $this->sendPostForm('setMyCommands', array_replace_recursive(
            $optionalParameters,
            $requiredParameters
        ));
    }

    public function getUpdates($offset = null, $limit = 100)
    {
        return $this->sendPostForm('getUpdates', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public function sendMessage($chatId, $text, $optionalParameters = [])
    {
        $requiredParameters = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        return $this->sendPostForm('sendMessage', array_replace_recursive(
            $optionalParameters,
            $requiredParameters
        ));
    }

    public function sendMediaGroup($chatId, $mediaArray, $optionalParameters = [])
    {
        $requiredParameters = [
            'chat_id' => $chatId,
            'media' => json_encode(array_values($mediaArray)),
        ];

        return $this->sendPostForm('sendMediaGroup', array_replace_recursive(
            $optionalParameters,
            $requiredParameters
        ));
    }

    public function setMyName($name, $optionalParameters = [])
    {
        $requiredParameters = [
            'name' => strval($name),
        ];

        return $this->sendPostForm('setMyName', array_replace_recursive(
            $optionalParameters,
            $requiredParameters
        ));
    }

    public function setMyDescription($description, $optionalParameters = [])
    {
        $requiredParameters = [
            'description' => $description,
        ];

        return $this->sendPostForm('setMyDescription', array_replace_recursive(
            $optionalParameters,
            $requiredParameters
        ));
    }

    public function setMyShortDescription($shortDescription, $optionalParameters = [])
    {
        $requiredParameters = [
            'short_description' => $shortDescription,
        ];

        return $this->sendPostForm('setMyShortDescription', array_replace_recursive(
            $optionalParameters,
            $requiredParameters
        ));
    }
}
