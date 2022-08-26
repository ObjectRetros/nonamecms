<?php

namespace App\Http\Middleware;

use App\Models\WebsiteIpBlacklist;
use App\Models\WebsiteIpWhitelist;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ipdata\ApiClient\Ipdata;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

class VPNCheckerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $httpClient = new Psr18Client();
        $psr17Factory = new Psr17Factory();
        $ipdata = new Ipdata(setting('ipdata_api_key'), $httpClient, $psr17Factory);

        $data = $ipdata->lookup($request->ip());

        if (array_key_exists('status', $data) && ($data['status'] === 400 || $data['status'] === 401)) {
            return $next($request);
        }

        // Skip check if vpn checker is disabled
        if (!(int)setting('vpn_block_enabled')) {
            return $next($request);
        }

        // Skip check if the rank is allowed to bypass the checker
        if (Auth::check() && Auth::user()->rank >= permission('min_rank_to_bypass_vpn_check')) {
            return $next($request);
        }

        // Fetch all whitelisted ASNs
        $asnWhitelist =  WebsiteIpWhitelist::query()
            ->select('asn')
            ->where('whitelist_asn', '=', '1')
            ->get()
            ->pluck('asn')
            ->toArray();

        // Skip check if the ASN is in the whitelist table & if "whitelist_asn" is true
        if ((array_key_exists('asn', $data) && array_key_exists('asn', $data['asn'])) && in_array($data['asn']['asn'], $asnWhitelist)) {
            return $next($request);
        }

        // Fetch all whitelisted IP addresses
        $ipWhitelist = WebsiteIpWhitelist::query()
            ->select('ip_address')
            ->get()
            ->pluck('ip_address')
            ->toArray();

        // Skip check if the IP is in the whitelist table
        if (in_array($request->ip(), $ipWhitelist)) {
            return $next($request);
        }

        // Check on the below + blacklist
        // Fetch all blacklisted ASNs
        $asnBlacklist =  WebsiteIpBlacklist::query()
            ->select('asn')
            ->where('blacklist_asn', '=', '1')
            ->get()
            ->pluck('asn')
            ->toArray();

        // Fetch all blacklisted IP addresses
        $ipBlacklist = WebsiteIpBlacklist::query()
            ->select('ip_address')
            ->get()
            ->pluck('ip_address')
            ->toArray();

        // Skip check if the IP is in the whitelist table
        if ((array_key_exists('asn', $data) && array_key_exists('asn', $data['asn']) && in_array($data['asn']['asn'], $asnBlacklist)) || in_array($request->ip(), $ipBlacklist)) {
            return to_route('me.show')->withErrors([
                'message' => __('We do not allow the usage of VPNs - If you think this is a mistake, you can contact the owner on our Discord.'),
            ]);
        }

        // Remove the following keys from the check
        if (array_key_exists('blocklists', $data['threat'])) {
            unset($data['threat']['blocklists']);
        }

        if (array_key_exists('is_icloud_relay', $data['threat'])) {
            unset($data['threat']['is_icloud_relay']);
        }

        if (array_key_exists('is_datacenter', $data['threat'])) {
            unset($data['threat']['is_datacenter']);
        }

        // If any of the below keys are true, restrict the user
        /*
            "is_tor"
            "is_proxy"
            "is_anonymous"
            "is_known_attacker"
            "is_known_abuser"
            "is_threat"
            "is_bogon"
         * */

        // If any of the above is true for the users IP, restrict and block their ip within the database
        if (array_key_exists('threat', $data) && in_array(true, array_values($data['threat']))) {
            // Add the ip & asn to the blacklist table
            WebsiteIpBlacklist::query()->create([
                'ip_address' => $request->ip(),
                'asn' => array_key_exists('asn', $data['asn']) ? $data['asn']['asn'] : null,
            ]);

            return to_route('me.show')->withErrors([
                'message' => __('We do not allow the usage of VPNs - If you think this is a mistake, you can contact the owner on our Discord.'),
            ]);
        }

        return $next($request);
    }
}