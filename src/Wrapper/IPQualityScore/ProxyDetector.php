<?php

declare(strict_types=1);

namespace NeiroNetwork\SyncDiscordXbox\Wrapper\IPQualityScore;

use NeiroNetwork\SyncDiscordXbox\Wrapper\IPQualityScore\Exception\ProxyDetectionException;
use NeiroNetwork\SyncDiscordXbox\Wrapper\IPQualityScore\Model\FreeProxyDetectResult;

class ProxyDetector{

	private \CurlHandle $curlHandle;

	public function __construct(private string $token){
		$this->curlHandle = curl_init("https://ipqualityscore.com/api/json/ip");
		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlHandle, CURLOPT_POST, true);
	}

	public function check(string $ip, int $strictness = 0, string $userAgent = "", string $userLanguage = "") : FreeProxyDetectResult{
		if(!filter_var($ip, FILTER_VALIDATE_IP)) throw new ProxyDetectionException("\"$ip\" is not a valid IP address");
		curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, [
			"key" => $this->token,
			"ip" => $ip,
			"strictness" => $strictness,
			"user_agent" => $userAgent,
			"user_language" => $userLanguage,
			"allow_public_access_points" => false,
		]);
		$response = curl_exec($this->curlHandle);
		if($response === false) throw new ProxyDetectionException(curl_error($this->curlHandle), curl_errno($this->curlHandle));
		$result = json_decode($response, true);
		if(($result["success"] ?? false) !== true) throw new ProxyDetectionException($result["message"] ?? "Failed to decode json");
		return new FreeProxyDetectResult($result);
	}
}