<?php

namespace App\Http\Resources\Payvoice;

use App\Http\Resources\JsonResource;
use App\Models\Payvoice;
use Illuminate\Http\Request;

class PayvoiceResource extends JsonResource
{
    const DEVICE_ICON = [
        'desktop' => 'images/device/desktop.svg',
        'phone' => 'images/device/phone.svg',
        'tablet' => 'images/device/tablet.svg',
        'robot' => 'images/device/robot.svg',
    ];

    const PLATFORM_ICON = [
        'AndroidOS' => 'images/platform/AndroidOS.svg',
        'ChromeOS' => 'images/platform/ChromeOS.svg',
        'iOS' => 'images/platform/iOS.svg',
        'Linux' => 'images/platform/Linux.svg',
        'Ubuntu' => 'images/platform/Ubuntu.svg',
        'Windows' => 'images/platform/Windows.svg',
    ];

    const BROWSER_ICON = [
        'Chrome' => 'images/browser/Chrome.svg',
        'Edge' => 'images/browser/Edge.svg',
        'Firefox' => 'images/browser/Firefox.svg',
        'IE' => 'images/browser/IE.svg',
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'useragent' => $this->useragent,
            'useragent_robot' => [
                'text' => $this->useragent_robot,
                'icon' => null,
            ],
            'useragent_browser' => [
                'text' => $this->useragent_browser,
                'icon' => $this->getBrowserIcon($this->resource),
            ],
            'useragent_device' => [
                'text' => $this->useragent_device,
                'icon' => $this->getDeviceIcon($this->resource),
            ],
            'useragent_platform' => [
                'text' => $this->useragent_platform,
                'icon' => $this->getPlatformIcon($this->resource),
            ],
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
        ];
    }

    public function getDeviceIcon(Payvoice $payvoice)
    {
        if (array_key_exists($payvoice->useragent_device, static::DEVICE_ICON)) {
            return asset(static::DEVICE_ICON[$payvoice->useragent_device]);
        }

        return null;
    }

    public function getPlatformIcon(Payvoice $payvoice)
    {
        if (array_key_exists($payvoice->useragent_platform, static::PLATFORM_ICON)) {
            return asset(static::PLATFORM_ICON[$payvoice->useragent_platform]);
        }

        return null;
    }

    public function getBrowserIcon(Payvoice $payvoice)
    {
        if (array_key_exists($payvoice->useragent_browser, static::BROWSER_ICON)) {
            return asset(static::BROWSER_ICON[$payvoice->useragent_browser]);
        }

        return null;
    }
}
