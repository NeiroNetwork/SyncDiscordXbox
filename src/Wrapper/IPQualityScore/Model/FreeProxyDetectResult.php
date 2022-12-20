<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Wrapper\IPQualityScore\Model;

final class FreeProxyDetectResult{

	public readonly int $fraudScore;
	public readonly string $countryCode;
	public readonly string $region;
	public readonly string $city;
	public readonly string $internetServiceProvider;
	public readonly int $autonomousSystemNumber;
	public readonly string $operatingSystem;
	public readonly string $browser;
	public readonly string $organization;
	public readonly bool $isCrawler;
	public readonly string $timezone;
	public readonly bool $mobile;
	public readonly string $host;
	public readonly bool $proxy;
	public readonly bool $vpn;
	public readonly bool $tor;
	public readonly bool $activeVpn;
	public readonly bool $activeTor;
	public readonly string $deviceBrand;
	public readonly string $deviceModel;
	public readonly bool $recentAbuse;
	public readonly bool $botStatus;
	public readonly string $zipCode;
	public readonly float $latitude;
	public readonly float $longitude;
	public readonly string $requestId;

	public readonly string $rawJson;

	public function __construct(array $result){
		$this->fraudScore = $result["fraud_score"];
		$this->countryCode = $result["country_code"];
		$this->region = $result["region"];
		$this->city = $result["city"];
		$this->internetServiceProvider = $result["ISP"];
		$this->autonomousSystemNumber = $result["ASN"];
		$this->operatingSystem = $result["operating_system"];
		$this->browser = $result["browser"];
		$this->organization = $result["organization"];
		$this->isCrawler = $result["is_crawler"];
		$this->timezone = $result["timezone"];
		$this->mobile = $result["mobile"];
		$this->host = $result["host"];
		$this->proxy = $result["proxy"];
		$this->vpn = $result["vpn"];
		$this->tor = $result["tor"];
		$this->activeVpn = $result["active_vpn"];
		$this->activeTor = $result["active_tor"];
		$this->deviceBrand = $result["device_brand"];
		$this->deviceModel = $result["device_model"];
		$this->recentAbuse = $result["recent_abuse"];
		$this->botStatus = $result["bot_status"];
		$this->zipCode = $result["zip_code"];
		$this->latitude = $result["latitude"];
		$this->longitude = $result["longitude"];
		$this->requestId = $result["request_id"];

		$this->rawJson = json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}
}